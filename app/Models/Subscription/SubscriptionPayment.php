<?php

namespace App\Models\Subscription;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubscriptionPayment extends Model
{
    use HasFactory;

    // Define the table associated with this model
    protected $table = 'subscription_payment';

    // Define the primary key of the table
    protected $primaryKey = 'spay_int_ref';

    // Define fillable attributes for mass assignment
    protected $fillable = [
        'spay_int_up_ref',
        'spay_int_su_ref',
        'spay_var_account_name',
        'spay_dou_amount',
        'spay_date_payment_date',
        'spay_var_remark',
        'spay_var_payment_image',
        'spay_enum_status',
        'spay_var_reject_reason'
    ];

    const CREATED_AT = 'spay_ts_created_at';
    const UPDATED_AT = 'spay_ts_updated_at';

    // Define any necessary relationships, such as linking to a user or subscription plan
    public function subscriptionUser()
    {
        return $this->belongsTo(SubscriptionUser::class, 'spay_int_su_ref');
    }
}
