<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\User\UserLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    public function sendResponse($message, $token = '', $result = '', $code = 200)
    {
        $response = [
            'success' => true,
            'message' => $message,
        ];

        if (!empty($result)) {
            $response['data'] = $result;
        }

        if (!empty($token)) {
            $response['token'] = $token;
        }

        // $ul = $this->encode_data($response);
        return response()->json($response, $code);
    }

    public function sendError($errorMEssage, $code)
    {
        $response = [
            'success' => false,
            'message' => $errorMEssage
        ];

        return response()->json($response, $code);
    }

    protected function isAuthorizedUser($id)
    {
        return Auth::user()->ul_int_profile_ref == $id;
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

    protected function getCpProfileDetails($userLoginID)
    {
        $userLoginDetails = UserLogin::where('ul_int_ref', $userLoginID)->value('ul_int_profile_ref');

        return $userLoginDetails;
    }
}
