<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/StudentSearch', 'module' => 'StudentSearch', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\StudentSearch\Controllers'], function () {

	Route::get('/', 'StudentSearchController@index');
	Route::post('/data', 'StudentSearchController@data');
	Route::post('/data/update', 'StudentSearchController@updateData');
});
