<?php

namespace App\Models\Certificate;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CpCertLink extends Model
{
    use HasFactory;

    protected $table = 'cp_cert_link';

    protected $primaryKey = 'cpcl_int_ref';

    protected $fillable = [
        'cpcl_int_cps_ref',
        'cpcl_int_cc_ref',
    ];

    const CREATED_AT = 'cpcl_ts_created_at';

    const UPDATED_AT = 'cpcl_ts_updated_at';

    public function certificate()
    {
        return $this->belongsTo(CpCertificate::class, 'cpcl_int_cc_ref', 'cc_int_ref');
    }
}
