<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Enrollment', 'module' => 'Enrollment', 'middleware' => ['web', 'auth', 'permission'], 'namespace' => 'App\Modules\Enrollment\Controllers'], function () {

	// Route::resource('Enrollment', 'EnrollmentController');
	Route::get('/', 'EnrollmentController@index');
	Route::get('/create', 'EnrollmentController@create');
	Route::post('/store', 'EnrollmentController@store');
	Route::get('/edit/{id}', 'EnrollmentController@edit');
	Route::post('/update/{id}', 'EnrollmentController@update');
	Route::get('/trash', 'EnrollmentController@trash');
	Route::get('/move_to_trash/{id}', 'EnrollmentController@moveToTrash');
	Route::get('/delete/{id}', 'EnrollmentController@delete');
	Route::get('/restore/{id}', 'EnrollmentController@restore');
	Route::post('/update_status', 'EnrollmentController@updateStatus');

	Route::get('/adm_data/import/{id}', 'EnrollmentController@import');
	Route::get('/import/template', 'EnrollmentController@importTemplate');
	Route::post('/import/store/{id}', 'EnrollmentController@storeImport');
});
