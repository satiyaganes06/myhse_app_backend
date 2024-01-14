<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobMain extends Model
{
    use HasFactory;
    protected $table = 'job_main';

    protected $primaryKey = 'jm_int_ref';

    protected $fillable = [
        'jm_int_booking_ref',
        'jm_date_setDate',
        'jm_text_address_1',
        'jm_text_address_2',
        'jm_int_state_ref',
        'jm_var_postcode',
        'jm_txt_job_desc',
        'jm_txt_remarks',
        'jm_int_status',
        'jm_int_progress_complete_status',
        'jm_varchar_progress_complete_commant'
    ];

    const CREATED_AT = 'jm_ts_created_at';
    const UPDATED_AT = 'jm_ts_updated_at';
}
