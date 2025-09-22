<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/LateSubmission', 'module' => 'LateSubmission', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\LateSubmission\Controllers'], function () {

    Route::get('/', 'LateSubmissionController@application_index');
    Route::get('/Process/Selection/validate/application/{application_id}', 'LateSubmissionController@validateApplication');
    Route::get('/Admin/Selection', 'LateSubmissionController@selection_application_index');
    Route::get('/Admin/Selection/{application_id}', 'LateSubmissionController@admin_run_selection');

    Route::get('/Process/Selection/{application_id}', 'LateSubmissionController@index');
    Route::post('/Process/Selection/{application_id}/store', 'LateSubmissionController@storeAllAvailability');

    Route::post('/Availability/store', 'LateSubmissionController@storeAllAvailability');

    Route::get('/Availability/Show/{form}', 'LateSubmissionController@show_all_availability');
    Route::get('/Availability/Show', 'LateSubmissionController@show_all_availability');

    Route::get('/Population/{application_id}', 'LateSubmissionController@population_change_application');
    Route::get('/Population/{application_id}/{version}', 'LateSubmissionController@population_change_application');
    Route::get('/Population/Version/{application_id}/{version}', 'LateSubmissionController@population_change_version');

    Route::get('/Submission/Result/{application_id}/{version}', 'LateSubmissionController@submissions_results_application');
    Route::get('/Submission/Result/{application_id}', 'LateSubmissionController@submissions_results_application');
    Route::get('/Submission/Result/Version/{application_id}/{version}', 'LateSubmissionController@submissions_results_version');

    Route::get('/Submission/SeatsStatus/Version/{value}', 'LateSubmissionController@seatStatusVersion');
    Route::get('/Submission/Result', 'LateSubmissionController@submissions_result');
    Route::get('/SeatsStatus/Version/{value}', 'LateSubmissionController@seatStatusVersion');


    Route::post('/Accept/list/{application_id}', 'LateSubmissionController@selection_accept');
    Route::post('/Revert/list', 'LateSubmissionController@selection_revert');

    Route::post('/Send/Test/Mail', 'LateSubmissionEditCommunicationController@send_test_email');

    //Route::get('/Revert/list', 'ProcessSelectionController@selection_revert');

    Route::get('/EditCommunication/', 'LateSubmissionEditCommunicationController@application_index');
    Route::get('/EditCommunication/application/{id}', 'LateSubmissionEditCommunicationController@index');
    Route::get('/EditCommunication/application/{id}/{status}', 'LateSubmissionEditCommunicationController@index');
    Route::post('/EditCommunication/get/emails', 'LateSubmissionEditCommunicationController@fetchEmails');

    Route::get('/EditCommunication/download/{id}',  'LateSubmissionEditCommunicationController@downloadFile');

    Route::get('/EditCommunication/preview/letter/{status}', 'LateSubmissionEditCommunicationController@previewPDF');
    Route::get('/EditCommunication/preview/letter/{status}/{application_id}', 'LateSubmissionEditCommunicationController@previewPDF');

    //Route::get('/EditCommunication/preview/{status}', 'LateSubmissionEditCommunicationController@previewEmail');
    Route::get('/EditCommunication/preview/email/{status}/{application_id}', 'LateSubmissionEditCommunicationController@previewEmail');
    Route::post('/EditCommunication/Send/Test/Mail', 'LateSubmissionEditCommunicationController@sendTestMail');




    Route::get('/EditCommunication/{status}', 'LateSubmissionEditCommunicationController@index');
    Route::post('/EditCommunication/store/letter', 'LateSubmissionEditCommunicationController@storeLetter');
    Route::post('/EditCommunication/store/email', 'LateSubmissionEditCommunicationController@storeEmail');
    Route::get('/EditCommunication/communicationPDF/{form_name}', 'LateSubmissionEditCommunicationController@generatePDF');
    Route::get('/EditCommunication/communicationEmail/{form_name}', 'LateSubmissionEditCommunicationController@generateEmail');

    Route::get('/EditCommunication/lettersLog', 'LateSubmissionEditCommunicationController@lettersLog');
    Route::get('/EditCommunication/deletePDF/{id}', 'LateSubmissionEditCommunicationController@deleteLettersLog');

    Route::get('/EditCommunication/emailsLog', 'LateSubmissionEditCommunicationController@emailsLog');
    Route::get('/EditCommunication/deleteemailsLog/{id}', 'LateSubmissionEditCommunicationController@deleteEmailsLog');
});

Route::group(['prefix' => 'admin/LateSubmission/Preliminary/Processing', 'module' => 'LateSubmission', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\LateSubmission\Controllers'], function () {

    Route::get('/', 'PreliminaryLateController@index');
    Route::post('/rollback', 'PreliminaryLateController@roll_back_submissions');

    Route::post('/calculate', 'PreliminaryLateController@calculate');
    Route::post('/commit', 'PreliminaryLateController@commit_score');
});
