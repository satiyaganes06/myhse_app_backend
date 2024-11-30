<?php

namespace App\Models\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class CategoryMain extends Model
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'category_main';

    protected $primaryKey = 'cm_int_ref';

    protected $fillable = [
        'cm_var_name',
        'cm_var_image_path',
        'cm_int_status',
    ];

    const CREATED_AT = 'cm_ts_created_at';

    const UPDATED_AT = 'cm_ts_updated_at';

    const DELETED_AT = 'cm_ts_deleted_at';

    // ine category main has multiple service main
    public function service_main()
    {
        return $this->hasMany(ServiceMainRef::class, 'sm_int_category_ref', 'cm_int_ref');
    }
}
