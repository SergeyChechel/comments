<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\ReplyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/', function () {
    return redirect()->route('comments.index');
});

Route::resource('comments', CommentController::class)
->only(['index','show', 'store','create']);

Route::resource('comments.replies', ReplyController::class)
->only(['index','show', 'store','create']);

Route::get('/refresh-captcha', 'App\Http\Controllers\CaptchaController@refresh')->name('refresh-captcha');

