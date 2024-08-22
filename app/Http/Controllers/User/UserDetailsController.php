<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\User\UserProfile;
use Exception;
use Illuminate\Support\Facades\DB;
use App\Models\Common\CompetentPersonTypes;

class UserDetailsController extends BaseController
{

    //!! Version 1
    // public function cpProfileDetails(Request $request)
    // {

    //     try {
    //         $userProfileDetails = UserProfile::where('up_int_ref', $request->input('cpID'))->first();

    //         return $this->sendResponse('get cp info', '', $userProfileDetails);
    //     } catch (Exception $e) {

    //         return $this->sendError('Error : ' . $e->getMessage(), 500);
    //     }
    // }

    // public function cpEmailVerificationStatusCheck(Request $request)
    // {

    //     try {
    //         $status = UserLogin::select('ul_ts_email_verified_at')->where('ul_var_emailaddress', $request->input('cpEmail'))->first();
    //        // view('emailVerification', ['status' => $status]);
    //         return $this->sendResponse('cp email verification status', '', $status);
    //     } catch (Exception $e) {

    //         return $this->sendError('Error : ' . $e->getMessage(), 500);
    //     }
    // }

    // public function cpFirstTimeStatusCheck(Request $request)
    // {

    //     try {
    //         $status = UserLogin::select('ul_int_first_time_login')->where('ul_var_emailaddress', $request->input('cpEmail'))->first();

    //         return $this->sendResponse('cp first time status', '', $status);
    //     } catch (Exception $e) {

    //         return $this->sendError('Error : ' . $e->getMessage(), 500);
    //     }
    // }

    // public function cpEmailVerificationStatusUpdate(Request $request)
    // {

    //     try {
    //         $status  = UserLogin::where('ul_var_emailaddress', $request->input('cpEmail'))->update(array('ul_ts_email_verified_at' => date('Y-m-d H:i:s')));

    //         return $this->sendResponse('Verified', '', $status);
    //     } catch (Exception $e) {

    //         return $this->sendError('Error : ' . $e->getMessage(), 500);
    //     }
    // }

    // public function cpCompleteProfile(Request $request)
    // {

    //     try {

    //         $validatorUser = Validator::make($request->all(), [
    //             'cpID' => 'required|integer',
    //             'cpAddress' => 'required|string|max:255',
    //             'cpZipCode' => 'required|integer',
    //             'cpState' => 'required|string|max:255'
    //         ]);

    //         UserProfile::where('up_int_ref', $request->input('cpID'))->update(
    //             array(
    //                 'up_var_address' => $request->input('cpAddress'),
    //                 'up_int_zip_code' => $request->input('cpZipCode'),
    //                 'up_var_state' => $request->input('cpState'),

    //             )
    //         );

    //         UserLogin::where('ul_int_profile_ref', $request->input('cpID'))->update(
    //             array(
    //                 'ul_int_first_time_login' => 1,
    //             )
    //         );


    //         return $this->sendResponse('Successfully complete your profile', '');
    //     } catch (Exception $e) {

    //         return $this->sendError('Error : ' . $e->getMessage(), 500);
    //     }
    // }

    // public function updateProfileInfo(Request $request){
    //     try {

    //         UserProfile::where('up_int_ref', $request->input('cpID'))->update(
    //             array(
    //                 'up_var_first_name' => $request->input('cpFirstName'),
    //                 'up_var_last_name' => $request->input('cpLastName'),
    //                 'up_var_nric' => $request->input('cpNRIC'),
    //                 'up_var_email_contact' => $request->input('cpEmail'),
    //                 'up_var_contact_no' => $request->input('cpPhoneNumber'),
    //                 'up_var_address' => $request->input('cpAddress'),
    //                 'up_int_zip_code' => $request->input('cpZipCode'),
    //                 'up_var_state' => $request->input('cpState')
    //             )
    //         );

    //         UserLogin::where('ul_int_profile_ref', $request->input('cpID'))->update(
    //             array(
    //                 'ul_int_first_time_login' => 1,
    //             )
    //         );


