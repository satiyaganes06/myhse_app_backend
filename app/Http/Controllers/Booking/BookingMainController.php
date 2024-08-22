<?php

namespace App\Http\Controllers\Booking;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Booking\BookingRequest;
use App\Models\Booking\BookingRequestNegotiation;
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
use DateTimeZone;

class BookingMainController extends BaseController
{

    public function getBookingsRequestDetailByID(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $limit = $request->input('limit');
                $role = $request->input('role');

                $bookingDetails = BookingRequest::join('cp_service', 'booking_request.br_int_cps_ref', '=', 'cp_service.cps_int_ref')
                    ->join('service_main_ref', 'cp_service.cps_int_service_ref', '=', 'service_main_ref.smr_int_ref')
                 //   ->join('user_profile', 'cp_service.cps_int_user_ref', '=', 'user_profile.up_int_ref')
                 ->join('user_profile', $role == 0 ? 'booking_request.br_int_req_user_ref' : 'cp_service.cps_int_user_ref', '=', 'user_profile.up_int_ref')
                    ->where('cp_service.cps_int_user_ref',  $id)
                    ->where('booking_request.br_int_status', $request->input('status'))
                    ->select(
                        'booking_request.*',
                        'cp_service.*',
                        'service_main_ref.*',
                        'user_profile.*',
                    )->orderBy('br_ts_created_at', 'desc')->paginate($limit);

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

    public function addBookingRequest(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'serviceID' => 'required|integer',
                'userID' => 'required|integer',
                'price' => 'required|numeric',
                'deadline' => 'required|string',
                'details' => 'required|string',
                'address' => 'sometimes|string',
                'zipCode' => 'sometimes|string',
                'state' => 'sometimes|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Validation Error: ' . $validator->errors()->first(), code: 400);
            }

            $bookingRequest = new BookingRequest([
                'br_int_cps_ref' => $request->input('serviceID'),
                'br_int_req_user_ref' => $request->input('userID'),
                'br_double_price' => $request->input('price'),
                'br_var_delivery_time' => $request->input('deadline'),
                'br_txt_task_detail' => $request->input('details'),
                'br_int_status' => 0,
                'br_ts_created_at' =>  now()
            ]);

            $optionalFields = ['address' => 'br_var_address', 'zipCode' => 'br_int_zip_code', 'state' => 'br_var_state'];
            foreach ($optionalFields as $input => $field) {
                if ($request->input($input)) {
                    $bookingRequest->$field = $request->input($input);
                }
            }

            $bookingRequest->save();

            if ($bookingRequest) {
                return $this->sendResponse(message: 'Request Sent Successfully', result: $bookingRequest);
            } else {
                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function getBookingRequestNegotiationDetailByID($id, $brID, Request $request)
    {
        try {
            if ($this->isAuthorizedUser($id)) {

                $limit = $request->input('limit') ?? 10;

                $bookingRequestsNegotiation = BookingRequestNegotiation::where('brn_br_int_ref', $brID)->orderBy('brn_ts_created_at', 'desc')->paginate($limit);

                if ($bookingRequestsNegotiation->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No negotiation found', code: 404);
                }

                return $this->sendResponse(message: 'Get Booking Negotiation Details', result: $bookingRequestsNegotiation);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function addBookingRequestNegotiationDetail(Request $request)
    {

        try {

            $validator = Validator::make($request->all(), [
                'bookingRequestID' => 'required|integer',
                'requestedPrice' => 'sometimes|numeric',
                'deadline' => 'sometimes|string',
                'remark' => 'required|string',
                'userType' => 'required|integer',
                'brnType' => 'required|integer',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Validation Error: ' . $validator->errors()->first(), code: 400);
            }

            $negotiation = new BookingRequestNegotiation([
                'brn_br_int_ref' => $request->input('bookingRequestID'),
                'brn_requested_price' => $request->input('requestedPrice') ?? null,
                'brn_txt_desc' => $request->input('remark'),
                'brn_date_deadline' => $request->input('deadline'),
                'brn_int_status' => 0,
                'brn_int_user_type' => $request->input('userType'),
                'brn_int_type' => $request->input('brnType'),
                'brn_ts_created_at' =>  now()
            ]);

            $negotiation->save();

            if ($negotiation) {
                return $this->sendResponse(message: 'Booking Negotiation Sent Successfully', result: $negotiation);
            } else {
                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function updateBookingRequestNegotiationStatusByID(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $validator = Validator::make($request->all(), [
                    'bookingRequestID' => 'required|integer',
                    'bookingRequestNegotiationID' => 'required|integer',
                    'status' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: 'Validation Error', code: 400);
                }

                // $request = BookingRequestNegotiation::join('cp_service', 'booking_main.bm_int_competent_person_service_id', '=', 'cp_service.cps_int_ref')
                //     ->where('booking_main.bm_int_ref', $request->input('bookingMainID'))
                //     ->select(
                //         'booking_main.*',
                //         'competent_person_services.*',
                //     )->first();

                // if ($request->cps_int_user_ref != $id) {
                //     return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
                // }

                BookingRequestNegotiation::where('brn_int_ref', $request->input('bookingRequestNegotiationID'))->update(
                    array(
                        'brn_int_status' => $request->input('status')
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

    public function updateBookingMainNegotiationStatusByID(Request $request, $id)
    {
        try {
            if($this->isAuthorizedUser($id)){
                $validator = Validator::make($request->all(), [
                    'bookingRequestID' => 'required|integer',
                    'deadline' => 'required|string',
                    'newPrice' => 'required|numeric',
                    'status' => 'required|integer',
                ]);

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: 'Validation Error : ' . $validator->errors()->first(), code: 400);
                }

                if ($request->input('status') == '1') {
                    DB::beginTransaction();

                    BookingRequest::where('br_int_ref', $request->input('bookingRequestID'))->update(
                        array(
                            'br_double_price' => $request->input('newPrice'),
                            'br_int_status' => $request->input('status'),
                            'br_var_delivery_time' => $request->input('deadline')
                        )
                    );


                    $jobMain = new JobMain([
                        'jm_br_ref' => $request->input('bookingRequestID'),
                        'jm_date_deadline' => $request->input('deadline'),
                        'jm_int_timeline_status' => 0,
                        'jm_int_status' => 0
                    ]);

                    $jobMain->save();


                    DB::commit();

                    //  return $this->sendResponse(message: 'Proposal accepted successfully', result: $jobMain);
                    return $this->sendResponse(message: 'Proposal accepted successfully');
                } else if ($request->input('status') == '2') {

                    BookingRequest::where('br_int_ref', $request->input('bookingRequestID'))->update(
                        array(
                            'br_int_status' => $request->input('status')
                        )
                    );

                    return $this->sendResponse(message: 'Proposal rejected successfully');
                } else {

                    return $this->sendError(errorMEssage: 'Invalid Status', code: 406);
                }
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);

        } catch (Exception $e) {

            DB::rollBack();
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }
}
