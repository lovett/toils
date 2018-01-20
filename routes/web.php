<?php

Auth::routes();

Route::get('/dashboard', 'DashboardController@index')->name('dashboard');

Route::resource('time', 'TimeController');
Route::resource('invoice', 'InvoiceController');
Route::resource('project', 'ProjectController');
Route::resource('client', 'ClientController');

Route::get(
    'invoice/suggestions/project/{projectId?}',
    [
        'as' => 'invoice.suggestByProject',
        'uses' => 'InvoiceController@suggestByProject'
    ]
);


Route::redirect('/', '/login', 301);
