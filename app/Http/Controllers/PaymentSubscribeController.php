<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Base\BaseController;
use App\Models\Subscribe\PaymentSubscribe;
use App\Models\Subscribe\RoleValidity;
use App\Models\RoleLogin;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PaymentSubscribeController extends BaseController
{
    public function uploadSubscriptionPaymentData(Request $request)
    {
        try {

            // $validatorSubPayment = Validator::make($request->all(), [
            //     'receiptPath' => 'required|string|max:255'
            // ]);

            // if ($validatorSubPayment->fails()) {

            //     return $this->sendError('Error : ' . $validatorSubPayment->errors() , 500);
            // }

            if ($request->hasFile('receiptPath')) {
                $encryptedReceiptPath = 'uploads/receipts/' . base64_encode($request->input('receiptPath'));

                $paymentSubscribe = new PaymentSubscribe([
                    'ps_int_billing_ref' => 7855,
                    'ps_var_holder_name' => $request->input('holderName'),
                    'ps_var_ref_no' => $request->input('userID'),
                    'ps_dt_transaction_time' => $request->input('transferedDate'),
                    'ps_var_amount' => $request->input('transferedAmount'),
                    'ps_var_proof_path' => $encryptedReceiptPath,
                    'ps_int_status' => 0,
                    'ps_var_remarks' => $request->input('remark'),
                    'ps_int_payment_category' => 2147483647
                ]);

                $paymentSubscribe->save();
            } else {


                $paymentSubscribe = new PaymentSubscribe([
                    'ps_int_billing_ref' => 7855,
                    'ps_var_holder_name' => '',
                    'ps_var_ref_no' => $request->input('userID'),
                    'ps_dt_transaction_time' => '',
                    'ps_var_amount' => 0,
                    'ps_var_proof_path' => '',
                    'ps_int_status' => 0,
                    'ps_var_remarks' => '',
                    'ps_int_payment_category' => 2147483647
                ]);

                $paymentSubscribe->save();
            }


            return $this->sendResponse('Send to admin for review.', '');
        } catch (\Throwable $th) {
            return $this->sendError('Error : ' . $th->getMessage(), 500);
        }
    }

    public function checkUserSubscription(Request $request)
    {

        try {

            $validatorSubPayment = Validator::make($request->all(), [
                'userID' => 'required|string|max:255'
            ]);

            if ($validatorSubPayment->fails()) {

                return $this->sendError('Error : ' . $validatorSubPayment->errors(), 500);
            }

            $subscriptionUser = PaymentSubscribe::where('ps_var_ref_no', $request->input('userID'))->first();


            return $this->sendResponse('Your Subscription Information', '', $subscriptionUser);
        } catch (\Throwable $th) {
            return $this->sendError('Error : ' . $th->getMessage(), 500);
        }
    }

    public function checkIfUserCP(Request $request)
    {
        try {
            $userRole = RoleLogin::join('role_validity', 'role_validity.rv_int_rolelogin_ref', '=', 'role_login.rl_int_ref')
                ->where('role_login.rl_int_user_ref', $request->input('userLoginID'))
                ->select(
                    'role_validity.*'
                )
                ->first();
                
            // $userRoleValidity = RoleValidity::join('role_login', 'role_login.rl_int_ref', '=', 'role_validity.rv_int_rolelogin_ref')
            //     ->where('role_login.rl_int_user_ref', $request->input('userLoginID'))
            //     ->select(
            //         'role_validity.*'
            //     )
            //     ->first();

            if ($userRole->rv_int_status == 1) {
                return $this->sendResponse('User Login Validity Information', '', [
                    'status' => 'Active',
                    'validUntil' => $userRole->rv_date_valid_until
                ]);

            } else {
                return $this->sendResponse('User Login Validity Information', '', [
                    'status' => 'No Active',
                    'validUntil' => $userRole->rv_date_valid_until
                ]);

            }

        } catch (\Throwable $th) {
            return $this->sendError('Error : ' . $th->getMessage(), 500);
        }
    }
}
