<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/DeniedSpace', 'module' => 'DeniedSpace', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\DeniedSpace\Controllers'], function () {

    Route::get('/', 'DeniedSpaceController@form_index');
    Route::post('/get/emails', 'DeniedSpaceController@fetchEmails');
    Route::get('/form/date/{form_id}', 'DeniedSpaceController@index');
    Route::post('/form/date/store/{form_id}', 'DeniedSpaceController@store_dates');
    Route::get('/form/communication/{form_id}', 'DeniedSpaceController@communication');

    Route::post('/form/communication/store/letter/{form_id}', 'DeniedSpaceController@store_letters');
    Route::post('/form/communication/store/email/{form_id}', 'DeniedSpaceController@store_emails');

    Route::get('/form/communication/preview/letter/{form_id}', 'DeniedSpaceController@preview_letter');
    Route::get('/form/communication/preview/email/{form_id}', 'DeniedSpaceController@preview_email');
    Route::post('/Send/Test/Mail', 'DeniedSpaceController@sendTestMail');
});

Route::group(['prefix' => 'DeniedSpace', 'module' => 'DeniedSpace', 'middleware' => ['web', 'super'], 'namespace' => 'App\Modules\DeniedSpace\Controllers'], function () {
    Route::get('/cron', 'DeniedSpaceController@cron_method');
});
