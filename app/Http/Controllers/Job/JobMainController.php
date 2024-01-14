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

class JobMainController extends BaseController
{

  public function cpJobMainListDetails(Request $request)
  {
    try {

      $bookingDetails = JobMain::join('booking_main', 'job_main.jm_int_booking_ref', '=', 'booking_main.bm_int_ref')
        //    ->join('job_payment', 'job_main.jm_int_ref', '=', 'job_payment.jp_int_ref')
        ->join('competent_person_services', 'booking_main.bm_int_competent_person_service_id', '=', 'competent_person_services.cps_int_ref')
        ->join('service_main', 'competent_person_services.cps_int_service_ref', '=', 'service_main.sm_int_ref')
        ->join('service_sub_list', 'service_main.sm_int_ref', '=', 'service_sub_list.ssl_int_ref')
        ->where('competent_person_services.cps_int_user_ref',  $request->input('cpID'))
        ->where('job_main.jm_int_status', $request->input('status'))
        ->select(
          'job_main.*',
          'booking_main.*',
          'competent_person_services.*',
          'service_main.*',
          'service_sub_list.*'
        )
        ->get();

      return $this->sendResponse('booking list', '', $bookingDetails);
    } catch (Exception $e) {
      return $this->sendError('Error : ' . $e->getMessage(), 500);
    }
  }

  public function cpJobMainDetails(Request $request)
  {
    try {

      $jobMainDetails = JobMain::where('jm_int_ref',  $request->input('jobMainID'))
        ->first();

      return $this->sendResponse('booking details', '', $jobMainDetails);
    } catch (Exception $e) {
      return $this->sendError('Error : ' . $e->getMessage(), 500);
    }
  }

  public function cpJobPaymentDetails(Request $request)
  {
    try {

      $jobPayment = JobPayment::where('jp_int_job_ref', '=', $request->input('jobMainID'))->first();


      return $this->sendResponse('join payment list', '', $jobPayment);
    } catch (Exception $e) {
      return $this->sendError('Error : ' . $e->getMessage(), 500);
    }
  }

  public function cpAddJobResultDetails(Request $request)
  {
    try {
      

      $file = $request->file('filePdf');
     // dd($file);
       $filePath = $file->store('reports', 'public');

      // $pdf = $request->file('pdf');
      // $path = $pdf->store('uploads/documents');
    // $path = Storage::disk('local')->put('uploads/documents', $pdf);

      $jobResult = new JobResult(
        [
          'jr_int_job_ref' => $request->input('jobMainID'),
          'jr_var_doc_title' => $request->input('progressDesc'),
          'jr_var_doc_path' => $filePath,
          'jr_bool_is_final_document' => 0,
        ]
      );

      $jobResult->save();


      return $this->sendResponse('booking list', '', $jobResult);
    } catch (Exception $e) {
      return $this->sendError('Error : ' . $e->getMessage(), 500);
    }
  }

  public function cpJobResultDetails(Request $request)
  {
    try {

      $jobResult = JobResult::where('jr_int_job_ref', '=', $request->input('jobResultID'))
      ->where('jr_bool_is_final_document', '=', $request->input('status'))
      ->get();


      return $this->sendResponse('join result list', '', $jobResult);
    } catch (Exception $e) {
      return $this->sendError('Error : ' . $e->getMessage(), 500);
    }
  }

  public function updateCpJobMainProgressCompleteStatus(Request $request)
  {
    try {

      JobMain::where('jm_int_ref', $request->input('jobMainID'))->update(
        array(
          'jm_int_progress_complete_status' => $request->input('status'),
          'jm_varchar_progress_complete_commant' => ''
        )
      );


      return $this->sendResponse('Job Main Progress Complete Status have been changed ', '',);
    } catch (Exception $e) {
      return $this->sendError('Error : ' . $e->getMessage(), 500);
    }
  }

  // app/Http/Controllers/FileController.php

  public function uploadJobResultFinalDocument(Request $request)
  {
    try{
      $request->validate([
        'pdf' => 'required|mimes:pdf|max:10240', // Adjust the file size limit as needed
      ]);
  
      $pdf = $request->file('pdf');
     // $path = $pdf->store('uploads/documents'); // 'pdfs' is the storage folder, you can change it as needed
      $path = Storage::disk('local')->put('uploads/documents', $pdf);
  
      $jobResult = new JobResult(
        [
          'jr_int_job_ref' => $request->input('jobMainID'),
          'jr_var_doc_title' => 'final report',
          'jr_var_doc_path' => $path,
          'jr_bool_is_final_document' => 1,
        ]
      );
  
      $jobResult->save();
  
  
      return $this->sendResponse('Send succeffully', '',);

    } catch (Exception $e) {
      return $this->sendError('Error : ' . $e->getMessage(), 500);
    }
  }
}
