<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/SetEligibility', 'module' => 'SetEligibility', 'middleware' => ['web', 'auth', 'permission'], 'namespace' => 'App\Modules\SetEligibility\Controllers'], function () {

    Route::get('/edit/{id}', 'SetEligibilityController@edit');
    Route::get('/edit/{id}/{application_id}', 'SetEligibilityController@edit');

    Route::post("/update/{id}", "SetEligibilityController@update");

    Route::get('/extra_values', 'SetEligibilityController@extra_values');
    Route::post('/extra_values/save', 'SetEligibilityController@extra_value_save');

    Route::get('/configurations', 'SetEligibilityController@configurations');
    Route::post('/configurations/save', 'SetEligibilityController@configurations_save');

    Route::resource('/', 'SetEligibilityController');
});
