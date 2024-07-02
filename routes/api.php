<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Test\test;
use App\Http\Controllers\User\CpDetailsController;
use App\Http\Controllers\User\ClientUserDetailsController;
use App\Http\Controllers\Common\CommonDataController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\Job\JobMainController;
use App\Http\Controllers\Booking\BookingMainController;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Certificate\CertificateController;
use App\Http\Controllers\PaymentSubscribeController;
use App\Http\Controllers\State\StateController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

//!! Version 2 with Access Token

Route::group(['prefix' => 'v2/auth'], function(){
    Route::post('/register', [AuthController::class, 'registerV2']);
    Route::post('/login', [AuthController::class, 'loginV2']);
});


Route::middleware('auth:sanctum')->group(function () {

    Route::group(['prefix' => 'v2/competent-person'], function(){
        //Test Routes
        Route::get("/test/getData", [test::class, 'testttt']);


        Route::get('/logout', [AuthController::class, 'logout']);

        // Manage User
        Route::get("/getCpProfileDetailsByID/{id}", [CpDetailsController::class, 'getCpProfileDetailsByID']);
        Route::patch("/updateCpProfileDetailsByID/{id}", [CpDetailsController::class, 'updateCpProfileDetailsByID']);
        Route::get("/getEmailStatusByID/{id}", [CpDetailsController::class, 'getEmailStatusByID']);
        Route::patch("/updateEmailStatusByID/{id}", [CpDetailsController::class, 'updateEmailStatusByID']);
        Route::get("/getCpFirstTimeStatusByID/{id}", [CpDetailsController::class, 'getCpFirstTimeStatusByID']);
        Route::get("/getCompetentPersonTypeList", [CpDetailsController::class, 'getCompententPersonTypeList']);

        // Service
        Route::get("/getServiceMainList", [ServiceController::class, 'getServiceMainList']);
        Route::get("/getSubServiceList", [ServiceController::class, 'getSubServiceList']);

        //State
        Route::get("/getStateList", [StateController::class, 'getStateList']);

    });

});


//!! Version 1

//Test
Route::post('/cp/test', [BaseController::class, 'getCpProfileDetails']);

//App Operations
Route::post("/cp/getCpProfileDetails/{id}", [CpDetailsController::class, 'cpProfileDetails']); // Done to V2
Route::post("/cp/getEmailVerificationStatus", [CpDetailsController::class, 'cpEmailVerificationStatusCheck']); // Done to V2
Route::post("/cp/updateEmailVerificationStatus", [CpDetailsController::class, 'cpEmailVerificationStatusUpdate']); // Done to V2
Route::post("/cp/getFirstTimeStatus", [CpDetailsController::class, 'cpFirstTimeStatusCheck']); // Done to V2

//Common
Route::get("/cp/getStateList", [CommonDataController::class, 'getStateList']); // Done to V2
Route::get("/cp/getServiceMainList", [CommonDataController::class, 'getServiceMainList']); // Done to V2
Route::get("/cp/getSubServiceList", [CommonDataController::class, 'getSubServiceList']); // Done to V2
Route::get("/cp/getCompetentPersonTypeList", [CommonDataController::class, 'getCompententPersonTypeList']); // Done to V2

Route::put('/cp/completeProfile', [CpDetailsController::class, 'cpCompleteProfile']); // Done to V2
Route::put('/cp/updateCpProfileInfo', [CpDetailsController::class, 'updateProfileInfo']); // Done to V2

//Booking Operations
Route::post('/cp/bookingMainList', [BookingMainController::class, 'cpBookingInfo']);
Route::post('/cp/cpBookingRequest', [BookingMainController::class, 'cpBookingRequest']);
Route::post('/cp/bookingDetails', [BookingMainController::class, 'cpBookingDetailsList']);
Route::post('/cp/addBookingRequest', [BookingMainController::class, 'addBookingRequest']);
Route::post('/cp/updateBookingMain', [BookingMainController::class, 'updateStatusBookingMain']);
Route::post('/cp/updateBookingRequest', [BookingMainController::class, 'updateStatusBookingRequest']);

//Job Operation
Route::post('/cp/getJobList', [JobMainController::class, 'cpJobMainListDetails']);
Route::post('/cp/getJobDetails', [JobMainController::class, 'cpJobMainDetails']);
Route::post('/cp/getJobPaymentDetails', [JobMainController::class, 'cpJobPaymentDetails']);
Route::post('/cp/getJobResultDetails', [JobMainController::class, 'cpJobResultDetails']);
Route::post('/cp/addJobResultDetails', [JobMainController::class, 'cpAddJobResultDetails']);
Route::post('/cp/updateJobMainProgressCompleteStatus', [JobMainController::class, 'updateCpJobMainProgressCompleteStatus']);
Route::post('/cp/uploadJobResultFinalReport', [JobMainController::class, 'uploadJobResultFinalDocument']);

//Service Operations
Route::post('/cp/getCpServiceList', [CommonDataController::class, 'getServiceList']);
Route::post('/cp/addServiceInfo', [ServiceController::class, 'addServiceDetails']);
Route::post('/cp/getMyServiceDetailsList', [ServiceController::class, 'getMyServiceDetailsList']);
Route::post('/cp/updateServiceDetails', [ServiceController::class, 'updateServiceDetails']);
Route::post('/cp/deleteServiceDetails', [ServiceController::class, 'deleteServiceDetails']);

//Certificate Operations
Route::post('/cp/getMyCertificateDetailsList', [CertificateController::class, 'getMyCertificateDetailsList']);
Route::post('/cp/addCertificateInfo', [CertificateController::class, 'addCertificateDetails']);
Route::post('/cp/updateCertificateDetails', [CertificateController::class, 'updateCertificateDetails']);
Route::post('/cp/deleteCertificateDetails', [CertificateController::class, 'deleteCertificateDetails']);

//Subscription Operations
Route::post('/cp/uploadSubscriptionPayment', [PaymentSubscribeController::class, 'uploadSubscriptionPaymentData']);
Route::post('/cp/checkUserSubscription', [PaymentSubscribeController::class, 'checkUserSubscription']);
Route::post('/cp/checkIfUserCP', [PaymentSubscribeController::class, 'checkIfUserCP']);

//Normal User Operations
Route::post("/clientUser/getClientUserProfileDetails", [ClientUserDetailsController::class, 'clientUserProfileDetails']);

//PDF View
// routes/web.php or routes/api.php

Route::get('/pdfviewer/{filename}', [CommonDataController::class, 'pdfView'])->where('filename', '.*');


//Auth
Route::post("/auth/register", [AuthController::class, 'register']);
Route::post("/auth/login", [AuthController::class, 'login']);
Route::post("/auth/logout", [AuthController::class, 'logout']);



