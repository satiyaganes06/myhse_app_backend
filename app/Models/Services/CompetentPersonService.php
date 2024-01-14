<?php

namespace App\Models\Services;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Booking\BookingMain;

class CompetentPersonService extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'competent_person_services';

    protected $primaryKey = 'cps_int_ref';

    protected $fillable = [
        'cps_int_user_ref',
        'cps_int_service_ref',
        'cps_certification_ref',
        'cps_txt_description',
        'cps_var_starting_price',
        'cps_int_status'
    ];

    const CREATED_AT = 'cps_ts_created_at';
    const UPDATED_AT = 'cps_ts_updated_at';

    public function bookings()
    {
        return $this->hasMany(BookingMain::class, 'bm_int_competent_person_service_id', 'cps_int_ref');
    }

    // public function serviceState()
    // {
    //     return $this->belongsTo(CpServicesState::class, 'css_int_services_ref', 'css_int_services_ref');
    // }
}
