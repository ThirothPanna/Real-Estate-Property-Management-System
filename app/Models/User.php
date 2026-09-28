<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use App\Models\Application;

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
    /**
 * Applications submitted by this user.
 */
public function applications()
{
    return $this->hasMany(Application::class);
}

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
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        if ($this->avatar && $storage->exists($this->avatar)) {
            return $storage->url($this->avatar);
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

    public function announcements()
    {
        return $this->belongsToMany(Announcement::class, 'announcement_recipient')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }
}