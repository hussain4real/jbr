<?php

use App\Actions\ManageResortUsers;
use App\Filament\Resources\Users\Pages\ManageUsers;
use App\Http\Controllers\Settings\ProfileController;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Models\Faq;
use App\Models\GuestRequest;
use App\Models\User;
use Filament\Actions\Testing\TestAction;
use Filament\Auth\Notifications\ResetPassword;
use Filament\Facades\Filament;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\HttpException;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
});

test('only an active super administrator can manage users', function (?string $role, bool $active, bool $allowed) {
    $actor = User::factory()->create(['resort_role' => $role, 'is_staff' => $active]);
    $target = User::factory()->create();
    expect(Gate::forUser($actor)->allows('viewAny', User::class))->toBe($allowed)
        ->and(Gate::forUser($actor)->allows('create', User::class))->toBe($allowed)
        ->and(Gate::forUser($actor)->allows('update', $target))->toBe($allowed)
        ->and(Gate::forUser($actor)->allows('delete', $target))->toBeFalse();
})->with([
    'ordinary account' => [null, false, false],
    'legacy administrator' => [null, true, false],
    'staff' => ['staff', true, false],
    'administrator' => ['admin', true, false],
    'super administrator' => ['super_admin', true, true],
    'disabled super administrator' => ['super_admin', false, false],
    'unknown role' => ['unknown', true, false],
]);

test('roles grant only their intended content and inbox permissions', function (?string $role, bool $active, bool $website, bool $inbox) {
    $user = User::factory()->create(['resort_role' => $role, 'is_staff' => $active]);
    expect(Gate::forUser($user)->allows('create', Faq::class))->toBe($website)
        ->and(Gate::forUser($user)->allows('viewAny', GuestRequest::class))->toBe($inbox)
        ->and($user->canAccessPanel(Filament::getPanel('admin')))->toBe($inbox);
})->with([
    'legacy retained' => [null, true, true, true],
    'staff' => ['staff', true, false, true],
    'administrator' => ['admin', true, true, true],
    'super administrator' => ['super_admin', true, true, true],
    'disabled' => ['super_admin', false, false, false],
    'invalid role' => ['other', true, false, false],
]);

test('users route denies staff and still requires the resort guard and MFA', function () {
    $this->get('/admin/users')->assertRedirect('/admin/login');
    $staff = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_STAFF, 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
    $this->actingAs($staff, 'resort')->get('/admin/users')->assertForbidden();
    Filament::auth()->logout();
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $this->actingAs($super, 'web')->get('/admin/users')->assertRedirect('/admin/login');
    $this->actingAs($super, 'resort')->get('/admin/users')->assertRedirect(route('filament.admin.auth.multi-factor-authentication.set-up-required'));
});

test('an enrolled super administrator can open user management', function () {
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN, 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
    $this->actingAs($super, 'resort')->get('/admin/users')->assertOk()->assertSee('Create resort account');
});

test('unknown access roles are rejected before an account is created', function () {
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    expect(fn () => app(ManageResortUsers::class)->create($super, ['name' => 'Invalid', 'email' => 'invalid@example.com', 'resort_role' => 'owner']))->toThrow(ValidationException::class);
    expect(User::query()->where('email', 'invalid@example.com')->exists())->toBeFalse();
});

test('a super administrator can create a separate account without sending credentials or an email', function () {
    Notification::fake();
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $existing = User::factory()->create(['name' => 'Abdulla Al Hajri', 'email' => 'admin@jauharat.com', 'is_staff' => true]);
    $passwordHash = $existing->password;
    $this->actingAs($super, 'resort');
    Livewire::test(ManageUsers::class)->callAction('create', [
        'name' => 'Lahusseine', 'email' => 'Lhassnaoui@tamkeen-hq.com', 'resort_role' => User::ROLE_ADMIN,
    ])->assertHasNoActionErrors();
    $created = User::query()->where('email', 'lhassnaoui@tamkeen-hq.com')->sole();
    expect($created->name)->toBe('Lahusseine')->and($created->resort_role)->toBe(User::ROLE_ADMIN)
        ->and($created->is_staff)->toBeTrue()->and($created->password)->toBeNull();
    expect($existing->fresh()->password)->toBe($passwordHash);
    expect($existing->fresh()->resort_role)->toBeNull();
    Notification::assertNothingSent();
});

test('user management rejects duplicate emails ignoring case', function () {
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    User::factory()->create(['email' => 'Existing@Example.com']);
    expect(fn () => app(ManageResortUsers::class)->create($super, ['name' => 'Duplicate', 'email' => 'existing@example.com', 'resort_role' => User::ROLE_STAFF]))
        ->toThrow(ValidationException::class, 'An account with this email address already exists.');
    expect(User::query()->count())->toBe(2);
});

