<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'guide_id',
        'guest_id',
        'start_time',
        'end_time',
        'actual_start_time',
        'comment',
        'total_guests',
        'status',
        'guest_booking_confirmation',
        'guide_booking_confirmation',
        'start_confirmation',
        'guest_reviewed',
        'guide_reviewed',
    ];

    public function guest()
    {
        return $this->belongsTo(User::class, 'guest_id');
    }

    public function guide()
    {
        return $this->belongsTo(User::class, 'guide_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}
