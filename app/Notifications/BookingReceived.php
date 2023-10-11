<?php

namespace App\Notifications;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingReceived extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    // use booking model and user model
    public function __construct(
        public Booking $booking
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // use getBookingTimeAttribute function from Booking model
        $bookingTime =  $this->booking->booking_time;
        $bookingDuration = $this->booking->booking_duration;
        $guestName = $this->booking->guest_name;
        $guideName = $this->booking->guide_name;
        
        return (new MailMessage)
                    ->line('You have received a new booking request.')
                    ->line('Booking ID: ' . $this->booking->id)
                    ->line('Booking Time: ' . $bookingTime)
                    ->line('Booking Duration: ' . $bookingDuration)
                    ->line('Booking Location: ' . '原宿駅')
                    ->line('Booking Price: ' . $this->booking->total_amount)
                    ->Line('Booking Comment: ' . $this->booking->comment)
                    // ->Line('Booking geuest number: ' . $this->booking->total_guests)
                    // ->line('Booking Status: ' . $this->booking->status)
                    // ->line('Booking Guest: ' . $guestName)
                    // ->line('Booking Guide: ' . $guideName)
                    // ->line('Booking Created At: ' . $this->booking->created_at)
                    // send the email to the guide after guest reservation
                    // ->action('View Booking', url('/bookings/' . $this->booking->id))
                    ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
