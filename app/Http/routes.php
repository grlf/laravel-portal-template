<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

// Auth Login/Logout routes
Route::get('/', 'Auth\AuthController@getLogin');
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Password reset link request routes
Route::get('password/email', 'Auth\PasswordController@getEmail');
Route::post('password/email', 'Auth\PasswordController@postEmail');

// Password reset routes
Route::get('password/reset/{token}', 'Auth\PasswordController@getReset');
Route::post('password/reset', 'Auth\PasswordController@postReset');

// Registration routes
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// Profile Routes
Route::get('profile', 'Auth\ProfileController@viewProfile');


// API ROUTES ==================================
Route::group(array('prefix' => 'api/v1/'), function () {


    //Dropdowns
    Route::post('dd/list', 'DropdownController@index');
    Route::patch('dd/sort', 'DropdownController@sort');
    Route::post('dd', 'DropdownController@store');
    Route::delete('dd', 'DropdownController@destroy');


    // Files
    Route::resource('files', 'FileController', array(
        'only' => array('store')
    ));
    Route::post('files/delete', 'FileController@deleteMultiple');


});