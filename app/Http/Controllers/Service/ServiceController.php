<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Booking\BookingMain;
use App\Models\Booking\BookingRequest;
use App\Models\Services\CompetentPersonService;
use App\Models\Services\CpServicesState;
use Illuminate\Support\Facades\Validator;
use App\Models\UserLogin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\Services\ServiceMain;
use App\Models\Services\ServiceSub;

use Exception;
use Nette\Schema\Expect;
use Symfony\Component\Console\Input\Input;

class ServiceController extends BaseController
{

    public function getServiceMainList()
    {
        try {
            $serviceMainList = ServiceMain::all();
            return $this->sendResponse(message: 'Get Service Main List', result: $serviceMainList);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e, code: 500);
        }
    }

    public function getSubServiceList()
    {
        try {
            $subServiceList = ServiceSub::all();

            if ($subServiceList->isEmpty()) {
                return $this->sendError(errorMEssage: 'No Sub Service Found', code: 404);
            }

            return $this->sendResponse(message: 'Get Sub Service List', result: $subServiceList);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e, code: 500);
        }
    }

    public function getServicesDetailByID(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $limit = $request->input('limit');

                $services = CompetentPersonService::join('service_sub_list', 'competent_person_services.cps_int_service_ref', '=', 'service_sub_list.ssl_int_ref') //! FIXME: ServiceMain is not neccessary
                    ->join('service_main', 'service_sub_list.ssl_int_servicemain_ref', '=', 'service_main.sm_int_ref') //! FIXME: ServiceMain is not neccessary
                    ->join('cp_certificate', 'competent_person_services.cps_certification_ref', '=', 'cp_certificate.cc_int_ref')
                    ->where('cps_int_user_ref', $id)
                    ->select(
                        'competent_person_services.*',
                        'service_sub_list.ssl_int_ref',
                        'service_sub_list.ssl_var_subservice_name',
                        'service_sub_list.ssl_var_img_path',
                        'service_main.sm_int_ref',
                        'service_main.sm_var_name',
                        'service_main.sm_var_img_path',
                        'cp_certificate.cc_int_ref',
                    )->orderBy('cps_ts_created_at', 'desc')->paginate($limit);

                if ($services->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No service found', code: 404);
                }

                //Fetch states for each service
                foreach ($services as $service) {
                    $states = CpServicesState::where('css_int_services_ref', $service->cps_int_ref)
                        ->pluck('css_int_states_ref')
                        ->toArray();
                    $service->states = $states;
                }


                return $this->sendResponse(message: 'Get Service Details', result: $services);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
    }

    public function addServiceDetail(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|integer',
                'subServiceID' => 'required|integer',
                'certificateID' => 'required|integer',
                'serviceDescription' => 'required|string',
                'startingPrice' => 'required|string',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Invalid Input' . $validator->errors(), code: 400);
            }

            DB::beginTransaction();

            $service = new CompetentPersonService();
            $service->cps_int_user_ref = $request->input('userID');
            $service->cps_int_service_ref = $request->input('subServiceID');
            $service->cps_certification_ref = $request->input('certificateID');
            $service->cps_txt_description = $request->input('serviceDescription');
            $service->cps_var_starting_price = $request->input('startingPrice');
            $service->cps_int_status = 0; //! FIXME: Check status Column in database (Prod)
            $service->save();

            // Store the state one by one
            $states = json_decode($request->input('serviceState'), true);
            foreach ($states as $state) {
                $stateTable = new CpServicesState();
                $stateTable->css_int_services_ref = $service->cps_int_ref;
                $stateTable->css_int_states_ref = $state;
                $stateTable->save();
            }

            DB::commit();

            $getService = CompetentPersonService::join('service_sub_list', 'competent_person_services.cps_int_service_ref', '=', 'service_sub_list.ssl_int_ref') //! FIXME: ServiceMain is not neccessary
                    ->join('service_main', 'service_sub_list.ssl_int_servicemain_ref', '=', 'service_main.sm_int_ref') //! FIXME: ServiceMain is not neccessary
                    ->join('cp_certificate', 'competent_person_services.cps_certification_ref', '=', 'cp_certificate.cc_int_ref')
                    ->where('cps_int_ref', $service->cps_int_ref)
                    ->select(
                        'competent_person_services.*',
                        'service_sub_list.ssl_int_ref',
                        'service_sub_list.ssl_var_subservice_name',
                        'service_sub_list.ssl_var_img_path',
                        'service_main.sm_int_ref',
                        'service_main.sm_var_name',
                        'service_main.sm_var_img_path',
                        'cp_certificate.cc_int_ref',
                    )->first();

            return $this->sendResponse(message: 'Service Added Successfully', result: $getService);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError('Error : ' . $e, 500);
        }
    }


    public function updateServiceDetail(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $validator = Validator::make($request->all(), [
                    'cpsID' => 'required|integer',
                    'subServiceID' => 'sometimes|integer',
                    'certificateID' => 'sometimes|integer',
                    'serviceDescription' => 'sometimes|string',
                    'startingPrice' => 'sometimes|string',
                    'serviceState' => 'sometimes|string',
                ]);

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: 'Invalid Input' . $validator->errors(), code: 400);
                }

                $service = CompetentPersonService::find($request->input('cpsID'));

                if ($service->cps_int_user_ref != $id) {
                    return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
                }

                $updateData = [];

                if ($request->input('subServiceID')) {
                    $updateData['cps_int_service_ref'] = $request->input('subServiceID');
                }

                if ($request->input('certificateID')) {
                    $updateData['cps_certification_ref'] = $request->input('certificateID');
                }

                if ($request->input('serviceDescription')) {
                    $updateData['cps_txt_description'] = $request->input('serviceDescription');
                }

                if ($request->input('startingPrice')) {
                    $updateData['cps_var_starting_price'] = $request->input('startingPrice');
                }

                DB::beginTransaction();

                $updateService = CompetentPersonService::where('cps_int_ref', $request->input('cpsID'))->update($updateData);
                $existingStates = CpServicesState::where('css_int_services_ref', $service->cps_int_ref)
                    ->pluck('css_int_states_ref')
                    ->toArray();

                $newStates = json_decode($request->input('serviceState'), true);

                // Determine states to add
                $statesToAdd = array_diff($newStates, $existingStates);

                // Determine states to delete
                $statesToDelete = array_diff($existingStates, $newStates);

                // Add new states
                foreach ($statesToAdd as $state) {
                    $stateTable = new CpServicesState();
                    $stateTable->css_int_services_ref = $service->cps_int_ref;
                    $stateTable->css_int_states_ref = $state;
                    $stateTable->save();
                }

                // Delete removed states
                CpServicesState::where('css_int_services_ref', $service->cps_int_ref)
                    ->whereIn('css_int_states_ref', $statesToDelete)
                    ->delete();

                DB::commit();

                if ($updateService) {
                    $service = CompetentPersonService::join('service_sub_list', 'competent_person_services.cps_int_service_ref', '=', 'service_sub_list.ssl_int_ref')
                        ->join('service_main', 'service_sub_list.ssl_int_servicemain_ref', '=', 'service_main.sm_int_ref')
                        ->join('cp_certificate', 'competent_person_services.cps_certification_ref', '=', 'cp_certificate.cc_int_ref')
                        ->where('cps_int_ref', $request->input('cpsID'))
                        ->select(
                            'competent_person_services.*',
                            'service_sub_list.*',
                            'service_main.*',
                            'cp_certificate.*',
                        )->first();
                    return $this->sendResponse(message: 'Updated Successfully', result: $service);
                } else {
                    DB::rollBack();
                    return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
                }
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            DB::rollBack();
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    //! FIXME: Add Foreign Key for cp_services_state table in database
    public function deleteServiceDetails($id, $cpsID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $deleteCertificate = CompetentPersonService::find($cpsID);

                if ($deleteCertificate) {

                    if ($deleteCertificate['cps_int_user_ref'] == $id) {
                        $deleteCertificate->delete();
                        return $this->sendResponse(message: 'Service Deleted Successfully');
                    }

                    return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
                }

                return $this->sendError(errorMEssage: 'Service Not Found', code: 404);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
    }
}

