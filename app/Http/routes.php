<?php
/**
 * Application routes
 */

// Homepage.
Route::get(
    '/',
    function () {
        return view('welcome');
    }
);

// Dashboard.
Route::get(
    'dashboard',
    [
        'middleware' => 'auth',
        'uses' => 'DashboardController@index'
    ]
);

// Authentication.
Route::get('auth/login', 'Auth\AuthController@getLogin');
Route::post('auth/login', 'Auth\AuthController@postLogin');
Route::get('auth/logout', 'Auth\AuthController@getLogout');

// Registration.
Route::get('auth/register', 'Auth\AuthController@getRegister');
Route::post('auth/register', 'Auth\AuthController@postRegister');

// Clients.
Route::resource('client', 'ClientController', ['middleware' => 'auth']);
Route::get('clients', ['as' => 'clients', 'uses' => 'ClientController@index']);

// Projects.
Route::resource('project', 'ProjectController');
Route::get(
    'projects',
    [
        'as' => 'projects',
        'uses' => 'ProjectController@index'
    ]
);

// Time.
Route::resource('time', 'TimeController', ['middleware' => 'auth']);
