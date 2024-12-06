<?php

namespace App\Http\Controllers\Service;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Models\Certificate\CpCertLink;
use App\Models\Post\CpPostLink;
use App\Models\Services\CategoryMain;
use App\Models\Services\CpService;
use App\Models\Services\ServiceMainRef;
use App\Models\Tag\CpTag;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ServiceController extends BaseController
{
    public function getCategoryMainList()
    {
        try {
            $categoryMainList = CategoryMain::where('cm_int_status', true)->get();

            if ($categoryMainList->isEmpty()) {
                return $this->sendError(errorMEssage: 'No category found', code: 404);
            }

            return $this->sendResponse(message: 'Get Category Main List', result: $categoryMainList);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : '.$e, code: 500);
        }
    }

    public function getServiceMainList(Request $request)
    {
        try {
            $categoryID = $request->input('categoryID');

            $serviceMainList = ServiceMainRef::where('smr_int_category_ref', $categoryID)->get();

            if ($serviceMainList->isEmpty()) {
                return $this->sendError(errorMEssage: 'No service found', code: 404);
            }

            return $this->sendResponse(message: 'Get Service Main List', result: $serviceMainList);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : '.$e, code: 500);
        }
    }

    public function getRelatedCertificate($id, $serviceID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $certificate = CpCertLink::join('cp_certificate', 'cp_cert_link.cpcl_int_cc_ref', '=', 'cp_certificate.cc_int_ref')
                    ->where('cpcl_int_cps_ref', $serviceID)
                    ->select('cp_certificate.*')
                    ->get();

                if ($certificate->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No certificate found', code: 404);
                }

                return $this->sendResponse(message: 'Get Certificate List', result: $certificate);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : '.$e, code: 500);
        }
    }

    public function getRelatedPost($id, $serviceID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $certificate = CpPostLink::join('cp_post', 'cp_post_link.cppl_int_cpp_ref', '=', 'cp_post.cpp_int_ref')
                    ->where('cppl_int_cps_ref', $serviceID)
                    ->select('cp_post.*')
                    ->get();

                if ($certificate->isEmpty()) {
                    return $this->sendError(errorMEssage: 'No post found', code: 404);
                }

                return $this->sendResponse(message: 'Get Post List', result: $certificate);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : '.$e, code: 500);
        }
    }

    public function getServicesDetailByID(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $limit = $request->input('limit');

                $services = CpService::join('service_main_ref', 'cp_service.cps_int_service_ref', '=', 'service_main_ref.smr_int_ref')
                    ->where('cps_int_user_ref', $id)->orderBy('cps_ts_created_at', 'desc')->paginate($limit);

                if ($services->count() === 0) {
                    return $this->sendError(errorMEssage: 'No service found', code: 404);
                }

                return $this->sendResponse(message: 'Get Service Details', result: $services);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError('Error : '.$e, 500);
        }
    }

    public function getCpServicesDetails(Request $request, $id, $cpID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $status = $request->input('status');

                $services = CpService::join('service_main_ref', 'cp_service.cps_int_service_ref', '=', 'service_main_ref.smr_int_ref')
                    ->where('cps_int_user_ref', $cpID)
                    ->where('cps_int_status', $status)
                    ->orderBy('cps_ts_created_at', 'desc')->get();

                if ($services->count() === 0) {
                    return $this->sendError(errorMEssage: 'No service found', code: 404);
                }

                return $this->sendResponse(message: 'Get Service Details', result: $services);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError('Error : '.$e, 500);
        }
    }

    public function getServiceByID($id, $serviceID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $service = CpService::join('service_main_ref', 'cp_service.cps_int_service_ref', '=', 'service_main_ref.smr_int_ref')
                    ->find($serviceID);

                if ($service == null) {
                    return $this->sendError(errorMEssage: 'No service found', code: 404);
                }

                return $this->sendResponse(message: 'Get Service Details', result: $service);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            return $this->sendError('Error : '.$e, 500);
        }
    }

    public function getAllService(Request $request, $id)
    {
        try {
            $limit = $request->input('limit');
            $category = $request->input('category') ?? null;

            if ($category != null) {
                $services = CpService::join('service_main_ref', 'cp_service.cps_int_service_ref', '=', 'service_main_ref.smr_int_ref')
                    ->where('cps_int_user_ref', '!=', $id)
                    ->where('cps_int_service_ref', $category)
                    ->where('cps_int_status', 1)
                    ->orderBy('cps_ts_created_at', 'desc')->paginate($limit);
            } else {
                $services = CpService::join('service_main_ref', 'cp_service.cps_int_service_ref', '=', 'service_main_ref.smr_int_ref')
                    ->where('cps_int_user_ref', '!=', $id)
                    ->where('cps_int_status', 1)
                    ->orderBy('cps_ts_created_at', 'desc')->paginate($limit);
            }

            if ($services->isEmpty()) {
                return $this->sendError(errorMEssage: 'No service found', code: 404);
            }

            return $this->sendResponse(message: 'Get Service Details', result: $services);
        } catch (Exception $e) {
            return $this->sendError('Error : '.$e, 500);
        }
    }

    public function getSearchServiceResult(Request $request, $id)
    {
        try {
            $limit = $request->input('limit');
            $query = $request->input('query');

            $services = CpService::join('service_main_ref', 'cp_service.cps_int_service_ref', '=', 'service_main_ref.smr_int_ref')
                ->where('cps_int_user_ref', '!=', $id)
                ->where('cps_int_status', 1)
                ->where('cps_var_title', 'like', '%'.$query.'%')
                ->orderBy('cps_ts_created_at', 'desc')->paginate($limit);

            if ($services->isEmpty()) {
                return $this->sendError(errorMEssage: 'No service found', code: 404);
            }

            return $this->sendResponse(message: 'Get Service Details', result: $services);
        } catch (Exception $e) {
            return $this->sendError('Error : '.$e, 500);
        }
    }

    public function addServiceDetail(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'userID' => 'required|integer',
                'serviceMainRefID' => 'required|integer',
                'serviceTitle' => 'required|string',
                'serviceDescription' => 'required|string',
                'startingPrice' => 'required|string',
                'estimateDeliveryTime' => 'required|integer',
                'serviceImage' => 'required|max:10000',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Invalid Input'.$validator->errors(), code: 400);
            }

            $fileURL = $this->uploadMedia($request->file('serviceImage'), 3);

            if (empty($fileURL)) {
                return $this->sendError(errorMEssage: 'Image Upload Error', code: 400);
            }

            DB::beginTransaction();

            $service = new CpService();
            $service->cps_int_user_ref = $request->input('userID');
            $service->cps_int_service_ref = $request->input('serviceMainRefID');
            $service->cps_var_image = $fileURL;
            $service->cps_var_title = $request->input('serviceTitle');
            $service->cps_txt_description = $request->input('serviceDescription');
            $service->cps_var_starting_price = $request->input('startingPrice');
            $service->cps_estimate_delivery_time = $request->input('estimateDeliveryTime');
            $service->cps_fl_average_rating = 0;
            $service->cps_int_status = 0;
            $service->save();

            // Store the certificate one by one
            $certificates = json_decode($request->input('serviceCertificates'), true);
            foreach ($certificates as $certificate) {
                $certLink = new CpCertLink();
                $certLink->cpcl_int_cps_ref = $service->cps_int_ref;
                $certLink->cpcl_int_cc_ref = $certificate;
                $certLink->save();
            }

            // Store the tag one by one
            $tags = json_decode($request->input('serviceTags'), true);
            foreach ($tags as $tag) {
                $tagLink = new CpTag();
                $tagLink->cpst_int_cps_ref = $service->cps_int_ref;
                $tagLink->cpst_int_tag_ref = $tag;
                $tagLink->save();
            }

            // Store the post one by one
            // $posts = json_decode($request->input('servicePosts'), true);
            // foreach ($posts as $post) {
            //     $postLink = new CpPostLink();
            //     $postLink->cppl_int_cps_ref = $service->cps_int_ref;
            //     $postLink->cppl_int_cpp_ref = $post;
            //     $postLink->save();
            // }

            // Store the state one by one
            // $states = json_decode($request->input('serviceState'), true);
            // foreach ($states as $state) {
            //     $stateTable = new CpServicesState();
            //     $stateTable->css_int_services_ref = $service->cps_int_ref;
            //     $stateTable->css_int_states_ref = $state;
            //     $stateTable->save();
            // }

            DB::commit();

            $getService = CpService::with(['tags'])
                ->find($service->cps_int_ref);

            return $this->sendResponse(message: 'Saved Service Successfully', result: $getService);
        } catch (Exception $e) {
            // DB::rollBack();
            return $this->sendError('Error : '.$e, 500);
        }
    }

    public function updateServiceDetail(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $validator = Validator::make($request->all(), [
                    'cpsID' => 'required|integer',
                    'serviceMainRefID' => 'required|integer',
                    'serviceTitle' => 'required|string',
                    'serviceDescription' => 'required|string',
                    'startingPrice' => 'required|string',
                    'estimateDeliveryTime' => 'required|integer',
                    'serviceImage' => 'sometimes|max:10000',
                ]);

                if ($validator->fails()) {
                    return $this->sendError(errorMEssage: 'Invalid Input'.$validator->errors(), code: 400);
                }

                $service = CpService::find($request->input('cpsID'));

                if ($service->cps_int_user_ref != $id) {
                    return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
                }

                $updateData = [
                    'cps_int_service_ref' => $request->input('serviceMainRefID'),
                    'cps_var_title' => $request->input('serviceTitle'),
                    'cps_txt_description' => $request->input('serviceDescription'),
                    'cps_var_starting_price' => $request->input('startingPrice'),
                    'cps_estimate_delivery_time' => $request->input('estimateDeliveryTime'),
                    'cps_int_status' => 0,
                ];

                if ($request->hasFile('serviceImage')) {
                    $fileURL = $this->uploadMedia($request->file('serviceImage'), 3);

                    if (empty($fileURL)) {
                        return $this->sendError(errorMEssage: 'Image Upload Error', code: 400);
                    }
                    $updateData['cps_var_image'] = $fileURL;
                }

                DB::beginTransaction();

                $updateService = CpService::where('cps_int_ref', $request->input('cpsID'))->update($updateData);

                // Certificate
                $existingCerts = CpCertLink::where('cpcl_int_cps_ref', $service->cps_int_ref)
                    ->pluck('cpcl_int_cc_ref')
                    ->toArray();

                $newCerts = json_decode($request->input('serviceCertificates'), true);

                // Determine certificate to add
                $certsToAdd = array_diff($newCerts, $existingCerts);

                // Determine certificate to delete
                $certsToDelete = array_diff($existingCerts, $newCerts);

                // Add new certificates
                foreach ($certsToAdd as $cert) {
                    $certificateLinkTable = new CpCertLink();
                    $certificateLinkTable->cpcl_int_cps_ref = $service->cps_int_ref;
                    $certificateLinkTable->cpcl_int_cc_ref = $cert;
                    $certificateLinkTable->save();
                }

                // Delete removed certificates
                CpCertLink::where('cpcl_int_cps_ref', $service->cps_int_ref)
                    ->whereIn('cpcl_int_cc_ref', $certsToDelete)
                    ->delete();

                // Post
                $existingPosts = CpPostLink::where('cppl_int_cps_ref', $service->cps_int_ref)
                    ->pluck('cppl_int_cpp_ref')
                    ->toArray();

                $newPosts = json_decode($request->input('servicePosts'), true);

                // Determine post to add
                $postsToAdd = array_diff($newPosts, $existingPosts);

                // Determine post to delete
                $postsToDelete = array_diff($existingPosts, $newPosts);

                // Add new posts
                foreach ($postsToAdd as $post) {
                    $postLinkTable = new CpPostLink();
                    $postLinkTable->cppl_int_cps_ref = $service->cps_int_ref;
                    $postLinkTable->cppl_int_cpp_ref = $post;
                    $postLinkTable->save();
                }

                // Delete removed posts
                CpPostLink::where('cppl_int_cps_ref', $service->cps_int_ref)
                    ->whereIn('cppl_int_cpp_ref', $postsToDelete)
                    ->delete();

                DB::commit();

                if ($updateService) {
                    return $this->sendResponse(message: 'Updated Successfully');
                } else {
                    //DB::rollBack();
                    return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
                }
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (Exception $e) {
            DB::rollBack();

            return $this->sendError(errorMEssage: 'Error : '.$e->getMessage(), code: 500);
        }
    }

    //! FIXME: Add Foreign Key for links table in database
    public function deleteServiceDetails($id, $cpsID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $deleteCertificate = CpService::find($cpsID);

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
            return $this->sendError('Error : '.$e, 500);
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
