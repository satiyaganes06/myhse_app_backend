<?php

namespace App\Models\Tag;

use App\Models\Services\CpService;
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

    const UPDATED_AT = 'cpst_dt_updated_at';

    public function tagList()
    {
        return $this->belongsTo(TagList::class, 'cpst_int_tag_ref', 'tl_int_ref');
    }

    public function service()
    {
        return $this->belongsTo(CpService::class, 'cpst_int_cps_ref', 'cps_int_ref');
    }
}
