<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\UserController;

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

Route::post('resource/auth', [UserController::class, 'requestAdminToken']);

Route::get('/', function () {
    return view('welcome');
});


Route::get('/{vue_capture?}', function (Request $request) {
    return view('welcome');
})->where('vue_capture', '[\/\w\.-]*');
