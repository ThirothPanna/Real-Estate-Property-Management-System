<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = [
        'tenancy_id', 'property_id', 'sender_id', 'recipient_id', 'body', 'read_at',
        'attachment_path', 'attachment_mime', 'attachment_name',
    ];

    protected $casts = ['read_at' => 'datetime'];

    public function tenancy()
    {
        return $this->belongsTo(Tenancy::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}
