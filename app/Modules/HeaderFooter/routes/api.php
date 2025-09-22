<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'HeaderFooter', 'middleware' => ['api'], 'namespace' => 'App\Modules\HeaderFooter\Controllers'], function () {

    Route::resource('HeaderFooter', 'HeaderFooterController');
});
