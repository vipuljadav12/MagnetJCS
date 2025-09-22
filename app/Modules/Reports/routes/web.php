<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Reports', 'module' => 'Reports', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\Reports\Controllers'], function () {

  Route::get('/missing/{id}/grade', 'ReportsController@missingGradeMain');
  Route::get('/courtreport', 'ReportsController@court_report');
  Route::get('/missing/{id}/{lresponse}/grade', 'ReportsController@missingGradeMain');
  Route::get('/missing/{id}/grade/response', 'ReportsController@missingGrade');
  Route::get('/missing/{id}/{lresponse}/grade/response', 'ReportsController@missingGrade');

  /*Route::get('/missing/{id}/cdi', 'ReportsController@missingCDIMain');
    Route::get('/missing/{id}/cdi/response', 'ReportsController@missingCDI');
    Route::get('/missing/{id}/{lresponse}/cdi/response', 'ReportsController@missingCDI');
    Route::get('/missing/{id}/{lresponse}/cdi/response', 'ReportsController@missingCDI');
    Route::get('/missing/{id}/mcpss', 'ReportsController@mcpssSubmissions');

    */
  Route::get('/missing/{id}/writing_prompt', 'ReportsController@missingWritingPrompt');
  Route::get('/missing/{id}/gifted_student', 'ReportsController@giftedStudent');
  Route::get('/missing/{id}/gifted_student/verification/{status}', 'ReportsController@giftedStudentVerification');

  Route::get('/missing/{id}/offerstatus', 'ReportsController@offerStatus');
  Route::get('/missing/{id}/offerstatus/{version}', 'ReportsController@offerStatus');
  Route::get('/missing/{id}/offerstatus/{type}/{version}', 'ReportsController@offerStatus');


  Route::get('/missing/{id}/duplicatestudent', 'ReportsController@duplicate_student');
  Route::get('/missing/{id}/duplicatestudent/{type}', 'ReportsController@duplicate_student');

  Route::get('/missing/{id}/writingprompt', 'ReportsController@missingWritingPrompt');
  Route::get('/missing/{id}/writingprompt/{type}', 'ReportsController@missingWritingPrompt');

  Route::get('/missing/{id}/populationchange/{application_id}', 'ReportsController@populationChange');
  Route::get('/missing/{id}/results/{application_id}', 'ReportsController@submissionResults');
  Route::get('/missing/{id}/selection_report/{application_id}', 'ReportsController@selectionReport');
  Route::get('/missing/{id}/selection_report/{application_id}/{version}/{type}', 'ReportsController@selectionReportVersion');


  Route::get('/process/logs', 'ReportsController@processingLogsReport');
  Route::get('/process/real/logs', 'ReportsController@processingRealLogsReport');


  Route::get('/export/missinggrade/{type}', 'ReportsController@missingGrade');
  Route::get('/export/missinggrade/{type}/{lresponse}', 'ReportsController@missingGrade');
  Route::get('/export/missingcdi/{type}', 'ReportsController@missingCDI');
  Route::get('/export/missingcdi/{type}/{lresponse}', 'ReportsController@missingCDI');

  Route::get('/missing/{id}/mcpss/verification/{status}', 'ReportsController@mcpssEmployeeVerification');
  Route::get('/missing/{id}/mcpss/changeStatus', 'ReportsController@mcpssEmployeeStatus');

  Route::get('/missing/{id}/gradecdiupload', 'ReportsController@gradeCdiUploadList');
  Route::get('/missing/{id}/gradecdiupload/{type}/confirmed', 'ReportsController@gradeCdiUploadConfirmed');
  Route::get('/program/by_choice', 'ReportsController@getAllProgramsByChoice');
});


