<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\User\UserProfile;
use App\Models\User;
use Illuminate\Http\Request;

class test extends Controller
{
    public function testttt()
    {

        $data = User::all();
        return response()->json($data);
    }
}
