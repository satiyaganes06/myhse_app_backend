<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Common\ServiceMain;
use App\Models\Common\SubService;
use App\Models\Common\State;
use Illuminate\Http\Request;

class CommonDataController extends BaseController
{
    public function getStateList(){
        $stateList = State::all();

        return $this->sendResponse('get state list', '', $stateList);
    }

    public function getServiceMainList(){
        $serviceList = ServiceMain::all();

        return $this->sendResponse('get service main list', '', $serviceList);
    }

    public function getSubServiceList(){
        $subServiceList = SubService::all();

        return $this->sendResponse('get sub service list', '', $subServiceList);
    }


    public function pdfView($filename)
    {
        $path = storage_path("app/uploads/documents/{$filename}");

        if (file_exists($path)) {
            return response()->file($path);
        } else {
            abort(404);
        }
    }
    
}
