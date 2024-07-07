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
    public function imageView($filename)
    {

    }

    public function fileView($filename)
    {
        $path = storage_path("app/uploads/documents/{$filename}");

        if (file_exists($path)) {
            return response()->file($path);
        } else {
            abort(404);
        }
    }

    public function downloadFileNImage($filename)
    {

    }

}
