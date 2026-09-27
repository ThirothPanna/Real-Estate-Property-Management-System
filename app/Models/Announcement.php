<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'title',
        'body',
        'audience',
        'property_id',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function recipients()
    {
        return $this->belongsToMany(User::class, 'announcement_recipient')
                    ->withPivot('read_at')
                    ->withTimestamps();
    }

    public function getRecipientCountAttribute(): int
    {
        return $this->recipients()->count();
    }
}