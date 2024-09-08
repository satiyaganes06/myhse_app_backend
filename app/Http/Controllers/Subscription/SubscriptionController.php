<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Common\CompetentPersonTypes;
use App\Models\Common\ServiceMain;
use App\Models\Common\SubService;
use App\Models\Common\State;
use Illuminate\Http\Request;
use App\Models\Subscription\SubscriptionPlan;
use App\Models\Subscription\SubscriptionUser;
use App\Models\Subscription\SubscriptionPayment;
use Exception;

class SubscriptionController extends BaseController
{
    public function getSubscriptionPlans($id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $subscriptionPlans = SubscriptionPlan::join('subscription_feature', 'subscription_plan.sp_int_ref', '=', 'subscription_feature.sf_int_sp_ref')
                    ->select('subscription_plan.*', 'subscription_feature.sf_var_feature_description')
                    ->get();
                if ($subscriptionPlans->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No Subscription Plans found', code: 404);
                }

                return $this->sendResponse(message: 'Get Subscription Plans', result: $subscriptionPlans);
            }
            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getSubscriptionUserByID($id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $subscriptionPlan = SubscriptionUser::where('su_int_up_ref', $id)->first();
                if (!$subscriptionPlan) {
                    return $this->sendError(errorMEssage: 'No User Subscription found', code: 404);
                }
                return $this->sendResponse(message: 'Get User Subscription Details', result: $subscriptionPlan);

            }
            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getSubscriptionPaymentByID($id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $subscriptionPayment = SubscriptionPayment::where('spay_int_up_ref', $id)->get();

                if ($subscriptionPayment->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No Subscription Payment found', code: 404);
                }
                return $this->sendResponse(message: 'Get Subscription Payment Details', result: $subscriptionPayment);

            }
            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }
}
