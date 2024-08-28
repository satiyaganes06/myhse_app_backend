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
                $limit = $request->input('limit'); // limit must
                $status = $request->input('status') ?? null;

                if ($status != null) {
                    $certificates = CpCertificate::where('cc_int_user_ref', $id)
                        ->where('cc_int_status', $status)
                        ->orderBy('cc_ts_created_at', 'desc')->get();
                } else {

                    $certificates = CpCertificate::where('cc_int_user_ref', $id)->orderBy('cc_ts_created_at', 'desc')->paginate($limit);
                }

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

    public function getCpCertificatesDetails(Request $request, $id, $cpID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $status = $request->input('status');


                $certificates = CpCertificate::where('cc_int_user_ref', $cpID)
                    ->where('cc_int_status', $status)
                    ->orderBy('cc_ts_created_at', 'desc')->get();


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
            // Start Template
            $validator = validator::make($request->all(), [
                'userID' => 'required|integer',
                'title' => 'required|string|max:255',
                'description' => 'required|string|max:255',
                'certRegistrationNo' => 'required|string|max:255',
                'certExpiryDate' => 'sometimes|date',
                'certificateImage' => 'required|max:2048',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: $validator->errors(), code: 400);
            }

            // End Template

            $certificates = CpCertificate::where('cc_int_user_ref', $request->input('userID'))
                ->where('cc_var_registration_no', $request->input('certRegistrationNo'))->first();

            if ($certificates) {
                return $this->sendError(errorMEssage: 'Certificate Already Exist', code: 400);
            }

            $fileURL = $this->uploadMedia($request->file('certificateImage'), 2);

            if (empty($fileURL)) {
                return $this->sendError(errorMEssage: 'Image Upload Error', code: 400);
            }

            $cpCetificate = CpCertificate::create([
                'cc_int_user_ref' => $request->input('userID'),
                'cc_var_title' => $request->input('title'),
                'cc_var_description' => $request->input('description'),
                'cc_var_registration_no' => $request->input('certRegistrationNo'),
                'cc_date_expiry_date' => $request->input('certExpiryDate'),
                'cc_var_path_document' => $fileURL,
                'cc_int_status' => 1
            ]);

            // Start Template
            if ($cpCetificate) {
                $cert = CpCertificate::where('cc_int_ref', $cpCetificate->cc_int_ref)->first();
                return $this->sendResponse(message: 'Save Certificate Successfully', result: $cert);
            } else {
                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }

            // End Template
        } catch (Exception $e) {

            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }

    public function updateCertificateDetail(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $validator = Validator::make($request->all(), [
                    'ccID' => 'required|integer',
                    'title' => 'required|string|max:255',
                    'description' => 'required|string|max:255',
                    'certRegistrationNo' => 'required|string|max:255',
                    'certExpiryDate' => 'sometimes|date',
                    'certificateImage' => 'sometimes|max:2048'
                ]);

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: $validator->errors(), code: 400);
                }

                $certificate = CpCertificate::find($request->input('ccID'));

                if ($certificate->cc_int_user_ref != $id) {
                    return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
                }

                $updateData = [
                    'cc_var_title' => $request->input('title'),
                    'cc_var_description' => $request->input('description'),
                    'cc_var_registration_no' => $request->input('certRegistrationNo'),
                    'cc_int_status' => 0
                ];

                if ($request->has('certExpiryDate')) {
                    $updateData['cc_date_expiry_date'] = $request->input('certExpiryDate');
                }

                if ($request->hasFile('certificateImage')) {
                    $fileURL = $this->uploadMedia($request->file('certificateImage'), 2);

                    if (empty($fileURL)) {
                        return $this->sendError(errorMEssage: 'Image Upload Error', code: 400);
                    }
                    $updateData['cc_var_path_document'] = $fileURL;
                }

                CpCertificate::where('cc_int_ref', $request->input('ccID'))->update($updateData);
                $updateCertificate = CpCertificate::where('cc_int_ref', $request->input('ccID'))->first();

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
            if ($this->isAuthorizedUser($id)) {
                $deleteCertificate = CpCertificate::find($ccID);

                if ($deleteCertificate) {
                    if ($deleteCertificate['cc_int_user_ref'] == $id) {
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
