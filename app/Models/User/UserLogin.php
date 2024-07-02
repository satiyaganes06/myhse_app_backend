<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;


class UserLogin extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_login';

    protected $primaryKey = 'ul_int_ref';
    protected $rememberTokenName = 'ul_var_remember_token';

    protected $fillable = [
        'ul_int_profile_ref',
        'ul_var_emailaddress',
        'ul_ts_email_verified_at',
        'ul_var_password',
        'ul_var_remember_token',
        'ul_int_first_time_login',
        'ul_var_exp_date',
        'ul_var_reset_token'
    ];

    const CREATED_AT = 'ul_ts_created_at';
    const UPDATED_AT = 'ul_ts_updated_at';
    const DELETED_AT = 'ul_ts_deleted_at';
}
