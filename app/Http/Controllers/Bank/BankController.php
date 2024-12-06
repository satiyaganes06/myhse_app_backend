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
            $bankList = BankInfo::join('bank_ref', 'bank_info.bi_int_bank_ref', '=', 'bank_ref.bref_int_ref')
                ->where('bi_int_status', 1)
               // ->select('bank_info.*', 'bank_ref.*')
                ->get();

            return $this->sendResponse(message: 'Get Bank List', result: $bankList);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : '.$e, code: 500);
        }
    }
}
