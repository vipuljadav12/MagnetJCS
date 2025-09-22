<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Waitlist','module' => 'Waitlist', 'middleware' => ['web','auth'], 'namespace' => 'App\Modules\Waitlist\Controllers'], function() {


    Route::get('/Process/Selection/validate/application/{application_id}', 'WaitlistController@validateApplication');
    Route::get('/', 'WaitlistController@application_index');
    Route::get('/Admin/Selection', 'WaitlistController@selection_application_index');
    Route::get('/Admin/Selection/{application_id}', 'WaitlistController@admin_run_selection');

    Route::get('/Process/Selection/{application_id}', 'WaitlistController@index');
    Route::post('/Process/Selection/{application_id}/store', 'WaitlistController@storeAllAvailability');

    Route::get('/Availability/Show/{application_id}', 'WaitlistController@show_all_availability');
    Route::get('/Availability/Show', 'WaitlistController@show_all_availability');

    Route::get('/Individual/Show/{form}', 'WaitlistController@show_all_individual');
    Route::get('/Individual/Show', 'WaitlistController@show_all_individual');

    Route::get('/Individual/Save/{program_id}/{grade}/{seats}', 'WaitlistController@saveIndividualAvailability');

    Route::get('/Individual/Show/Response/{program_id}', 'WaitlistController@individual_program_show');
    Route::post('/Individual/store', 'WaitlistController@storeIndividualAvailability');

    Route::get('/Population/{application_id}', 'WaitlistController@population_change_application');
    Route::get('/Population/{application_id}/{version}', 'WaitlistController@population_change_application');

    Route::get('/Population/Version/{application_id}/{version}', 'WaitlistController@population_change_version');

    //Route::get('/Population/Version/{version}', 'WaitlistController@population_change_version');
    Route::get('/Submission/Result/{application_id}/{version}', 'WaitlistController@submissions_results_application');
    Route::get('/Submission/Result/{application_id}', 'WaitlistController@submissions_results_application');
    Route::get('/Submission/Result/Version/{application_id}/{version}', 'WaitlistController@submissions_results_version');


#    Route::get('/SeatsStatus/Version/{application_id}/{value}', 'WaitlistController@seatStatusVersion');
    Route::get('/SeatsStatus/Version/{value}', 'WaitlistController@seatStatusVersion');
    Route::get('/Submission/Result', 'WaitlistController@submissions_result');

    Route::post('/Accept/list/{application_id}', 'WaitlistController@selection_accept');
    Route::post('/Revert/list', 'WaitlistController@selection_revert');

    Route::post('/Send/Test/Mail', 'WaitlistEditCommunicationController@send_test_email');

    //Route::get('/Revert/list', 'ProcessSelectionController@selection_revert');

    Route::get('/EditCommunication/','WaitlistEditCommunicationController@application_index');
    Route::get('/EditCommunication/application/{id}','WaitlistEditCommunicationController@index');
    Route::get('/EditCommunication/application/{id}/{status}','WaitlistEditCommunicationController@index');
    Route::post('/EditCommunication/get/emails','WaitlistEditCommunicationController@fetchEmails');

    Route::get('/EditCommunication/download/{id}',  'WaitlistEditCommunicationController@downloadFile');

    Route::get('/EditCommunication/preview/letter/{status}', 'WaitlistEditCommunicationController@previewPDF');
    Route::get('/EditCommunication/preview/letter/{status}/{application_id}', 'WaitlistEditCommunicationController@previewPDF');

    //Route::get('/EditCommunication/preview/{status}', 'WaitlistEditCommunicationController@previewEmail');
    Route::get('/EditCommunication/preview/email/{status}/{application_id}', 'WaitlistEditCommunicationController@previewEmail');
    Route::post('/EditCommunication/Send/Test/Mail','WaitlistEditCommunicationController@sendTestMail');




    Route::get('/EditCommunication/{status}','WaitlistEditCommunicationController@index');
    Route::post('/EditCommunication/store/letter','WaitlistEditCommunicationController@storeLetter');
    Route::post('/EditCommunication/store/email','WaitlistEditCommunicationController@storeEmail');
    Route::get('/EditCommunication/communicationPDF/{form_name}','WaitlistEditCommunicationController@generatePDF');
    Route::get('/EditCommunication/communicationEmail/{form_name}','WaitlistEditCommunicationController@generateEmail');
    
    Route::get('/EditCommunication/lettersLog','WaitlistEditCommunicationController@lettersLog');
    Route::get('/EditCommunication/deletePDF/{id}','WaitlistEditCommunicationController@deleteLettersLog');

    Route::get('/EditCommunication/emailsLog','WaitlistEditCommunicationController@emailsLog');
    Route::get('/EditCommunication/deleteemailsLog/{id}','WaitlistEditCommunicationController@deleteEmailsLog');


});