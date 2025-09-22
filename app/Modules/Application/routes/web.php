<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Application', 'module' => 'Application', 'middleware' => ['web', 'auth', 'permission'], 'namespace' => 'App\Modules\Application\Controllers'], function () {

    Route::get('/', 'ApplicationController@index');
    Route::get('/create', 'ApplicationController@create');
    Route::get('/start_end_date', 'ApplicationController@start_end_date');
    Route::post('/store', 'ApplicationController@store');
    Route::get('/edit/{id}', 'ApplicationController@edit');
    Route::post('/update/{id}', 'ApplicationController@update');
    Route::get('/delete/{id}', 'ApplicationController@delete');
    Route::get('/trash', 'ApplicationController@trash');
    Route::get('/restore/{id}', 'ApplicationController@restore');

    Route::get('/status', 'ApplicationController@status');

    Route::get('/preview/{type}/{id}', 'ApplicationController@previewSection');
    Route::post('Send/Test/Mail', 'ApplicationController@sendTestMail');
});
