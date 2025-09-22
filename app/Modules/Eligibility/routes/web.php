<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Eligibility', 'module' => 'Eligibility', 'middleware' => ['web', 'auth', 'super'], 'namespace' => 'App\Modules\Eligibility\Controllers'], function () {

    Route::get('/', 'EligibilityController@index');
    Route::get('/create', 'EligibilityController@create');
    Route::get('/getTemplateHtml/{id}', 'EligibilityController@getTemplateHtml');
    Route::post('/store', 'EligibilityController@store');
    Route::get('/edit/{eligibility}', 'EligibilityController@edit');
    Route::post('/update/{id}', 'EligibilityController@update');
    Route::get('/delete/{id}', 'EligibilityController@delete');
    Route::get('/trash', 'EligibilityController@trash');
    Route::get('/restore/{id}', 'EligibilityController@restore');
    Route::get('/view/{id}', 'EligibilityController@view');
    Route::get('/status', 'EligibilityController@status');
    Route::post('/checkEligiblityName', 'EligibilityController@checkEligiblityUnique');

    Route::get('/subjectManagement/{id?}', 'SubjectManagementController@index');
    Route::post('/updateSubjectManagement', 'SubjectManagementController@updateSubjectManagement');


    Route::group(['prefix' => "/set"], function () {
        Route::get("/", "SetEligibiltyController@index");
    });

    Route::get('/edit/grade/subject', 'EligibilityController@editGradeSubject');
    Route::post('/store/grade/subject', 'EligibilityController@storeGradeSubject');
});

/*Route::group([],function())*/