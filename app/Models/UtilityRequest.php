<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UtilityRequest extends Model
{
    protected $fillable = [
        'tenancy_id',
        'utility_type',
        'provider_name',
        'message',
        'status',
        'landlord_response',
        'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function tenancy(): BelongsTo
    {
        return $this->belongsTo(Tenancy::class);
    }
}