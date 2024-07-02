<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\User\UserProfile;
use Illuminate\Http\Request;

class test extends Controller
{
    public function testttt()
    {

        $data = UserProfile::all();
        return response()->json($data);
    }
}
