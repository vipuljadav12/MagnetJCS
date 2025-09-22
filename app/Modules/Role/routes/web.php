<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Role','module' => 'Role', 'middleware' => ['web','auth','super'], 'namespace' => 'App\Modules\Role\Controllers'], function() {

		Route::get('/', 'RoleController@index');
	    Route::get('create', 'RoleController@create');
	    Route::post('store', 'RoleController@store');
	    Route::get('edit/{id}', 'RoleController@edit');
	    Route::post('update', 'RoleController@update');
	    Route::get('trash/{id}', 'RoleController@trash');
	    Route::get('status_change/{id}', 'RoleController@statusChange');
	    Route::get('/roles/permission','RoleController@getRolesPermssion');

	    Route::get('/trashindex','RoleController@trashIndex');
		Route::get('/restore/{id}','RoleController@restore');
	    Route::get('/delete/{id}','RoleController@delete');
});