// public function getMyServiceDetailsList(Request $request)
//     {
//         // $data = $request->all();
//         try {
//             $service = CompetentPersonService::join('service_sub_list', 'competent_person_services.cps_int_service_ref', '=', 'service_sub_list.ssl_int_ref')
//                 ->join('service_main', 'service_sub_list.ssl_int_servicemain_ref', '=', 'service_main.sm_int_ref')
//                 ->join('cp_certificate', 'competent_person_services.cps_certification_ref', '=', 'cp_certificate.cc_int_ref')
//                 ->join('cp_services_state', 'competent_person_services.cps_int_service_ref', '=', 'cp_services_state.css_int_services_ref')
//                 ->where('cps_int_user_ref', $request->input('cpID'))
//                 ->select(
//                     'competent_person_services.*',
//                     'service_sub_list.ssl_int_ref',
//                     'service_sub_list.ssl_var_subservice_name',
//                     'service_sub_list.ssl_var_img_path',
//                     'service_main.sm_int_ref',
//                     'service_main.sm_var_name',
//                     'service_main.sm_var_img_path',
//                     'cp_certificate.cc_var_registration_no',
//                     'cp_services_state.css_int_states_ref'
//                 )->get();

//             // $userServices =  DB::select("
//             //     SELECT
//             //         cps.cps_int_ref,
//             //         cps.cps_int_user_ref,
//             //         cps.cps_int_service_ref,
//             //         cps.cps_certification_ref,
//             //         cps.cps_txt_description,
//             //         cps.cps_var_starting_price,
//             //         cps.cps_int_status,
//             //         cps.cps_ts_created_at,
//             //         cps.cps_ts_updated_at,
//             //         GROUP_CONCAT(css.css_int_states_ref) AS css_int_states_ref
//             //     FROM competent_person_services cps
//             //     JOIN cp_services_state css ON cps.cps_int_ref = css.css_int_services_ref
//             //     WHERE cps.cps_int_user_ref = 7
//             //     GROUP BY cps.cps_int_ref, cps.cps_int_user_ref, cps.cps_int_service_ref, cps.cps_certification_ref, cps.cps_txt_description, cps.cps_var_starting_price, cps.cps_int_status, cps.cps_ts_created_at, cps.cps_ts_updated_at,
//             // ");


