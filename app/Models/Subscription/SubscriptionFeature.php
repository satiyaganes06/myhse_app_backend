<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionFeature extends Model
{
    use HasFactory;

    // Define the table associated with this model
    protected $table = 'subscription_feature';

    // Define the primary key of the table
    protected $primaryKey = 'sf_int_ref';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'sf_int_sp_ref',
        'sf_var_feature_description'
    ];

    const CREATED_AT = 'sf_ts_created_at';
    const UPDATED_AT = 'sf_ts_updated_at';

    // .. // Define relationships, such as linking to users with a subscription
    // public function subscriptionUsers()
    // {
    //     return $this->hasMany(SubscriptionUser::class, 'su_int_sp_ref');
    // }
}
