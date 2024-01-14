<?php

namespace App\Http\Controllers\Test;

use App\Http\Controllers\Controller;
use App\Models\UserProfile;
use Illuminate\Http\Request;

class test extends Controller
{
    public function test()
    {   
        
        $data = UserProfile::all();
        return response()->json($data);
    }
}
