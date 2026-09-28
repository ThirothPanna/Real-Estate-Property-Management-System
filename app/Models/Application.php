<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'full_name',
        'email',
        'phone',
        'date_of_birth',
        'property_id',
        'unit_number',
        'current_address',
        'current_city',
        'current_state',
        'current_zip',
        'employer',
        'job_title',
        'monthly_income',
        'previous_landlord',
        'previous_landlord_phone',
        'move_in_date',
        'occupants',
        'pets',
        'notes',
        'status',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'move_in_date' => 'date',
        'monthly_income' => 'decimal:2',
    ];

    /**
     * The tenant who submitted this application.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}