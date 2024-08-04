<?php

namespace App\Models\Certificate;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;

class CpCertificate extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'cp_certificate';

    protected $primaryKey = 'cc_int_ref';

    protected $fillable = [
        'cc_int_user_ref',
        'cc_var_title',
        'cc_var_description',
        'cc_var_registration_no',
        'cc_date_expiry_date',
        'cc_var_path_document',
        'cc_int_status'
    ];

    const CREATED_AT = 'cc_ts_created_at';
    const UPDATED_AT = 'cc_ts_updated_at';


}
