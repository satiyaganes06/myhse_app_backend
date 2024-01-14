<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\UserLogin;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use Exception;
use Nette\Schema\Expect;

class CpDetailsController extends BaseController
{
    public function cpProfileDetails(Request $request)
    {

        try {
            $userProfileDetails = UserProfile::where('up_int_ref', $request->input('cpID'))->first();

            return $this->sendResponse('get cp info', '', $userProfileDetails);
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function cpEmailVerificationStatusCheck(Request $request)
    {

        try {
            $status = UserLogin::select('ul_ts_email_verified_at')->where('ul_var_emailaddress', $request->input('cpEmail'))->first();
           // view('emailVerification', ['status' => $status]);
            return $this->sendResponse('cp email verification status', '', $status);
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function cpFirstTimeStatusCheck(Request $request)
    {

        try {
            $status = UserLogin::select('ul_int_first_time_login')->where('ul_var_emailaddress', $request->input('cpEmail'))->first();

            return $this->sendResponse('cp first time status', '', $status);
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function cpEmailVerificationStatusUpdate(Request $request)
    {

        try {
            $status  = UserLogin::where('ul_var_emailaddress', $request->input('cpEmail'))->update(array('ul_ts_email_verified_at' => date('Y-m-d H:i:s')));

            return $this->sendResponse('Verified', '', $status);
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function cpCompleteProfile(Request $request)
    {

        try {

            $validatorUser = Validator::make($request->all(), [
                'cpID' => 'required|integer',
                'cpAddress' => 'required|string|max:255',
                'cpZipCode' => 'required|integer',
                'cpState' => 'required|string|max:255'
            ]);
            
            UserProfile::where('up_int_ref', $request->input('cpID'))->update(
                array(
                    'up_var_address' => $request->input('cpAddress'),
                    'up_int_zip_code' => $request->input('cpZipCode'),
                    'up_var_state' => $request->input('cpState'),

                )
            );

            UserLogin::where('ul_int_profile_ref', $request->input('cpID'))->update(
                array(
                    'ul_int_first_time_login' => 1,
                )
            );


            return $this->sendResponse('Successfully complete your profile', '');
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function updateProfileInfo(Request $request){
        try {

            UserProfile::where('up_int_ref', $request->input('cpID'))->update(
                array(
                    'up_var_first_name' => $request->input('cpFirstName'),
                    'up_var_last_name' => $request->input('cpLastName'),
                    'up_var_nric' => $request->input('cpNRIC'),
                    'up_var_email_contact' => $request->input('cpEmail'),
                    'up_var_contact_no' => $request->input('cpPhoneNumber'),
                    'up_var_address' => $request->input('cpAddress'),
                    'up_int_zip_code' => $request->input('cpZipCode'),
                    'up_var_state' => $request->input('cpState')
                )
            );

            UserLogin::where('ul_int_profile_ref', $request->input('cpID'))->update(
                array(
                    'ul_int_first_time_login' => 1,
                )
            );


            return $this->sendResponse('Successfully complete your profile', '');
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }


}
