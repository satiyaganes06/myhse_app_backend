<?php

namespace App\Models\Services;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Booking\BookingMain;

class ServiceMainRef extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'service_main_ref';

    protected $primaryKey = 'smr_int_ref';

    protected $fillable = [
        'smr_var_service_name',
        'smr_var_img_path',
        'smr_int_status'
    ];

    const CREATED_AT = 'smr_ts_created_at';
    const UPDATED_AT = 'smr_ts_updated_at';


}
