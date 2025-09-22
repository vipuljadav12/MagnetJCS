<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'Application', 'middleware' => ['api'], 'namespace' => 'App\Modules\Application\Controllers'], function () {

    Route::resource('Application', 'ApplicationController');
});
