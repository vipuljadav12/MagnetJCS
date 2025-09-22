<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/Form', 'module' => 'Form', 'middleware' => ['web', 'auth', 'super'], 'namespace' => 'App\Modules\Form\Controllers'], function () {

	Route::get('/', 'FormController@index');
	Route::get('/create', 'FormController@create');
	Route::post('/store', 'FormController@store');
	Route::get('/edit/{id}', 'FormController@edit');
	Route::get('/edit/{pageid}/{id}', 'FormController@edit');
	Route::post('/update/{id}', 'FormController@update');
	Route::post('/update/{pageid}/{id}', 'FormController@update');
	Route::get('/delete/{id}', 'FormController@delete');
	Route::get('/trash', 'FormController@trash');
	Route::get('/restore/{id}', 'FormController@restore');
	Route::get("/getField", "FormController@getField");
	Route::post("/saveBuild", "FormController@saveBuild");

	Route::get('/status', 'FormController@status');
	Route::get('/uniqueurl', 'FormController@uniqueurl');


	//latest routes
	Route::get("/insertField", "FormController@insertField");
	Route::get("/getFormContent", "FormController@getFormContent");
	Route::get("/removeField/{build}", "FormController@removeField");
	Route::get("/fieldEditor/{build}", "FormController@fieldEditor");
	Route::post("/saveSingle", "FormController@saveSingle");
	Route::get("/removeOption/{option}", "FormController@removeOption");

	Route::get("/changeTitle/{form_id}/{page_id}", "FormController@changeTitle");
	Route::post("/registerSort", "FormController@registerSort");
});
