<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class PropertyPhoto extends Model
{
    use HasFactory;

    protected $fillable = ['property_id', 'file_path', 'is_cover', 'sort_order'];

    protected $casts = ['is_cover' => 'boolean'];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getUrlAttribute(): string
    {
        if ($this->file_path && Storage::disk('public')->exists($this->file_path)) {
            return Storage::disk('public')->url($this->file_path);
        }
        return 'https://via.placeholder.com/800x500/e5e7eb/9ca3af?text=No+Photo';
    }
}