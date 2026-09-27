<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Lease extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenancy_id',
        'landlord_id',
        'user_id',
        'title',
        'notes',
        'file_path',
        'original_name',
        'mime_type',
        'size',
        'acknowledged_at',
    ];

    protected $casts = [
        'acknowledged_at' => 'datetime',
    ];

    public function tenancy()
    {
        return $this->belongsTo(Tenancy::class);
    }

    public function landlord()
    {
        return $this->belongsTo(User::class, 'landlord_id');
    }

    public function tenant()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = $this->size;
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return number_format($bytes / 1024, 1) . ' KB';
        return $bytes . ' B';
    }

    public function isAcknowledged(): bool
    {
        return !is_null($this->acknowledged_at);
    }
}