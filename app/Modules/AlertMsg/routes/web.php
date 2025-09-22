<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/AlertMsg', 'module' => 'AlertMsg', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\AlertMsg\Controllers'], function () {

    Route::get('/', 'AlertMsgController@index');
    Route::get('/create', 'AlertMsgController@create');
    Route::post('/store', 'AlertMsgController@store');
    Route::get('/edit/{id}', 'AlertMsgController@edit');
    Route::post('/update/{id}', 'AlertMsgController@update');
});
