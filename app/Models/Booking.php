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
        'total_amount',
        'guest_booking_confirmation',
        'guide_booking_confirmation',
        'start_confirmation',
        'guest_reviewed',
        'guide_reviewed',
    ];

    protected $dates = ['start_time', 'end_time', 'actual_start_time'];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'actual_start_time' => 'datetime',
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

    // get first name  + last name from booking guest
    public function getGuestNameAttribute()
    {
        return $this->guest->first_name . ' ' . $this->guest->last_name;
    }

    // get first name  + last name from booking guide
    public function getGuideNameAttribute()
    {
        return $this->guide->first_name . ' ' . $this->guide->last_name;
    }
    // get booking time with the format of 'Y-m-d H:i:s'
    public function getBookingTimeAttribute()
    {
        return $this->start_time->format('Y-m-d H:i:s');
    }
    // get booking duration using start_time and end_time interval
    public function getBookingDurationAttribute()
    {
        $interval = $this->start_time->diff($this->end_time);
        if ($interval->h > 0) {
            return $interval->h . ' hours';
        } else {
            return $interval->i . ' minutes';
        }
    }

    //booking calculateCancellation total_amount for guest if status = offer-pending then return total_amount =0, status = accepted then return total_amount * 0.2 other status return 
    public function calculateCancellationTotalAmount()
    {
        if ($this->status === 'offer-pending') {
            return 0;
        } elseif ($this->status === 'accepted') {
            return $this->total_amount * 0.2;
        } else {
            return $this->total_amount;
        }
    }



    public function calculateCancellationFee()
    {
        return $this->guide->hourly_rate * $this->cancellationFeePercentage();
    }

    // check request hourly_rate is eqaul to guide hourly_rate & check request booking status is equal to booking status
    public function isSameAsLastBooking($request)
    {
        $lastBooking = $this->getLastBooking();

        if ($lastBooking) {
            return $lastBooking->guide->hourly_rate === $request->hourly_rate && $lastBooking->status === $request->status;
        }

        return json_encode(['error' => 'Retry Again']);
    }


}
