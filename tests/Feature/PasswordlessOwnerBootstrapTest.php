<?php

use App\Actions\ManageResortUsers;
use App\Models\User;
use Filament\Auth\Pages\PasswordReset\ResetPassword;
use Filament\Facades\Filament;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Livewire;
use Symfony\Component\Console\Tester\CommandTester;

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('admin'));
    Notification::fake();
    $this->handoffFilename = 'test-'.Str::uuid().'.link';
    $this->handoffPath = storage_path('app/private/owner-setup-handoff/'.$this->handoffFilename);
});

afterEach(function () {
    if (is_file($this->handoffPath)) {
        unlink($this->handoffPath);
    }
});

test('first owner creation generates no password or email and preserves existing accounts', function () {
    $existing = User::factory()->create(['is_staff' => true]);
    $before = $existing->fresh()->getAttributes();
    $url = null;
    $owner = app(ManageResortUsers::class)->prepareOwnerSetup('Owner@Example.invalid', 'Owner', true, function (string $link) use (&$url): void {
        $url = $link;
    });
    expect($owner->email)->toBe('owner@example.invalid')->and($owner->password)->toBeNull()->and($owner->isSuperAdmin())->toBeTrue();
    expect($existing->fresh()->getAttributes())->toBe($before);
    expect(Auth::guard('resort')->attempt(['email' => $owner->email, 'password' => 'Anything42!']))->toBeFalse();
    expect(Auth::guard('web')->attempt(['email' => $owner->email, 'password' => 'Anything42!']))->toBeFalse();
    expect($url)->toContain('/admin/password-reset/reset');
    Notification::assertNothingSent();
});

test('native setup consumes its token once and still requires authenticator enrollment', function () {
    $url = '';
    $owner = app(ManageResortUsers::class)->prepareOwnerSetup('owner@example.invalid', 'Owner', true, function (string $link) use (&$url): void {
        $url = $link;
    });
    parse_str(parse_url($url, PHP_URL_QUERY), $query);
    $token = $query['token'];
    expect(Password::broker('users')->tokenExists($owner, $token))->toBeTrue();
    Livewire::test(ResetPassword::class, ['email' => $owner->email, 'token' => $token])
        ->fillForm(['password' => 'SyntheticPassword42!', 'passwordConfirmation' => 'SyntheticPassword42!'])
        ->call('resetPassword')->assertHasNoFormErrors();
    expect(Hash::check('SyntheticPassword42!', $owner->fresh()->password))->toBeTrue();
    expect(Password::broker('users')->tokenExists($owner, $token))->toBeFalse();
    $this->actingAs($owner->fresh(), 'resort')->get('/admin/users')
        ->assertRedirect(route('filament.admin.auth.multi-factor-authentication.set-up-required'));
    expect(fn () => app(ManageResortUsers::class)->prepareOwnerSetup($owner->email, null, false, static function (string $url): void {}))
        ->toThrow(ValidationException::class);
});

test('pending owner setup can be reissued but the old token expires and is invalidated', function () {
    $urls = [];
    $handoff = function (string $url) use (&$urls): void {
        $urls[] = $url;
    };
    $users = app(ManageResortUsers::class);
    $owner = $users->prepareOwnerSetup('owner@example.invalid', 'Owner', true, $handoff);
    parse_str(parse_url($urls[0], PHP_URL_QUERY), $first);
    expect(Password::broker('users')->tokenExists($owner, $first['token']))->toBeTrue();
    $users->prepareOwnerSetup($owner->email, null, false, $handoff);
    parse_str(parse_url($urls[1], PHP_URL_QUERY), $second);
    expect(Password::broker('users')->tokenExists($owner, $second['token']))->toBeTrue()
        ->and(Password::broker('users')->tokenExists($owner, $first['token']))->toBeFalse();
    $this->travel(61)->minutes();
    expect(Password::broker('users')->tokenExists($owner, $second['token']))->toBeFalse();
    expect(User::query()->count())->toBe(1)->and($owner->fresh()->password)->toBeNull();
});

test('passwordless bootstrap rejects duplicates and any existing super administrator', function () {
    $existing = User::factory()->create(['email' => 'Owner@Example.invalid']);
    $users = app(ManageResortUsers::class);
    $handoff = static function (string $url): void {};
    expect(fn () => $users->prepareOwnerSetup('owner@example.invalid', 'Owner', true, $handoff))->toThrow(ValidationException::class);
    $existing->forceFill(['resort_role' => User::ROLE_SUPER_ADMIN, 'is_staff' => false])->save();
    expect(fn () => $users->prepareOwnerSetup('new@example.invalid', 'New', true, $handoff))->toThrow(ValidationException::class);
    expect(fn () => $users->prepareOwnerSetup($existing->email, null, false, $handoff))->toThrow(ValidationException::class);
    expect(User::query()->count())->toBe(1);
});

