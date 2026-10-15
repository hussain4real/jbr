<?php

use App\Actions\ManageResortUsers;
use App\Models\Faq;
use App\Models\GuestRequest;
use App\Models\User;
use App\Providers\Filament\AdminPanelProvider;
use Filament\Auth\Pages\PasswordReset\ResetPassword;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\PanelRegistry;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

test('private administrator setup is signed for the admin host and grants only website and inbox access', function () {
    Notification::fake();
    config(['website.admin_domain' => 'preview.example.invalid']);
    app(PanelRegistry::class)->register((new AdminPanelProvider(app()))->panel(Panel::make()));
    require base_path('vendor/filament/filament/routes/web.php');
    Route::getRoutes()->refreshNameLookups();
    URL::setRoutes(Route::getRoutes());
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    URL::forceScheme('https');
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $url = '';

    $user = app(ManageResortUsers::class)->prepareAccountSetup($owner, [
        'name' => 'New Administrator', 'email' => 'NewAdmin@Example.invalid', 'resort_role' => User::ROLE_ADMIN,
    ], function (string $link) use (&$url): void {
        $url = $link;
    });

    expect($user->email)->toBe('newadmin@example.invalid');
    expect($user->password)->toBeNull();
    expect($user->getAppAuthenticationSecret())->toBeNull();
    expect($user->resort_role)->toBe(User::ROLE_ADMIN);
    expect($user->is_staff)->toBeTrue();
    expect(Gate::forUser($user)->allows('create', Faq::class))->toBeTrue();
    expect(Gate::forUser($user)->allows('viewAny', GuestRequest::class))->toBeTrue();
    expect(Gate::forUser($user)->allows('viewAny', User::class))->toBeFalse();
    expect(Gate::forUser($user)->allows('create', User::class))->toBeFalse();
    expect(parse_url($url, PHP_URL_HOST))->toBe('preview.example.invalid');
    expect(URL::hasValidSignature(Request::create($url)))->toBeTrue();
    expect(URL::hasValidSignature(Request::create(str_replace('preview.example.invalid', 'public.example.invalid', $url))))->toBeFalse();
    $this->get($url)->assertOk()->assertSee('Reset password');
    $this->actingAs($user, 'resort')->get('https://preview.example.invalid/admin/users')
        ->assertRedirect(route('filament.admin.auth.multi-factor-authentication.set-up-required'));
    $user->forceFill(['app_authentication_secret' => 'JBSWY3DPEHPK3PXP'])->save();
    $this->get('https://preview.example.invalid/admin/users')->assertForbidden();
    Notification::assertNothingSent();
});

test('a private setup token is consumed by the resort password reset form', function () {
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $url = '';
    $user = app(ManageResortUsers::class)->prepareAccountSetup($owner, [
        'name' => 'Administrator', 'email' => 'single-use@example.invalid', 'resort_role' => User::ROLE_ADMIN,
    ], function (string $link) use (&$url): void {
        $url = $link;
    });
    parse_str(parse_url($url, PHP_URL_QUERY), $query);

    Livewire::test(ResetPassword::class, ['email' => $user->email, 'token' => $query['token']])
        ->fillForm(['email' => $user->email, 'password' => 'Only-in-tests123!', 'passwordConfirmation' => 'Only-in-tests123!'])
        ->call('resetPassword')->assertHasNoFormErrors();

    expect($user->fresh()->password)->not->toBeNull();
    $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    expect(Password::broker('users')->tokenExists($user, $query['token']))->toBeFalse();
});

test('private setup tokens expire after sixty minutes without granting a password', function () {
    $this->freezeTime();
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $url = '';
    $user = app(ManageResortUsers::class)->prepareAccountSetup($owner, [
        'name' => 'Administrator', 'email' => 'expires@example.invalid', 'resort_role' => User::ROLE_ADMIN,
    ], function (string $link) use (&$url): void {
        $url = $link;
    });
    parse_str(parse_url($url, PHP_URL_QUERY), $query);
    $broker = Password::broker('users');
    expect($broker->tokenExists($user, $query['token']))->toBeTrue();

    $this->travel(61)->minutes();

    expect($broker->tokenExists($user, $query['token']))->toBeFalse();
    expect($user->fresh()->password)->toBeNull();
});

