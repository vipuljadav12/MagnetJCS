<?php

use Illuminate\Support\Facades\Route;

Route::group(["prefix"=>"admin/AuditTrailData",'module' => 'AuditTrailData', 'middleware' => ['web','auth'], 'namespace' => 'App\Modules\AuditTrailData\Controllers'], function() {

    Route::resource('', 'AuditTrailDataController');

});
