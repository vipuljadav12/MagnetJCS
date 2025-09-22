<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'AuditTrailData', 'middleware' => ['api'], 'namespace' => 'App\Modules\AuditTrailData\Controllers'], function() {

    Route::resource('AuditTrailData', 'AuditTrailDataController');

});
