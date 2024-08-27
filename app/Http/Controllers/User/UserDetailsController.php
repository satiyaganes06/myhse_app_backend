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

    // Role
    public function getRoleByID($id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $role = User::select('ul_var_role')->where('ul_int_profile_ref', $id)->first();
                return $this->sendResponse(message: 'Get My Role', result: $role);
            }

            return $this->sendError('Unauthorized Request', 401);
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function updateRoleByID(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $validator = validator::make(
                    $request->all(),
                    [
                        'role' => 'required|string:max:50'
                    ]
                );

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: 'Validator ' . $validator->errors()->first(), code: 400);
                }

                if($request->role == 'CLIENT' || $request->role == 'CP'){
                    return $this->sendError(errorMEssage: 'Invalid role', code: 400);
                }

                $existingRoles = User::where('ul_int_profile_ref', $id)->value('ul_var_role');
                $rolesArray = explode(',', $existingRoles);

                // Add the new role if it's not already present
                if (!in_array($request->role, $rolesArray)) {
                    $rolesArray[] = $request->role;
                }

                $updatedRoles = implode(',', $rolesArray);

                $role = User::where('ul_int_profile_ref', $id)->update(
                    array(
                        'ul_var_role' => $updatedRoles
                    )
                );

                if ($role) {
                    return $this->sendResponse(message: 'Role Updated Successfully', result: $role);
                } else {
                    return $this->sendError(errorMEssage: 'Role Update Failed', code: 500);
                }
            }

            return $this->sendError('Unauthorized Request', 401);
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
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
