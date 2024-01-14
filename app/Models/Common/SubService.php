<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubService extends Model
{
    use HasFactory;

    protected $table = 'service_sub_list';

    protected $primaryKey = 'ssl_int_ref';

    protected $fillable = [
        'ssl_int_servicemain_ref',
        'ssl_var_subservice_name',
        'ssl_var_img_path',
        'ssl_int_status'
    ];

    const CREATED_AT = 'ssl_ts_created_at';
    const UPDATED_AT = 'ssl_ts_updated_at';
}
