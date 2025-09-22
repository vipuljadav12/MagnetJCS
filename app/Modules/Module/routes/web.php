<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Module', 'module' => 'Module', 'middleware' => ['web', 'auth', 'super'], 'namespace' => 'App\Modules\Module\Controllers'], function () {

    Route::get('/', 'ModuleController@index');
    Route::get('create', 'ModuleController@create');
    Route::get('edit/{id}', 'ModuleController@edit');
    Route::get('trash', 'ModuleController@trash');
    Route::get('restore/{id}', 'ModuleController@restore');
    Route::get('delete/{id}', 'ModuleController@delete');
    Route::get('destroy/{id}', 'ModuleController@destroy');
    Route::post('store', 'ModuleController@store');
    Route::post('update', 'ModuleController@update');
});
