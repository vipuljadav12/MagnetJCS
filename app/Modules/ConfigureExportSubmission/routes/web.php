<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/ConfigureExportSubmission', 'module' => 'ConfigureExportSubmission', 'middleware' => ['web', 'auth', 'super'], 'namespace' => 'App\Modules\ConfigureExportSubmission\Controllers'], function () {

	Route::get('/', 'ConfigureExportSubmissionController@index');
	Route::post('/update', 'ConfigureExportSubmissionController@update');
});
