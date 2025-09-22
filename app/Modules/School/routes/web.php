<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/School', 'module' => 'School', 'middleware' => ['web', 'auth', 'super'], 'namespace' => 'App\Modules\School\Controllers'], function () {

	Route::get('/', 'SchoolController@index');
	Route::get('create', 'SchoolController@create');
	Route::post('store', 'SchoolController@store');
	Route::get('edit/{id}', 'SchoolController@edit');
	Route::post('update/{id}', 'SchoolController@update');
	Route::get('checkunique', 'SchoolController@unique');
	Route::get('changestatus', 'SchoolController@changeStatus');
	Route::get('trash', 'SchoolController@trash');
	Route::get('delete/{id}', 'SchoolController@delete');
	Route::get('restore/{id}', 'SchoolController@restore');
});
