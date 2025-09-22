<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'admin/WritingPrompt', 'module' => 'WritingPrompt', 'middleware' => ['web', 'auth'], 'namespace' => 'App\Modules\WritingPrompt\Controllers'], function () {

	Route::get('/', 'WritingPromptController@index');
	Route::post('/store', 'WritingPromptController@store');
	Route::post('/send/linkmail', 'WritingPromptController@sendLinkMail');
	Route::post('/clear', 'WritingPromptController@clear');
	Route::get('/print/{submission_id}/{program_id}', 'WritingPromptController@print');
});


// Front
Route::group(['prefix' => '/WritingPrompt', 'module' => 'WritingPrompt', 'middleware' => ['web'], 'namespace' => 'App\Modules\WritingPrompt\Controllers'], function () {
	Route::get('/expired/msg', 'WritingPromptFrontController@expired');

	Route::get('/{slug}', 'WritingPromptFrontController@create');
	Route::post('/exam', 'WritingPromptFrontController@exam');
	Route::post('/store/exam', 'WritingPromptFrontController@storeExam');
	Route::post('/submitted/{id}', 'WritingPromptFrontController@submitted');

	Route::get('/preview/{eligibility_id}.{program_id}.{application_id}', 'WritingPromptFrontController@previewFront');
});
