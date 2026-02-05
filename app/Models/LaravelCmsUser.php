<?php

namespace App\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class LaravelCmsUser extends Authenticatable implements FilamentUser
{
    use SoftDeletes;

    // Pastikan table name didefinisikan kalau nama class tidak sesuai konvensi plural (LaravelCmsUser -> laravel_cms_users is correct, but let's be safe)
    protected $table = 'laravel_cms_users';

    protected $fillable = ['name', 'email', 'password'];
    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'password' => 'hashed',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }
}