test('ordinary and inactive actors cannot prepare private administrator setup', function (?string $role, bool $enabled) {
    $actor = User::factory()->create(['is_staff' => $enabled, 'resort_role' => $role]);

    expect(fn () => app(ManageResortUsers::class)->prepareAccountSetup($actor, [
        'name' => 'Denied', 'email' => 'denied@example.invalid', 'resort_role' => User::ROLE_ADMIN,
    ], function (): void {
        throw new RuntimeException('Handoff must not run.');
    }))->toThrow(AuthorizationException::class);

    $this->assertDatabaseMissing('users', ['email' => 'denied@example.invalid']);
    $this->assertDatabaseCount('password_reset_tokens', 0);
})->with(['ordinary' => [null, false], 'staff' => ['staff', true], 'admin' => ['admin', true], 'disabled owner' => ['super_admin', false]]);

test('private setup rechecks an owner disabled after it was loaded', function () {
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    User::query()->whereKey($owner->id)->update(['is_staff' => false]);

    expect(fn () => app(ManageResortUsers::class)->prepareAccountSetup($owner, [
        'name' => 'Denied', 'email' => 'stale@example.invalid', 'resort_role' => User::ROLE_ADMIN,
    ], function (): void {}))->toThrow(AuthorizationException::class);

    $this->assertDatabaseMissing('users', ['email' => 'stale@example.invalid']);
});

test('private setup rejects any role outside the approved administrator scope', function (?string $role) {
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);

    expect(fn () => app(ManageResortUsers::class)->prepareAccountSetup($owner, [
        'name' => 'Denied', 'email' => 'role@example.invalid', 'resort_role' => $role,
    ], function (): void {}))->toThrow(ValidationException::class);

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('password_reset_tokens', 0);
})->with(['missing' => [null], 'staff' => ['staff'], 'super administrator' => ['super_admin'], 'unknown' => ['owner']]);

test('private setup rejects an existing case-insensitive account without changing credentials or MFA', function () {
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $existing = User::factory()->create(['email' => 'Existing@Example.invalid', 'is_staff' => true, 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
    $before = $existing->fresh()->getAttributes();

    expect(fn () => app(ManageResortUsers::class)->prepareAccountSetup($owner, [
        'name' => 'Duplicate', 'email' => 'existing@example.invalid', 'resort_role' => User::ROLE_ADMIN,
    ], function (): void {}))->toThrow(ValidationException::class);

    expect($existing->fresh()->getAttributes())->toBe($before);
    $this->assertDatabaseCount('users', 2);
    $this->assertDatabaseCount('password_reset_tokens', 0);
});

test('handoff failure rolls back the pending account and its setup token', function () {
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);

    expect(fn () => app(ManageResortUsers::class)->prepareAccountSetup($owner, [
        'name' => 'Rollback', 'email' => 'rollback@example.invalid', 'resort_role' => User::ROLE_ADMIN,
    ], function (): void {
        throw new RuntimeException('Private destination unavailable.');
    }))->toThrow(RuntimeException::class);

    $this->assertDatabaseMissing('users', ['email' => 'rollback@example.invalid']);
    $this->assertDatabaseCount('password_reset_tokens', 0);
});

test('private setup refuses a broker with a longer expiry', function () {
    config(['auth.passwords.users.expire' => 120]);
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);

    expect(fn () => app(ManageResortUsers::class)->prepareAccountSetup($owner, [
        'name' => 'Denied', 'email' => 'duration@example.invalid', 'resort_role' => User::ROLE_ADMIN,
    ], function (): void {}))->toThrow(ValidationException::class);

    $this->assertDatabaseCount('users', 1);
});

test('the operator command requires confirmation and never creates an account noninteractively', function () {
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);

    $this->artisan('website:account-setup', ['email' => 'noninteractive@example.invalid', '--actor-email' => $owner->email,
        '--name' => 'Denied', '--role' => 'admin', '--setup-file' => 'noninteractive.link', '--no-interaction' => true])->assertFailed();

    $this->assertDatabaseCount('users', 1);
});

