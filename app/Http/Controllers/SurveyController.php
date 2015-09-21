<?php

	namespace App\Http\Controllers;

	use Crypt;
	use DB;
	use App\User;
	use App\Survey;
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Input;
	use URL;
	
	class SurveyController extends Controller
	{
		private $color1;
		
		public function __construct() {
			$this->color1 = "indigo";
			if (!empty(session("color1"))) {
				$this->color1 = session("color1");
			}
		}
		
		/**
		 * Gets a survey using survey_id
		 *
		 * @param  survey_id
		 * @return Response
		 */
		public function getSurveyById($survey_id) {
			return DB::table('surveys')->where("survey_id", $survey_id)->first();
		}
		
		/**
		 * Lists all the surveys of the logged in user
		 *
		 * @param  None
		 * @return Response
		 */
		public function mySurveysPage() {
			if (empty(session('email'))) {
				return view("errors/unauthorized-page", ["color1"=>$this->color1]);
			}
			$user_id = Crypt::decrypt(session('user_id'));
			$name = Crypt::decrypt(session('name'));
			$user_surveys = DB::table('surveys')->where("user_id", $user_id)->get();
			return view("surveys/my-surveys-page", ["color1"=>$this->color1, "surveys"=>$user_surveys, "name"=>$name, "user_id"=>$user_id]);
		}
		
		/**
		 * Create survey page
		 *
		 * @param  None
		 * @return Response
		 */
		public function createSurveyPage() {
			if (empty(session('email'))) {
				return view("errors/unauthorized-page", ["color1"=>$this->color1]);
			}
			$user_id = Crypt::decrypt(session('user_id'));
			$name = Crypt::decrypt(session('name'));
			return view("surveys/create-survey-page", ["color1"=>$this->color1, "name"=>$name, "user_id"=>$user_id]);
		}
		
		/**
		 * Create survey and write to database
		 *
		 * @param  Array
		 * @return Response
		 */
		public function createSurveyDB() {
			if (empty(session('email'))) {
				return view("errors/unauthorized-page", ["color1"=>$this->color1]);
			}
			$user_id = Crypt::decrypt(session('user_id'));
			if (strlen($_POST["the_file_name_logo"]) > 0) {
				$logo = $_POST["the_file_name_logo"];
			} else {
				$logo = "default.jpg";
			}
			if (strlen($_POST["the_password"]) > 0) {
				$new_password = Crypt::encrypt($_POST["the_password"]);
			} else {
				$new_password = "";
			}
			$new_survey = [
				"user_id" => $user_id,
				"title" => $_POST["the_title"],
				"description" => $_POST["the_description"],
				"logo" => $_POST["the_file_name_logo"],
				"date" => date("Y-m-d H:i:s"),
				"password" => $new_password,
				"theme" => "default"
			];
			$new_user_id = DB::table('surveys')->insertGetId($new_survey);
			$new_user_id = Crypt::encrypt($new_user_id);
			return json_encode(array("code"=>1, "data"=>$new_survey, "new_user_id"=>$new_user_id));
		}

		/**
		 * Creating survey (adding question and choices)
		 * 
		 * @param  None
		 * @return Response
		 */
		public function createActualSurveyPage($title, $survey_id) {
			$survey_id = Crypt::decrypt($survey_id);
			$survey_info = $this->getSurveyById($survey_id);
			$survey_id = Crypt::encrypt($survey_id);
			$user_id = Crypt::decrypt(session('user_id'));
			$name = Crypt::decrypt(session('name'));
			if (strlen($survey_info->password) > 0) {
				$survey_info->password = Crypt::decrypt($survey_info->password);
			}
			return view("surveys/create-actual-survey-page", ["color1"=>$this->color1, "survey_id"=>$survey_id, "survey"=>$survey_info, "name"=>$name, "user_id"=>$user_id]);
		}
		
		/**
		 * updates certain fields in survey
		 * 
		 * @param  None
		 * @return Response
		 */
		public function updateValueSurveyDB() {
			$survey_id = Crypt::decrypt($_POST["survey_id"]);
			$field = $_POST["field"];
			$value = $_POST["value"];
			if ($field=="password") {
				$value = Crypt::encrypt($value);
			}
			DB::table("surveys")->where("survey_id", $survey_id)->update([$field=>$value]);
			return json_encode(array("code"=>1, "message"=>$field." has been changed to ".$value."."));
			//return ["code"=>1, "full_path"=>"", "file_name"=>"", "message"=>$field." has been changed to " . $value];
		}
		
		/**
		 * Upload logo for surveys
			*
		 * @param  None
		 * @return Response
		 */
		public function uploadLogo() {
			$user = new User();
			if (!Input::hasFile('file')) {
				return "File not found, please try again";
			}
			$uploaddir = "images/logo/";
			$new_file_info = $this->uploadImg('file', $uploaddir);
			if (getimagesize($new_file_info["full_path"])) {
				return ["code"=>1, "full_path"=>$new_file_info["full_path"], "file_name"=>$new_file_info["file_name"], "message"=>"Uploaded successfully."];
			} else {
				return ["code"=>-1, "full_path"=>"", "file_name"=>"", "message"=>"Upload failed."];
			}
		}
		public function uploadImg($fileField, $dirName) {
			if (Input::hasFile($fileField)) {
				$base_url = URL::to('/');
				$time = md5(microtime());
				$extension = Input::file($fileField)->getClientOriginalExtension();
				$img_hash = $base_url . '/' . $dirName . $time . '.'. $extension;
				Input::file($fileField)->move($dirName, $img_hash);
				$file_name = $time . '.'. $extension;
				return array("full_path"=>$img_hash, "file_name"=>$file_name);
			}
		}
		
	}
	
?>