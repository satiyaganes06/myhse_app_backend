<?php

namespace App\Models\Services;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Booking\BookingRequest;
use App\Models\Certificate\CpCertLink;
use App\Models\Post\CpPostLink;

class CpService extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'cp_service';

    protected $primaryKey = 'cps_int_ref';

    protected $fillable = [
        'cps_int_user_ref',
        'cps_int_service_ref',
        'cps_var_image',
        'cps_txt_description',
        'cps_var_starting_price',
        'cps_estimate_delivery_time',
        'cps_fl_average_rating',
        'cps_int_status'
    ];

    const CREATED_AT = 'cps_ts_created_at';
    const UPDATED_AT = 'cps_ts_updated_at';

    public function certificates()
    {
        return $this->hasMany(CpCertLink::class, 'cpcl_int_cps_ref', 'cps_int_ref');
    }

    public function posts()
    {
        return $this->hasMany(CpPostLink::class, 'cppl_int_cps_ref', 'cps_int_ref');
    }

    public function bookings()
    {
        return $this->hasMany(BookingRequest::class, 'bm_int_competent_person_service_id', 'cps_int_ref');
    }

    // public function serviceState()
    // {
    //     return $this->belongsTo(CpServicesState::class, 'css_int_services_ref', 'css_int_services_ref');
    // }
}
