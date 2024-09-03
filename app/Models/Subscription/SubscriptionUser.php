<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionUser extends Model
{
    use HasFactory;

    // Define the table associated with this model
    protected $table = 'subscription_user';

    // Define the primary key of the table
    protected $primaryKey = 'su_int_ref';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'su_int_up_ref',
        'su_int_sp_ref',
        'su_date_start_date',
        'su_date_end_date',
        'su_enum_status',
    ];

    const CREATED_AT = 'su_ts_created_at';
    const UPDATED_AT = 'su_ts_updated_at';

    // Define relationships
    public function subscriptionPlan()
    {
        return $this->belongsTo(SubscriptionPlan::class, 'su_int_sp_ref');
    }

    public function subscriptionPayments()
    {
        return $this->hasMany(SubscriptionPayment::class, 'spay_int_su_ref');
    }
}
