<?php

use Illuminate\Support\Facades\Route;

Route::group(['module' => 'Users', 'middleware' => ['api'], 'namespace' => 'App\Modules\Users\Controllers'], function() {

    Route::resource('Users', 'UsersController');

});
