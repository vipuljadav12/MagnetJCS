<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'Enrollment', 'middleware' => ['api'], 'namespace' => 'App\Modules\Enrollment\Controllers'], function() {

    Route::resource('Enrollment', 'EnrollmentController');

});
