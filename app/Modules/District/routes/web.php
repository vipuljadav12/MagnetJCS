<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'admin/District','module' => 'District', 'middleware' => ['web','auth','super'], 'namespace' => 'App\Modules\District\Controllers'], function() {

    Route::get('/', 'DistrictController@index');
    Route::get('/create', 'DistrictController@create');
    Route::post('/store', 'DistrictController@store');
    Route::get('/edit/{id}', 'DistrictController@edit');
    Route::post('/update/{id}', 'DistrictController@update');
    Route::get('/delete/{id}', 'DistrictController@delete');
    Route::get('/trash', 'DistrictController@trash');
    Route::get('/restore/{id}', 'DistrictController@restore');

    Route::get('/uniqueurl', 'DistrictController@uniqueUrl');
    Route::get('/uniqueslug', 'DistrictController@uniqueSlug');
    Route::get('/status', 'DistrictController@status');

});
