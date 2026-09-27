<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Document extends Model
{
    use HasFactory;

    protected $fillable = [
        'landlord_id',
        'property_id',
        'title',
        'file_path',
        'original_name',
        'mime_type',
        'size',
    ];

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function getUrlAttribute(): string
    {
        /** @var \Illuminate\Filesystem\FilesystemAdapter $storage */
        $storage = Storage::disk('public');

        if ($this->file_path && $storage->exists($this->file_path)) {
            return $storage->url($this->file_path);
        }
        return '#';
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function getIconAttribute(): string
    {
        $mime = (string) $this->mime_type;
        if (str_contains($mime, 'pdf'))                              return 'pdf';
        if (str_contains($mime, 'image'))                            return 'image';
        if (str_contains($mime, 'word') || str_contains($mime, 'document')) return 'doc';
        return 'file';
    }
}