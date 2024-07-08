<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Base\BaseController as BaseController;
use Illuminate\Http\Request;
use App\Models\User\UserProfile;
use App\Models\User;
use App\Models\User\RoleLogin;
use App\Models\User\UserRole;
use App\Models\User\PasswordResets;
use Laravel\Sanctum\PersonalAccessToken;
use Exception;
use Faker\Provider\ar_EG\Person;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


class AuthController extends BaseController
{
    // Version 2

    public function registration(Request $request)
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
            return $this->sendError(errorMEssage: 'Error : ' . $validator->errors(), code: 400); //Bad request
        }

        $user = User::where('ul_var_emailaddress', $request->input('upEmailContact'))->first();

        if ($user) {
            return $this->sendError(errorMEssage: 'Email already exists', code: 500);
        } else {
            try {
                DB::beginTransaction();

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
                    'ul_var_password' => Hash::make($request->input('ulPassword'), [
                        'rounds' => 12,
                        'salt' => uniqid(bin2hex(random_bytes(12)))
                    ]),
                ]);
                $userLogin->save();

                $userRole = UserRole::where('ur_var_name', 'Competent Person')->value('ur_int_ref');

                $roleLogin = new RoleLogin([
                    'rl_int_user_ref' => $userProfile->up_int_ref,
                    'rl_int_role_ref' => $userRole, // Assign desired role here
                ]);
                $roleLogin->save();

                $token = $userLogin->createToken('developmentRegisterApiToken')->plainTextToken;

                DB::commit();

                return $this->sendResponse(message: 'User registered successfully.', result: $token);
            } catch (\Throwable $th) {
                DB::rollBack();
                return $this->sendError(errorMEssage: 'Error : ' . $th->getMessage(), code: 500);
            }
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
                return $this->sendError(errorMEssage: 'Error : ' . $validator->errors(), code: 400); //Bad request
            }

            $user = User::where('ul_var_emailaddress', $request->input('ulEmail'))->first();

            if (!$user) {
                return $this->sendError(errorMEssage: 'Email does not exist', code: 401);
            }

            $userRole = UserRole::where('ur_var_name', 'Competent Person')->value('ur_int_ref');
            $roleLogin = RoleLogin::where('rl_int_user_ref', $user->ul_int_profile_ref)->value('rl_int_role_ref');

            if ($userRole != $roleLogin) {
                return $this->sendError(errorMEssage: 'Sorry, access denied. Only Competent Persons are allowed to log in using the mobile app.', code: 401);
            }

            $credentials = [
                'ul_var_emailaddress' => $request->input('ulEmail'),
                'password' => $request->input('ulPassword')
            ];

            if (Auth::attempt($credentials)) {
                $user = Auth::user();
                $token = $user->createToken('developmentLoginApiToken')->plainTextToken;

                return $this->sendResponse(message: 'Login successfully.', result: $token);
            } else {

                return $this->sendError(errorMEssage: 'Email or Password does not match', code: 401);
            }
        } catch (\Throwable $th) {
            return $this->sendError(errorMEssage: 'Error : ' . $th->getMessage(), code: 500);
        }
    }

    public function getUserID(){
        return Auth::user()->ul_int_profile_ref;
    }

    public function forgotPassword(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Error : ' . $validator->errors(), code: 400); // Bad request
            }

            $user = User::where('ul_var_emailaddress', $request->input('email'))->first();

            if (!$user) {
                return $this->sendError(errorMEssage: 'Email does not exist', code: 404);
            }

            PasswordResets::where('email', $request->input('email'))->delete();

            $token = rand(10000, 99999);
            PasswordResets::create([
                'email' => $request->input('email'),
                'token' => Hash::make($token),
                'created_at' => now(),
            ]);

            //! Send email with the token
            // You can use Laravel's built-in notification system or any other method to send the email
            // For example:
            // Notification::send($user, new ResetPasswordNotification($token));

            return $this->sendResponse(message: 'Password reset link sent to your email.', token: $token);
        }catch(\Throwable $th){
            return $this->sendError(errorMEssage: 'Error : ' . $th->getMessage(), code: 500);
        }
    }


    public function resetPassword(Request $request)
    {
        try{
            $validator = Validator::make($request->all(), [
                'email' => 'required|email',
                'token' => 'required|string',
                'password' => 'required|string|min:6',
            ]);

            if ($validator->fails()) {
                return $this->sendError(errorMEssage: 'Error : ' . $validator->errors(), code: 400); // Bad request
            }

            $passwordReset = PasswordResets::where('email', $request->input('email'))->first();

            if (!$passwordReset || !Hash::check($request->input('token'), $passwordReset->token)) {
                return $this->sendError(errorMEssage: 'Invalid token or email', code: 400);
            }

            $user = User::where('ul_var_emailaddress', $request->input('email'))->first();

            if (!$user) {
                return $this->sendError(errorMEssage: 'Email does not exist', code: 404);
            }

            $user->ul_var_password = Hash::make($request->input('password'));
            $user->save();

            // Delete the password reset token
            PasswordResets::where('email', $request->input('email'))->delete();

            return $this->sendResponse(message: 'Password has been reset successfully.');
        }catch(\Throwable $th){
            return $this->sendError(errorMEssage: 'Error : ' . $th->getMessage(), code: 500);
        }
    }

    public function logout()
    {
        try {
            Auth::user()->tokens()->delete();
            return $this->sendResponse(message: 'Logout successfully.');
        } catch (\Exception $e) {
            return $this->sendError(errorMEssage: 'Error : ' . $e->getMessage(), code: 500);
        }
    }
}

// public function register(){

//     $model =  RoleLogin::all();

//     return view('test.model', ['model' => $model]);
// }
