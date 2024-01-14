<?php

namespace App\Models\Services;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Booking\BookingMain;

class ServiceMain extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'service_main';

    protected $primaryKey = 'sm_int_ref';

    protected $fillable = [
        'sm_var_name',
        'sm_var_img_path',
        'sm_int_status'
    ];

    const CREATED_AT = 'sm_ts_created_at';
    const UPDATED_AT = 'sm_ts_updated_at';

    
}
