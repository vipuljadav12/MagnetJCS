<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/ZonedSchool', 'module' => 'ZonedSchool', 'middleware' => ['web'], 'namespace' => 'App\Modules\ZonedSchool\Controllers'], function () {
	Route::post('/search1', 'ZonedSchoolController@search1');
});
Route::group(['prefix' => 'admin/ZonedSchool', 'module' => 'ZonedSchool', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\ZonedSchool\Controllers'], function () {

	Route::get('/', 'ZonedSchoolController@index');
	Route::get('/getzonedschool', 'ZonedSchoolController@getZonedSchool');
	Route::post('/search', 'ZonedSchoolController@search');

	Route::get('/edit/{id}', 'ZonedSchoolController@edit');
	Route::post('/update/{id}', 'ZonedSchoolController@update');
	//	Route::post('/search1', 'ZonedSchoolController@search1');

	Route::get('/export', 'ZonedSchoolController@search');
	Route::get('/import', 'ZonedSchoolController@importZonedSchoolGet');
	Route::post('/import', 'ZonedSchoolController@importZonedSchool');
});
