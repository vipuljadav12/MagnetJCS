<?php
/*Route::group(['domain' => '{company}.magnet.mov', 'middleware' => 'SubDomain'], function()
{ 
    Auth::routes();

    Route::get('/', function($company)
    {
       return view('welcome', compact('company'));
    });
    Route::get('/home', 'HomeController@index');
  
});

Route::group(['domain' => 'magnet.mov', 'middleware' => 'SubDomain'], function()
{ 
    Auth::routes();
    
    Route::get('/', function()
    {
       return view('welcome');
    });
    Route::get('/home', 'HomeController@index');
  
});
*/
//\App\Tenant::getTenants();


Auth::routes();
Route::get('/copydata', 'HomeController@copy_data');

Route::get('/incorrectinfo/{studentid}', 'HomeController@incorrectInfo');
Route::get('/msgs/{slug}', 'HomeController@msgDisp');
Route::get('/msgs/{slug}/{id}', 'HomeController@msgDisp');
Route::get('/formsubmitted/error', 'HomeController@formSubmittedDisp');

Route::get('/noaddressmatch', 'HomeController@noAddressMatch');

Route::get("/customError","HomeController@customError");
//Route::get('/login', 'Auth\LoginController@showLoginForm');
Route::group(['prefix' => 'admin'], function () {
    Route::get('dashboard', 'admin\AdminController@index'); 
    Route::get('changedistrict/{district_id}', 'admin\AdminController@changedistrict'); 
    Route::get('changeenrollment/{enrollment_id}', 'admin\AdminController@changedEnrollment'); 
    Route::get('load_addresses', 'admin\AdminController@loadAddresses'); 
    Route::get('magnet_dashboard', 'admin\AdminController@magnetDashboard');

    Route::get('districtadmin/offer', 'admin\AdminController@districtAdminOffer');
    Route::get('districtadmin/offer/chart1/{program_id}', 'admin\AdminController@loadDynamicChart1');
    Route::get('districtadmin/offer/chart2/{program_id}', 'admin\AdminController@loadDynamicChart2');

    Route::get('superadmin/offer', 'admin\AdminController@superAdminOffer');
    Route::get('superadmin/offer/{version}', 'admin\AdminController@superAdminOffer');

    Route::get('superadmin/offer/chart1/{version}/{program_id}', 'admin\AdminController@superOfferAcceptedChart');
    Route::get('superadmin/offer/chart2/{version}/{program_id}', 'admin\AdminController@superOfferDeclinedChart');
    Route::get('superadmin/offer/chart3/{version}/{program_id}', 'admin\AdminController@superOfferWaitlistedChart');


    Route::get('submission', 'admin\AdminController@adminSubmission');
    Route::get('show_override_addresses', 'admin\AdminController@loadOverrideAddresses'); 
    
    Route::get('magnet_dashboard/late_submissions', 'admin\AdminController@magnetDashboard');


});

Route::get('/download/instructions/{filename}', 'HomeController@downloadRecommendationInstructions');
Route::get('/', 'HomeController@applicationIndex');
Route::get('/application/{id}', 'HomeController@index');
Route::get('/stagecheck', 'HomeController@indexStage');
Route::get('/phone/submission', 'HomeController@phoneSubmission');
Route::get('/getdob/{grade}/{id}', 'HomeController@getDOB');
Route::get('/print/application/{confirmation_no}', 'HomeController@printApplicationMsg');

Route::get('/showinfo/{id}', '\App\Modules\Files\Controllers\FilesController@showPopupText');

Route::post('/step-1', 'HomeController@showProcessForm');
Route::post('/nextstep', 'HomeController@showNextStep');
Route::get('/previewform/{pageid}/{form_id}', 'HomeController@previewForm');

Route::get('/check/sibling/{id}', 'HomeController@checkSibling');
Route::get('/check/sibling/{id}/{program_id}', 'HomeController@checkSibling');
Route::get('/check/return/student/{id}/{bdate}', 'HomeController@checkReturnStudent');
Route::get('/returning-student', 'HomeController@returningStudent');
Route::get('/returning-student/{ssid}', 'HomeController@returningStudentNext');
Route::post('/return-student', 'HomeController@submitReturnStudent');
Route::post('/return-student-step1', 'HomeController@submitReturnStudentStep1');
Route::post('/return-student-step2', 'HomeController@submitReturnStudentStep2');


Route::get('/check/student/{id}', 'HomeController@checkStudent');
Route::get('/admin/shortCode/list', 'admin\AdminController@getShortCodeList');

Route::get('/check/program/sibling/{id}', 'HomeController@checkSiblingEnabled');

Route::get('/Document','HomeController@documentUploadIndex');
Route::post('/Document/store','HomeController@storeDocumentUpload');

//Route::get('/recommendation/{subject}.{submission_id}','HomeController@recommendationForm');
Route::get('/recommendation/{subject}.{submission_id}.{program_id}','HomeController@recommendationForm');
Route::get('/recommendation/preview/{subject}.{eligibility_id}.{program_id}.{application_id}','HomeController@recommendationFormPreview');

Route::post('/answer/save','HomeController@saveRecommendationForm');

##Route::get('/home', 'HomeController@index')->name('home');
Route::view('/test', 'test');
Route::get("/one",function(){
    print_r("expression");
})->middleware("filterall");