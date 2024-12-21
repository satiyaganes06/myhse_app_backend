<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPlan extends Model
{
    use HasFactory;

    // Define the table associated with this model
    protected $table = 'subscription_plan';

    // Define the primary key of the table
    protected $primaryKey = 'sp_int_ref';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'sp_var_name',
        'sp_int_user_category',
        'sp_var_description',
        'sp_double_price',
        'sp_var_price_slug',
        'sp_enum_billing_cycle',
        'sp_int_status'
    ];

    const CREATED_AT = 'sp_ts_created_at';
    const UPDATED_AT = 'sp_ts_updated_at';

    // Define relationships, such as linking to users with a subscription
    public function subscriptionUsers()
    {
        return $this->hasMany(SubscriptionUser::class, 'su_int_sp_ref');
    }
}
