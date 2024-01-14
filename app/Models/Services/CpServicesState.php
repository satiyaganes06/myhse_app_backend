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

    protected $table = 'cp_services_state';

    protected $primaryKey = 'css_int_ref';

    protected $fillable = [
        'css_int_services_ref',
        'css_int_states_ref'
    ];

}
