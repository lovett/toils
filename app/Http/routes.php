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

// Dashboard
Route::get('dashboard', [
    'middleware' => 'auth',
    'uses' => 'DashboardController@index'
]);

// Authentication
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// Clients
Route::group(['middleware' => 'auth', 'as' => 'clients::', 'prefix' => 'clients'], function () {
    Route::get('/', [
        'as' => 'index',
        'uses' => 'ClientController@index',
    ]);

    Route::get('create', [
        'as' => 'create',
        'uses' => 'ClientController@create',
    ]);

    Route::post('store', [
        'as' => 'store',
        'uses' => 'ClientController@store',
    ]);

    Route::get('show/{id}', [
        'as' => 'show',
        'uses' => 'ClientController@show',
    ]);

    Route::get('edit/{id}', [
        'as' => 'edit',
        'uses' => 'ClientController@edit',
    ]);

    Route::post('update/{id}', [
        'as' => 'update',
        'uses' => 'ClientController@update',
    ]);

    Route::post('destroy/{id}', [
        'as' => 'destroy',
        'uses' => 'ClientController@destroy',
    ]);

});