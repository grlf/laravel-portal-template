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

Route::get('/', function () {
    return view('welcome');
});


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