<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpPostLink extends Model
{
    use HasFactory;

    protected $table = 'cp_post_link';

    protected $primaryKey = 'cppl_int_ref';

    protected $fillable = [
        'cppl_int_es_ref',
        'cppl_int_ep_ref'
    ];

    const CREATED_AT = 'cppl_ts_created_at';
    const UPDATED_AT = 'cppl_ts_updated_at';
}
