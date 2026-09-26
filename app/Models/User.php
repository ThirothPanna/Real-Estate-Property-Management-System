<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'avatar',
        'role',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    public function getAvatarUrlAttribute(): string
    {
        if ($this->avatar && Storage::disk('public')->exists($this->avatar)) {
            return Storage::disk('public')->url($this->avatar);
        }

        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name)
             . '&background=22c55e&color=fff&size=200&bold=true';
    }

    public function isTenant(): bool
    {
        return $this->role === 'tenant';
    }

    public function isLandlord(): bool
    {
        return $this->role === 'landlord';
    }

    public function dashboardRoute(): string
    {
        return $this->isLandlord() ? 'landlord.dashboard' : 'tenant.dashboard';
    }

    public function tenancies()
    {
        return $this->hasMany(Tenancy::class, 'user_id');
    }

    public function currentTenancy()
    {
        return $this->hasOne(Tenancy::class, 'user_id')->where('status', 'active')->latest();
    }

    public function landlordTenancies()
    {
        return $this->hasMany(Tenancy::class, 'landlord_id');
    }
}