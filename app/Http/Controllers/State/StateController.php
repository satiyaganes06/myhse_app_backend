<?php

namespace App\Http\Controllers\State;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Http\Controllers\Controller;
use App\Models\Common\CompetentPersonTypes;
use App\Models\Common\ServiceMain;
use App\Models\Common\SubService;
use App\Models\Common\State;
use Illuminate\Http\Request;

class StateController extends BaseController
{
    public function getStateList(){
        try {

            $stateList = State::all();
            return $this->sendResponse(message: 'Get State List', result: $stateList);

        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e, code: 500);
        }
    }



}
