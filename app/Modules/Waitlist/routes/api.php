<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'ProcessSelection', 'middleware' => ['api'], 'namespace' => 'App\Modules\ProcessSelection\Controllers'], function() {

    Route::resource('ProcessSelection', 'ProcessSelectionController');

});