test('a failed private handoff rolls back owner creation and its setup token', function () {
    expect(fn () => app(ManageResortUsers::class)->prepareOwnerSetup('owner@example.invalid', 'Owner', true, static function (string $url): void {
        throw new RuntimeException('Private storage unavailable');
    }))->toThrow(RuntimeException::class);
    expect(User::query()->exists())->toBeFalse()->and(DB::table('password_reset_tokens')->exists())->toBeFalse();
});

test('operator creation requires confirmation and writes the link exclusively with private permissions', function () {
    $command = new CommandTester(Artisan::all()['website:super-admin']);
    $command->setInputs(['yes']);
    expect($command->execute(['email' => 'owner@example.invalid', '--create' => true, '--name' => 'Owner', '--setup-file' => $this->handoffFilename], ['interactive' => true]))->toBe(0);
    expect($command->getDisplay())->toContain('Password setup prepared in private storage: '.$this->handoffPath)->not->toContain('https://', 'http://', '?token=');
    expect(fileperms($this->handoffPath) & 0777)->toBe(0600)
        ->and(fileperms(dirname($this->handoffPath)) & 0777)->toBe(0700);
    $contents = file_get_contents($this->handoffPath);
    $command->setInputs(['yes']);
    expect($command->execute(['email' => 'owner@example.invalid', '--setup-file' => $this->handoffFilename], ['interactive' => true]))->toBe(1);
    expect(file_get_contents($this->handoffPath))->toBe($contents);
    Notification::assertNothingSent();
});

test('unsafe filenames and noninteractive owner creation cannot issue a token', function () {
    $command = new CommandTester(Artisan::all()['website:super-admin']);
    expect($command->execute(['email' => 'owner@example.invalid', '--create' => true, '--name' => 'Owner', '--setup-file' => '../outside.link'], ['interactive' => true]))->toBe(1);
    expect($command->execute(['email' => 'owner@example.invalid', '--create' => true, '--name' => 'Owner', '--setup-file' => $this->handoffFilename], ['interactive' => false]))->toBe(1);
    expect(User::query()->exists())->toBeFalse()->and(DB::table('password_reset_tokens')->exists())->toBeFalse()->and(file_exists($this->handoffPath))->toBeFalse();
});

test('cancelling full-access confirmation cannot create an owner or setup token', function () {
    $command = new CommandTester(Artisan::all()['website:super-admin']);
    $command->setInputs(['no']);
    expect($command->execute(['email' => 'owner@example.invalid', '--create' => true, '--name' => 'Owner', '--setup-file' => $this->handoffFilename], ['interactive' => true]))->toBe(1);
    expect(User::query()->exists())->toBeFalse()->and(DB::table('password_reset_tokens')->exists())->toBeFalse();
});

test('failed reissue preserves the pending owner and its previous usable token', function () {
    $url = '';
    $users = app(ManageResortUsers::class);
    $owner = $users->prepareOwnerSetup('owner@example.invalid', 'Owner', true, function (string $link) use (&$url): void {
        $url = $link;
    });
    parse_str(parse_url($url, PHP_URL_QUERY), $query);
    expect(fn () => $users->prepareOwnerSetup($owner->email, null, false, static function (string $link): void {
        throw new RuntimeException('Handoff unavailable');
    }))->toThrow(RuntimeException::class);
    expect(Password::broker('users')->tokenExists($owner, $query['token']))->toBeTrue()
        ->and($owner->fresh()->isSuperAdmin())->toBeTrue()->and($owner->fresh()->password)->toBeNull();
});

test('the nullable password migration preserves existing password and MFA verifiers', function () {
    Schema::table('users', function (Blueprint $table): void {
        $table->string('password')->nullable(false)->change();
    });
    $legacy = User::factory()->create(['is_staff' => true, 'app_authentication_secret' => 'JBSWY3DPEHPK3PXP']);
    $before = $legacy->fresh()->getAttributes();
    $migration = require database_path('migrations/2026_10_13_103139_allow_passwordless_resort_account_setup.php');
    $migration->up();
    expect($legacy->fresh()->getAttributes())->toBe($before);
    $pending = User::factory()->create(['password' => null]);
    expect($pending->password)->toBeNull()->and($pending->isSuperAdmin())->toBeFalse();
    expect(fn () => $migration->down())->toThrow(RuntimeException::class);
});
