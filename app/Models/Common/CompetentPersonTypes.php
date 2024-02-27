<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompetentPersonTypes extends Model
{
    use HasFactory;

    protected $table = 'competent_person_types';

    protected $primaryKey = 'cpt_int_ref';

    protected $fillable = [
        'cpt_var_short_name',
        'cpt_var_long_name'
    ];

    const CREATED_AT = 'cpt_ts_created_at';
    const UPDATED_AT = 'cpt_ts_updated_at';
}
