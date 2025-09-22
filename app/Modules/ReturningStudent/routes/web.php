<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/ReturningStudent', 'module' => 'ReturningStudent', 'middleware' => ['web', 'auth', 'super'], 'namespace' => 'App\Modules\ReturningStudent\Controllers'], function () {

	Route::get('/', 'ReturningStudentController@index');
	Route::post('/store', 'ReturningStudentController@store');
	Route::get('/download/sample', 'ReturningStudentController@downloadSample');
});
