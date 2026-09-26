<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaseDocument extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'original_name',
        'file_path',
        'mime_type',
        'size',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Human-readable file size (KB / MB).
     */
    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->size;

        if ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        }
        if ($bytes >= 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Icon keyword based on mime type.
     */
    public function getIconAttribute(): string
    {
        $mime = (string) $this->mime_type;

        if (str_contains($mime, 'pdf')) {
            return 'pdf';
        }
        if (str_contains($mime, 'image')) {
            return 'image';
        }
        if (str_contains($mime, 'word') || str_contains($mime, 'document')) {
            return 'doc';
        }

        return 'file';
    }
}