    //         return $this->sendResponse('Successfully complete your profile', '');
    //     } catch (Exception $e) {

    //         return $this->sendError('Error : ' . $e->getMessage(), 500);
    //     }
    // }


    //!! Version 2

    public function getMyProfileDetailsByID($id)
    {
        try {

          //  if ($this->isAuthorizedUser($id)) {
                $userProfile = UserProfile::where('up_int_ref', $id)->first();
                return $this->sendResponse(message: 'Get My Profile Informations', result: $userProfile);
          //  }

           // return $this->sendError('Unauthorized Request', 401);
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getUserProfileDetailByID($id, $clientID)
    {
        try {
            if($this->isAuthorizedUser($id)){
                $userProfileDetails = UserProfile::where('up_int_ref', $clientID)->first();
                return $this->sendResponse(message: 'Get Client Profile Detail', result: $userProfileDetails);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getEmailStatusByID($id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $status = User::select('ul_ts_email_verified_at')->where('ul_int_profile_ref', $id)->first();
                return $this->sendResponse(message: 'Get Email Verification Status', result: $status);
            }

            return $this->sendError('Unauthorized Request', 401);
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function updateEmailStatusByID($id)
    {

        try {

            $status  = User::where('ul_int_profile_ref', $id)->update(array('ul_ts_email_verified_at' => now()));

            if ($status) {
                return $this->sendResponse(message: 'Email Verified Successfully', result: $status);
            }else{
                return $this->sendError(errorMEssage: 'Email Verification Failed', code: 500);
            }

        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getMyFirstTimeStatusByID($id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $status = User::select('ul_int_first_time_login')->where('ul_int_profile_ref', $id)->first();

                return $this->sendResponse(message: 'Get my first time status', result: $status);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function updateMyProfileDetailsByID(Request $request, $id)
    {
        try {

            if($this->isAuthorizedUser($id)){
                $validator = validator::make(
                    $request->all(),
                    [
                        'up_var_first_name' => 'required|string|max:255',
                        'up_var_last_name' => 'required|string|max:255',
                        'up_var_nric' => 'required|string|max:255',
                        'up_var_contact_no' => 'required|string|max:255',
                        'up_int_iscompany' => 'required|integer',
                        'up_var_company_no'=>'sometimes|string|max:255',
                        'up_var_address' => 'required|string|max:255',
                        'up_int_zip_code' => 'required|integer',
                        'up_var_state' => 'required|string|max:255',
                        'up_txt_desc' => 'sometimes|string',
                        'up_var_pic' => 'sometimes|max:2048'
                    ]
                );

                if($validator->fails()){
                    return $this->sendError(errorMEssage: 'Validator ' . $validator->errors()->first(), code: 400);
                }

                $updatedData = $request->except(['_method']);

                if($request->hasFile('up_var_pic')){
                    $picPath = $this->uploadMedia($request->file('up_var_pic'), 0);

                    if(empty($picPath)){
                        return $this->sendError(errorMEssage: 'Image Upload Error', code: 400);
                    }

                    $updatedData['up_var_pic'] = $picPath;
                }

                DB::beginTransaction();

                UserProfile::where('up_int_ref', $id)->update(
                    $updatedData
                );

                User::where('ul_int_profile_ref', $id)->update(
                    array(
                        'ul_int_first_time_login' => 1,
                    )
                );

                DB::commit();


                $userProfileInfo = UserProfile::find($id);

                return $this->sendResponse(message: 'Profile Updated Successfully', result: $userProfileInfo);
            }

            return $this->sendError('Unauthorized Request', 401);

        } catch (Exception $e) {

            DB::rollBack();
            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    //! FIXME: Remove this method if not needed
    public function getCompententPersonTypeList(){
        try {
            $competentPersonTypeList = CompetentPersonTypes::all();
            return $this->sendResponse(message: 'Get Competent Person Type List', result: $competentPersonTypeList);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e, code: 500);
        }
    }
}
