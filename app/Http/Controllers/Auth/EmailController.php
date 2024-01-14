<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Base\BaseController as BaseController;
use App\Mail\VerificationMail;
use Illuminate\Http\Request;
use App\Models\UserProfile;
use App\Models\UserLogin;
use App\Models\RoleLogin;
use Laravel\Sanctum\PersonalAccessToken;
use Exception;
use Faker\Provider\ar_EG\Person;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;


class EmailController extends BaseController
{

    

    public function sendVerificationEmail($cpEmail)
    {
       Mail::to('satiyaganes.sg@gmail.com')->send(new VerificationMail(2133));

       return response()->json(['message' => 'Verification email sent']);
    }

    public function verifyEmailAddress($cpLoginID){

        try {
            $status  = UserLogin::where('ul_int_ref', $this->decode_data($cpLoginID))->update(array('ul_ts_email_verified_at' => date('Y-m-d H:i:s')));

            return view('emailVerifcation.successEmailVerification');
        } catch (Exception $e) {

            return view('emailVerifcation.failureEmailVerification');
        }
    
    }
    // public function verifyEmailAddress($lid)
	// {
		
	// 	$emailAddress= $this ->getEmailAddressFromUserLogin($lid);
		
	// 	$passData = new Passdata_model();
		
	// 	$encEmail = $passData->encode_data($emailAddress);
		
	// 	$link = base_url()."/verify/".$encEmail;
		
	// 	$subject = 'Verify your Account';
		
	// 	$message = '<div><center><h1>Verify Your Account</h1>
	// 				<h3>You\'ve recently register your account with MyHSE Application System. <br>Please verify your account by click on link below to continue login.</h3>					
	// 				<div style="width: 400px; border-radius: 50px; font-size: 15px; background: rgb(238, 42, 42); line-height: 18.69px; margin: 0px auto; text-align: center;">
	// 					<a href="'.$link.'" target="_blank" > <p style="margin: 0px; font-size: 14px;"><span style="font-size: 15px; color: rgb(255, 255, 255); line-height: 50px;">
	// 					Verify My Account &nbsp;</span></p>
	// 					</a>
	// 				</div>
					
	// 				</center></div>
	// 				<br><br>
					
	// 				<br><br>
	// 				<div><center><p>Rest assured your account is safe with us.<br>For any questions please contact the <a href="#" target="_blank">Support Team</a></p></center></div>';
		
    //     $emailAdd = $emailAddress;
		
	// 	$email = \Config\Services::email();
		
	// 	$filename = 'assets/images/logo/email/header_email.png';
	// 	$email->attach($filename,'inline');
	// 	$cid = $email->setAttachmentCID($filename);
	// 	$headerMsg ='<div><table width="100%"><tr><td><center><img style="max-width: 100%;height: auto;" src="cid:'.$cid.'" alt="photo1" /></center></td></tr></table></div>';
		
		
		
		
	// 	$footerMsg = '<div style="background-color:grey;color:white;padding:1em;"><center>&#169;. All Rights Reserved.</center></div>';
		
	// 	$contentMsg = $headerMsg.$message.$footerMsg;
		
		
       
    //     $email->setTo($emailAdd);
    //     $email->setFrom('noreply-notifications@hse.com', 'My HSE Application System');
    //     $email->setSubject($subject);
    //     $email->setMessage($contentMsg);
		
    //     if ($email->send()) 
    //     // if ($this->email->send()) 
	// 	{
	// 		$data = $email->printDebugger(['headers']);
    //         // var_dump($data);die;
    //         return true;
    //     } 
	// 	else 
	// 	{
    //         $data = $email->printDebugger(['headers']);
    //          // var_dump($data);die;
	// 		return false;
    //     }
		
	// }
}

