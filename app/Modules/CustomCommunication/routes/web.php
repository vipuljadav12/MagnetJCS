<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/CustomCommunication', 'module' => 'CustomCommunication', 'middleware' => ['web', 'auth', 'permission'], 'namespace' => 'App\Modules\CustomCommunication\Controllers'], function () {

    Route::get('/', 'CustomCommunicationController@index');
    Route::get('/create', 'CustomCommunicationController@create');
    Route::post('/store', 'CustomCommunicationController@store');

    Route::get('/edit/{id}', 'CustomCommunicationController@edit');
    Route::post('/update/{id}', 'CustomCommunicationController@update');

    Route::get('/generated/{type}/{template_id}', 'CustomCommunicationController@existingData');
    //Route::get('/generated/{ty}/{template_id}', 'CustomCommunicationController@existingData');
    Route::get('/download/{id}',  'CustomCommunicationController@downloadFile');

    Route::post('/get/emails', 'CustomCommunicationController@fetchEmails');

    Route::get('/generate/individual/{id}', 'CustomCommunicationController@generateIndividual');
    Route::get('/status', 'CustomCommunicationController@status');

    Route::get('/preview/{id}', 'CustomCommunicationController@previewPDF');
    Route::get('/preview/email/{id}', 'CustomCommunicationController@previewEmail');
    Route::post('/Send/Test/Mail', 'CustomCommunicationController@sentPreviewEmail');

    Route::post('/individual', 'CustomCommunicationController@generate_letter_now_individual');
    Route::post('/individual/email', 'CustomCommunicationController@send_email_now_individual');
    Route::get('/email/individual/{id}', 'CustomCommunicationController@preview_email_now_individual');
});
