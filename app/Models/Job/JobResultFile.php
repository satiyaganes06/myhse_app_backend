<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobResultFile extends Model
{
    use HasFactory;

    protected $table = 'job_result_file';

    protected $primaryKey = 'jrf_int_ref';

    protected $fillable = [
        'jrf_jr_ref',
        'jrf_files_path'
    ];

    const CREATED_AT = 'jrf_ts_created_at';
    const UPDATED_AT = 'jrf_ts_updated_at';
}