test('duplicate email errors from the account action are visible in the creation form', function () {
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    User::factory()->create(['email' => 'duplicate@example.invalid']);
    $this->actingAs($super, 'resort');
    Livewire::test(ManageUsers::class)->callAction('create', [
        'name' => 'Duplicate', 'email' => 'DUPLICATE@example.invalid', 'resort_role' => User::ROLE_STAFF,
    ])->assertHasActionErrors(['email']);
    expect(User::query()->count())->toBe(2);
});

test('a super administrator can change another account role and disable access without changing credentials or MFA', function () {
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $target = User::factory()->create(['is_staff' => true, 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
    $before = $target->getAttributes();
    $this->actingAs($super, 'resort');
    Livewire::test(ManageUsers::class)->callAction(TestAction::make('edit')->table($target), [
        'name' => $target->name, 'resort_role' => User::ROLE_STAFF, 'is_staff' => false,
    ])->assertHasNoActionErrors();
    $target->refresh();
    expect($target->resort_role)->toBe(User::ROLE_STAFF)->and($target->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
    expect($target->getAttributes())->toMatchArray(['password' => $before['password'], 'email' => $before['email'], 'app_authentication_secret' => $before['app_authentication_secret']]);
});

test('service authorization prevents forged role changes and stale super administrator access', function () {
    $staff = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_ADMIN]);
    $target = User::factory()->create();
    expect(fn () => app(ManageResortUsers::class)->update($staff, $target, ['name' => 'Changed', 'resort_role' => User::ROLE_SUPER_ADMIN, 'is_staff' => true]))->toThrow(AuthorizationException::class);
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    User::query()->whereKey($super->id)->update(['is_staff' => false]);
    expect(fn () => app(ManageResortUsers::class)->create($super, ['name' => 'Forged', 'email' => 'forged@example.com', 'resort_role' => User::ROLE_SUPER_ADMIN]))->toThrow(AuthorizationException::class);
    expect($target->fresh()->is_staff)->toBeFalse();
    expect(User::query()->where('email', 'forged@example.com')->exists())->toBeFalse();
});

test('role and authentication fields cannot be mass assigned or injected into a management update', function () {
    $ordinary = User::factory()->create();
    $ordinary->fill(['resort_role' => User::ROLE_SUPER_ADMIN, 'is_staff' => true])->save();
    expect($ordinary->fresh()->isSuperAdmin())->toBeFalse();
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $password = $ordinary->password;
    app(ManageResortUsers::class)->update($super, $ordinary, [
        'name' => 'Approved', 'resort_role' => User::ROLE_STAFF, 'is_staff' => true,
        'password' => 'attacker', 'email' => 'attacker@example.com', 'app_authentication_secret' => 'forged',
    ]);
    expect($ordinary->fresh()->password)->toBe($password)->and($ordinary->fresh()->email)->toBe($ordinary->email)
        ->and($ordinary->fresh()->app_authentication_secret)->toBeNull();
});

test('a super administrator cannot change their own access or remove the final super administrator', function () {
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    expect(fn () => app(ManageResortUsers::class)->update($super, $super, ['name' => $super->name, 'resort_role' => User::ROLE_STAFF, 'is_staff' => false]))->toThrow(AuthorizationException::class);
    $this->actingAs($super, 'web')->delete(route('profile.destroy'), ['password' => 'password'])->assertForbidden();
    $this->artisan('website:staff', ['email' => $super->email, '--revoke' => true])->assertFailed();
    expect($super->fresh()->isSuperAdmin())->toBeTrue();
});

test('a super administrator can demote another super administrator while keeping their own access', function () {
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $other = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    app(ManageResortUsers::class)->update($super, $other, ['name' => $other->name, 'resort_role' => User::ROLE_ADMIN, 'is_staff' => true]);
    expect($other->fresh()->isSuperAdmin())->toBeFalse()->and($super->fresh()->isSuperAdmin())->toBeTrue();
});

test('setup links use the resort reset flow and reject disabled accounts', function () {
    Notification::fake();
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $target = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_STAFF]);
    $this->actingAs($super, 'resort');
    Livewire::test(ManageUsers::class)->callAction(TestAction::make('sendSetupLink')->table($target))->assertHasNoActionErrors();
    Notification::assertSentTo($target, ResetPassword::class, fn (ResetPassword $notification): bool => str_contains($notification->url, '/admin/password-reset/reset') && str_contains($notification->url, urlencode($target->email)));
    $target->forceFill(['is_staff' => false])->save();
    expect(fn () => app(ManageResortUsers::class)->sendSetupLink($super, $target))->toThrow(AuthorizationException::class);
});

test('bootstrap requires an explicit existing account and never replaces an existing super administrator', function () {
    $target = User::factory()->create(['is_staff' => true]);
    $hash = $target->password;
    expect(fn () => app(ManageResortUsers::class)->bootstrap('missing@example.com'))->toThrow(ValidationException::class);
    $this->artisan('website:super-admin', ['email' => $target->email, '--no-interaction' => true])->assertFailed();
    expect($target->fresh()->isSuperAdmin())->toBeFalse();
    app(ManageResortUsers::class)->bootstrap($target->email);
    expect($target->fresh()->isSuperAdmin())->toBeTrue()->and($target->fresh()->password)->toBe($hash);
    $other = User::factory()->create();
    expect(fn () => app(ManageResortUsers::class)->bootstrap($other->email))->toThrow(ValidationException::class);
    expect($other->fresh()->isSuperAdmin())->toBeFalse();
});

