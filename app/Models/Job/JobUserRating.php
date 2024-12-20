<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobUserRating extends Model
{
    use HasFactory;

    protected $table = 'job_user_rating';

    protected $primaryKey = 'jur_int_ref';

    protected $fillable = [
        'jur_jm_ref',
        'jur_var_up_ref',
        'jur_rating_point',
        'jur_txt_comment',
        'jur_int_cps_ref',
        'jur_int_user_type'
    ];

    const CREATED_AT = 'jur_ts_created_at';
    const UPDATED_AT = 'jur_ts_updated_at';
}
