<?php

namespace App\Http\Controllers\Certificate;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Booking\BookingMain;
use App\Models\Booking\BookingRequest;
use App\Models\Certificate\CpCertificate;
use App\Models\Services\CompetentPersonService;
use App\Models\Services\CpServicesState;
use Illuminate\Support\Facades\Validator;
use App\Models\UserLogin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use DateTime;
use Exception;
use Illuminate\Support\Facades\Date;
use Nette\Schema\Expect;
use Symfony\Component\Console\Input\Input;

class CertificateController extends BaseController
{


    public function addCertificateDetails(Request $request)
    {
        try {


            if ($request->hasFile('certificatePDF') && $request->file('certificatePDF')->isValid()) {
                $file = $request->file('certificatePDF');

                try {
                    // Specify the destination folder outside of the Laravel public directory
                    $destinationPath = 'myhse/uploads/certificates';

                    // Generate a unique filename
                    $fileName = time() . '_' . $file->getClientOriginalName();

                    // Move the uploaded file to the destination folder
                    $file->move($destinationPath, $fileName);

                    // Get the full path of the uploaded file
                    $filePath = $destinationPath . '/' . $fileName;
                } catch (\Throwable $th) {

                    return $this->sendResponse("Error: " . $th->getMessage(), '', 500);
                }
            } else {

                return $this->sendError('Invalid file or file upload failed.', '');
            }

            $ccResult = new CpCertificate(
                [
                    'cc_int_user_ref ' => $request->input('cpID'),
                    'cc_int_cpt_ref ' => $request->input('cptID'),
                    'cc_var_registration_no' =>$request->input('certRegistrationNo'),
                    'cc_date_expiry_date' => $request->input('certExpiryDate'),
                    'cc_var_path_document' => $filePath,
                    'cc_int_status' => 0
                ]
            );
           

            $ccResult->save();


            return $this->sendResponse('Added Successfully', '', $ccResult);
        } catch (Exception $e) {
            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }


    public function getMyCertificateDetailsList(Request $request)
    {
        // $data = $request->all();
        try {
            $certificate = CpCertificate::join('competent_person_types', 'cp_certificate.cc_int_cpt_ref', '=', 'competent_person_types.cpt_int_ref')
                ->where('cc_int_user_ref', $request->input('cpID'))
                ->select(
                    'competent_person_types.*',
                    'cp_certificate.*',
                )->get();


            return $this->sendResponse('Certificate Details', '', $certificate, 200);
        } catch (Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
    }
}
// $certificate = CompetentPersonService::join('service_main', 'competent_person_services.cps_int_service_ref', '=', 'service_main.sm_int_ref')
//                 ->join('cp_certificate', 'competent_person_services.cps_certification_ref', '=', 'cp_certificate.cc_int_ref')
//                 ->join('service_sub_list', 'service_main.sm_int_ref', '=', 'service_sub_list.ssl_int_servicemain_ref')
//                 ->join('competent_person_types', 'cp_certificate.cc_int_cpt_ref', '=', 'competent_person_types.cpt_int_ref')
//                 ->where('cc_int_user_ref', $request->input('cpID'))
//                 ->select(
//                     'competent_person_services.*',
//                     'cp_certificate.*',
//                     'competent_person_types.cpt_var_short_name',
//                     'competent_person_types.cpt_var_long_name',
//                     'service_sub_list.ssl_var_subservice_name',
//                     'service_sub_list.ssl_var_img_path',
//                     'service_main.sm_var_name',
//                     'service_main.sm_var_img_path'
//                 )->get();