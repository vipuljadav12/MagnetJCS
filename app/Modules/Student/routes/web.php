<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Student', 'module' => 'Student', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\Student\Controllers'], function () {

    Route::get('/', 'StudentController@index');
    Route::get('loadData', 'StudentController@loadData');

    Route::get('grade/{id}', 'StudentController@grade');
    Route::get('cdi', 'StudentController@cdi');
    Route::get('cdi_details/{id}', 'StudentController@cdiDetails');
});
