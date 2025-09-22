<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/ManualSelection', 'module' => 'ManualSelection', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\ManualSelection\Controllers'], function () {

	Route::get('/', 'ManualSelectionController@index');

	Route::get('/pre_req', 'ManualSelectionController@pre_req_index');
	Route::get('/pre_req/create', 'ManualSelectionController@create');
	Route::post('/pre_req/store', 'ManualSelectionController@store');

	Route::get('/pre_req/edit/{id}', 'ManualSelectionController@edit');
	Route::post('/pre_req/update/{id}', 'ManualSelectionController@update');
});
Route::group(['prefix' => 'admin/Reports', 'module' => 'ManualSelection', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\ManualSelection\Controllers'], function () {

	Route::get('/missing/{id}/program_pre_req', 'ManualSelectionReportController@missingProgramPrerequisite');
	Route::get('/missing/{id}/program_pre_req/response', 'ManualSelectionReportController@missingProgramPrerequisiteResponse');
	Route::get('/missing/{id}/program_no_pre_req/response', 'ManualSelectionReportController@missingProgramNoPrerequisiteResponse');
});
