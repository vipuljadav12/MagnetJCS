<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Priority', 'module' => 'Priority', 'middleware' => ['web', 'auth', 'super'], 'namespace' => 'App\Modules\Priority\Controllers'], function () {

	Route::get('/', 'PriorityController@index');
	Route::get('/create', 'PriorityController@create');
	Route::post('/store', 'PriorityController@store');
	Route::get('/edit/{id}', 'PriorityController@edit');
	Route::post('/update', 'PriorityController@update');
	Route::get('/delete/{id}', 'PriorityController@delete');
	Route::get('/trash', 'PriorityController@showTrash');
	Route::get('/restore/{id}', 'PriorityController@restoreFromTrash');
	Route::post('/checkname', 'PriorityController@checkName');
	Route::post('/updatestatus', 'PriorityController@updateStatus');
});
