<?php

namespace App\Models\Services;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Models\Booking\BookingRequest;
use App\Models\Certificate\CpCertLink;
use App\Models\Post\CpPostLink;
use App\Models\Tag\CpTag;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        'cps_int_status',
    ];

    const CREATED_AT = 'cps_ts_created_at';

    const UPDATED_AT = 'cps_ts_updated_at';

    public function certificateLinks()
    {
        // return $this->hasMany(CpCertLink::class, 'cpcl_int_cps_ref', 'cps_int_ref')
        //     ->join('cp_certificate', 'cp_cert_link.cpcl_int_cc_ref', '=', 'cp_certificate.cc_int_ref')
        //     ->select('cp_certificate.*');
        return $this->hasManyThrough(
            CpCertLink::class,
            CpService::class,
            'cps_int_ref', // Foreign key on CpCertLink
            'cpcl_int_cps_ref', // Foreign key on CpService
            'cps_int_ref', // Local key on CpService
            'cpcl_int_cc_ref' // Local key on CpCertLink
        )->join('cp_certificate', 'cp_cert_link.cpcl_int_cc_ref', '=', 'cp_certificate.cc_int_ref')
         ->select('cp_certificate.*');
    }

    public function certificates()
    {
        return $this->hasManyThrough(
            CpCertLink::class,
            CpService::class,
            'cps_int_ref', // Foreign key on CpCertLink
            'cpcl_int_cps_ref', // Foreign key on CpService
            'cps_int_ref', // Local key on CpService
            'cpcl_int_cc_ref' // Local key on CpCertLink
        )->join('cp_certificate', 'cp_cert_link.cpcl_int_cc_ref', '=', 'cp_certificate.cc_int_ref')
         ->select('cp_certificate.*');
    }

    public function tags()
    {
        return $this->hasMany(CpTag::class, 'cpst_int_cps_ref', 'cps_int_ref');
    }

    public function posts()
    {
        return $this->hasManyThrough(
            CpPostLink::class,
            CpService::class,
            'cps_int_ref', // Foreign key on CpPostLink
            'cppl_int_cps_ref', // Foreign key on CpService
            'cps_int_ref', // Local key on CpService
            'cppl_int_cpp_ref' // Local key on CpPostLink
        )->join('cp_post', 'cp_post_link.cppl_int_cpp_ref', '=', 'cp_post.cpp_int_ref')
         ->select('cp_post.*');
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
