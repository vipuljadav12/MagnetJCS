<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'PhoneOrder', 'middleware' => ['api'], 'namespace' => 'App\Modules\PhoneOrder\Controllers'], function () {

    Route::resource('PhoneOrder', 'PhoneOrderController');
});
