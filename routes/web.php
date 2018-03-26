<?php

Auth::routes();

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

Route::get('invoice/{invoiceId}/receipt', [
    'as' => 'invoice.receipt',
    'uses' => 'InvoiceController@receipt'
]);

Route::resource('invoice', 'InvoiceController');


Route::get('time/suggestions/project/{projectId?}', [
    'as' => 'time.suggestByProject',
    'uses' => 'TimeController@suggestByProject'
]);

Route::post('time/finish', [
    'as' => 'time.finish',
    'uses' => 'TimeController@finish',
]);

Route::resource('time', 'TimeController');
Route::resource('estimate', 'EstimateController');

Route::redirect('/', '/login', 301);
