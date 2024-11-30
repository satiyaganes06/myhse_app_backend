<?php

namespace App\Models\Services;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class ServiceMainRef extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'service_main_ref';

    protected $primaryKey = 'smr_int_ref';

    protected $fillable = [
        'smr_int_category_ref',
        'smr_var_service_name',
        'smr_var_img_path',
        'smr_int_status',
    ];

    const CREATED_AT = 'smr_ts_created_at';

    const UPDATED_AT = 'smr_ts_updated_at';

    // one service main belongs to one category main
    public function category_main()
    {
        return $this->belongsTo(CategoryMain::class, 'smr_int_category_ref', 'cm_int_ref');
    }
}
