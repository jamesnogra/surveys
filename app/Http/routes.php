<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', function () {
    return view('welcome');
});


/*
| This is for the user-related routes
*/
Route::get("/users", "UserController@index");
Route::get("/users/login-user-page", "UserController@loginUserPage");
Route::post("/users/login-user-db", "UserController@loginUserDB");
Route::get("/users/logout", "UserController@logout");
Route::get("/users/view-user-page/{name}/{user_id}", "UserController@viewUserPage");
Route::get("/users/add-user-page", "UserController@addUserPage");
Route::post("/users/add-user-db", "UserController@addUserDB");
Route::post("/users/upload-picture", "UserController@uploadPicture");
Route::get("/users/change-theme-page/{where}/{survey_id?}", "UserController@changeThemePage");
Route::post("/users/set-theme-session/", "UserController@setThemeSession");

/*
| This is for the survey-related routes
*/
Route::get("/surveys/my-surveys-page", "SurveyController@mySurveysPage");
Route::get("/surveys/create-survey-page", "SurveyController@createSurveyPage");
Route::post("/surveys/create-survey-db", "SurveyController@createSurveyDB");
Route::post("/surveys/upload-logo", "SurveyController@uploadLogo");
Route::get("/surveys/create-actual-survey-page/{title}/{survey_id}", "SurveyController@createActualSurveyPage");
Route::post("/surveys/update-value-survey-db", "SurveyController@updateValueSurveyDB");
Route::post("/surveys/save-survey-questions-choices-db", "SurveyController@saveSurveyQuestionsChoicesDB");
Route::post("/surveys/delete-survey-db", "SurveyController@deleteSurveyDB");
Route::post("/surveys/get-questions-choices-db", "SurveyController@getQuestionsChoicesDB");
Route::post("/surveys/generate-link-code-db", "SurveyController@generateLinkCodeDB");
Route::get("/surveys/answer-survey-page/{title}/{link_code}", "SurveyController@answerSurveyPage");
Route::post("/surveys/check-responses-db", "SurveyController@checkResponsesDB");
Route::post("/surveys/save-responses-db", "SurveyController@saveResponsesDB");

/*
| This is for the other routes
 */
Route::get("/surveys/select-theme-page", "OtherController@selectThemePage");