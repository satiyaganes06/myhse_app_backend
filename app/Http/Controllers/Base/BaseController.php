<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\User\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

class BaseController extends Controller
{

    public function sendResponse($message, $token = '', $result = '', $code = 200)
    {
        $response = [
            'success' => true,
            'status-code' => $code,
            'message' => $message,
        ];

        if (!empty($result)) {
            $response['data'] = $result;
        }

        if (!empty($token)) {
            $response['token'] = $token;
        }

        // $ul = $this->encode_data($response);
        return response()->json(data: $response, status: $code);
    }

    public function sendError($errorMEssage, $code)
    {
        $response = [
            'success' => false,
            'status-code' => $code,
            'message' => $errorMEssage
        ];

        return response()->json(data: $response, status: $code);
    }

    protected function isAuthorizedUser($id)
    {
        return Auth::user()->ul_int_profile_ref == $id;
    }

    protected function uploadMedia($file, $folder)
    {
        $folderName = ['UserProfileImage', 'PostImage', 'CertificateImage', 'ServiceImage', 'ServiceDocument', 'PaymentReceipt', 'JobResultFile'];

        try {

            $fileName = time() . '_' . $file->getClientOriginalName();

            $path = $file->storeAs('uploads/' . $folderName[$folder] , $fileName);

            //    // $path = $file->store('uploads/images/profile'); // 'pdfs' is the storage folder, you can change it as needed
            // $path = $file->store('uploads/images'); // 'pdfs' is the storage folder, you can change it as needed
            // //  $path = Storage::disk('local')->put('uploads/documents', $pdf);

            return $path;
        } catch (Exception $e) {
            return $e;
        }
    }


    public function encode_data($data)
    {
        $encData = base64_encode($data);
        $encURI = urlencode($data);
        return str_split($encURI);
    }

    public function decode_data($data)
    {
        $decData = urldecode($data);
        $decData = base64_decode($decData);
        return $decData;
    }
}
