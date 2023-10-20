<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use \Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory,
    Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'login_type',
        'social_id',
        'first_name',
        'last_name',
        'gender',
        'profile_image',
        'level',
        'introduction',
        'hourly_rate',
        'birthday',
        'occupation',
        'user_type',
        'status',
        'role',
        'latitude',
        'longitude',
        'email',
        'password',
    ];


    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'email',
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = ['review_average', 'review_count'];

    public function bookings()
    {
        return $this->hasMany(Booking::class);
    }

    public function lastBookingAsGuest()
    {
        return $this->hasOne(Booking::class, 'guest_id')->latest();
    }

    public function lastBookingAsGuide()
    {
        return $this->hasOne(Booking::class, 'guide_id')->latest();
    }

    public function bookingsAsGuest()
    {
        return $this->hasMany(Booking::class, 'guest_id');
    }

    public function bookingsAsGuide()
    {
        return $this->hasMany(Booking::class, 'guide_id');
    }

    public function writtenReviews()
    {
        return $this->hasMany(Review::class, 'reviewer_id');
    }   

    public function receivedReviews()
    {
        return $this->hasMany(Review::class, 'reviewee_id');
    }

    // get user's review_average
    public function getReviewAverageAttribute()
    {
        return $this->receivedReviews()->avg('rating');
    }

    // get user's review_count
    public function getReviewCountAttribute()
    {
        return $this->receivedReviews()->count();
    }
     
    // check if user is guest
    public function isGuest()
    {
        return $this->user_type === 'guest';
    }

    // check if user is guide
    public function isGuide()
    {
        return $this->user_type === 'guide';
    }

    // get current login user
    public function getCurrentUser()
    {
        return auth()->user();
    }

    // get the other user of the booking
    public function getOtherUserOfBooking($booking)
    {
        if ($this->isGuest()) {
            return $booking->guide;
        }

        return $booking->guest;
    }

    public function doesntHaveBookingsAsGuide()
    {
        return $this->bookingsAsGuide()->doesntExist();
    }

    public function doesntHaveBookingsAsGuest()
    {
        return $this->bookingsAsGuest()->doesntExist();
    }

    public function hasBookingsWithEligibleStatusForGuide()
    {
        return $this->bookingsAsGuide()
            ->where(function ($query) {
                $query->whereNull('status')
                ->orWhere('status', 'reviewed')
                ->orWhere('status', 'cancelled')
                ->orWhere('guide_reviewed', true);
            })->exists();
    }

    public function hasBookingsWithEligibleStatusForGuest()
    {
        return $this->bookingsAsGuest()
            ->where(function ($query) {
                $query->whereNull('status')
                ->orWhere('status', 'reviewed')
                ->orWhere('status', 'cancelled')
                ->orWhere('guest_reviewed', true);
            })->exists();
    }

    public function isGuideActive()
    {
        return $this->status === 'active';
    }

    public function hasSpecificBookingsAsGuide()
    {
        if ($this->doesntHaveBookingsAsGuide()) {
            return true;
        }

        if (!$this->isGuideActive()) {
            return false;
        }

        return $this->hasBookingsWithEligibleStatusForGuide();
    }

    public function hasSpecificBookingsAsGuest()
    {
        if ($this->doesntHaveBookingsAsGuest()) {
            return true;
        }

        return $this->hasBookingsWithEligibleStatusForGuest();
    }

    public function isEligibleForGuideStatus()
    {
        $lastBooking = $this->lastBookingAsGuide;

        if (is_null($lastBooking)) {
            return true;
        }

        return in_array($lastBooking->status, [null, 'reviewed', 'cancelled']) || $lastBooking->guide_reviewed === true;
    }


    public static function getSpecificGuides()
    {
        return self::all()->filter(function ($user) {
            return $user->isGuide() && $user->isGuideActive() && (!$user->bookingsAsGuide()->exists() ||
                $user->isEligibleForGuideStatus()
            );
        });
    }

    // hasStatedBookingsAsGuide method whereDoesntHave('bookingsAsGuide')is not necessary
    public function hasStartedBookingsAsGuide()
    {
        if (!$this->isGuide()) {
            return false;
        }

        return $this->bookingsAsGuide()->where('status', 'started')->exists();
    }

    // canCancelBookingAsGuest method(user_type = guest  & whose booking status = offer-pending or accepted)
    public function canCancelBookingAsGuest()
    {
        if ($this->isGuest()) {
            return false;
        }

        return $this->bookingsAsGuest()
        ->where('status', 'offer-pending')
        ->orWhere('status', 'accepted')
        ->exists();
    }

    // canCancelBookingAsGuide method(user_type = guide  & whose booking status = offer-pending)
    public function canCancelBookingAsGuide()
    {
        if ($this->isGuide()) {
            return false;
        }

        return $this->bookingsAsGuide()->where('status', 'offer-pending')->exists();
    }

    // canGuestWriteReview (user_type = guest & whose booking status = finished & whose booking guest_reviewed = false)
    public function canGuestWriteReview()
    {
        if (!$this->isGuest()) {
            return false;
        }

        return $this->bookingsAsGuest()->where('status', 'finished')
        ->where('guest_reviewed', false)
            ->exists();
    }

    // canGuideWriteReview (user_type = guide & whose booking status = finished & whose booking guide_reviewed = false)
    public function canGuideWriteReview()
    {
        if (!$this->isGuide()) {
            return false;
        }

        return $this->bookingsAsGuide()->where('status', 'finished')
        ->where('guide_reviewed', false)
            ->exists();
    }

    // review ratings order
    public function scopeReviewRatings($query)
    {
        return $query->withCount(['receivedReviews as review_ratings' => function ($query) {
            $query->select(DB::raw('coalesce(avg(rating),0)'));  // coalesce: if null, return 0
        }]);
    }

    // review count order
    public function scopeReviewCount($query)
    {
        return $query->withCount('receivedReviews');
    }

    // level order
    public function scopeLevel($query)
    {
        return $query->orderBy('level', 'desc');
    }

    // hourly rate order
    public function scopeHourlyRate($query)
    {
        return $query->orderBy('hourly_rate', 'asc');
    }

    //closest distance of users location from Harajuku 35.6700° N, 139.7090° E (latitude, longitude) order
    public function scopeClosestDistance($query)
    {
        return $query->orderByRaw('SQRT(POW(69.1 * (latitude - 35.6700), 2) + POW(69.1 * (139.7090 - longitude) * COS(latitude / 57.3), 2))');
    }

    // get all users where updated_at is 15 min ago
    public function scopeOnline($query)
    {
        return $query->where('updated_at', '>=', now()->subMinutes(15));
    }




}
