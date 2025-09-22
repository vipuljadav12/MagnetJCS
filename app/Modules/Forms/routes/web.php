<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/forms', 'module' => 'Form', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\Form\Controllers'], function () {

    Route::get('{id}', 'FormController@create');
    Route::post('store', 'FormController@store');
    Route::get('edit/{id}', 'FormController@edit');
    Route::post('store/style', 'FormController@setFormStyle');
    Route::get('preview/{id}', 'FormController@preview');
});
