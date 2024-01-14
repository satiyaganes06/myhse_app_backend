<?php

namespace App\Models\Subscribe;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSubscribe extends Model
{
    use HasFactory;
    protected $table = 'payment_subscribe';

    protected $primaryKey = 'ps_int_ref';

    protected $fillable = [
        'ps_int_billing_ref',
        'ps_var_holder_name',
        'ps_var_ref_no',
        'ps_dt_transaction_time',
        'ps_var_amount',
        'ps_var_proof_path',
        'ps_int_status',
        'ps_var_remarks',
        'ps_int_payment_category'
    ];

    const CREATED_AT = 'ps_dt_created_at';
    const UPDATED_AT = 'ps_ts_updated_at';
}
