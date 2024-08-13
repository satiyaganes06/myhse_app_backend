<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Test\test;
use App\Http\Controllers\Auth\EmailController;
use App\Http\Controllers\Common\CommonDataController;
use App\Http\Controllers\Base\BaseController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get(
    '/test',
    [test::class, 'testttt']
);
// routes/web.php

Route::group(['prefix' => 'common'], function(){

    Route::get('/downloadFile/{filepath}', [BaseController::class, 'downloadFile']);
    Route::get('/imageViewer/{filepath}',[BaseController::class, 'imageViewer'])->name('image.show');

});

Route::get('/pdfviewer/{filename}', [CommonDataController::class, 'pdfView'])->where('filename', '.*');
Route::get('/emailVerification/{cpLoginID}', [EmailController::class, 'verifyEmailAddress']);

