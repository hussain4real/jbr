<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthentication;
use Filament\Auth\MultiFactor\App\Concerns\InteractsWithAppAuthenticationRecovery;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthentication;
use Filament\Auth\MultiFactor\App\Contracts\HasAppAuthenticationRecovery;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property Carbon|null $two_factor_confirmed_at
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property bool $is_staff
 * @property string|null $resort_role
 */
#[Fillable(['name', 'email', 'password'])]
#[Hidden(['password', 'two_factor_secret', 'two_factor_recovery_codes', 'remember_token'])]
class User extends Authenticatable implements FilamentUser, HasAppAuthentication, HasAppAuthenticationRecovery, PasskeyUser
{
    public const ROLE_STAFF = 'staff';

    public const ROLE_ADMIN = 'admin';

    public const ROLE_SUPER_ADMIN = 'super_admin';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    use InteractsWithAppAuthentication, InteractsWithAppAuthenticationRecovery;

    protected $attributes = ['is_staff' => false];

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->canManageGuestRequests();
    }

    /** @return array<string, string> */
    public static function resortRoles(): array
    {
        return [self::ROLE_STAFF => 'Staff — guest inbox', self::ROLE_ADMIN => 'Administrator — website and guest inbox', self::ROLE_SUPER_ADMIN => 'Super administrator — website, guest inbox and users'];
    }

    public function resortRole(): ?string
    {
        return $this->resort_role ?? ($this->is_staff ? self::ROLE_ADMIN : null);
    }

    public function isSuperAdmin(): bool
    {
        return $this->is_staff && $this->resortRole() === self::ROLE_SUPER_ADMIN;
    }

    public function canManageWebsite(): bool
    {
        return $this->is_staff && in_array($this->resortRole(), [self::ROLE_ADMIN, self::ROLE_SUPER_ADMIN], true);
    }

    public function canManageGuestRequests(): bool
    {
        return $this->is_staff && array_key_exists($this->resortRole() ?? '', self::resortRoles());
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_staff' => 'boolean',
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }
}
