<?php

namespace App\Http\Controllers\Common;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Common\CompetentPersonTypes;
use App\Models\Common\ServiceMain;
use App\Models\Common\SubService;
use App\Models\Common\State;
use Illuminate\Http\Request;

class CommonDataController extends BaseController
{
    public function getStateList(){
        try {
            $stateList = State::all();
            return $this->sendResponse('get state list', '', $stateList);
        } catch (\Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
    }

    public function getServiceMainList(){
       try {
            $serviceMainList = ServiceMain::all();
            return $this->sendResponse('get service main list', '', $serviceMainList);
        } catch (\Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
    }

    public function getSubServiceList(){
       try {
            $subServiceList = SubService::all();
            return $this->sendResponse('get sub service list', '', $subServiceList);
        } catch (\Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
    }

    public function getCompententPersonTypeList(){
        try {
            $competentPersonTypeList = CompetentPersonTypes::all();
            return $this->sendResponse('get competent person type list', '', $competentPersonTypeList);
        } catch (\Exception $e) {
            return $this->sendError('Error : ' . $e, 500);
        }
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
