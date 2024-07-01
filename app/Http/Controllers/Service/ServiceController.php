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

use Exception;
use Nette\Schema\Expect;
use Symfony\Component\Console\Input\Input;

class ServiceController extends BaseController
{


    public function addServiceDetails(Request $request)
    {
        $data = $request->all();
        try {

            DB::beginTransaction();

            $service = new CompetentPersonService();
            $service->cps_int_user_ref = $request->input('cpID');
            $service->cps_int_service_ref = $request->input('subServiceID');
            $service->cps_certification_ref = $request->input('certificateID');
            $service->cps_txt_description = $request->input('serviceDesc');
            $service->cps_var_starting_price = $request->input('startingPrice');
            $service->cps_int_status = 0;
            $service->save();

            $states = json_decode($data['serviceState'], true);
            foreach ($states as $state) {
                $stateTable = new CpServicesState();
                $stateTable->css_int_services_ref = $data['subServiceID'];
                $stateTable->css_int_states_ref = $state;
                $stateTable->save();
            }

            DB::commit();

            return $this->sendResponse('Service Added Successfully', '');
        } catch (Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
    }

    public function getMyServiceDetailsList(Request $request)
    {
        // $data = $request->all();
        try {
            $service = CompetentPersonService::join('service_sub_list', 'competent_person_services.cps_int_service_ref', '=', 'service_sub_list.ssl_int_ref')
                ->join('service_main', 'service_sub_list.ssl_int_servicemain_ref', '=', 'service_main.sm_int_ref')
                ->join('cp_certificate', 'competent_person_services.cps_certification_ref', '=', 'cp_certificate.cc_int_ref')
                ->join('cp_services_state', 'competent_person_services.cps_int_service_ref', '=', 'cp_services_state.css_int_services_ref')
                ->where('cps_int_user_ref', $request->input('cpID'))
                ->select(
                    'competent_person_services.*',
                    'service_sub_list.ssl_int_ref',
                    'service_sub_list.ssl_var_subservice_name',
                    'service_sub_list.ssl_var_img_path',
                    'service_main.sm_int_ref',
                    'service_main.sm_var_name',
                    'service_main.sm_var_img_path',
                    'cp_certificate.cc_var_registration_no',
                    'cp_services_state.css_int_states_ref'
                )->get();

            // $userServices =  DB::select("
            //     SELECT 
            //         cps.cps_int_ref,
            //         cps.cps_int_user_ref,
            //         cps.cps_int_service_ref,
            //         cps.cps_certification_ref,
            //         cps.cps_txt_description,
            //         cps.cps_var_starting_price,
            //         cps.cps_int_status,
            //         cps.cps_ts_created_at,
            //         cps.cps_ts_updated_at,
            //         GROUP_CONCAT(css.css_int_states_ref) AS css_int_states_ref
            //     FROM competent_person_services cps
            //     JOIN cp_services_state css ON cps.cps_int_ref = css.css_int_services_ref
            //     WHERE cps.cps_int_user_ref = 7
            //     GROUP BY cps.cps_int_ref, cps.cps_int_user_ref, cps.cps_int_service_ref, cps.cps_certification_ref, cps.cps_txt_description, cps.cps_var_starting_price, cps.cps_int_status, cps.cps_ts_created_at, cps.cps_ts_updated_at,
            // ");


            return $this->sendResponse('Service Details', '', $service);
        } catch (Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
    }

    public function updateServiceDetails(Request $request)
    {
        // $data = $request->all();
        try {

            CompetentPersonService::where('cps_int_ref', $request->input('cpsID'))->update(
                array(
                    'cps_int_service_ref' => $request->input('subServiceID'),
                    'cps_certification_ref ' => $request->input('certificateID'),
                    'cps_txt_description' => $request->input('serviceDesc'),
                    'cps_var_starting_price' => $request->input('startingPrice'),
                    'cps_int_publish_status' => $request->input('publishStatus'),
                    'cps_int_status' => 0
                )
            );


            return $this->sendResponse('Updated Successfully', '');
        } catch (Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
    }

    public function deleteServiceDetails(Request $request)
    {
        try {
            CompetentPersonService::where('cps_int_ref', $request->input('cpsID'))->delete();
            return $this->sendResponse('Service Deleted Successfully', '');
        } catch (Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
    }
}
