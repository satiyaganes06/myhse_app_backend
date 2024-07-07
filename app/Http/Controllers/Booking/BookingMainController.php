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

    public function getBookingsDetailByID(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $limit = $request->input('limit');

                $bookingDetails = BookingMain::join('competent_person_services', 'booking_main.bm_int_competent_person_service_id', '=', 'competent_person_services.cps_int_ref')
                    ->join('service_main', 'competent_person_services.cps_int_service_ref', '=', 'service_main.sm_int_ref')
                    ->where('competent_person_services.cps_int_user_ref',  $id)
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
                    )->paginate($limit);

                if ($bookingDetails->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No booking found', code: 404);
                }

                return $this->sendResponse(message: 'Get Booking Details', result: $bookingDetails);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getBookingRequestDetailByID($id, $brID, Request $request)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $limit = $request->input('limit') ?? 10;

                $bookingRequests = BookingRequest::where('br_int_bookingmain_ref', $brID)->paginate($limit);

                if ($bookingRequests->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No negotiation found', code: 404);
                }

                return $this->sendResponse(message: 'Get Booking Negotiation Details', result: $bookingRequests);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function addBookingRequestDetail(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'bookingMainID' => 'required|integer',
                'requestedPrice' => 'required|numeric',
                'remark' => 'required|string',
                'status' => 'required|integer',
                'userType' => 'required|integer'
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Validation Error', code: 400);
            }

            $bookingRequest = new BookingRequest([
                'br_int_bookingmain_ref' => $request->input('bookingMainID'),
                'br_text_request' => $request->input('requestedPrice'),
                'br_txt_remark_reason' => $request->input('remark'),
                'br_int_status' => $request->input('status'),
                'br_int_user_type' => $request->input('userType')
            ]);

            $bookingRequest->save();

            if ($bookingRequest) {
                return $this->sendResponse(message: 'Booking Negotiation Sent Successfully', result: $bookingRequest);
            } else {
                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function updateBookingRequestStatusByID(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $validator = Validator::make($request->all(), [
                    'bookingMainID' => 'required|integer',
                    'bookingRequestID' => 'required|integer',
                    'status' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: 'Validation Error', code: 400);
                }

                $bookingDetails = BookingMain::join('competent_person_services', 'booking_main.bm_int_competent_person_service_id', '=', 'competent_person_services.cps_int_ref')
                    ->where('booking_main.bm_int_ref', $request->input('bookingMainID'))
                    ->select(
                        'booking_main.*',
                        'competent_person_services.*',
                    )->first();

                if ($bookingDetails->cps_int_user_ref != $id) {
                    return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
                }

                BookingRequest::where('br_int_ref', $request->input('bookingRequestID'))->update(
                    array(
                        'br_int_status' => $request->input('status')
                    )
                );

                if ($request->input('status') == '1') {
                    return $this->sendResponse(message: 'Accepted Successfully');
                } else {
                    return $this->sendResponse(message: 'Rejected Successfully');
                }
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    //! FIXME: Refactor this job payment and job main creation to a separate function
    public function updateBookingMainStatusByID(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'bookingMainID' => 'required|integer',
                'newPrice' => 'required|numeric',
                'status' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Validation Error', code: 400);
            }

            $bookingDetails = BookingMain::join('competent_person_services', 'booking_main.bm_int_competent_person_service_id', '=', 'competent_person_services.cps_int_ref')
                ->where('booking_main.bm_int_ref', $request->input('bookingMainID'))
                ->select(
                    'booking_main.*',
                    'competent_person_services.*',
                )->first();

            if ($bookingDetails->cps_int_user_ref != $id) {
                return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
            }

            if ($request->input('status') == '1') {
                DB::beginTransaction();

                BookingMain::where('bm_int_ref', $request->input('bookingMainID'))->update(
                    array(
                        'bm_dt_booking_datetime' => now(),
                        'bm_var_total_amount' => $request->input('newPrice'),
                        'bm_int_status' => $request->input('status')
                    )
                );

                $jobMain = new JobMain([
                    'jm_int_booking_ref' => $bookingDetails->bm_int_ref,
                    'jm_date_setDate' => $bookingDetails->bm_dt_booking_datetime,
                    'jm_text_address_1' => $bookingDetails->bm_txt_address1,
                    'jm_text_address_2' => $bookingDetails->bm_txt_address2,
                    'jm_int_state_ref' => $bookingDetails->bm_int_state_ref,
                    'jm_var_postcode' => $bookingDetails->bm_var_postcode,
                    'jm_txt_job_desc' => $bookingDetails->bm_txt_task_detail,
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

                return $this->sendResponse(message: 'Booking Accepted Successfully');

            } else if ($request->input('status') == '2') {

                BookingMain::where('bm_int_ref', $request->input('bookingMainID'))->update(
                    array(
                        'bm_int_status' => $request->input('status')
                    )
                );

                return $this->sendResponse(message: 'Booking Rejected Successfully');
            } else {

                return $this->sendError(errorMEssage: 'Invalid Status', code: 406);

            }

        } catch (Exception $e) {

            DB::rollBack();
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }
}
