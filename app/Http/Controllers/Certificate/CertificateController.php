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
use DateTime;
use Exception;
use Illuminate\Support\Facades\Date;
use Nette\Schema\Expect;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\Log;

class CertificateController extends BaseController
{


    public function getCertificatesDetailByID(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $limit = $request->input('limit');
                $certificates = CpCertificate::join('competent_person_types', 'cp_certificate.cc_int_cpt_ref', '=', 'competent_person_types.cpt_int_ref')
                    ->where('cc_int_user_ref', $id)
                    ->select(
                        'cp_certificate.*',
                        'competent_person_types.*',
                    )->orderBy('cc_ts_created_at', 'desc')->paginate($limit);

                if ($certificates->isEmpty()) {
                    return $this->sendResponse(message: 'No certificate found.', code: 404);
                }

                return $this->sendResponse(message: 'Get Certificate Details', result: $certificates);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e, code: 500);
        }
    }

    public function addCertificateDetail(Request $request)
    {
        try {

            $validator = validator::make($request->all(), [
                'userID' => 'required|integer',
                'cptID' => 'required|integer',
                'certRegistrationNo' => 'required|string|max:255',
                'certExpiryDate' => 'sometimes|date',
                'certificateFile' => 'required|mimes:pdf|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: $validator->errors(), code: 400);
            }

            $certificates = CpCertificate::where('cc_int_user_ref', $request->input('userID'))
            ->where('cc_var_registration_no', $request->input('certRegistrationNo'))->first();

            if($certificates){
                return $this->sendError(errorMEssage: 'Certificate Already Exist', code: 400);
            }

            $fileURL = $this->uploadFile($request->file('certificateFile'));

            if (empty($fileURL)) {
                return $this->sendError(errorMEssage: 'File Upload Error', code: 400);
            }

            $cpCetificate = CpCertificate::create([
                'cc_int_user_ref' => $request->input('userID'),
                'cc_int_cpt_ref' => $request->input('cptID'),
                'cc_var_registration_no' => $request->input('certRegistrationNo'),
                'cc_date_expiry_date' => $request->input('certExpiryDate'), //! FIXME: Add the default null in database (Prod)
                'cc_var_path_document' => $fileURL,
                'cc_int_status' => 0
            ]);

            if($cpCetificate){
                $cert = CpCertificate::join('competent_person_types', 'cp_certificate.cc_int_cpt_ref', '=', 'competent_person_types.cpt_int_ref')
                    ->where('cc_int_ref', $cpCetificate->cc_int_ref)
                    ->select(
                        'cp_certificate.*',
                        'competent_person_types.*',
                    )->first();
                return $this->sendResponse(message: 'Save Certificate Successfully', result: $cert);
            }else{
                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }

        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function updateCertificateDetail(Request $request, $id)
    {
        try {
            if($this->isAuthorizedUser($id)){
                $validator = Validator::make($request->all(), [
                    'ccID' => 'required|integer',
                    'cptID' => 'required|integer',
                    'certRegistrationNo' => 'required|string|max:255',
                    'certExpiryDate' => 'sometimes|date',
                    'certificateFile' => 'sometimes|mimes:pdf|max:2048'
                ]);

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: $validator->errors() , code: 400);
                }

                $certificate = CpCertificate::find($request->input('ccID'));

                if($certificate->cc_int_user_ref != $id){
                    return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
                }

                $updateData = [
                    'cc_int_cpt_ref' => $request->input('cptID'),
                    'cc_var_registration_no' => $request->input('certRegistrationNo'),
                    'cc_date_expiry_date' => $request->input('certExpiryDate'),
                    'cc_int_status' => 0
                ];

                if ($request->hasFile('certificateFile')) {
                    $fileURL = $this->uploadFile($request->file('certificateFile'));

                    if (empty($fileURL)) {
                        return $this->sendError(errorMEssage: 'File Upload Error', code: 400);
                    }
                    $updateData['cc_var_path_document'] = $fileURL;
                }

                CpCertificate::where('cc_int_ref', $request->input('ccID'))->update($updateData);
                $updateCertificate = CpCertificate::join('competent_person_types', 'cp_certificate.cc_int_cpt_ref', '=', 'competent_person_types.cpt_int_ref')
                ->where('cc_int_ref', $request->input('ccID'))
                ->select(
                    'cp_certificate.*',
                    'competent_person_types.*',
                )->first();

                return $this->sendResponse(message: 'Updated Successfully', result: $updateCertificate);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);

        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function deleteCertificateDetailByID($id, $ccID)
    {
        try {
            if($this->isAuthorizedUser($id)){
                $deleteCertificate = CpCertificate::find($ccID);

                if($deleteCertificate){
                    if($deleteCertificate['cc_int_user_ref'] == $id){
                        $deleteCertificate->delete();
                        return $this->sendResponse(message: 'Certificate Deleted Successfully');
                    }

                    return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
                }

                return $this->sendError(errorMEssage: 'Certificate Not Found', code: 404);

            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);

        } catch (Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e, code: 500);
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


// public function addCertificateDetail(Request $request)
//     {
//         try {

//             $validator = validator::make($request->all(), [
//                 'id' => 'required',
//                 'cptID' => 'required',
//                 'certRegistrationNo' => 'required',
//                 'certExpiryDate' => 'required',
//                 'certificateFile' => 'required|mimes:pdf|max:2048',
//             ]);

//             if($validator->fails()){
//                 return $this->sendError(errorMEssage: 'Invalid Input', code: 400);
//             }

//             $fileURL = $this->uploadFile($request->file('certificateFile'));

//             if($fileURL->isEmpty()){
//                 return $this->sendError(errorMEssage: 'File Upload Error', code: 500);
//             }


//             if ($request->hasFile('certificatePDF') && $request->file('certificatePDF')->isValid()) {
//                 $file = $request->file('certificatePDF');

//                 try {
//                     // Specify the destination folder outside of the Laravel public directory
//                     $destinationPath = 'myhse/uploads/certificates';

//                     // Generate a unique filename
//                     $fileName = time() . '_' . $file->getClientOriginalName();

//                     // Move the uploaded file to the destination folder
//                     $file->move($destinationPath, $fileName);

//                     // Get the full path of the uploaded file
//                     $filePath = $destinationPath . '/' . $fileName;
//                 } catch (\Throwable $th) {

//                     return $this->sendResponse("Error: " . $th->getMessage(), '', 500);
//                 }
//             } else {

//                 return $this->sendError('Invalid file or file upload failed.', '');
//             }

//             $ccResult = new CpCertificate(
//                 [
//                     'cc_int_user_ref' => $request->input('id'),
//                     'cc_int_cpt_ref' => $request->input('cptID'),
//                     'cc_var_registration_no' => $request->input('certRegistrationNo'),
//                     'cc_date_expiry_date' => $request->input('certExpiryDate'),
//                     'cc_var_path_document' => $filePath,
//                     'cc_int_status' => 0
//                 ]
//             );


//             $ccResult->save();


//             return $this->sendResponse('Added Successfully', '', $ccResult);

//         } catch (Exception $e) {

//             return $this->sendError('Error : ' . $e->getMessage(), 500);

//         }
//     }
