<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/DistrictConfiguration', 'module' => 'DistrictConfiguration', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\DistrictConfiguration\Controllers'], function () {

	Route::get('/', 'DistrictConfigurationController@index');
	Route::post('/store', 'DistrictConfigurationController@store');
	Route::get('/student/search', 'DistrictConfigurationController@searchIndex');

	Route::get('/edit_text', 'DistrictConfigurationController@edit_text');
	Route::post('/saveEditText', 'DistrictConfigurationController@saveEditText');

	Route::get('/edit_waitlist_text', 'DistrictConfigurationController@edit_waitlist_text');
	Route::post('/saveWaitlistEditText', 'DistrictConfigurationController@saveWaitlistEditText');

	Route::get('/edit_email', 'DistrictConfigurationController@edit_email');
	Route::post('/saveEditEmail', 'DistrictConfigurationController@saveEditEmail');
	Route::get('preview/thanks/email/{email_type}', 'DistrictConfigurationController@previewThanksEmail');

	Route::get('/edit_waitlist_email', 'DistrictConfigurationController@edit_waitlist_email');
	Route::post('/saveWaitlistEditEmail', 'DistrictConfigurationController@saveWaitlistEditEmail');
	Route::get('preview/thanks/email/waitlist/{email_type}', 'DistrictConfigurationController@previewWaitlistThanksEmail');

	Route::get('/edit_late_submission_email', 'DistrictConfigurationController@edit_late_submission_email');
	Route::post('/saveLateSubmissionEditEmail', 'DistrictConfigurationController@saveLateSubmissionEditEmail');
	Route::get('preview/thanks/email/late_submission/{email_type}', 'DistrictConfigurationController@previewLateSubmissionThanksEmail');

	Route::get('/edit_late_submission_text', 'DistrictConfigurationController@edit_late_submission_text');
	Route::post('/saveLateSubmissionEditText', 'DistrictConfigurationController@saveSubmissionEditText');

	Route::post('Send/Test/Mail', 'DistrictConfigurationController@sendTestMail');
});
