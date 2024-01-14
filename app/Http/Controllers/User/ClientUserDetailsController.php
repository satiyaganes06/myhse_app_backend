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

class ClientUserDetailsController extends BaseController
{
    public function clientUserProfileDetails(Request $request)
    {

        try {
            $userProfileID = $this->getCpProfileDetails($request->input('clientUserLoginID'));
            $userProfileDetails = UserProfile::where('up_int_ref', $userProfileID)->first();

            return $this->sendResponse('get user info', '', $userProfileDetails);
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    
}
