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
        'jm_br_ref',
        'jm_result_complete_status',
        'jm_double_price',
        'jm_date_deadline',
        'jm_int_timeline_status',
        'jm_int_status'
    ];

    const CREATED_AT = 'jm_ts_created_at';
    const UPDATED_AT = 'jm_ts_updated_at';
}
