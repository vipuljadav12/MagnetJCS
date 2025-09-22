<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Process/Selection', 'module' => 'ProcessSelection', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\ProcessSelection\Controllers'], function () {
    Route::get('/run', 'ProcessSelectionController@application_index');
    Route::get('/', 'ProcessSelectionController@index');
    Route::get('/step2/{application_id}', 'ProcessSelectionController@index_step2');
    Route::get('/validate/application/{application_id}', 'ProcessSelectionController@validateApplication');

    Route::get('/statereport', 'ProcessSelectionController@currentStateReport');

    Route::post('/selection_report', 'ProcessSelectionController@processSelectionReport');
    Route::get('/test/{id}', 'ProcessSelectionController@selectProcessProgram');

    Route::post('/store', 'ProcessSelectionController@store');

    Route::get('settings', 'ProcessSelectionController@settings_index');
    // Route::get('settings/{application_id}', 'ProcessSelectionController@settings_step_two');
    // Route::post('settings/store', 'ProcessSelectionController@storeSettings');

    /* Selection Report */
    Route::get('/Population', 'ProcessSelectionController@population_change_application');
    Route::get('/Population/Application', 'ProcessSelectionController@population_change_application');
    Route::get('/Population/{value}', 'ProcessSelectionController@population_change');
    Route::get('/Population/Application/{value}', 'ProcessSelectionController@population_change_application');

    Route::get('/Results/{value}', 'ProcessSelectionController@submissions_results');
    Route::get('/Results/Application/{value}', 'ProcessSelectionController@submissions_results_application');
    Route::get('/Results/Application', 'ProcessSelectionController@submissions_results_application');

    Route::post('/Accept/list', 'ProcessSelectionController@selection_accept');
    Route::post('/Revert/list', 'ProcessSelectionController@selection_revert');
    //Route::get('/Revert/list', 'ProcessSelectionController@selection_revert');

});

Route::group(['prefix' => 'admin/Preliminary/Processing', 'module' => 'ProcessSelection', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\ProcessSelection\Controllers'], function () {

    Route::get('/', 'PreliminaryController@index');
    Route::post('/rollback', 'PreliminaryController@roll_back_submissions');

    Route::post('/calculate', 'PreliminaryController@calculate');
    Route::post('/commit', 'PreliminaryController@commit_score');
});


Route::group(['prefix' => 'admin/Process/Selection', 'module' => 'ReportsController', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\Reports\Controllers'], function () {
    Route::get('settings/{application_id}', 'ReportsController@editSwingSettings');
    Route::post('settings/store/{application_id}', 'ReportsController@storeSwingSettings');
});
