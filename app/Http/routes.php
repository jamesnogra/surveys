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

/*
| This is for the survey-related routes
*/
Route::get("/surveys/my-surveys-page", "SurveyController@mySurveysPage");
Route::get("/surveys/create-survey-page", "SurveyController@createSurveyPage");
Route::post("/surveys/create-survey-db", "SurveyController@createSurveyDB");
Route::post("/surveys/upload-logo", "SurveyController@uploadLogo");
Route::get("/surveys/create-actual-survey-page/{title}/{survey_id}", "SurveyController@createActualSurveyPage");