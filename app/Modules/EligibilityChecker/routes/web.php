<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/EligibilityChecker', 'module' => 'EligibilityChecker', 'middleware' => ['web', 'auth', 'super'], 'namespace' => 'App\Modules\EligibilityChecker\Controllers'], function () {

    Route::get('/', 'EligibilityCheckerController@index');
    Route::get('/application/{application_id}', 'EligibilityCheckerController@application_data');
});

/*Route::group([],function())*/