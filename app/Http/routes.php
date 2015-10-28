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


Route::get('sign-on', 'GraphController@token');

Route::get('sign-on/callback', function() {
	return $_REQUEST;
});


Route::get('api/{endpoint}/s/{skiptoken}', 'GraphController@endpointWithPagination');

Route::get('api/{endpoint}/{item}', 'GraphController@endpointWithItem');

Route::get('api/{endpoint}', 'GraphController@endpoint');

