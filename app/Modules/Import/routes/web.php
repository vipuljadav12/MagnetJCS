<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/import', 'module' => 'Import', 'middleware' => ['web', 'auth', 'super'], 'namespace' => 'App\Modules\Import\Controllers'], function () {

    Route::get('/gifted_students', 'ImportController@importGiftedStudents');
    Route::post('/gifted_students/save', 'ImportController@saveGiftedStudents');

    Route::get('/agt_nch', 'ImportController@importAGTNewCentury');
    Route::post('/agt_nch/save', 'ImportController@storeImportAGTNewCentury');

    Route::get('/non_jcs_students', 'ImportController@importNonJCSStudents');
    Route::post('/non_jcs_students/save', 'ImportController@storeNonJCSStudent');
    Route::get('/non_jcs_students/download/sample', 'ImportController@downloadSampleNonJCSStudent');

    Route::get('/zoned_school_override', 'ImportController@importNonJCSStudents');
    Route::post('/zoned_school_override/save', 'ImportController@storeNonJCSStudent');
    Route::get('/zoned_school_override/download/sample', 'ImportController@downloadSampleNonJCSStudent');

    Route::get('/exceptionality', 'ImportController@importExceptionality');
    Route::post('/exceptionality/save', 'ImportController@saveExceptionality');

    Route::get('/submissions', 'ImportController@importSubmissions');
    Route::get('/submissions/sample', 'ImportController@importSubmissionsSample');
    Route::post('/submissions/save', 'ImportController@storeImportSubmissions');
});

/*admin/import/gifted_students*/