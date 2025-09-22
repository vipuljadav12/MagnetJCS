<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/GenerateApplicationData', 'module' => 'GenerateApplicationData', 'middleware' => ['web', 'auth', "permission"], 'namespace' => 'App\Modules\GenerateApplicationData\Controllers'], function () {

    Route::get('/', 'GenerateApplicationDataController@index');
    Route::post('/generate', 'GenerateApplicationDataController@generateData');
    Route::get('/generated', 'GenerateApplicationDataController@existingData');
    Route::get('/download/{id}',  'GenerateApplicationDataController@downloadFile');

    Route::get('/generate/individual/{id}', 'GenerateApplicationDataController@generateIndividual');

    Route::get('/contract/', 'GenerateApplicationDataController@generateContractIndex');
    Route::post('/contract/generate', 'GenerateApplicationDataController@generateContract');
    Route::get('/contract/generated', 'GenerateApplicationDataController@existingContractData');
    Route::get('/contract/download/{id}',  'GenerateApplicationDataController@downloadContractFile');

    Route::get('/generated/form', 'GenerateApplicationDataController@allGeneratedFormsIndex');
    Route::post('/generated/form/export', 'GenerateApplicationDataController@exportAllGeneratedFormsIndex');
});
