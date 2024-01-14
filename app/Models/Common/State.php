<?php

namespace App\Models\Common;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory;

    protected $table = 'states_ref';

    protected $primaryKey = 'sr_int_ref';

    protected $fillable = [
        'sr_var_name',
        'sr_int_status'
    ];

    public $timestamps = false;
}
