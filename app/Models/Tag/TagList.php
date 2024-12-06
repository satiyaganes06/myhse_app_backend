<?php

namespace App\Models\Tag;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TagList extends Model
{
    use HasFactory;

    protected $table = 'tag_list';

    protected $primaryKey = 'tl_int_ref';

    protected $fillable = [
        'tl_var_name',
        'tl_int_status',
    ];

    const CREATED_AT = 'tl_dt_created_at';

    const UPDATED_AT = 'tl_dt_created_at';
}
