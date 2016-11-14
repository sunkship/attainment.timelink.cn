<?php

/*
|--------------------------------------------------------------------------
| Routes File
|--------------------------------------------------------------------------
|
| Here is where you will register all of the routes in an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(array('middleware'=>['web'],'namespace'=>'Admin'),function(){
    Route::get('/', function () {
        if (Auth::guest()) return view('auth.login');
        else redirect('/wall');
    });
    Route::get('login','AuthController@getLogin');
    Route::post('login','AuthController@postLogin');
    Route::get('logout','AuthController@getLogout');
    Route::get('password','AuthController@getPassword');
    Route::post('password','AuthController@postPassword');
    Route::get('captcha/{config?}', '\Mews\Captcha\CaptchaController@getCaptcha');
    Route::get('/WechatLogin','WechatController@loginAction');
    Route::get('/admin','AuthController@getAdminLogin');
});

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| This route group applies the "web" middleware group to every route
| it contains. The "web" middleware group is defined in your HTTP
| kernel and includes session state, CSRF protection, and more.
|
*/

Route::group(array('middleware' => ['web','auth'],'namespace'=>'Admin'), function () {
    Route::get('/wall','WallController@getWall');
    Route::get('/table','WallController@getTable');
    Route::get('/target','WallController@getTarget');
    Route::post('/write','WallController@postWrite');
    Route::get('/get_code','WallController@receiveWechatCode');
});

