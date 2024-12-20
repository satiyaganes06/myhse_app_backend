<?php

namespace App\Models\Services;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use App\Models\Booking\BookingMain;

class CpServicesState extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'cps_states';

    protected $primaryKey = 'cs_int_ref';

    protected $fillable = [
        'cs_int_cps_ref',
        'cs_int_states_ref',
        'cs_int_status'
    ];

    //! FIXME: Add this in database (Prod)
    const CREATED_AT = 'cs_dt_created_at';
    const UPDATED_AT = 'cs_dt_updated_at';
}
