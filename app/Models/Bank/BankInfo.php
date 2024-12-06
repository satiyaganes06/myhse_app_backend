<?php

namespace App\Models\Bank;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankInfo extends Model
{
    use HasFactory;

    protected $table = 'bank_info';

    protected $primaryKey = 'bi_int_ref';

    protected $fillable = [
        'bi_int_bank_ref',
        'bi_var_holder_name',
        'bi_var_account_no',
        'bi_int_status',
    ];

    const CREATED_AT = 'bi_ts_created_at';

    const UPDATED_AT = 'bi_ts_updated_at';

    const DELETED_AT = 'bi_ts_deleted_at';
}
