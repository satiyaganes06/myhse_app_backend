<?php

namespace App\Http\Controllers\Subscription;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Common\CompetentPersonTypes;
use App\Models\Common\ServiceMain;
use App\Models\Common\SubService;
use App\Models\Common\State;
use App\Models\Subscription\SubscriptionFeature;
use Illuminate\Http\Request;
use App\Models\Subscription\SubscriptionPlan;
use App\Models\Subscription\SubscriptionUser;
use App\Models\Subscription\SubscriptionPayment;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class SubscriptionController extends BaseController
{
    public function getSubscriptionPlans($id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $subscriptionPlans = SubscriptionPlan::join('subscription_feature', 'subscription_plan.sp_int_ref', '=', 'subscription_feature.sf_int_sp_ref')
                ->select('subscription_plan.*', DB::raw('GROUP_CONCAT(subscription_feature.sf_var_feature_description) as features'))
                ->groupBy('subscription_plan.sp_int_ref')
                ->get();

            // Prepare the final response with features
            foreach ($subscriptionPlans as $subscriptionPlan) {
                $subscriptionPlan->features = explode(',', $subscriptionPlan->features); // Convert concatenated string to array
            }

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
                $subscriptionPlan = SubscriptionUser::join('subscription_plan', 'subscription_user.su_int_sp_ref', '=', 'subscription_plan.sp_int_ref')
                    ->select('subscription_plan.*', 'subscription_user.*')
                    ->where('su_int_up_ref', $id)->first();
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

    public function checkUserSubscription($id){
        try {
            if ($this->isAuthorizedUser($id)) {
                $subscriptionUser = SubscriptionUser::join('subscription_plan', 'subscription_user.su_int_sp_ref', '=', 'subscription_plan.sp_int_ref')

                    ->where('su_int_up_ref', $id)->first();

                if (!$subscriptionUser) {
                    return $this->sendError(errorMEssage: 'User has no subscription', code: 404);
                }

                return $this->sendResponse(message: 'Get Current Subscription', result: $subscriptionUser);
            }
            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function addSubscriptionPayment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required:integer',
                'suID' => 'required:integer',
                'accountName' => 'required:string',
                'paymentDate' => 'required:date',
                'paymentAmount' => 'required:double',
                'remark' => 'required:string'
            ]);

            if ($validator->fails()) {
                return $this->sendError('Validation Error.', $validator->errors());
            }

            $fileURL = $this->uploadMedia($request->file('paymentReceipt'), 5);

            if (empty($fileURL)) {
                return $this->sendError(errorMEssage: 'Image Upload Error', code: 400);
            }

            $payment = new SubscriptionPayment();
            $payment->spay_int_up_ref = $request->userID;
            $payment->spay_int_su_ref = $request->suID;
            $payment->spay_var_account_name = $request->accountName;
            $payment->spay_date_payment_date = $request->paymentDate;
            $payment->spay_dou_amount = $request->paymentAmount;
            $payment->spay_var_remark = $request->remark;
            $payment->spay_var_payment_image = $fileURL;
            $payment->save();

            if ($payment) {
                return $this->sendResponse(message: 'Payment receipt submited successfully');
            } else {
                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }
        } catch (Exception $e) {
            return $this->sendError('Error : ' . $e->getMessage(), 500);
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