//             return $this->sendResponse('Service Details', '', $service);
//         } catch (Exception $e) {
//             return $this->sendError('Error : ' . $e, 500);
//         }
//     }

//     public function updateServiceDetails(Request $request)
//     {
//         // $data = $request->all();
//         try {

//             CompetentPersonService::where('cps_int_ref', $request->input('cpsID'))->update(
//                 array(
//                     'cps_int_service_ref' => $request->input('subServiceID'),
//                     'cps_certification_ref ' => $request->input('certificateID'),
//                     'cps_txt_description' => $request->input('serviceDesc'),
//                     'cps_var_starting_price' => $request->input('startingPrice'),
//                     'cps_int_publish_status' => $request->input('publishStatus'),
//                     'cps_int_status' => 0
//                 )
//             );


//             return $this->sendResponse('Updated Successfully', '');
//         } catch (Exception $e) {
//             return $this->sendError('Error : ' . $e, 500);
//         }
//     }

//     public function deleteServiceDetails(Request $request)
//     {
//         try {
//             CompetentPersonService::where('cps_int_ref', $request->input('cpsID'))->delete();
//             return $this->sendResponse('Service Deleted Successfully', '');
//         } catch (Exception $e) {
//             return $this->sendError('Error : ' . $e, 500);
//         }
//     }
