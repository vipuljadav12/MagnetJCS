<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix'=>'admin/PhoneOrder','module' => 'PhoneOrder', 'middleware' => ['web'], 'namespace' => 'App\Modules\PhoneOrder\Controllers'], function() {

    Route::resource('/', 'PhoneOrderController');

});
