<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'School', 'middleware' => ['api'], 'namespace' => 'App\Modules\School\Controllers'], function () {

    Route::resource('School', 'SchoolController');
});
