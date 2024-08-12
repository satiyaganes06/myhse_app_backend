<?php

namespace App\Http\Controllers\Job;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Booking\BookingMain;
use App\Models\Booking\BookingRequest;
use App\Models\Job\JobMain;
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

class JobPaymentController extends BaseController
{

    public function getJobInitialPaymentStatusByID($id, $brID, $jmID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $bookingReq = BookingRequest::select('br_double_price')->find($brID);

                $firstPayment = $bookingReq->br_double_price * 0.1;

                $jobPayment = JobPayment::where('jp_jm_ref', $jmID)->get();

                if (!$jobPayment) {
                    return $this->sendError(errorMEssage: 'Payment not found', code: 404);
                }

                $totalAmount = 0; // Initialize total amount

                foreach ($jobPayment as $payment) {
                    if ($payment->jp_int_status == 1) {
                        $totalAmount += $payment->jp_double_account_transfer_amount; // Sum amounts
                    }
                    // If jp_int_status is 2, we skip to the next payment
                }


                // Check if total amount equals first payment
                if ($totalAmount == $firstPayment) {
                    $reason = null;
                    $paymentStatus = 'true'; // Return true if amounts match
                }else{
                    $reason = JobPayment::where('jp_jm_ref', $jmID)->where('jp_int_status', 2)->orderBy('jp_ts_created_at', 'desc')->first()->jp_var_reject_reason ?? null;
                    $paymentStatus = 'false'; // Return false if amounts do not match
                }



                return $this->sendResponse(message: 'Get Orders Details', result: [
                    'paymentStatus' => $paymentStatus,
                    'reason' => $reason
                ]);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
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
