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
        'jp_jm_ref',
        'jp_var_up_ref',
        'jp_int_type',
        'jp_var_acount_transfer_name',
        'jp_date_account_transfer_date',
        'jp_double_account_transfer_amount',
        'jp_var_account_transfer_remark',
        'jp_var_receipt',
        'jp_int_status',
        'jp_var_reject_reason'
    ];

    const CREATED_AT = 'jp_ts_created_at';
    const UPDATED_AT = 'jp_ts_updated_at';
}
