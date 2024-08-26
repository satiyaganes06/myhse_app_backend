<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Booking\BookingMain;
use App\Models\Booking\BookingRequest;
use App\Models\Job\JobMain;
use App\Models\Job\JobPayment;
use App\Models\Job\JobResult;
use App\Models\Job\JobUserRating;
use Illuminate\Support\Facades\Validator;
use App\Models\UserLogin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Storage;

use Exception;
use Nette\Schema\Expect;
use Symfony\Component\Console\Input\Input;

class JobUserRatingController extends BaseController
{

    public function getJobUserRatingByID($id, $jmID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $jobMain = JobUserRating::where('jur_jm_ref', $jmID)->first();

                if ($jobMain) {
                    return $this->sendResponse(message: 'Get User Rating Details', result: $jobMain);
                }
                return $this->sendError(errorMEssage: 'No review found', code: 404);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }


    public function addJobUserRating(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $validator = Validator::make($request->all(), [
                    'jmID' => 'required:integer',
                    'serviceID' => 'required:integer',
                    'rating_point' => 'required:double',
                    'comment' => 'required:string'
                ]);

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: $validator->errors(), code: 400);
                }

                $jobUserRating = new JobUserRating();
                $jobUserRating->jur_jm_ref = $request->input('jmID');
                $jobUserRating->jur_var_up_ref = $id;
                $jobUserRating->jur_rating_point = $request->input('rating_point');
                $jobUserRating->jur_txt_comment = $request->input('comment');
                $jobUserRating->jur_int_cps_ref = $request->input('serviceID');

                $jobUserRating->save();

                return $this->sendResponse(message: 'Thank you for feedback.', result: $jobUserRating);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }
}
