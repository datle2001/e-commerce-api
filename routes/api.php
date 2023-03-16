<?php

use App\Http\Controllers\StripePaymentController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
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
Route::controller(StripePaymentController::class)->group(function(){
    Route::get('stripe', 'stripe');
    Route::post('stripe', 'stripePost')->name('stripe.post');
});

Route::group(['prefix' => 'v1'], function () {
    Route::resource('products', ProductController::class);
    Route::resource('user', UserController::class);

    Route::post('login', function(Request $request) : object {
        $email = $request->input('email');
        $password = $request->input('password');
        $user = DB::table('users')
            ->select(['id', 'username', 'password' ,'email'])
            ->where('email', '=', $email, 'and', 'password', '=', $password)
            ->get()->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        return $user;
    });

    Route::post('register', function(Request $request) : object {
        $email = $request->input('email');
        $password = $request->input('password');
        $username = $request->input('username');
        $count = DB::table('users')->where('email','='. $email)->count();

        if($count > 0){
            return response("Email already used", 401);
        } 
        $data = [
            "email" => $email,
            "password" => $password,
            "username" => $username
        ];

        $data['id'] = DB::table('users')->insertGetId($data);
         
        return (object) $data;
    });
});
