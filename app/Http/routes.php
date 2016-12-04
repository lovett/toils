
<?php
/**
 * Application routes.
 */

// Homepage.
Route::get(
    '/',
    function () {
        return redirect()->route('dashboard');
    }
);

// Dashboard.
Route::get(
    'dashboard',
    [
        'as' => 'dashboard',
        'uses' => 'DashboardController@index',
    ]
);

// Authentication.
Route::get(
    'auth/login',
    [
        'as' => 'login',
        'uses' => 'Auth\AuthController@getLogin',
    ]
);

Route::post(
    'auth/login',
    [
        'as' => 'postLogin',
        'uses' => 'Auth\AuthController@postLogin',
    ]
);

Route::get(
    'auth/logout',
    [
        'as' => 'logout',
        'uses' => 'Auth\AuthController@getLogout',
    ]
);

// Registration.
Route::get(
    'auth/register',
    [
        'as' => 'register',
        'uses' => 'Auth\AuthController@getRegister',
    ]
);

Route::post(
    'auth/register',
    [
        'as' => 'postRegister',
        'uses' => 'Auth\AuthController@postRegister',
    ]
);

// Clients.
Route::resource('client', 'ClientController', ['middleware' => 'auth']);
Route::get('clients', ['as' => 'clients', 'uses' => 'ClientController@index']);

// Projects.
Route::resource('project', 'ProjectController');
Route::get(
    'projects',
    [
        'as' => 'projects',
        'uses' => 'ProjectController@index',
    ]
);

// Time.
Route::resource('time', 'TimeController', ['middleware' => 'auth']);

// Invoices.
Route::resource('invoice', 'InvoiceController', ['middlware' => 'auth']);
Route::get(
    'invocies',
    [
        'as' => 'invoices',
        'uses' => 'InvoiceController@index',
    ]
);