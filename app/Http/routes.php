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

Route::get('/', 'PagesController@index');

Route::get('login', 'GraphController@login');
Route::get('logout', 'Auth\AuthController@getLogout');
Route::get('sign-on', 'GraphController@token');


Route::group(['prefix' => 'api'], function () {
    Route::get('{endpoint}/s/{skiptoken}', 'GraphController@endpointWithPagination');
	Route::get('{endpoint}/{item}', 'GraphController@endpointWithItem');
	Route::get('{endpoint}', 'GraphController@endpoint');
});


