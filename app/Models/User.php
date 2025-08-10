<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Notifications\Api\EmailVerificationNotification;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    public function sendEmailVerificationNotification()
    {
        $token = $this->generateOtpToken();
        $this->notify(new EmailVerificationNotification($token));
    }

    public function generateOtpToken()
    {
        $token = rand(10000, 99999);
        $this->otp = $token;
        $this->otp_expire_at = now()->addMinutes(10);
        $this->save();
        return $token;
    }

    protected $table = 'users';
    /**
     * 
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function roomBookings()
    {
        return $this->hasMany(RoomBooking::class);
    }

    public function hotelReviews()
    {
        return $this->hasMany(HotelReview::class);
    }

    public function flightBookings()
    {
        return $this->hasMany(FlightBooking::class);
    }
}
