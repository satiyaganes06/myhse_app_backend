<?php

namespace App\Http\Controllers\Post;

use App\Http\Controllers\Base\BaseController;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Post\CpPost;
use App\Models\Post\CpPostLink;
use App\Models\User\UserProfile;
use App\Models\Services\ServiceMainRef;
use Illuminate\Support\Facades\Validator;

class PostController extends BaseController
{

    public function getAllPostDetails(Request $request, $id)
    {
        try {

            if ($this->isAuthorizedUser($id)) {
                $limit = $request->input('limit') ?? 10;

                $posteInfos = CpPost::join('user_profile', 'cp_post.cpp_int_user_ref', '=', 'user_profile.up_int_ref')
                    ->where('cpp_int_status', 1)
                    ->where('cpp_int_user_ref', '!=', $id)
                    ->paginate($limit);

                if ($posteInfos->isEmpty()) {
                    return $this->sendResponse(message: 'No posts found.', code: 404);
                }

                return $this->sendResponse(message: 'Get All Post Details', result: $posteInfos);
            }

            return $this->sendError('Unauthorized User', 401);
        } catch (\Throwable $th) {
            return $this->sendError('Error : ' . $th->getMessage(), 500);
        }
    }

    public function getCpPostDetails(Request $request, $id)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $limit = $request->input('limit');

                $posteInfos = CpPost::join('service_main_ref', 'cp_post.cpp_int_service_main_ref', '=', 'service_main_ref.smr_int_ref')
                    ->where('cpp_int_user_ref', $id)->paginate($limit);

                if ($posteInfos->isEmpty()) {
                    return $this->sendResponse(message: 'No posts found.', code: 404);
                }

                return $this->sendResponse(message: 'Get My Post Details', result: $posteInfos);
            }

            return $this->sendError('Unauthorized User', 401);
        } catch (\Throwable $th) {
            return $this->sendError('Error : ' . $th->getMessage(), 500);
        }
    }

    public function addPostDetail(Request $request)
    {
        try {
            $validator = validator::make($request->all(), [
                'userID' => 'required|integer',
                'description' => 'required|string|max:255',
                'serviceCategory' => 'required|integer',
                'postImage' => 'required|max:10000' // 10mb
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Invalid input' . $validator->errors()->first(), code: 400);
            }

            $fileURL = $this->uploadMedia($request->file('postImage'), 1);

            if (empty($fileURL)) {
                return $this->sendError(errorMEssage: 'Image Upload Error', code: 400);
            }

            $cpPost = CpPost::create([
                'cpp_int_user_ref' => $request->input('userID'),
                'cpp_txt_desc' => $request->input('description'),
                'cpp_var_image' => $fileURL,
                'cpp_int_service_main_ref' => $request->input('serviceCategory'),
                'cpp_int_status' => 0
            ]);

            if ($cpPost) {
                $cert = CpPost::join('service_main_ref', 'cp_post.cpp_int_service_main_ref', '=', 'service_main_ref.smr_int_ref')->where('cpp_int_ref', $cpPost->cpp_int_ref)->first();
                return $this->sendResponse(message: 'Save Post Successfully', result: $cert);
            } else {
                return $this->sendError(errorMEssage: 'Something went wrong', code: 500);
            }
        } catch (\Throwable $th) {
            return $this->sendError(errorMEssage: 'Error : ' . $th->getMessage(), code: 500);
        }
    }

    public function updatePostDetail(Request $request, $id)
    {
        try {
            $validator = validator::make($request->all(), [
                'postID' => 'required|integer',
                'description' => 'required|string|max:255',
                'serviceCategory' => 'required|integer',
                'postImage' => 'sometimes|max:10000', // 10mb
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Invalid input', code: 400);
            }

            $cpPost = CpPost::where('cpp_int_ref', $request->input('postID'))->first();

            if ($cpPost->cpp_int_user_ref != $id) {
                return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
            }

            $updateData = [
                'cpp_txt_desc' => $request->input('description'),
                'cpp_int_service_main_ref' => $request->input('serviceCategory'),
                'cpp_int_status' => 0
            ];

            if ($request->hasFile('postImage')) {
                $fileURL = $this->uploadMedia($request->file('postImage'), 1);

                if (empty($fileURL)) {
                    return $this->sendError(errorMEssage: 'Image Upload Error', code: 400);
                }
                $updateData['cpp_var_image'] = $fileURL;
            }

            CpPost::where('cpp_int_ref', $request->input('postID'))->update($updateData);
            $updatePost = CpPost::join('service_main_ref', 'cp_post.cpp_int_service_main_ref', '=', 'service_main_ref.smr_int_ref')->where('cpp_int_ref', $request->input('postID'))->first();

            return $this->sendResponse(message: 'Updated Successfully', result: $updatePost);
        } catch (\Throwable $th) {
            return $this->sendError(errorMEssage: 'Error : ' . $th->getMessage(), code: 500);
        }
    }

    public function deletePostDetail($id, $postID)
    {
        try {
            if ($this->isAuthorizedUser($id)) {
                $deletePost = CpPost::find($postID);

                if ($deletePost) {
                    if ($deletePost['cpp_int_user_ref'] == $id) {
                        $deletePost->delete();
                        return $this->sendResponse(message: 'Post Deleted Successfully');
                    }

                    return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
                }

                return $this->sendError(errorMEssage: 'Post Not Found', code: 404);
            }

            return $this->sendError(errorMEssage: 'Unauthorized Request', code: 401);
        } catch (\Throwable $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e, code: 500);
        }
    }
}
