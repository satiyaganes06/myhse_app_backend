<?php

use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\EmailController;
use App\Http\Controllers\Test\test;
use App\Http\Controllers\User\UserDetailsController;
use App\Http\Controllers\User\ClientUserDetailsController;
use App\Http\Controllers\Common\CommonDataController;
use App\Http\Controllers\Service\ServiceController;
use App\Http\Controllers\Job\JobMainController;
use App\Http\Controllers\Booking\BookingMainController;
use App\Http\Controllers\Base\BaseController;
use App\Http\Controllers\Certificate\CertificateController;
use App\Http\Controllers\Job\JobPaymentController;
use App\Http\Controllers\Job\JobResultController;
use App\Http\Controllers\Job\JobUserRatingController;
use App\Http\Controllers\PaymentSubscribeController;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\State\StateController;
use App\Http\Controllers\Subscription\SubscriptionController;
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
    Route::post('/register', [AuthController::class, 'registration']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);

});


Route::middleware('auth:sanctum')->group(function () {

    Route::group(['prefix' => 'v2/common'], function(){

        // Auth
        Route::get('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/getUserID', [AuthController::class, 'getUserID']);
        Route::post('/verify-email/{id}', [EmailController::class, 'sendVerificationEmail']);

        // Upload Media
        Route::post('/uploadMediaWithPost', [BaseController::class, 'uploadMediaWithPost']);

        // View Image
        Route::get('/imageViewer/{filepath}',[BaseController::class, 'imageViewer'])->name('image.show');

        // Manage User
        Route::get("/manage-user/getMyProfileDetailsByID/{id}", [UserDetailsController::class, 'getMyProfileDetailsByID']);
        Route::get("/manage-user/getUserProfileDetailByID/{id}/{clientID}", [UserDetailsController::class, 'getUserProfileDetailByID']);
        Route::patch("/manage-user/updateMyProfileDetailsByID/{id}", [UserDetailsController::class, 'updateMyProfileDetailsByID']);
        Route::get("/manage-user/getEmailStatusByID/{id}", [UserDetailsController::class, 'getEmailStatusByID']);
        Route::patch("/manage-user/updateEmailStatusByID/{id}", [UserDetailsController::class, 'updateEmailStatusByID']); //! Move out of the sanctum
        Route::get("/manage-user/getMyFirstTimeStatusByID/{id}", [UserDetailsController::class, 'getMyFirstTimeStatusByID']);
        Route::get("/manage-user/getRoleByID/{id}", [UserDetailsController::class, 'getRoleByID']);
        Route::patch("/manage-user/updateRoleByID/{id}", [UserDetailsController::class, 'updateRoleByID']);

        // State
        Route::get("/state/getStateList", [StateController::class, 'getStateList']);

        // Service
        Route::get("/service/getServiceByID/{id}/{serviceID}", [ServiceController::class, 'getServiceByID']);
        Route::get("/service/getRelatedCertificate/{id}/{serviceID}", [ServiceController::class, 'getRelatedCertificate']);
        Route::get("/service/getRelatedPost/{id}/{serviceId}", [ServiceController::class, 'getRelatedPost']);

        // Booking
        Route::get('/booking/getBookingsRequestDetailByID/{id}', [BookingMainController::class, 'getBookingsRequestDetailByID']);
        Route::get('/booking/getBookingRequestNegotiationDetailByID/{id}/{brID}', [BookingMainController::class, 'getBookingRequestNegotiationDetailByID']);
        Route::post('/booking/addBookingRequestNegotiationDetail', [BookingMainController::class, 'addBookingRequestNegotiationDetail']);
        Route::patch('/booking/updateBookingRequestNegotiationStatusByID/{id}', [BookingMainController::class, 'updateBookingRequestNegotiationStatusByID']);
        Route::patch('/booking/updateBookingMainNegotiationStatusByID/{id}', [BookingMainController::class, 'updateBookingMainNegotiationStatusByID']); // Un-finished

        // Job
        Route::get('/job/getJobMainDetailsByID/{id}', [JobMainController::class, 'getJobMainDetailsByID']);
        Route::get('/job/getJobMainDetailByID/{id}/{brID}', [JobMainController::class, 'getJobMainDetailByID']);
        Route::patch('/job/updateJobMainTimeline/{id}', [JobMainController::class, 'updateJobMainTimeline']);

        Route::post('/job/addJobPayment', [JobPaymentController::class, 'insertJobPayment']);
        Route::get('/job/getJobInitialPaymentStatusByID/{id}/{brID}/{jmID}', [JobPaymentController::class, 'getJobInitialPaymentStatusByID']);
        Route::get('/job/getJobInitialPaymentDetailByID/{id}/{jmID}', [JobPaymentController::class, 'getJobInitialPaymentDetailByID']);
        Route::get('/job/getJobFinalPaymentStatusByID/{id}/{brID}/{jmID}', [JobPaymentController::class, 'getJobFinalPaymentStatusByID']);
        Route::get('/job/getJobFinalPaymentDetailByID/{id}/{jmID}', [JobPaymentController::class, 'getJobFinalPaymentDetailByID']);
        Route::get('/job/getJobResultByID/{id}/{jmID}', [JobResultController::class, 'getJobResultByID']);
        Route::get('/job/getFinalJobResultByID/{id}/{jmID}', [JobResultController::class, 'getFinalJobResultByID']);
        Route::get('/job/getJobResultCommentsByID/{id}/{jrID}', [JobResultController::class, 'getJobResultCommentsByID']);
        Route::post('/job/addJobResultComment', [JobResultController::class, 'addJobResultComment']);
        Route::get('/job/getJobUserRatingByID/{id}/{jmID}', [JobUserRatingController::class, 'getJobUserRatingByID']);
        Route::get('/job/getReviewByServiceID/{id}/{serviceID}', [JobUserRatingController::class, 'getReviewByServiceID']);
        Route::get('/job/getRatingByServiceID/{id}/{serviceID}', [JobUserRatingController::class, 'getRatingByServiceID']);
    });

    Route::group(['prefix' => 'v2/client'], function(){
        //Post
        Route::get("/post/getAllPostDetails/{id}", [PostController::class, 'getAllPostDetails']);

        // Booking
        Route::post("/booking/addBookingRequest", [BookingMainController::class, 'addBookingRequest']);

        // Certificate
        Route::get("/certificate/getCpCertificatesDetails/{id}/{cpID}", [CertificateController::class, 'getCpCertificatesDetails']);

        // Post
        Route::get("/post/getCpPostDetails/{id}/{cpID}", [PostController::class, 'getCpPostDetails']);

        // Service
        Route::get("/service/getCpServicesDetails/{id}/{cpID}", [ServiceController::class, 'getCpServicesDetails']);
        Route::get("/service/getAllService/{id}", [ServiceController::class, 'getAllService']);
        Route::get("/service/getSearchServiceResult/{id}", [ServiceController::class, 'getSearchServiceResult']);

        // Job
        Route::post('/job/addJobUserRating/{id}', [JobUserRatingController::class, 'addJobUserRating']);
        Route::patch('/job/updateJobResultStatus/{id}', [JobResultController::class, 'updateJobResultStatus']);

    });

    Route::group(['prefix' => 'v2/competent-person'], function(){

        //Test Routes
        Route::get("/test/getData", [test::class, 'testttt']);

        // Manage User
        Route::get("/manage-user/getCompetentPersonTypeList", [UserDetailsController::class, 'getCompententPersonTypeList']);

        // Certificate
        Route::get("/certificate/getCertificatesDetailByID/{id}", [CertificateController::class, 'getCertificatesDetailByID']);
        Route::post("/certificate/addCertificateDetail", [CertificateController::class, 'addCertificateDetail']);
        Route::patch("/certificate/updateCertificateDetail/{id}", [CertificateController::class, 'updateCertificateDetail']);
        Route::delete("/certificate/deleteCertificateDetailByID/{id}/{ccID}", [CertificateController::class, 'deleteCertificateDetailByID']);

        // Post
        Route::get("/post/getPostDetailsByID/{id}", [PostController::class, 'getPostDetailsByID']);
        Route::post("/post/addPostDetail", [PostController::class, 'addPostDetail']);
        Route::patch("/post/updatePostDetail/{id}", [PostController::class, 'updatePostDetail']);
        Route::delete("/post/deletePostDetailByID/{id}/{postID}", [PostController::class, 'deletePostDetail']);

        // Service
        Route::get("/service/getServiceMainList", [ServiceController::class, 'getServiceMainList']);
        Route::get("/service/getSubServiceList", [ServiceController::class, 'getSubServiceList']);

        Route::get('/service/getServicesDetailByID/{id}', [ServiceController::class, 'getServicesDetailByID']);
        Route::post('/service/addServiceDetail', [ServiceController::class, 'addServiceDetail']);
        Route::patch('/service/updateServiceDetail/{id}', [ServiceController::class, 'updateServiceDetail']);
        Route::delete('/service/deleteServiceDetails/{id}/{cpsID}', [ServiceController::class, 'deleteServiceDetails']);

        // Job
        Route::patch('/job/updateJobMainResultStatus/{id}', [JobMainController::class, 'updateJobMainResultStatus']);
        Route::post('/job/addJobResult', [JobResultController::class, 'addJobResult']);

        // Subscription
        Route::get('/subscription/getSubscriptionPlans/{id}', [SubscriptionController::class, 'getSubscriptionPlans']);
        Route::get('/subscription/checkUserSubscription/{id}', [SubscriptionController::class, 'checkUserSubscription']);
        Route::get('/subscription/getSubscriptionUserByID/{id}', [SubscriptionController::class, 'getSubscriptionUserByID']);
        Route::post('/subscription/addSubscriptionPayment', [SubscriptionController::class, 'addSubscriptionPayment']);
        Route::get('/subscription/getSubscriptionPaymentByID/{id}', [SubscriptionController::class, 'getSubscriptionPaymentByID']);

        //Image and File Viewer
        Route::get('/viewer/pdfviewer/{filename}', [CommonDataController::class, 'fileView'])->where('filename', '.*'); // Un-finished
        Route::get('/viewer/imageviewer/{filename}', [CommonDataController::class, 'imageView'])->where('filename', '.*'); // Un-finished
        Route::get('/viewer/downloadfile/{filename}', [CommonDataController::class, 'downloadFileNImage'])->where('filename', '.*'); // Un-finished

    });

});


