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

                $description = SubscriptionFeature::whereIn('sf_int_sp_ref', $subscriptionPlans->pluck('sp_int_ref'))->get();

                $groupedDescription = $description->groupBy('sf_int_sp_ref');

                foreach ($subscriptionPlans as $subscriptionPlan) {
                    $subscriptionPlan->description = $groupedDescription[$subscriptionPlan->sp_int_ref] ?? [];
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
            $payment->spay_var_account_name = $request->account_name;
            $payment->spay_date_payment_date = $request->paymentDate;
            $payment->spay_dou_amount = $request->paymentAmount;
            $payment->spay_var_remark = $request->remark;
            $payment->spay_var_payment_image = $fileURL;
            $payment->spay_var_reject_reason = '';
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
