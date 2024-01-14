<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Test\test;
use App\Http\Controllers\Auth\EmailController;
use App\Http\Controllers\Common\CommonDataController;
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
    [test::class, 'test']
);
// routes/web.php


Route::get('/pdfviewer/{filename}', [CommonDataController::class, 'pdfView'])->where('filename', '.*');
Route::get('/emailVerification/{cpLoginID}', [EmailController::class, 'verifyEmailAddress']);

