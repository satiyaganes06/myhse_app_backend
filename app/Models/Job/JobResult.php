<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobResult extends Model
{
    use HasFactory;

    protected $table = 'job_result';

    protected $primaryKey = 'jr_int_ref';

    protected $fillable = [
        'jr_jm_ref',
        'jr_txt_description',
        'jr_int_delivery_item',
        'jr_double_progress_percent',
        'jr_int_status'
    ];

    const CREATED_AT = 'jr_ts_created_at';
    const UPDATED_AT = 'jr_ts_updated_at';
}
