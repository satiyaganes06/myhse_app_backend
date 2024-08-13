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




}
