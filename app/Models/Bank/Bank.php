<?php

namespace App\Models\Bank;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $table = 'bank_ref';

    protected $primaryKey = 'bref_int_ref';

    protected $fillable = [
        'bref_var_name',
        'bref_var_swift_code',
        'bref_var_logo_path',
        'bref_int_status',
    ];

    public function bankInfo()
    {
        return $this->hasMany(BankInfo::class, 'bi_int_bank_ref', 'bref_int_ref');
    }
}