Route::group(['prefix' => 'admin/Reports', 'module' => 'Reports', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\Reports\Controllers'], function () {

  Route::get('/', 'ReportsController@application_index');

  Route::get('/Waitlist/', 'ReportsController@waitlist_application_index');

  Route::get('/Waitlist/Selection/{application_id}', 'ReportsController@waitlist_index');
  Route::get('/Waitlist/Selection/{application_id}/{grade}', 'ReportsController@waitlist_index');


  Route::get('/Selection/{application_id}', 'ReportsController@index');
  Route::get('/Selection/{application_id}/{grade}', 'ReportsController@index');

  Route::get('/waitlist/index', 'ReportsController@waitlist_index');
  Route::get('/waitlist/index/{grade}', 'ReportsController@waitlist_index');


  Route::get('/late_submission/index', 'ReportsController@late_submission_index');
  Route::get('/late_submission/index/{grade}', 'ReportsController@late_submission_index');


  Route::get('/newreport', 'ReportsController@newreport');


  Route::get('/setting/update/{field}/{val}', 'ReportsController@settingUpdate');


  Route::get('/missing', 'ReportsController@missing_index');
  Route::get('/admin_review', 'ReportsController@admin_index');

  Route::get('/missing/{id}/recommendation', 'ReportsController@missingSubmissionRecommendation');
  Route::get('/missing/{id}/recommendation/export/{program_id}', 'ReportsController@exportMissingSubmissionRecommendation');

  Route::get('/missing/grade/{program_id}', 'ReportsController@missingGrade');
  Route::get('/export/missinggrade', 'ReportsController@missingGrade');
  Route::get('/export/missingcdi', 'ReportsController@missingCDI');


  Route::get('/import/missing/{id}/grade', 'ReportsController@importGradeGet');
  Route::post('/import/missing/grade', 'ReportsController@importGrade');
  Route::get('/import/missing/{id}/cdi', 'ReportsController@importCDIGet');
  Route::post('/import/missing/cdi', 'ReportsController@importCDI');

  Route::post('/missing/grade/save/{id}', 'ReportsController@saveGrade');
  Route::post('/missing/cdi/save/{id}', 'ReportsController@saveCDI');


  Route::get('/missing/cdi', 'ReportsController@missingCDI');
  Route::get('/missing/cdi/{program_id}', 'ReportsController@missingCDI');

  Route::get('/{grade}', 'ReportsController@index');
  Route::get('/export', 'ReportsController@index');

  Route::get('/missing/{id}/committee_score', 'ReportsController@missingCommitteeScore');
  Route::get('/missing/{id}/committee_score/response', 'ReportsController@missingCommitteeScore');
  Route::post('/missing/committee_score/save/{id}', 'ReportsController@saveCommitteeScore');

  Route::get('/export/submissions', 'ReportsController@export_submissions');
  Route::post('/export/submissions', 'ReportsController@exportSubmissionsReportData');

  Route::get('/import/missing/{id}/committee_score', 'ReportsController@importCommitteeScoreGet');
  Route::post('/import/missing/committee_score', 'ReportsController@importCommitteeScore');


  /* Route::get('/edit/{id}', 'SubmissionsController@edit');
    Route::post('/update/{id}', 'SubmissionsController@update');
    Route::post('/update/audition/{id}', 'SubmissionsController@updateAudition');
    Route::post('/update/WritingPrompt/{id}', 'SubmissionsController@updateWritingPrompt');
    Route::post('/update/InterviewScore/{id}', 'SubmissionsController@updateInterviewScore');
    Route::post('/update/CommitteeScore/{id}', 'SubmissionsController@updateCommitteeScore');
    Route::post('/update/ConductDisciplinaryInfo/{id}', 'SubmissionsController@updateConductDisciplinaryInfo');
    Route::post('/update/StandardizedTesting/{id}', 'SubmissionsController@updateStandardizedTesting');
    Route::post('/update/AcademicGradeCalculation/{id}', 'SubmissionsController@updateAcademicGradeCalculation');

    Route::post('/storegrades/{id}', 'SubmissionsController@storeGrades');*/

  Route::get('/missing/{id}/test_score', 'ReportsController@missingTestScore');
  Route::get('/missing/{id}/test_score/response', 'ReportsController@missingTestScore');
  Route::post('/missing/test_score/save/{id}', 'ReportsController@saveTestScore');

  Route::get('/missing/{id}/interview_score', 'ReportsController@missingInterviewScore');
  Route::get('/missing/{id}/interview_score/response', 'ReportsController@missingInterviewScore');
  Route::post('/missing/interview_score/save/{id}', 'ReportsController@saveInterviewScore');

  Route::get('/missing/{id}/academic_score', 'ReportsController@missingAcademicScore');
  Route::get('/missing/{id}/academic_score/response', 'ReportsController@missingAcademicScore');
  Route::post('/missing/academic_score/save/{id}', 'ReportsController@saveAcademicScore');

  Route::get('/missing/{id}/eligibility', 'ReportsController@missingEligibility');

  Route::get('/missing/{id}/academy_selection', 'ReportsController@missingAcademicSelection');
  Route::get('/missing/{id}/academy_selection/response', 'ReportsController@missingAcademicSelectionResponse');
  Route::post('/missing/academy_selection/update', 'ReportsController@missingAcademicSelectionUpdateStatus');

  Route::get('/missing/{id}/returning_students', 'ReportsController@missingReturningStudents');
  Route::get('/missing/{id}/returning_students/response', 'ReportsController@missingReturningStudentsResponse');
});
