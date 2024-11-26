<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeSlot extends Model
{
    /** @use HasFactory<\Database\Factories\TimeSlotFactory> */
    use HasFactory;

    protected $fillable = [
        'location_id',
        'day_of_week',
        'start_time',
        'end_time',
        'cost_per_hour',
    ];

    public function location():BelongsTo
    {
        return $this->belongsTo(Location::class);
    }
}
