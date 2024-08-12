<?php

namespace App\Models\Job;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobResultComment extends Model
{
    use HasFactory;

    protected $table = 'job_result_comment';

    protected $primaryKey = 'jrc_int_ref';

    protected $fillable = [
        'jrc_jr_ref',
        'jrc_int_user_type',
        'jrc_txt_comment'
    ];

    const CREATED_AT = 'jrc_ts_created_at';
    const UPDATED_AT = 'jrc_ts_updated_at';
}