//! Version 3

// Route::group(['prefix' => 'v2/competent-person'], function(){

//     //Test Routes
//     Route::get("/test/getData", [test::class, 'testttt']);

//     // Auth
//     Route::get('/auth/logout', [AuthController::class, 'logout']);
//     Route::get('/auth/getUserID', [AuthController::class, 'getUserID']);

//     // Manage User
//     Route::get("/manage-user/getCpProfileDetailsByID/{id}", [CpDetailsController::class, 'getCpProfileDetailsByID']);
//     Route::get("/manage-user/getClientUserProfileDetailByID/{id}/{clientID}", [CpDetailsController::class, 'getClientUserProfileDetailByID']);
//     Route::patch("/manage-user/updateCpProfileDetailsByID/{id}", [CpDetailsController::class, 'updateCpProfileDetailsByID']);
//     Route::get("/manage-user/getEmailStatusByID/{id}", [CpDetailsController::class, 'getEmailStatusByID']);
//     Route::patch("/manage-user/updateEmailStatusByID/{id}", [CpDetailsController::class, 'updateEmailStatusByID']);
//     Route::get("/manage-user/getCpFirstTimeStatusByID/{id}", [CpDetailsController::class, 'getCpFirstTimeStatusByID']);
//     Route::get("/manage-user/getCompetentPersonTypeList", [CpDetailsController::class, 'getCompententPersonTypeList']);

