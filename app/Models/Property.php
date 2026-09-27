<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'name',
        'address',
        'city',
        'state',
        'zip',
        'country',
        'type',
        'bedrooms',
        'bathrooms',
        'square_feet',
        'rent_amount',
        'description',
        'status',
        'latitude',
        'longitude',
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

    public function getFullAddressAttribute(): string
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->zip,
            $this->country,
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

    /**
     * Cover image URL — first tries cover photo, then any photo, then inline placeholder.
     */
    public function getCoverUrlAttribute(): string
    {
        if ($this->relationLoaded('photos')) {
            $cover = $this->photos->firstWhere('is_cover', true) ?? $this->photos->first();
            if ($cover) {
                return $cover->url;
            }
        }

        $cover = $this->photos()->where('is_cover', true)->first()
              ?? $this->photos()->first();

        if ($cover) {
            return $cover->url;
        }

        return PropertyPhoto::placeholderSvg();
    }

    public function getImageUrlAttribute(): string
    {
        return $this->cover_url;
    }

    public function getPhotosCountAttribute(): int
    {
        return $this->photos()->count();
    }

    public function getMapEmbedUrlAttribute(): string
    {
        $q = ($this->latitude && $this->longitude)
            ? ($this->latitude . ',' . $this->longitude)
            : urlencode($this->full_address);

        return 'https://www.google.com/maps?q=' . $q . '&output=embed';
    }

    public function getMapLinkAttribute(): string
    {
        $q = ($this->latitude && $this->longitude)
            ? ($this->latitude . ',' . $this->longitude)
            : urlencode($this->full_address);

        return 'https://www.google.com/maps/search/?api=1&query=' . $q;
    }
}