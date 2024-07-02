<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Base\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User\UserProfile;
use App\Models\User;
use App\Models\User\RoleLogin;
use Laravel\Sanctum\PersonalAccessToken;
use Exception;
use Faker\Provider\ar_EG\Person;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;


class AuthController extends BaseController
{
    public function register(Request $request)
    {
        try {
            $user = UserLogin::where('ul_var_emailaddress', $request->input('upEmailContact'))->first();

            if ($user) {
                return $this->sendError('Email already exists', 500);
            } else {

                try {

                    $validatorUser = Validator::make($request->all(), [
                        'upfirstName' => 'required|string|max:255',
                        'upLastName' => 'required|string|max:255',
                        'upNric' => 'required|string|max:255',
                        'upEmailContact' => 'required|string|max:255',
                        'upContactNo' => 'required|string|max:255',
                        'ulPassword' => 'required|min:6'
                    ]);

                    if ($validatorUser->fails()) {

                        return $this->sendError('Error : ' . $validatorUser->errors(), 500);
                    }
                    // $validated = $request->validated();
                    DB::beginTransaction();

                    // Insert into user_profile
                    $userProfile = new UserProfile([
                        'up_var_first_name' => $request->input('upfirstName'),
                        'up_var_last_name' => $request->input('upLastName'),
                        'up_var_nric' => $request->input('upNric'),
                        'up_var_email_contact' => $request->input('upEmailContact'),
                        'up_var_contact_no' => $request->input('upContactNo'),
                    ]);
                    $userProfile->save();

                    //Insert into user_login
                    $userLogin = new UserLogin([
                        'ul_int_profile_ref' => $userProfile->up_int_ref,
                        'ul_var_emailaddress' => $request->input('upEmailContact'),
                        'ul_var_password' => Hash::make($request->input('ulPassword')),
                    ]);
                    $userLogin->save();

                    // Insert into role_login
                    $roleLogin = new RoleLogin([
                        'rl_int_user_ref' => $userProfile->up_int_ref,
                        'rl_int_role_ref' => 4, // Assign desired role here
                    ]);
                    $roleLogin->save();

                    //$token = $userLogin->createToken('API Token')->plainTextToken;

                    DB::commit();

                    return $this->sendResponse('User registered successfully.', '');
                } catch (\Exception $e) {

                    DB::rollBack();
                    //return response()->json(['success' => false, 'message' => 'Error: ' . $e->getMessage()], 500);
                    return $this->sendError('Error : ' . $e->getMessage(), 500);
                }
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error : ' . $th->getMessage(), 500);
        }
    }

    public function login(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [
                'ulEmail' => 'required|string|max:255',
                'ulPassword' => 'required|min:6'
            ]);

            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => "Validation Error: " . $validator->errors()], 401);
                return $this->sendError('Error : ' . $validator->errors(), 500);
            }

            // if(!Auth::attempt($request->only('ul_var_emailaddress', 'ul_var_password'))){
            //     return response()->json(['success' => false, 'message' => 'Email or Password does not match'], 401);
            // }

            $user = UserLogin::where('ul_var_emailaddress', $request->input('ulEmail'))->first();

            if (!$user || !Hash::check($request->input('ulPassword'), $user->ul_var_password)) {
                return $this->sendError('Email or Password does not match', 401);
            }

            $token = $user->createToken('API Token')->plainTextToken;

            return $this->sendResponse('Login successfully.', $token, $user);
        } catch (\Exception $e) {

            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function logout(Request $request)
    {
        try {
            PersonalAccessToken::where('id', $request->input('id'))->delete();
            return $this->sendResponse('Logout successfully.', '');
        } catch (\Exception $e) {
            return $this->sendError('Error : ' . $e->getMessage(), 500);
        }
    }

    public function registerV2(Request $request)
    {
        $validator = validator::make($request->all(), [
            'upfirstName' => 'required|string|max:255',
            'upLastName' => 'required|string|max:255',
            'upNric' => 'required|string|max:255',
            'upEmailContact' => 'required|string|max:255',
            'upContactNo' => 'required|string|max:10',
            'ulPassword' => 'required|min:6',
            'ulConfirmPassword' => 'required|min:6|same:ulPassword',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Error : ' . $validator->errors(), 400); //Bad request
        }

        try {
            DB::beginTransaction();

            // Insert into user_profile
            $userProfile = new UserProfile([
                'up_var_first_name' => $request->input('upfirstName'),
                'up_var_last_name' => $request->input('upLastName'),
                'up_var_nric' => $request->input('upNric'),
                'up_var_email_contact' => $request->input('upEmailContact'),
                'up_var_contact_no' => $request->input('upContactNo'),
            ]);
            $userProfile->save();

            //Insert into user_login
            $userLogin = new User([
                'ul_int_profile_ref' => $userProfile->up_int_ref,
                'ul_var_emailaddress' => $request->input('upEmailContact'),
                'ul_var_password' => Hash::make($request->input('ulPassword')),
            ]);
            $userLogin->save();

            // Insert into role_login
            $roleLogin = new RoleLogin([
                'rl_int_user_ref' => $userProfile->up_int_ref,
                'rl_int_role_ref' => 4, // Assign desired role here
            ]);
            $roleLogin->save();

            $token = $userLogin->createToken(bin2hex('developmentRegisterApiToken'))->plainTextToken;

            DB::commit();

            return $this->sendResponse('User registered successfully.', $token);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->sendError('Error : ' . $th->getMessage(), 500);
        }
    }

    public function loginV2(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'ulEmail' => 'required|string|max:255',
            'ulPassword' => 'required|min:6'
        ]);


        if($validator->fails()){
            return $this->sendError('Error : ' . $validator->errors(), 400); //Bad request
        }

        try {

            $credentials = [
                'ul_var_emailaddress' => $request->input('ulEmail'),
                'ul_var_password' => $request->input('ulPassword')
            ];

            if(Auth::attempt($credentials)){
                $user = Auth::user();
                $token = $user->createToken('developmentLoginApiToken')->plainTextToken;

                return $this->sendResponse('Login successfully.', $token);

            }else{
                return $this->sendError('Email or Password does not match', 401);
            }
        } catch (\Throwable $th) {
            return $this->sendError('Error : ' . $th->getMessage(), 500);
        }
    }

}

// public function register(){

//     $model =  RoleLogin::all();

//     return view('test.model', ['model' => $model]);
// }
