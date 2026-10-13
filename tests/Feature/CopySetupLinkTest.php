<?php

use App\Actions\ManageResortUsers;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Models\User;
use App\Providers\Filament\AdminPanelProvider;
use Filament\Actions\Testing\TestAction;
use Filament\Auth\Pages\PasswordReset\ResetPassword;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

function setupLinkOwner(): User
{
    return User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN, 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
}

function setupLinkTarget(): User
{
    return User::factory()->create(['email' => 'setup@example.invalid', 'is_staff' => true, 'resort_role' => User::ROLE_ADMIN, 'password' => null]);
}

function setupLinkToken(array $result): string
{
    parse_str(parse_url($result['url'], PHP_URL_QUERY), $query);

    return $query['token'];
}

test('copy setup uses the signed preview host, defaults to one hour and never sends email or logs a link', function () {
    $this->freezeTime();
    Notification::fake();
    Log::spy();
    config(['website.admin_domain' => 'preview.jauharat.com']);
    app(PanelRegistry::class)->register((new AdminPanelProvider(app()))->panel(Panel::make()));
    require base_path('vendor/filament/filament/routes/web.php');
    Route::getRoutes()->refreshNameLookups();
    URL::setRoutes(Route::getRoutes());
    URL::forceScheme('https');
    $owner = setupLinkOwner();
    $target = setupLinkTarget();
    $before = $target->fresh()->getAttributes();
    $result = app(ManageResortUsers::class)->generateSetupLink($owner, $target);
    $token = setupLinkToken($result);
    expect(parse_url($result['url'], PHP_URL_HOST))->toBe('preview.jauharat.com');
    expect(URL::hasValidSignature(Request::create($result['url'])))->toBeTrue();
    expect(URL::hasValidSignature(Request::create(str_replace('preview.jauharat.com', 'jauharat.com', $result['url']))))->toBeFalse();
    expect($result['expiresAt'])->toBe(now()->addHour()->toIso8601String());
    expect($target->fresh()->getAttributes())->toBe($before);
    expect(DB::table('password_reset_tokens')->sole()->token)->not->toBe($token);
    Log::shouldHaveReceived('notice')->once()->with('Resort user access private_setup_link_generated', [
        'actor_id' => $owner->id, 'target_id' => $target->id, 'role' => 'admin', 'active' => true,
    ]);
    Notification::assertNothingSent();
    $this->travel(60)->minutes();
    expect(Password::broker('users')->tokenExists($target, $token))->toBeFalse();
});

test('only enrolled active super administrators can generate setup links', function (?string $role, bool $active, ?string $mfa) {
    $actor = User::factory()->create(['is_staff' => $active, 'resort_role' => $role, 'app_authentication_secret' => $mfa]);
    $target = setupLinkTarget();
    expect(fn () => app(ManageResortUsers::class)->generateSetupLink($actor, $target))->toThrow(AuthorizationException::class);
    expect(DB::table('password_reset_tokens')->count())->toBe(0);
})->with([
    'ordinary' => [null, false, null], 'admin' => ['admin', true, 'JBSWY3DPEHPK3PXP'],
    'staff' => ['staff', true, 'JBSWY3DPEHPK3PXP'], 'disabled owner' => ['super_admin', false, 'JBSWY3DPEHPK3PXP'],
    'unenrolled owner' => ['super_admin', true, null],
]);

test('copy setup rejects ineligible accounts and stale actor authorization', function () {
    $owner = setupLinkOwner();
    $target = setupLinkTarget();
    $target->forceFill(['password' => 'already-set'])->save();
    expect(fn () => app(ManageResortUsers::class)->generateSetupLink($owner, $target))->toThrow(AuthorizationException::class);
    $target->forceFill(['password' => null, 'is_staff' => false])->save();
    expect(fn () => app(ManageResortUsers::class)->generateSetupLink($owner, $target))->toThrow(AuthorizationException::class);
    $target->forceFill(['is_staff' => true])->save();
    User::query()->whereKey($owner->id)->update(['is_staff' => false]);
    expect(fn () => app(ManageResortUsers::class)->generateSetupLink($owner, $target))->toThrow(AuthorizationException::class);
    expect(DB::table('password_reset_tokens')->count())->toBe(0);
});

test('unsupported lifetimes are rejected without issuing a token', function (int $minutes) {
    $owner = setupLinkOwner();
    $target = setupLinkTarget();
    expect(fn () => app(ManageResortUsers::class)->generateSetupLink($owner, $target, $minutes))->toThrow(ValidationException::class);
    expect(DB::table('password_reset_tokens')->count())->toBe(0);
})->with([0, 61, 901, -1]);

