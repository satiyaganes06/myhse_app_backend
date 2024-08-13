<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Booking\BookingMain;
use App\Models\Booking\BookingRequest;
use App\Models\Job\JobMain;
use App\Models\Job\JobPayment;
use App\Models\Job\JobResult;
use App\Models\Job\JobResultComment;
use App\Models\Job\JobResultFile;
use Illuminate\Support\Facades\Validator;
use App\Models\UserLogin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Storage;

use Exception;
use Nette\Schema\Expect;
use Symfony\Component\Console\Input\Input;

class JobResultController extends BaseController
{

    public function getJobResultByID($id, $jmID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $jobResults = JobResult::where('jr_jm_ref', $jmID)->orderBy('jr_ts_created_at', 'desc')->get();

                if ($jobResults->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No result found', code: 404);
                }

                $files = JobResultFile::whereIn('jrf_jr_ref', $jobResults->pluck('jr_int_ref'))->get();

                // Group images by booking request
                $groupedImages = $files->groupBy('jrf_jr_ref');

                // Add images to booking requests
                foreach ($jobResults as $jobResult) {
                    $jobResult->mediaURL = $groupedImages[$jobResult->jr_int_ref] ?? [];
                }

                return $this->sendResponse(message: 'Get Result Details', result: $jobResults);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function addJobResult(Request $request)
    {
        try {
            // Start Template
            $validator = validator::make($request->all(), [
                'jmID' => 'required|integer',
                'description' => 'required|string|max:255',
                'type' => 'required|integer',
                'progressMedia' => 'sometimes|max:10000',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: $validator->errors(), code: 400);
            }

            // End Template

            DB::beginTransaction();

            $jobResult = JobResult::create([
                'jr_jm_ref' => $request->input('jmID'),
                'jr_txt_description' => $request->input('description'),
                'jr_int_type_item' => $request->input('type'),
                'jr_double_progress_percent' => 0,
                'jr_int_status' => 0
            ]);

            if ($request->hasFile('progressMedia')) {
                $fileURL = $this->uploadMedia($request->file('progressMedia'), 6);

                if (empty($fileURL)) {
                    return $this->sendError(errorMEssage: 'Image Upload Error', code: 400);
                }

                JobResultFile::create([
                    'jrf_jr_ref' => $jobResult->jr_int_ref,
                    'jrf_files_path' => $fileURL
                ]);
            }

            DB::commit();

            // Start Template
            if ($jobResult) {

                return $this->sendResponse(message: 'Sent Successfully');
            } else {
                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }

            // End Template
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getJobResultCommentsByID(Request $request, $id, $jrID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $limit = $request->input('limit');

                $comments = JobResultComment::where('jrc_jr_ref', $jrID)->orderBy('jrc_ts_created_at', 'desc')->paginate($limit);

                if ($comments->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No comment found', code: 404);
                }

                return $this->sendResponse(message: 'Get Comment Details', result: $comments);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function addJobResultComment(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'jrID' => 'required|integer',
                'type' => 'required|integer',
                'comment' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Validation Error: ' . $validator->errors()->first(), code: 400);
            }

            $comment = new JobResultComment([
                'jrc_jr_ref' => $request->input('jrID'),
                'jrc_int_user_type' => $request->input('type'),
                'jrc_txt_comment' => $request->input('comment'),
                'jrc_ts_created_at' => now()
            ]);

            $comment->save();

            if ($comment) {
                return $this->sendResponse(message: 'Comment Sent Successfully', result: $comment);
            } else {
                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }
}
