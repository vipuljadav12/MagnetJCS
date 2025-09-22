<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/GiftedStudents', 'module' => 'GiftedStudents', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\GiftedStudents\Controllers'], function () {

    Route::get('/', 'GiftedStudentsController@index');
    Route::get('/create', 'GiftedStudentsController@create');
    Route::post('/store', 'GiftedStudentsController@store');

    Route::get('/import', 'GiftedStudentsController@importGiftedStudent');
    Route::post('/import/save', 'GiftedStudentsController@saveGiftedStudent');

    Route::get('/delete/{id}', 'GiftedStudentsController@destroy');
});
