<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/HeaderFooterConfig', 'module' => 'HeaderFooter', 'middleware' => ['web'], 'namespace' => 'App\Modules\HeaderFooter\Controllers'], function () {

	Route::get('/', 'HeaderFooterController@index');
	Route::post('store', 'HeaderFooterController@save');
});
