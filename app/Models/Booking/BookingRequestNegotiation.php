<?php

namespace App\Models\Booking;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class BookingRequestNegotiation extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'booking_request_negotiation';

    protected $primaryKey = 'brn_int_ref';

    protected $fillable = [
        'brn_br_int_ref',
        'brn_txt_desc',
        'brn_requested_price',
        'brn_int_status',
        'brn_int_user_type',
        'brn_int_type',
        'brn_date_deadline'
    ];

    const CREATED_AT = 'brn_ts_created_at';
    const UPDATED_AT = 'brn_ts_updated_at';



}
