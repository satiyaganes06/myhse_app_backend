<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Booking\BookingMain;
use App\Models\Booking\BookingRequest;
use App\Models\Job\JobPayment;
use App\Models\Job\JobMain;
use Illuminate\Support\Facades\Validator;
use App\Models\UserLogin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use DateTime;
use Exception;
use Nette\Schema\Expect;
use Symfony\Component\Console\Input\Input;

class BookingMainController extends BaseController
{

    public function cpBookingInfo(Request $request)
    {

        try {

            // $validator = Validator::make($request->all(), [
            //     'cpID' => 'required|integer',
            // ]);

            $bookingMainList = BookingMain::where('bm_int_cp_ref', $request->input('cpID'))->get();


            return $this->sendResponse('booking list', '', $bookingMainList);
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function cpBookingDetailsList(Request $request)
    {
       try {
      //  $cpID = $this->getCpProfileDetails($request->input('cpID'));

        // $validator = Validator::make($request->all(), [
        //     'cpID' => 'required|integer',
        // ]);

        // $bookingMainList = BookingMain::where('bm_int_cp_ref', $request->input('cpID'))->get();

        $bookingDetails = BookingMain::join('competent_person_services', 'booking_main.bm_int_competent_person_service_id', '=', 'competent_person_services.cps_int_ref')
            ->join('service_main', 'competent_person_services.cps_int_service_ref', '=', 'service_main.sm_int_ref')
            ->where('competent_person_services.cps_int_user_ref',  $request->input('cpID'))
            ->where('booking_main.bm_int_status', $request->input('status'))
            ->select(
                'booking_main.*',
                'competent_person_services.*',
                'service_main.sm_int_ref',
                'service_main.sm_var_name',
                'service_main.sm_var_img_path',
                'service_main.sm_int_status',
                'service_main.sm_ts_created_at',
                'service_main.sm_ts_updated_at'
            )
            ->get();


        // Retrieve the competent person services along with their bookings for a specific user
        // $bookingDetails = CompetentPersonService::with('bookings')
        //     ->where('cps_int_user_ref', $request->input('cpID'))
        //     ->get();

        return $this->sendResponse('booking list', '', $bookingDetails);
       } catch (Exception $e) {
         return $this->sendError('Error : ' . $e->getMessage(), 500);
       }
    }

    public function cpBookingRequest(Request $request)
    {
        try {

            // $validator = Validator::make($request->all(), [
            //     'cpID' => 'required|integer',
            // ]);

            // $bookingMainList = BookingMain::where('bm_int_cp_ref', $request->input('cpID'))->get();

            // $bookingRequestDetails = BookingRequest::where('br_int_bookingmain_ref', '=', $request->input('bookingRequestID'))->get();

            $bookingRequests = DB::select('select * from booking_requests where br_int_bookingmain_ref = ?', [$request->input('bookingRequestID')]);

            return $this->sendResponse('booking request details', '', $bookingRequests);
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function addBookingRequest(Request $request)
    {
        try {

            // Insert into user_profile
            $bookingRequest = new BookingRequest([
                'br_int_bookingmain_ref' => $request->input('bookingMainID'),
                'br_text_request' => $request->input('requestedPrice'),
                'br_txt_remark_reason' => $request->input('remark'),
                'br_int_status' => $request->input('status')
            ]);

            $bookingRequest->save();

            return $this->sendResponse('Request Send', '', $bookingRequest);
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function updateStatusBookingRequest(Request $request)
    {
        try {

            // Insert into user_profile
            BookingRequest::where('br_int_ref', $request->input('bookingRequestID'))->update(
                array(
                    'br_int_status' => $request->input('status')
                )
            );

            BookingMain::where('bm_int_ref', $request->input('bookingMainID'))->update(
                array(
                    'bm_var_total_amount' => $request->input('newPrice')
                )
            );

            if($request->input('status') == '1'){
                return $this->sendResponse('Accept booking Request Successfully', '');
            }else{
                return $this->sendResponse('Reject booking Request Successfully', '');}
            
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function updateStatusBookingMain(Request $request)
    {
        try {

            $date = date('d-m-y h:i:s');

            // Insert into user_profile
            BookingMain::where('bm_int_ref', $request->input('bookingMainID'))->update(
                array(
                    'bm_dt_booking_datetime' => $date,
                    'bm_int_status' => $request->input('status')
                )
            );

            $bookingMainInfo = BookingMain::find($request->input('bookingMainID'));

            DB::beginTransaction();

            $jobMain = new JobMain([
                'jm_int_booking_ref' => $bookingMainInfo->bm_int_ref,
                'jm_date_setDate' => $bookingMainInfo->bm_dt_booking_datetime,
                'jm_text_address_1' => $bookingMainInfo->bm_txt_address1,
                'jm_text_address_2' => $bookingMainInfo->bm_txt_address2,
                'jm_int_state_ref' => $bookingMainInfo->bm_int_state_ref,
                'jm_var_postcode' => $bookingMainInfo->bm_var_postcode,
                'jm_txt_job_desc' => '',
                'jm_txt_remarks' => '',
                'jm_int_status' => 0
            ]);
            
            $jobMain->save();

            $jobPayment = new JobPayment([ 
                'jp_int_job_ref' => $jobMain->jm_int_ref,
                'jp_var_first_payment' => '',
                'jp_var_total_payment' => '',
                'jp_int_status' => 0
            ]);

            $jobPayment->save();

            DB::commit();

            return $this->sendResponse('Accept Offer Request Successfully', '');
        } catch (Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

}
