<?php

namespace App\Models\Booking;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class BookingRequest extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'booking_requests';

    protected $primaryKey = 'br_int_ref';

    protected $fillable = [
        'br_int_bookingmain_ref',
        'br_text_request',
        'br_txt_remark_reason',
        'br_int_status',
        'br_int_user',
        'br_int_type'
    ];

    const CREATED_AT = 'br_ts_created_at';
    const UPDATED_AT = 'br_ts_updated_at';



}
