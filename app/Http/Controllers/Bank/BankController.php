<?php

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\Base\BaseController;
use App\Models\Bank\BankInfo;

class BankController extends BaseController
{
    public function getBankInfoList()
    {
        try {
            // with bank ref
            $bankList = BankInfo::where('bi_int_status', 1)
                ->with('bank')
                ->get();

            return $this->sendResponse(message: 'Get Bank List', result: $bankList);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : '.$e, code: 500);
        }
    }
}