//     // Certificate
//     Route::get("/certificate/getCertificatesDetailByID/{id}", [CertificateController::class, 'getCertificatesDetailByID']);
//     Route::post("/certificate/addCertificateDetail", [CertificateController::class, 'addCertificateDetail']);
//     Route::patch("/certificate/updateCertificateDetail/{id}", [CertificateController::class, 'updateCertificateDetail']);
//     Route::delete("/certificate/deleteCertificateDetailByID/{id}/{ccID}", [CertificateController::class, 'deleteCertificateDetailByID']);

//     // Service
//     Route::get("/service/getServiceMainList", [ServiceController::class, 'getServiceMainList']);
//     Route::get("/service/getSubServiceList", [ServiceController::class, 'getSubServiceList']);

//     Route::get('/service/getServicesDetailByID/{id}', [ServiceController::class, 'getServicesDetailByID']);
//     Route::post('/service/addServiceDetail', [ServiceController::class, 'addServiceDetail']);
//     Route::patch('/service/updateServiceDetail/{id}', [ServiceController::class, 'updateServiceDetail']);
//     Route::delete('/service/deleteServiceDetails/{id}/{cpsID}', [ServiceController::class, 'deleteServiceDetails']);

//     // State
//     Route::get("/state/getStateList", [StateController::class, 'getStateList']);

