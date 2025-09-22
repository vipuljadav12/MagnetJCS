<?php

use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'cron', 'module' => 'CronManagement', 'middleware' => ['web'], 'namespace' => 'App\Modules\CronManagement\Controllers'], function () {
	Route::get('/Audition', 'AuditionEmailCron@sendMail');
	Route::get('/WritingPrompt', 'WritingPromptEmailCron@sendMail');
});
