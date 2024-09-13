<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Booking\BookingMain;
use App\Models\Booking\BookingRequest;
use App\Models\Job\JobMain;
use App\Models\Job\JobUserRating;
use App\Models\Job\JobPayment;
use App\Models\Job\JobResult;
use Illuminate\Support\Facades\Validator;
use App\Models\UserLogin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use Illuminate\Support\Facades\Storage;

use Exception;
use Nette\Schema\Expect;
use Symfony\Component\Console\Input\Input;

class JobMainController extends BaseController
{
    public function getJobMainDetailsByID(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $limit = $request->input('limit');
                $role = $request->input('role');


                $jobMain = JobMain::join('booking_request', 'job_main.jm_br_ref', '=', 'booking_request.br_int_ref')
                    ->join('cp_service', 'booking_request.br_int_cps_ref', '=', 'cp_service.cps_int_ref')
                    ->join('service_main_ref', 'cp_service.cps_int_service_ref', '=', 'service_main_ref.smr_int_ref')
                    ->join('user_profile', $role == 0 ? 'cp_service.cps_int_user_ref'  : 'booking_request.br_int_req_user_ref', '=', 'user_profile.up_int_ref')
                    ->where($role == 0 ? 'booking_request.br_int_req_user_ref' : 'cp_service.cps_int_user_ref',  $id)
                    ->where('job_main.jm_int_status', $request->input('status'))
                    ->select(
                        'booking_request.*',
                        'cp_service.*',
                        'service_main_ref.*',
                        'user_profile.*'
                    )->orderBy('job_main.jm_ts_created_at', 'desc')->paginate($limit);


                if ($jobMain->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No order found', code: 404);
                }

                return $this->sendResponse(message: 'Get Orders Details', result: $jobMain);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getJobMainDetailByID($id, $brID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $jobMain = JobMain::where('jm_br_ref', $brID)->first();

                if ($jobMain) {
                    return $this->sendResponse(message: 'Get Orders Details', result: $jobMain);
                }
                return $this->sendError(errorMEssage: 'No data found', code: 404);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function updateJobMainResultStatus(Request $request, $id)
    {
        try {
            if($this->isAuthorizedUser($id)){
                $validator = Validator::make($request->all(), [
                    'jmID' => 'required|integer',
                    'status' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: 'Validation Error: ' . $validator->errors()->first(), code: 400);
                }

                $update = JobMain::where('jm_int_ref', $request->input('jmID'))->update(
                    array(
                        'jm_result_complete_status' => $request->input('status')
                    )
                );

                if($update){
                    return $this->sendResponse(message: 'Your request sent to client successfully.', result: $request->input('status'));
                }

                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function updateJobMainTimeline(Request $request, $id)
    {
        try {
            if($this->isAuthorizedUser($id)){
                $validator = Validator::make($request->all(), [
                    'jmID' => 'required|integer',
                    'timeline' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: 'Validation Error: ' . $validator->errors()->first(), code: 400);
                }

                $update = JobMain::where('jm_int_ref', $request->input('jmID'))->update(
                    array(
                        'jm_int_timeline_status' => $request->input('timeline')
                    )
                );

                if($update){
                    return $this->sendResponse(message: 'Update the timeline status successfully.', result: $request->input('timeline'));
                }

                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getCpSummaryDetailsByID($id){
        try {

            if(!$this->isAuthorizedUser($id)){
                return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
            }

            $jobMain = JobMain::join('booking_request', 'job_main.jm_br_ref', '=', 'booking_request.br_int_ref')
            ->join('cp_service', 'booking_request.br_int_cps_ref', '=', 'cp_service.cps_int_ref')
            ->where('cp_service.cps_int_user_ref', $id)
            ->select('job_main.*', 'booking_request.*', 'cp_service.*')
            ->get();

            $bookingRequest = BookingRequest::join('cp_service', 'booking_request.br_int_cps_ref', '=', 'cp_service.cps_int_ref')
            ->where('cp_service.cps_int_user_ref', $id)
            ->where('booking_request.br_int_status', 0)
            ->get();

            $totalBookingRequest = $bookingRequest->count();

            $totalJob = $jobMain->count();
            $totalJobMainStatus0 = $jobMain->where('jm_int_status', 0)->count();
            $totalJobMainStatus1 = $jobMain->where('jm_int_status', 1)->count();
            $totalJobMainStatus2 = $jobMain->where('jm_int_status', 2)->count();

            // $revenue = ExpertRevenueAccount::where('era_up_var_ref',  $request->input('expertID'))->first();

            // $totalEarning = $revenue->era_double_total_balance;

            $totalRating = JobUserRating::join('job_main', 'job_user_rating.jur_jm_ref', '=', 'job_main.jm_int_ref')
            ->join('booking_request', 'job_main.jm_br_ref', '=', 'booking_request.br_int_ref')
            ->join('cp_service', 'booking_request.br_int_cps_ref', '=', 'cp_service.cps_int_ref')
            ->where('cp_service.cps_int_user_ref', $id)
            ->avg('job_user_rating.jur_rating_point');


            $summary = array(
                'totalJob' => $totalJob,
                'totalBookingRequest' => $totalBookingRequest,
                'totalJobMainStatus0' => $totalJobMainStatus0,
                'totalJobMainStatus1' => $totalJobMainStatus1,
                'totalJobMainStatus2' => $totalJobMainStatus2,
                'totalRating' => number_format($totalRating, 2) ?? 0,
            );

            return $this->sendResponse(message: 'Get Summary', result: $summary);

        } catch (\Throwable $th) {

            return $this->sendError(errorMEssage: $th->getMessage(), code:500);

        }
    }
}

// public function cpJobMainListDetails(Request $request)
//   {
//     try {

//       $bookingDetails = JobMain::join('booking_main', 'job_main.jm_int_booking_ref', '=', 'booking_main.bm_int_ref')
//         //    ->join('job_payment', 'job_main.jm_int_ref', '=', 'job_payment.jp_int_ref')
//         ->join('competent_person_services', 'booking_main.bm_int_competent_person_service_id', '=', 'competent_person_services.cps_int_ref')
//         ->join('service_main', 'competent_person_services.cps_int_service_ref', '=', 'service_main.sm_int_ref')
//         ->join('service_sub_list', 'service_main.sm_int_ref', '=', 'service_sub_list.ssl_int_ref')
//         ->where('competent_person_services.cps_int_user_ref',  $request->input('cpID'))
//         ->where('job_main.jm_int_status', $request->input('status'))
//         ->select(
//           'job_main.*',
//           'booking_main.*',
//           'competent_person_services.*',
//           'service_main.*',
//           'service_sub_list.*'
//         )
//         ->get();

//       return $this->sendResponse('booking list', '', $bookingDetails);
//     } catch (Exception $e) {
//       return $this->sendError('Error : ' . $e->getMessage(), 500);
//     }
//   }

//   public function cpJobMainDetails(Request $request)
//   {
//     try {

//       $jobMainDetails = JobMain::where('jm_int_ref',  $request->input('jobMainID'))
//         ->first();

//       return $this->sendResponse('booking details', '', $jobMainDetails);
//     } catch (Exception $e) {
//       return $this->sendError('Error : ' . $e->getMessage(), 500);
//     }
//   }

//   public function cpJobPaymentDetails(Request $request)
//   {
//     try {

//       $jobPayment = JobPayment::where('jp_int_job_ref', '=', $request->input('jobMainID'))->first();


//       return $this->sendResponse('join payment list', '', $jobPayment);
//     } catch (Exception $e) {
//       return $this->sendError('Error : ' . $e->getMessage(), 500);
//     }
//   }

//   public function cpAddJobResultDetails(Request $request)
//   {
//     try {


//       if ($request->hasFile('pdf') && $request->file('pdf')->isValid()) {
//         $file = $request->file('pdf');

//         try {
//           // Specify the destination folder outside of the Laravel public directory
//           $destinationPath = 'myhse/uploads/test';

//           // Generate a unique filename
//           $fileName = time() . '_' . $file->getClientOriginalName();

//           // Move the uploaded file to the destination folder
//           $file->move($destinationPath, $fileName);

//           // Get the full path of the uploaded file
//           $filePath = $destinationPath . '/' . $fileName;

//         } catch (\Throwable $th) {

//           return $this->sendResponse("Error: " . $th->getMessage(), '', 500);
//         }
//       } else {

//         return $this->sendError('Invalid file or file upload failed.', '');

//       }

//       $jobResult = new JobResult(
//         [
//           'jr_int_job_ref' => $request->input('jobMainID'),
//           'jr_var_doc_title' => $request->input('progressDesc'),
//           'jr_var_doc_path' => $filePath,
//           'jr_bool_is_final_document' => 0,
//         ]
//       );


//       $jobResult->save();


//       return $this->sendResponse('Progress report sent successfully', '', $jobResult);
//     } catch (Exception $e) {
//       return $this->sendError('Error : ' . $e->getMessage(), 500);
//     }
//   }

//   public function cpJobResultDetails(Request $request)
//   {
//     try {

//       $jobResult = JobResult::where('jr_int_job_ref', '=', $request->input('jobResultID'))
//         ->where('jr_bool_is_final_document', '=', $request->input('status'))
//         ->get();


//       return $this->sendResponse('join result list', '', $jobResult);
//     } catch (Exception $e) {
//       return $this->sendError('Error : ' . $e->getMessage(), 500);
//     }
//   }

//   public function updateCpJobMainProgressCompleteStatus(Request $request)
//   {
//     try {

//       JobMain::where('jm_int_ref', $request->input('jobMainID'))->update(
//         array(
//           'jm_int_progress_complete_status' => $request->input('status'),
//           'jm_varchar_progress_complete_commant' => ''
//         )
//       );


//       return $this->sendResponse('Job Main Progress Complete Status have been changed ', '',);
//     } catch (Exception $e) {
//       return $this->sendError('Error : ' . $e->getMessage(), 500);
//     }
//   }

//   public function uploadJobResultFinalDocument(Request $request)
//   {
//     try {
//       $request->validate([
//         'pdf' => 'required|mimes:pdf|max:10240', // Adjust the file size limit as needed
//       ]);

//       $pdf = $request->file('pdf');
//       // $path = $pdf->store('uploads/documents'); // 'pdfs' is the storage folder, you can change it as needed
//       $path = Storage::disk('local')->put('uploads/documents', $pdf);

//       $jobResult = new JobResult(
//         [
//           'jr_int_job_ref' => $request->input('jobMainID'),
//           'jr_var_doc_title' => 'final report',
//           'jr_var_doc_path' => $path,
//           'jr_bool_is_final_document' => 1,
//         ]
//       );

//       $jobResult->save();

//       $this->updateCpJobMainStatus($request);

//       return $this->sendResponse('Send succeffully', '',);
//     } catch (Exception $e) {
//       return $this->sendError('Error : ' . $e->getMessage(), 500);
//     }
//   }

//   public function updateCpJobMainStatus(Request $request)
//   {
//     try {

//       JobMain::where('jm_int_ref', $request->input('jobMainID'))->update(
//         array(
//           'jm_int_status' => $request->input('jobMainStatus')
//         )
//       );

//       return ;

//     } catch (Exception $e) {
//       return $this->sendError('Error : ' . $e->getMessage(), 500);
//     }
//   }
