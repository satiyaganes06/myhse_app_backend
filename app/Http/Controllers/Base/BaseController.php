<?php

namespace App\Http\Controllers\Base;

use App\Http\Controllers\Controller;
use App\Models\UserLogin;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function sendResponse($message, $token, $result = ''){
        $response = [
            'success' => true,
            'message' => $message,
            'token' => $token
        ];

        if(!empty($result)){
            $response['data'] = $result;
        }

       // $ul = $this->encode_data($response);
        return response()->json($response, 200);
    }

    public function sendError($errorMEssage, $code){
        $response = [
            'success' => false,
            'message' => $errorMEssage,
        ];

        // if(!empty($errorMEssage)){
        //     $response['data'] = $errorMEssage;
        // }

        return response()->json($response, $code);

    }

    public function encode_data($data) {
      $encData = base64_encode($data);
        $encURI = urlencode($data);
        return str_split($encURI);
    }
    
    public function decode_data($data) {
        $decData = urldecode($data);
        $decData = base64_decode($decData);
        return $decData;
    }

    public function getCpProfileDetails($userLoginID){
        $userLoginDetails = UserLogin::where('ul_int_ref', $userLoginID)->value('ul_int_profile_ref');

        return $userLoginDetails;
    }
}
