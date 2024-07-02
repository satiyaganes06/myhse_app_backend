<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'user_login';

    protected $primaryKey = 'ul_int_ref';
    protected $rememberTokenName = 'ul_var_remember_token';


    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'ul_var_password',
        'ul_var_remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    const PASSWORD = 'ul_var_password';
    const CREATED_AT = 'ul_ts_created_at';
    const UPDATED_AT = 'ul_ts_updated_at';
    const DELETED_AT = 'ul_ts_deleted_at';

    // Add this method to override the default password field
    public function getAuthPassword()
    {
        return $this->ul_var_password;
    }
}