test('a repeated request is throttled and explicit later replacement invalidates the previous link', function () {
    $this->freezeTime();
    $owner = setupLinkOwner();
    $target = setupLinkTarget();
    $users = app(ManageResortUsers::class);
    $first = setupLinkToken($users->generateSetupLink($owner, $target));
    expect(fn () => $users->generateSetupLink($owner, $target, 900))->toThrow(ValidationException::class);
    expect(Password::broker('users')->tokenExists($target, $first))->toBeTrue();
    $this->travel(61)->seconds();
    $second = setupLinkToken($users->generateSetupLink($owner, $target, 900));
    expect(Password::broker('users')->tokenExists($target, $first))->toBeFalse();
    expect(Password::broker('users')->tokenExists($target, $second))->toBeTrue();
    expect(DB::table('password_reset_tokens')->count())->toBe(1);
});

test('extended setup survives cleanup after an hour, expires at fifteen hours and ordinary resets retain their default', function () {
    $this->freezeTime();
    $owner = setupLinkOwner();
    $target = setupLinkTarget();
    $ordinary = User::factory()->create();
    $broker = Password::broker('users');
    $normalToken = $broker->createToken($ordinary);
    $result = app(ManageResortUsers::class)->generateSetupLink($owner, $target, 900);
    $token = setupLinkToken($result);
    $this->travel(61)->minutes();
    $broker->getRepository()->deleteExpired();
    expect($broker->tokenExists($target, $token))->toBeTrue();
    expect($broker->tokenExists($ordinary, $normalToken))->toBeFalse();
    expect(config('auth.passwords.users.expire'))->toBe(60);
    $this->travel(839)->minutes();
    expect($broker->tokenExists($target, $token))->toBeFalse();
    $this->travel(1)->seconds();
    expect(URL::hasValidSignature(Request::create($result['url'])))->toBeFalse();
});

test('ordinary reset replacement clears the extended expiry', function () {
    $owner = setupLinkOwner();
    $target = setupLinkTarget();
    $setupToken = setupLinkToken(app(ManageResortUsers::class)->generateSetupLink($owner, $target, 900));
    $broker = Password::broker('users');
    $normalToken = $broker->createToken($target);
    expect(DB::table('password_reset_tokens')->sole()->setup_expires_at)->toBeNull();
    expect($broker->tokenExists($target, $setupToken))->toBeFalse();
    $this->travel(61)->minutes();
    expect($broker->tokenExists($target, $normalToken))->toBeFalse();
});

test('setup redemption sets the password once and still requires MFA enrollment', function () {
    $owner = setupLinkOwner();
    $target = setupLinkTarget();
    $result = app(ManageResortUsers::class)->generateSetupLink($owner, $target, 900);
    $token = setupLinkToken($result);
    Livewire::test(ResetPassword::class, ['email' => $target->email, 'token' => $token])
        ->fillForm(['password' => 'SetupPassword42!', 'passwordConfirmation' => 'SetupPassword42!'])
        ->call('resetPassword')->assertHasNoFormErrors();
    expect(Hash::check('SetupPassword42!', $target->fresh()->password))->toBeTrue();
    expect(DB::table('password_reset_tokens')->count())->toBe(0);
    $status = Password::broker('users')->reset(['email' => $target->email, 'token' => $token, 'password' => 'OtherPassword42!'], function (): void {
        throw new RuntimeException('A used token must not reach the callback.');
    });
    expect($status)->toBe(Password::INVALID_TOKEN);
    $this->actingAs($target->fresh(), 'resort')->get('/admin')->assertRedirect(route('filament.admin.auth.multi-factor-authentication.set-up-required'));
});

test('a setup token stops working when the account is disabled or a password is set elsewhere', function () {
    $owner = setupLinkOwner();
    $target = setupLinkTarget();
    $token = setupLinkToken(app(ManageResortUsers::class)->generateSetupLink($owner, $target));
    $target->forceFill(['is_staff' => false])->save();
    expect(Password::broker('users')->tokenExists($target->fresh(), $token))->toBeFalse();
    $target->forceFill(['is_staff' => true, 'password' => 'already-set'])->save();
    expect(Password::broker('users')->tokenExists($target->fresh(), $token))->toBeFalse();
});

test('opening and cancelling copy setup issues nothing and its Livewire response does not retain the link in component state', function () {
    $owner = setupLinkOwner();
    $target = setupLinkTarget();
    $this->actingAs($owner, 'resort');
    $page = Livewire::test(ManageUsers::class)->mountAction(TestAction::make('generateSetupLink')->table($target));
    expect(DB::table('password_reset_tokens')->count())->toBe(0);
    $page->call('unmountAction');
    expect(DB::table('password_reset_tokens')->count())->toBe(0);
    $page->call('generateSetupLink', $target->id, 60);
    expect(DB::table('password_reset_tokens')->count())->toBe(1);
    expect(json_encode($page->instance()->all()))->not->toContain('signature=')->not->toContain('expiresAt');
});
