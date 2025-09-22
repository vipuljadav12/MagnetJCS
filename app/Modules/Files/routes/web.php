<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Files', 'module' => 'Files', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\Files\Controllers'], function () {

	Route::get('/', 'FilesController@index');
	Route::get('/create', 'FilesController@create');
	Route::post('/store', 'FilesController@store');
	Route::get('/edit/{id}', 'FilesController@edit');
	Route::post('/sort_update', 'FilesController@sortUpdate');

	Route::post('/update/{id}', 'FilesController@update');
	Route::get('/delete/{id}', 'FilesController@delete');

	Route::get('/status_update', 'FilesController@statusUpdate');
	Route::get('/unique_title', 'FilesController@uniqueTitle');
});
