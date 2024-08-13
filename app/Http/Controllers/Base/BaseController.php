<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\User\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Response;

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

    public function imageViewer($filepath)
    {
        //    dd($this->decode_data($filepath));
        $path = storage_path($this->decode_data($filepath));
        $contents = file_get_contents($path);
        $mime = mime_content_type($path);

        if (file_exists($path)) {
            // return response()->file($path);
            return Response::make($contents, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'inline', // This header indicates to display the content inline (in the browser)
            ]);
        } else {

            $path1 = storage_path('app/uploads/images/CertificateDocument/504708-200.png');
            $contents1 = file_get_contents($path1);
            $mime1 = mime_content_type($path1);

            return Response::make($contents1, 200, [
                'Content-Type' => $mime1,
                'Content-Disposition' => 'inline', // This header indicates to display the content inline (in the browser)
            ]);
        }
    }

    public function downloadFile($filepath)
    {
        //    dd($this->decode_data($filepath));
        $path = storage_path($this->decode_data($filepath));
        $contents = file_get_contents($path);
        $mime = mime_content_type($path);

        if (file_exists($path)) {
            // return response()->file($path);
            return Response::make($contents, 200, [
                'Content-Type' => $mime,
                'Content-Disposition' => 'attachment', // This header indicates to download the file
            ]);
        } else {
            $path1 = storage_path('app/uploads/images/CertificateDocument/504708-200.png');
            $contents1 = file_get_contents($path1);
            $mime1 = mime_content_type($path1);

            return Response::make($contents1, 200, [
                'Content-Type' => $mime1,
                'Content-Disposition' => 'attachment', // This header indicates to download the file
            ]);
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
