<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobPayment extends Model
{
    use HasFactory;
    protected $table = 'job_payment';

    protected $primaryKey = 'jp_int_ref';

    protected $fillable = [
        'jp_int_job_ref',
        'jp_var_first_payment',
        'jp_var_total_payment',
        'jp_int_status'
    ];

    const CREATED_AT = 'jp_ts_created_at';
    const UPDATED_AT = 'jp_ts_updated_at';
}
