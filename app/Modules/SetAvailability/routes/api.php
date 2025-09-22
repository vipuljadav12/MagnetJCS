<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'SetAvailability', 'middleware' => ['api'], 'namespace' => 'App\Modules\SetAvailability\Controllers'], function() {

    Route::resource('SetAvailability', 'SetAvailabilityController');

});
