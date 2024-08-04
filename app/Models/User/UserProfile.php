<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserProfile extends Model
{
    use HasFactory;

    protected $table = 'user_profile';

    protected $primaryKey = 'up_int_ref';

    protected $fillable = [
        'up_var_first_name',
        'up_var_last_name',
        'up_var_nric',
        'up_int_iscompany',
        'up_var_company_no',
        'up_var_pic',
        'up_var_contact_no',
        'up_var_address',
        'up_int_zip_code',
        'up_var_state',
        'up_txt_desc'
    ];

    const CREATED_AT = 'up_ts_created_at';
    const UPDATED_AT = 'up_ts_updated_at';
}
