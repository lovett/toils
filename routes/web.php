<?php

Auth::routes();

Route::resource('time', 'TimeController');
Route::resource('invoice', 'InvoiceController');
Route::resource('project', 'ProjectController');
Route::resource('client', 'ClientController');

Route::get('/dashboard', [
    'as' => 'dashboard',
    'uses' => 'DashboardController@index',
]);

Route::get('invoice/suggestions/project/{projectId?}', [
    'as' => 'invoice.suggestByProject',
    'uses' => 'InvoiceController@suggestByProject'
]);

Route::get('time/suggestions/project/{projectId?}', [
    'as' => 'time.suggestByProject',
    'uses' => 'TimeController@suggestByProject'
]);

Route::redirect('/', '/login', 301);
