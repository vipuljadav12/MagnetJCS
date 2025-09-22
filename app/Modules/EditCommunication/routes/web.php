<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/EditCommunication', 'module' => 'EditCommunication', 'middleware' => ['web', 'auth', 'permission'], 'namespace' => 'App\Modules\EditCommunication\Controllers'], function () {

    Route::get('/', 'EditCommunicationController@application_index');

    Route::post('/get/emails', 'EditCommunicationController@fetchEmails');
    Route::get('/application/{id}', 'EditCommunicationController@index');

    Route::get('/download/{id}',  'EditCommunicationController@downloadFile');

    Route::get('/preview/{status}', 'EditCommunicationController@previewPDF');
    Route::get('/preview/{status}/{application_id}', 'EditCommunicationController@previewPDF');
    Route::get('/preview/email/{status}', 'EditCommunicationController@previewEmail');
    Route::get('/preview/email/{status}/{application_id}', 'EditCommunicationController@previewEmail');


    Route::get('/application/{id}/{status}', 'EditCommunicationController@index');
    Route::post('/store/letter', 'EditCommunicationController@storeLetter');
    Route::post('/store/email', 'EditCommunicationController@storeEmail');
    Route::get('/communicationPDF/{form_name}', 'EditCommunicationController@generatePDF');
    Route::get('/communicationEmail/{form_name}', 'EditCommunicationController@generateEmail');

    Route::get('/lettersLog', 'EditCommunicationController@lettersLog');
    Route::get('/deletePDF/{id}', 'EditCommunicationController@deleteLettersLog');

    Route::get('/emailsLog', 'EditCommunicationController@emailsLog');
    Route::get('/deleteemailsLog/{id}', 'EditCommunicationController@deleteEmailsLog');

    Route::post('/Send/Test/Mail', 'EditCommunicationController@sendTestMail');
});