test('the operator command writes a private setup file and rejects filename collisions', function () {
    Notification::fake();
    $this->freezeTime();
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $filename = 'test-admin-'.Str::uuid().'.link';
    $path = storage_path('app/private/account-setup-handoff/'.$filename);
    $question = 'Create cli@example.invalid as admin (website publishing and guest inbox; no user management), approved by '.$owner->email.', with private password setup?';
    try {
        $this->artisan('website:account-setup', ['email' => 'cli@example.invalid', '--actor-email' => $owner->email,
            '--name' => 'Administrator', '--role' => 'admin', '--setup-file' => $filename])
            ->expectsConfirmation($question, 'yes')
            ->expectsOutput('Administrator created with no password or email. Authenticator MFA remains mandatory.')
            ->expectsOutput('Private setup file: '.$path)
            ->expectsOutput('One-use setup expires at '.now()->addHour()->utc()->format('Y-m-d H:i:s').' UTC.')
            ->assertSuccessful();
        $user = User::query()->where('email', 'cli@example.invalid')->sole();
        expect($user->password)->toBeNull();
        expect(fileperms($path) & 0777)->toBe(0600);
        expect(fileperms(dirname($path)) & 0777)->toBe(0700);
        $before = file_get_contents($path);
        $collisionQuestion = 'Create collision@example.invalid as admin (website publishing and guest inbox; no user management), approved by '.$owner->email.', with private password setup?';

        $this->artisan('website:account-setup', ['email' => 'collision@example.invalid', '--actor-email' => $owner->email,
            '--name' => 'Collision', '--role' => 'admin', '--setup-file' => $filename])
            ->expectsConfirmation($collisionQuestion, 'yes')->assertFailed();

        expect(file_get_contents($path))->toBe($before);
        $this->assertDatabaseMissing('users', ['email' => 'collision@example.invalid']);
        $this->assertDatabaseCount('password_reset_tokens', 1);
        Notification::assertNothingSent();
    } finally {
        @unlink($path);
    }
});

test('the operator command rejects unsafe destinations before creation', function () {
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);

    $this->artisan('website:account-setup', ['email' => 'unsafe@example.invalid', '--actor-email' => $owner->email,
        '--name' => 'Denied', '--role' => 'admin', '--setup-file' => '../unsafe.link'])->assertFailed();

    $this->assertDatabaseCount('users', 1);
});

test('the operator command rejects an administrator acting as the account creator', function () {
    $admin = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_ADMIN]);

    $this->artisan('website:account-setup', ['email' => 'denied-cli@example.invalid', '--actor-email' => $admin->email,
        '--name' => 'Denied', '--role' => 'admin', '--setup-file' => 'denied-cli.link'])
        ->expectsOutput('An existing active super administrator is required.')->assertFailed();

    $this->assertDatabaseCount('users', 1);
    $this->assertDatabaseCount('password_reset_tokens', 0);
});

test('the operator command refuses a symlinked private handoff directory', function () {
    $owner = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $previousStorage = storage_path();
    $root = sys_get_temp_dir().'/jbr-admin-setup-test-'.Str::uuid();
    mkdir($root.'/app/private', 0700, true);
    mkdir($root.'/destination', 0700);
    $directory = $root.'/app/private/account-setup-handoff';
    symlink($root.'/destination', $directory);
    app()->useStoragePath($root);
    try {
        $question = 'Create symlink@example.invalid as admin (website publishing and guest inbox; no user management), approved by '.$owner->email.', with private password setup?';

        $this->artisan('website:account-setup', ['email' => 'symlink@example.invalid', '--actor-email' => $owner->email,
            '--name' => 'Denied', '--role' => 'admin', '--setup-file' => 'symlink.link'])
            ->expectsConfirmation($question, 'yes')->assertFailed();

        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('password_reset_tokens', 0);
        expect(file_exists($root.'/destination/symlink.link'))->toBeFalse();
    } finally {
        app()->useStoragePath($previousStorage);
        unlink($directory);
        rmdir($root.'/app/private');
        rmdir($root.'/app');
        rmdir($root.'/destination');
        rmdir($root);
    }
});
