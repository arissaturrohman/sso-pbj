<?php

namespace App\Models;

use App\Models\Application;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\Contracts\OAuthenticatable;
use Laravel\Passport\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements OAuthenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $fillable = [
        'name',
        'nip',
        'no_hp',
        'email',
        'password',
        'must_change_password',
        'status',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'must_change_password' => 'boolean',
            'status' => 'boolean',

            'last_login_at' => 'datetime',
        ];
    }

    public function applications()
    {
        return $this->belongsToMany(
            Application::class,
            'application_user'
        )
            ->withPivot([
                'role',
                'granted_by',
            ])
            ->withTimestamps();
    }
}
