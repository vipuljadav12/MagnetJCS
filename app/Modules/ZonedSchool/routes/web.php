<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/ZonedSchool', 'module' => 'ZonedSchool', 'middleware' => ['web'], 'namespace' => 'App\Modules\ZonedSchool\Controllers'], function () {
	Route::post('/search1', 'AddressValidateController@getSuggestion');
	Route::get('/search2/{form_id}', 'AddressValidateController@getSuggestionCurrent');
});
Route::group(['prefix' => 'admin/ZonedSchool', 'module' => 'ZonedSchool', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\ZonedSchool\Controllers'], function () {

	// Route::get('/', 'ZonedSchoolController@masterindex');
	Route::get('/master/{master_address_id}', 'ZonedSchoolController@index');
	Route::get('/master/changeStatus/{master_address_id}', 'ZonedSchoolController@masterChangeStatus');

	Route::get('/overrideAddress', 'ZonedSchoolController@addressOverrideAddress');

	Route::get('/getzonedschool/{master_address_id}', 'ZonedSchoolController@getZonedSchool');
	Route::post('/search', 'ZonedSchoolController@search');

	Route::get('/create', 'ZonedSchoolController@create');
	Route::post('/store', 'ZonedSchoolController@store');

	Route::get('/edit/{id}', 'ZonedSchoolController@edit');
	Route::post('/update/{id}', 'ZonedSchoolController@update');
	//	Route::post('/search1', 'ZonedSchoolController@search1');

	Route::get('/export', 'ZonedSchoolController@search');
	Route::get('/import/{id}', 'ZonedSchoolController@importZonedSchoolGet');
	Route::post('/import', 'ZonedSchoolController@importZonedSchool');
});
