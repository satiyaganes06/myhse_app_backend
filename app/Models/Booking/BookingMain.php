<?php

namespace App\Models\Booking;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Services\CompetentPersonService;

class BookingMain extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'booking_main';

    protected $primaryKey = 'bm_int_ref';

    protected $fillable = [
        'bm_int_competent_person_service_id',
        'bm_int_cp_ref',
        'bm_int_req_user_ref',
        'bm_dt_booking_datetime',
        'bm_var_subject_title',
        'bm_txt_address1',
        'bm_txt_address2',
        'bm_var_city',
        'bm_int_state_ref',
        'bm_var_postcode',
        'bm_var_total_amount',
        'bm_txt_task_detail',
        'bm_int_status'
    ];

    const CREATED_AT = 'bm_ts_created_at';
    const UPDATED_AT = 'bm_ts_updated_at';

    public function competentPersonService()
    {
        return $this->belongsTo(CompetentPersonService::class, 'cps_int_ref', 'bm_int_competent_person_service_id');
    }
}

