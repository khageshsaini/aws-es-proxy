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

Route::get('login', 'Auth\AuthController@showLoginForm');
Route::post('login', 'Auth\AuthController@login');
Route::get('logout', 'Auth\AuthController@logout');

Route::get('/', function () {
	return \Redirect::to('/home');
});

Route::get('/home', [
	'middleware' => 'auth:aws',
	'uses' => function () {
		return view('home');
	} 
]);

Route::any('{path}', [
			'middleware' => ['auth:aws', 'iframe_only'],
			'uses' => 'ProxyController@__invoke'
		])->where('path', '.*');
