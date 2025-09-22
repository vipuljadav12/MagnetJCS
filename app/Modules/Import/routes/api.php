<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'Import', 'middleware' => ['api'], 'namespace' => 'App\Modules\Import\Controllers'], function () {

    Route::resource('Import', 'ImportController');
});
