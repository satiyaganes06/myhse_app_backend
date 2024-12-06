<?php

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\Controller;
use App\Models\Bank\BankInfo;

class BankController extends Controller
{
    public function getBankInfoList()
    {
        try {
            $bankList = BankInfo::where('bi_int_status', 1)->get();

            return $this->sendResponse(message: 'Get Bank List', result: $bankList);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : '.$e, code: 500);
        }
    }
}
