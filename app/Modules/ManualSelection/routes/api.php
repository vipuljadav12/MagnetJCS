<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'ManualSelection', 'middleware' => ['api'], 'namespace' => 'App\Modules\ManualSelection\Controllers'], function () {

    Route::resource('ManualSelection', 'ManualSelectionController');
});
