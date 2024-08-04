<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpPost extends Model
{
    use HasFactory;
    protected $table = 'cp_post';

    protected $primaryKey = 'cpp_int_ref';

    protected $fillable = [
        'cpp_int_user_ref',
        'cpp_txt_desc',
        'cpp_var_image',
        'cpp_int_service_main_ref',
        'cpp_int_status'
    ];

    const CREATED_AT = 'cpp_ts_created_at';
    const UPDATED_AT = 'cpp_ts_updated_at';
}
