<?php

namespace App\Models\Tag;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpTag extends Model
{
    use HasFactory;

    protected $table = 'cps_tag';

    protected $primaryKey = 'cpst_int_ref';

    protected $fillable = [
        'cpst_int_cps_ref',
        'cpst_int_tag_ref',
    ];

    const CREATED_AT = 'cpst_dt_created_at';
}
