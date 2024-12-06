<?php

namespace App\Http\Controllers\Bank;

use App\Http\Controllers\Base\BaseController;
use App\Models\Bank\BankInfo;

class BankController extends BaseController
{
    public function __construct()
    {
        mb_internal_encoding('UTF-8');
    }

    public function getBankInfoList()
    {
        try {
            $bankList = BankInfo::join('bank_ref', 'bank_info.bi_int_bank_ref', '=', 'bank_ref.bref_int_ref')
                ->where('bi_int_status', 1)
                ->get()
                ->map(function ($item) {
                    return collect($item)->map(function ($value) {
                        return is_string($value) ? mb_convert_encoding($value, 'UTF-8', 'UTF-8') : $value;
                    })->all();
                });

            return $this->sendResponse(message: 'Get Bank List', result: $bankList);
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : '.$e->getMessage(), code: 500);
        }
    }
}
