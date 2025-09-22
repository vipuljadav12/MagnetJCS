<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Permission', 'module' => 'Permission', 'middleware' => ['web', 'auth', 'super'], 'namespace' => 'App\Modules\Permission\Controllers'], function () {

	Route::get('/', 'PermissionController@index');
	Route::get('create', 'PermissionController@create');
	Route::post('/store', 'PermissionController@store');
	Route::get('edit/{id}', 'PermissionController@edit');
	Route::post('update/{id}', 'PermissionController@update');
	Route::get('trash/{id}', 'PermissionController@trash');

	Route::get('/trashindex', 'PermissionController@trashIndex');
	Route::get('/restore/{id}', 'PermissionController@restore');
	Route::get('/delete/{id}', 'PermissionController@delete');
});