//     // Booking
//     Route::get('/booking/getBookingsDetailByID/{id}', [BookingMainController::class, 'getBookingsDetailByID']);
//     Route::get('/booking/getBookingRequestDetailByID/{id}/{brID}', [BookingMainController::class, 'getBookingRequestDetailByID']);
//     Route::post('/booking/addBookingRequestDetail', [BookingMainController::class, 'addBookingRequestDetail']);
//     Route::patch('/booking/updateBookingRequestStatusByID/{id}', [BookingMainController::class, 'updateBookingRequestStatusByID']);
//     Route::patch('/booking/updateBookingMainStatusByID/{id}', [BookingMainController::class, 'updateBookingMainStatusByID']); // Un-finished

//     //Image and File Viewer
//     Route::get('/viewer/pdfviewer/{filename}', [CommonDataController::class, 'fileView'])->where('filename', '.*'); // Un-finished
//     Route::get('/viewer/imageviewer/{filename}', [CommonDataController::class, 'imageView'])->where('filename', '.*'); // Un-finished
//     Route::get('/viewer/downloadfile/{filename}', [CommonDataController::class, 'downloadFileNImage'])->where('filename', '.*'); // Un-finished

// });


//!! Version 1

//Test
Route::post('/cp/test', [BaseController::class, 'getCpProfileDetails']);

//App Operations
// Route::post("/cp/getCpProfileDetails/{id}", [CpDetailsController::class, 'cpProfileDetails']); // Done to V2
// Route::post("/cp/getEmailVerificationStatus", [CpDetailsController::class, 'cpEmailVerificationStatusCheck']); // Done to V2
// Route::post("/cp/updateEmailVerificationStatus", [CpDetailsController::class, 'cpEmailVerificationStatusUpdate']); // Done to V2
// Route::post("/cp/getFirstTimeStatus", [CpDetailsController::class, 'cpFirstTimeStatusCheck']); // Done to V2

//Common
Route::get("/cp/getStateList", [CommonDataController::class, 'getStateList']); // Done to V2
Route::get("/cp/getServiceMainList", [CommonDataController::class, 'getServiceMainList']); // Done to V2
Route::get("/cp/getSubServiceList", [CommonDataController::class, 'getSubServiceList']); // Done to V2
Route::get("/cp/getCompetentPersonTypeList", [CommonDataController::class, 'getCompententPersonTypeList']); // Done to V2

