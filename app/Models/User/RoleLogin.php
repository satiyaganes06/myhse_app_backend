<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoleLogin extends Model
{
    use HasFactory;
    protected $table = 'role_login';

    protected $primaryKey = 'rl_int_ref';

    protected $fillable = [
        'rl_int_user_ref',
        'rl_int_role_ref',
        'rl_int_status'
    ];

    public $timestamps = false;
}
