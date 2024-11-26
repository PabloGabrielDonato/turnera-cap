<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    /** @use HasFactory<\Database\Factories\BookingFactory> */
    use HasFactory;

    protected $fillable = [
        'location_id',
        'user_id',
        'start_time',
        'end_time',
        'people_count',
    ];

    protected $casts = [
        'start_time' => 'datetime:Y-m-d H:i:s', // Asegura que se formatee como string
        'end_time' => 'datetime:Y-m-d H:i:s',
    ];

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