// Route::put('/cp/completeProfile', [CpDetailsController::class, 'cpCompleteProfile']); // Done to V2
// Route::put('/cp/updateCpProfileInfo', [CpDetailsController::class, 'updateProfileInfo']); // Done to V2

//Booking Operations
Route::post('/cp/bookingMainList', [BookingMainController::class, 'cpBookingInfo']); // Done to V2
Route::post('/cp/cpBookingRequest', [BookingMainController::class, 'cpBookingRequest']);
Route::post('/cp/bookingDetails', [BookingMainController::class, 'cpBookingDetailsList']); // Done to V2
Route::post('/cp/addBookingRequest', [BookingMainController::class, 'addBookingRequest']); // Done to V2
Route::post('/cp/updateBookingMain', [BookingMainController::class, 'updateStatusBookingMain']); // Done to V2
Route::post('/cp/updateBookingRequest', [BookingMainController::class, 'updateStatusBookingRequest']); // Done to V2

//Job Operation
Route::post('/cp/getJobList', [JobMainController::class, 'cpJobMainListDetails']);
Route::post('/cp/getJobDetails', [JobMainController::class, 'cpJobMainDetails']);
Route::post('/cp/getJobPaymentDetails', [JobMainController::class, 'cpJobPaymentDetails']);
Route::post('/cp/getJobResultDetails', [JobMainController::class, 'cpJobResultDetails']);
Route::post('/cp/addJobResultDetails', [JobMainController::class, 'cpAddJobResultDetails']);
Route::post('/cp/updateJobMainProgressCompleteStatus', [JobMainController::class, 'updateCpJobMainProgressCompleteStatus']);
Route::post('/cp/uploadJobResultFinalReport', [JobMainController::class, 'uploadJobResultFinalDocument']);

//Service Operations
Route::post('/cp/getCpServiceList', [CommonDataController::class, 'getServiceList']); // Done to V2
Route::post('/cp/addServiceInfo', [ServiceController::class, 'addServiceDetails']); // Done to V2
Route::post('/cp/getMyServiceDetailsList', [ServiceController::class, 'getMyServiceDetailsList']); // Done to V2
Route::post('/cp/updateServiceDetails', [ServiceController::class, 'updateServiceDetails']); // Done to V2
Route::post('/cp/deleteServiceDetails', [ServiceController::class, 'deleteServiceDetails']); // Done to V2

//Certificate Operations
Route::post('/cp/getMyCertificateDetailsList', [CertificateController::class, 'getMyCertificateDetailsList']); // Done to V2
Route::post('/cp/addCertificateInfo', [CertificateController::class, 'addCertificateDetails']); // Done to V2
Route::post('/cp/updateCertificateDetails', [CertificateController::class, 'updateCertificateDetails']); // Done to V2
Route::post('/cp/deleteCertificateDetails', [CertificateController::class, 'deleteCertificateDetails']); // Done to V2

//Subscription Operations
Route::post('/cp/uploadSubscriptionPayment', [PaymentSubscribeController::class, 'uploadSubscriptionPaymentData']);
Route::post('/cp/checkUserSubscription', [PaymentSubscribeController::class, 'checkUserSubscription']);
Route::post('/cp/checkIfUserCP', [PaymentSubscribeController::class, 'checkIfUserCP']);



//PDF View
// routes/web.php or routes/api.php

Route::get('/pdfviewer/{filename}', [CommonDataController::class, 'pdfView'])->where('filename', '.*'); // Done to V2


//Auth
Route::post("/auth/register", [AuthController::class, 'register']); // Done to V2
Route::post("/auth/login", [AuthController::class, 'login']); // Done to V2
Route::post("/auth/logout", [AuthController::class, 'logout']); // Done to V2



