<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix'=> 'admin/Availability','module' => 'SetAvailability', 'middleware' => ['web','auth','permission'], 'namespace' => 'App\Modules\SetAvailability\Controllers'], function() {

    Route::get('/Set', 'SetAvailabilityController@index');
    Route::get('/getOptionsByProgram/{program}', 'SetAvailabilityController@getOptionsByProgram');
    Route::post('/store', 'SetAvailabilityController@store');

    Route::resource('/', 'SetAvailabilityController');

    // application filter programs
	Route::get('/get_programs', 'SetAvailabilityController@getPrograms');
	Route::get('/import', 'SetAvailabilityController@import');    
    Route::get('/import/template', 'SetAvailabilityController@importTemplate');    
	Route::post('/import/store', 'SetAvailabilityController@storeImport');    
    // Rising Composition Import
    Route::group(['prefix'=>'import_rising_composition'], function() {
        Route::get('/', 'SetAvailabilityController@importRisingComposition');    
        Route::get('/template', 'SetAvailabilityController@importRisingCompositionTemplate');    
        Route::post('/store', 'SetAvailabilityController@storeImportRisingComposition');
    });
});
