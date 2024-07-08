<?php

namespace App\Models\User;

use Illuminate\Database\Eloquent\Model;

class PasswordResets extends Model
{
    // The table associated with the model.
    protected $table = 'password_reset_tokens';

    // Indicates if the model should be timestamped.
    public $timestamps = false;

    // The attributes that are mass assignable.
    protected $fillable = [
        'email', 'token', 'created_at',
    ];
}
