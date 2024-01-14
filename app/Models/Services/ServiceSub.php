<?php

namespace App\Models\Services;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Booking\BookingMain;

class ServiceSub extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'service_sub_list';

    protected $primaryKey = 'ssl_int_ref';

    protected $fillable = [
        'ssl_int_servicemain_ref',
        'ssl_var_subservice_name',
        'ssl_var_img_path',
        'ssl_int_status'
    ];

    const CREATED_AT = 'ssl_ts_created_at';
    const UPDATED_AT = 'sll_ts_updated_at';

    
}
