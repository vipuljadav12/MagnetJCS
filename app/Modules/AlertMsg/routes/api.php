<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'Configuration', 'middleware' => ['api'], 'namespace' => 'App\Modules\Configuration\Controllers'], function () {

    Route::resource('Configuration', 'ConfigurationController');
});
