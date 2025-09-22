<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'Priority', 'middleware' => ['api'], 'namespace' => 'App\Modules\Priority\Controllers'], function() {

    Route::resource('Priority', 'PriorityController');

});
