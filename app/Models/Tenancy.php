<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tenancy extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'property_id',
        'landlord_id',
        'lease_start',
        'lease_end',
        'rent_amount',
        'security_deposit',
        'status',
    ];

    protected $casts = [
        'lease_start'      => 'date',
        'lease_end'        => 'date',
        'rent_amount'      => 'decimal:2',
        'security_deposit' => 'decimal:2',
    ];

    public function tenant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'active'  => 'background:#dcfce7; color:#16a34a;',
            'pending' => 'background:#fef3c7; color:#d97706;',
            'ended'   => 'background:#f3f4f6; color:#6b7280;',
            default   => 'background:#f3f4f6; color:#6b7280;',
        };
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}