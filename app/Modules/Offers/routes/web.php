<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'Offers', 'module' => 'Offers', 'middleware' => ['web'], 'namespace' => 'App\Modules\Offers\Controllers'], function () {

    Route::get('/{slug}', 'OffersController@offerChoice');
    Route::post('/store', 'OffersController@offerSave');
    // Route::get('/Contract/{slug}', 'OffersController@onlineContract');
    //Route::get('/Contract/Option/{slug}', 'OffersController@contractOption');
    //Route::post('/Contract/Option/store', 'OffersController@contractOptionStore');

    //Route::get('/Contract/Fill/{slug}', 'OffersController@onlineContract');
    //Route::post('/Contract/Fill/Store/{slug}', 'OffersController@finalizeContract');
    Route::get('/cron/autodecline', 'OffersController@autoDecline');
});

Route::group(['prefix' => 'admin/Offers', 'module' => 'Offers', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\Offers\Controllers'], function () {
    Route::get('/{slug}', 'OffersController@adminOfferChoice');
    Route::get('/Contract/Fill/{slug}', 'OffersController@onlineContract');
});


Route::group(['prefix' => 'Waitlist/Offers', 'module' => 'Offers', 'middleware' => ['web'], 'namespace' => 'App\Modules\Offers\Controllers'], function () {

    Route::get('/{slug}', 'WaitlistOffersController@offerChoice');
    Route::post('/store', 'WaitlistOffersController@offerSave');
    // Route::get('/Contract/{slug}', 'OffersController@onlineContract');
    //Route::get('/Contract/Option/{slug}', 'WaitlistOffersController@contractOption');
    //Route::post('/Contract/Option/store', 'WaitlistOffersController@contractOptionStore');

    // Route::get('/Contract/Fill/{slug}', 'WaitlistOffersController@onlineContract');
    //Route::post('/Contract/Fill/Store/{slug}', 'WaitlistOffersController@finalizeContract');
    Route::get('/cron/autodecline', 'WaitlistOffersController@autoDecline');
});

Route::group(['prefix' => 'admin/Waitlist/Offers', 'module' => 'Offers', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\Offers\Controllers'], function () {
    Route::get('/{slug}', 'WaitlistOffersController@adminOfferChoice');
    //Route::get('/Contract/Fill/{slug}', 'WaitlistOffersController@onlineContract');

});

Route::group(['prefix' => 'LateSubmission/Offers', 'module' => 'Offers', 'middleware' => ['web'], 'namespace' => 'App\Modules\Offers\Controllers'], function () {

    Route::get('/{slug}', 'LateSubmissionOffersController@offerChoice');
    Route::post('/store', 'LateSubmissionOffersController@offerSave');
    // Route::get('/Contract/{slug}', 'OffersController@onlineContract');
    // Route::get('/Contract/Option/{slug}', 'LateSubmissionOffersController@contractOption');
    //Route::post('/Contract/Option/store', 'LateSubmissionOffersController@contractOptionStore');

    //Route::get('/Contract/Fill/{slug}', 'LateSubmissionOffersController@onlineContract');
    //Route::post('/Contract/Fill/Store/{slug}', 'LateSubmissionOffersController@finalizeContract');
    Route::get('/cron/autodecline', 'LateSubmissionOffersController@autoDecline');
});

Route::group(['prefix' => 'admin/LateSubmission/Offers', 'module' => 'Offers', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\Offers\Controllers'], function () {
    Route::get('/{slug}', 'LateSubmissionOffersController@adminOfferChoice');
    //Route::get('/Contract/Fill/{slug}', 'LateSubmissionOffersController@onlineContract');

});
