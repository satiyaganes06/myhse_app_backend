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
        'jm_int_accept_result',
        'jm_date_deadline',
        'jm_int_timeline_status',
        'jm_int_status'
    ];

    const CREATED_AT = 'jm_ts_created_at';
    const UPDATED_AT = 'jm_ts_updated_at';
}
