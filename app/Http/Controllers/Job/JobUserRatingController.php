<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Models\Job\JobUserRating;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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

    public function getRatingByServiceID($id, $serviceID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $review = JobUserRating::where('jur_int_cps_ref', $serviceID)->get();
                $rating = $review->avg('jur_rating_point');

                if ($review) {
                    return $this->sendResponse(message: 'Get User Rating Details', result: round($rating, 1));
                }

                return $this->sendError(errorMEssage: 'No rating found', code: 404);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getReviewByServiceID(Request $request, $id, $serviceID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $limit = $request->input('limit') ?? 10;

                $review = JobUserRating::join('user_profile', 'user_profile.up_int_ref', '=', 'job_user_rating.jur_var_up_ref')
                    ->where('jur_int_cps_ref', $serviceID)
                    ->orderby('jur_ts_created_at', 'desc')->paginate($limit);

                $rating = JobUserRating::where('jur_int_cps_ref', $serviceID)->avg('jur_rating_point');

                if ($review->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No review found', code: 404);
                }

                return $this->sendResponse(message: 'Get User Review Details', result: [
                    'rating' => round($rating, 1),
                    'review' => $review,
                ]);
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
                    'comment' => 'required:string',
                    'user_type' => 'integer',
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
                $jobUserRating->jur_int_user_type = 0;

                $jobUserRating->save();

                if ($jobUserRating) {
                    $data = JobUserRating::find($jobUserRating->jur_int_ref);

                    return $this->sendResponse(message: 'Thank you for feedback.', result: $data);
                } else {
                    return $this->sendError(errorMEssage: 'Error in adding rating', code: 500);
                }
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }
}
