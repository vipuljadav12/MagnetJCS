<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'From', 'middleware' => ['api'], 'namespace' => 'App\Modules\From\Controllers'], function () {

    Route::resource('From', 'FromController');
});