test('server provisioning defaults new staff to inbox access and preserves existing administrator access', function () {
    $this->artisan('website:staff', ['email' => 'new-staff@example.com', '--name' => 'New Staff'])
        ->expectsQuestion('Password (at least 12 characters, mixed case, letters and numbers)', 'TestStaffPassword42')
        ->assertSuccessful();
    $staff = User::query()->where('email', 'new-staff@example.com')->sole();
    expect($staff->canManageGuestRequests())->toBeTrue()->and($staff->canManageWebsite())->toBeFalse();
    $existing = User::factory()->create(['is_staff' => true]);
    $this->artisan('website:staff', ['email' => $existing->email])->assertSuccessful();
    expect($existing->fresh()->canManageWebsite())->toBeTrue();
});

test('server granting an existing ordinary account gives only staff access', function () {
    $ordinary = User::factory()->create();
    $this->artisan('website:staff', ['email' => $ordinary->email])->assertSuccessful();
    expect($ordinary->fresh()->resort_role)->toBe(User::ROLE_STAFF)
        ->and($ordinary->fresh()->canManageGuestRequests())->toBeTrue()
        ->and($ordinary->fresh()->canManageWebsite())->toBeFalse();
});

test('a stale revoke cannot disable an account bootstrapped after the command preflight read', function () {
    $target = User::factory()->create(['is_staff' => true]);
    $promoted = false;
    DB::listen(function (QueryExecuted $query) use ($target, &$promoted): void {
        if (! $promoted && str_contains($query->sql, 'LOWER(email)')) {
            $promoted = true;
            app(ManageResortUsers::class)->bootstrap($target->email);
        }
    });
    $this->artisan('website:staff', ['email' => $target->email, '--revoke' => true])->assertFailed();
    expect($promoted)->toBeTrue()->and($target->fresh()->isSuperAdmin())->toBeTrue();
});

test('profile deletion rechecks an account promoted after request authorization', function () {
    $target = User::factory()->create();
    $request = ProfileDeleteRequest::create('/settings/profile', 'DELETE');
    $request->setUserResolver(fn (): User => $target);
    expect($request->authorize())->toBeTrue();
    $users = app(ManageResortUsers::class);
    $users->bootstrap($target->email);
    expect(fn () => app(ProfileController::class)->destroy($request, $users))->toThrow(HttpException::class);
    expect($target->fresh()->isSuperAdmin())->toBeTrue();
});

test('revoked legacy administrators retain their role and cannot delete their profile', function () {
    $target = User::factory()->create(['is_staff' => true]);
    $this->artisan('website:staff', ['email' => $target->email, '--revoke' => true])->assertSuccessful();
    expect($target->fresh()->resort_role)->toBe(User::ROLE_ADMIN);
    $this->actingAs($target->fresh(), 'web')->delete(route('profile.destroy'), ['password' => 'password'])->assertForbidden();
    $this->artisan('website:staff', ['email' => $target->email])->assertSuccessful();
    expect($target->fresh()->canManageWebsite())->toBeTrue();
});

test('disabling an account blocks its already established resort session on the next request', function () {
    $super = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_SUPER_ADMIN]);
    $staff = User::factory()->create(['is_staff' => true, 'resort_role' => User::ROLE_STAFF, 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
    $session = [Auth::guard('resort')->getName() => $staff->id];
    $this->withSession($session)->get('/admin')->assertOk();
    app(ManageResortUsers::class)->update($super, $staff, ['name' => $staff->name, 'resort_role' => User::ROLE_STAFF, 'is_staff' => false]);
    Auth::guard('resort')->forgetUser();
    $this->withSession($session)->get('/admin')->assertForbidden();
});

test('the additive role migration preserves legacy users and authentication data without promoting anyone', function () {
    Schema::table('users', function (Blueprint $table): void {
        $table->dropColumn('resort_role');
    });
    $legacy = User::factory()->create(['is_staff' => true, 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
    $ordinary = User::factory()->create();
    $before = $legacy->getAttributes();
    $migration = require database_path('migrations/2026_10_13_084124_add_resort_role_to_users_table.php');
    $migration->up();
    expect($legacy->fresh()->canManageWebsite())->toBeTrue()->and($legacy->fresh()->isSuperAdmin())->toBeFalse();
    expect($legacy->fresh()->getAttributes())->toMatchArray($before);
    expect($ordinary->fresh()->canAccessPanel(Filament::getPanel('admin')))->toBeFalse();
    expect(User::query()->whereNotNull('resort_role')->exists())->toBeFalse();
});
