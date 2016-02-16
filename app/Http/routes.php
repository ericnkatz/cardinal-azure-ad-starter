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

Route::get('login', ['as' => 'login', 'uses' => 'GraphController@login']);
Route::get('logout', ['as' => 'logout', 'uses' => 'Auth\AuthController@getLogout']);
Route::get('sign-on', ['as' => 'token', 'uses' => 'GraphController@token']);

Route::get('problem', function() {

	$message = [
		'Nothing to see here here!',
		'Ruh roh!',
		'Sorry for the interruption!'
	];
	shuffle($message);

	return view('error', ['message' => $message[0] ]);
})->name('problem');

Route::group(['middleware' => 'auth'], function() {
	Route::get('profile/refresh', ['as' => 'update_profile', 'uses' => 'PagesController@updateProfile']);
	Route::get('profile', ['as' => 'profile', 'uses' => 'PagesController@getProfile']);
});

Route::group(['as' => 'api::', 'prefix' => 'api'], function () {
    Route::get('{endpoint}/s/{skiptoken}',  ['uses' => 'GraphController@endpointWithPagination']);
	Route::get('{endpoint}/{item}',  ['as' => 'item', 'uses' => 'GraphController@endpointWithItem']);
	Route::get('{endpoint}',  ['as' => 'endpoint', 'uses' => 'GraphController@endpoint']);
});
