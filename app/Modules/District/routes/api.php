<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'District', 'middleware' => ['api'], 'namespace' => 'App\Modules\District\Controllers'], function() {

    Route::resource('District', 'DistrictController');

});
