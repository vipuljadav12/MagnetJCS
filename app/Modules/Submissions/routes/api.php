<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'Submissions', 'middleware' => ['api'], 'namespace' => 'App\Modules\Submissions\Controllers'], function() {

    Route::resource('Submissions', 'SubmissionsController');

});
