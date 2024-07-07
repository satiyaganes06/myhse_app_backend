<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;


class UserRole extends Model
{
    use HasFactory;

    protected $table = 'user_roles';

    protected $primaryKey = 'ur_int_ref';

    protected $fillable = [
        'ur_var_name'
    ];

    const CREATED_AT = 'ur_ts_created_at';
    const UPDATED_AT = 'ur_ts_updated_at';
}
