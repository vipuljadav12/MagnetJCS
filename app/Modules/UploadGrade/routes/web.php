<?php

//Route::group(['prefix'=>'admin/upload','module' => 'UploadGradeCdi', 'middleware' => ['web','auth','permission'], 'namespace' => 'App\Modules\UploadGradeCdi\Controllers'], function() {

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'upload', 'module' => 'UploadGrade', 'middleware' => ['web'], 'namespace' => 'App\Modules\UploadGrade\Controllers'], function () {

    Route::get('/{id}/grade', 'UploadGradeController@index');
    Route::post('/grade/checkSubmissionId', 'UploadGradeController@checkSubmissionId');
    Route::post('/grade/uploadFiles', 'UploadGradeController@uploadFiles');
});
