<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id', 'name', 'address', 'city', 'state', 'zip', 'country',
        'type', 'bedrooms', 'bathrooms', 'square_feet', 'rent_amount',
        'description', 'status', 'latitude', 'longitude',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
        'latitude'    => 'decimal:7',
        'longitude'   => 'decimal:7',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function photos()
    {
        return $this->hasMany(PropertyPhoto::class)->orderBy('sort_order');
    }

    public function tenancies()
    {
        return $this->hasMany(Tenancy::class);
    }

    public function activeTenancy()
    {
        return $this->hasOne(Tenancy::class)->where('status', 'active')->latest();
    }

    public function currentTenant()
    {
        return $this->activeTenancy?->tenant;
    }

    public function isOccupied(): bool
    {
        return $this->activeTenancy()->exists();
    }

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address, $this->city, $this->state, $this->zip, $this->country,
        ]);
        return implode(', ', $parts);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'available'   => 'background:#dcfce7; color:#16a34a;',
            'occupied'    => 'background:#dbeafe; color:#2563eb;',
            'maintenance' => 'background:#fef3c7; color:#d97706;',
            default       => 'background:#f3f4f6; color:#6b7280;',
        };
    }

    public function getCoverUrlAttribute(): string
    {
        $cover = $this->photos->firstWhere('is_cover', true) ?? $this->photos->first();
        return $cover ? $cover->url : 'https://via.placeholder.com/800x500/e5e7eb/9ca3af?text=No+Photo';
    }

    public function getPhotosCountAttribute(): int
    {
        return $this->photos->count();
    }

    public function getMapEmbedUrlAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            $q = $this->latitude . ',' . $this->longitude;
        } else {
            $q = urlencode($this->full_address);
        }
        return 'https://www.google.com/maps?q=' . $q . '&output=embed';
    }

    public function getMapLinkAttribute(): string
    {
        if ($this->latitude && $this->longitude) {
            $q = $this->latitude . ',' . $this->longitude;
        } else {
            $q = urlencode($this->full_address);
        }
        return 'https://www.google.com/maps/search/?api=1&query=' . $q;
    }
}