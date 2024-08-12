<?php

namespace App\Models\Booking;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Services\CompetentPersonService;

class BookingRequest extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'booking_request';

    protected $primaryKey = 'br_int_ref';

    protected $fillable = [
        'br_int_req_user_ref',
        'br_int_cps_ref',
        'br_txt_task_detail',
        'br_var_address',
        'br_int_zip_code',
        'br_var_state',
        'br_double_price',
        'br_var_delivery_time',
        'br_int_status'
    ];

    const CREATED_AT = 'br_ts_created_at';
    const UPDATED_AT = 'br_ts_updated_at';


}

