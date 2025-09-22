<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'Student', 'middleware' => ['api'], 'namespace' => 'App\Modules\Student\Controllers'], function () {

    Route::resource('Student', 'StudentController');
});
