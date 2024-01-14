<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobResult extends Model
{
    use HasFactory;
    protected $table = 'job_result';

    protected $primaryKey = 'jr_int_ref ';

    protected $fillable = [
        'jr_int_job_ref',
        'jr_var_doc_title',
        'jr_var_doc_path',
        'jr_bool_is_final_document',
        'jr_int_progress_status'
    ];
}
