<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'ConfigureExportSubmission', 'middleware' => ['api'], 'namespace' => 'App\Modules\ConfigureExportSubmission\Controllers'], function () {

    Route::resource('ConfigureExportSubmission', 'PriorityController');
});
