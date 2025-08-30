<?php

namespace App\Models\Admin;

use Filament\Panel;
use Filament\Models\Contracts\HasName;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements FilamentUser, MustVerifyEmail, HasName
{
    /** @use HasFactory<\Database\Factories\AdminFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'password',
        'phone',
        'email_verified_at',
        'verified'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasVerifiedEmail() && $this->verified;
    }

    /**
     * Get the user's name for Filament.
     * This tells Filament to use first_name and last_name instead of name.
     */
    public function getFilamentName(): string
    {
        $firstName = $this->first_name ?? '';
        $lastName = $this->last_name ?? '';

        $fullName = trim($firstName . ' ' . $lastName);

        return $fullName ?: $this->email;
    }
}
