<?php

	namespace App\Http\Controllers;

	use Crypt;
	use DB;
	use App\User;
	use App\Survey;
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Input;
	use URL;
	use Illuminate\Routing\Redirector;
	
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
		 * Gets a survey using link_id
			*
		 * @param  link_id
		 * @return Response
		 */
		public function getSurveyByLinkId($link_code) {
			return DB::table('surveys')->where("link_code", $link_code)->first();
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
				$logo = "default.png";
			}
			if (strlen($_POST["the_password"]) > 0) {
				$new_password = md5($_POST["the_password"]);
			} else {
				$new_password = "";
			}
			$new_survey = [
				"user_id" => $user_id,
				"title" => $_POST["the_title"],
				"description" => $_POST["the_description"],
				"logo" => $logo ,
				"date" => date("Y-m-d H:i:s"),
				"password" => $new_password,
				"theme" => "default",
				"link_code" => str_random(16)
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
				if (strlen($value) == 0) { $value = ""; }
				else { $value = md5($value); }
			}
			DB::table("surveys")->where("survey_id", $survey_id)->update([$field=>$value]);
			return json_encode(array("code"=>1, "message"=>$field." has been changed to ".$value."."));
			//return ["code"=>1, "full_path"=>"", "file_name"=>"", "message"=>$field." has been changed to " . $value];
		}
		
		/**
		 * adds all questions and choices in a survey
		 * 
		 * @param  survey_id, questions, and choices
		 * @return Response
		 */
		public function saveSurveyQuestionsChoicesDB() {
			$questions_choices = json_decode($_POST["questions_choices"]);
			$survey_id = Crypt::decrypt($_POST["survey_id"]);
			$this->deleteQuestionsAndChoicesAndResponses($survey_id);
			$question_num = 1;
			foreach($questions_choices as $item) {
				if ((isset($item->question_text)) && (strlen($item->question_text)>0)) {
					$new_question = ["survey_id"=>$survey_id, "question_text"=>$item->question_text, "question_type"=>$item->question_type, "question_num"=>$question_num];
					$new_question_id = DB::table('questions')->insertGetId($new_question);
					$choice_num = 1;
					foreach($item->choices as $choice) {
						if ((isset($choice->choice_text)) && (strlen($choice->choice_text)>0)) {
							$new_choice = ["survey_id"=>$survey_id, "choice_num"=>$choice_num, "choice_text"=>$choice->choice_text, "question_id"=>$new_question_id];
							$new_choice_id = DB::table('choices')->insertGetId($new_choice);
							$choice_num++;
						}
					}
					$question_num++;
				}
			}
			return "";
		}
		
		/**
		 * deletes a survey and all of its questions and choices
		 * 
		 * @param  survey_id
		 * @return None
		 */
		public function getQuestionsChoicesDB() {
			$survey_id = Crypt::decrypt($_POST["survey_id"]);
			$survey_info = DB::table('surveys')->where("survey_id", $survey_id)->first();
			$questions_choices = [];
			$x = 0;
			$questions = DB::table('questions')->where("survey_id", $survey_id)->get();
			foreach($questions as $question) {
				$questions_choices[$x] = $question;
				$questions_choices[$x]->choices = DB::table('choices')->where("question_id", $question->question_id)->get();
				$x++;
			}
			$survey_info->questions_choices = $questions_choices;
			return json_encode($survey_info);
		}
		
		/**
		 * deletes a survey and all of its questions and choices
		 * 
		 * @param  survey_id
		 * @return None
		 */
		public function deleteSurveyDB() {
			$survey_id = Crypt::decrypt($_POST["survey_id"]);
			DB::table('surveys')->where("survey_id", $survey_id)->delete();
			$this->deleteQuestionsAndChoicesAndResponses($survey_id);
		}
		
		/**
		 * deletes all questions and choices in a survey
		 * 
		 * @param  survey_id
		 * @return None
		 */
		public function deleteQuestionsAndChoicesAndResponses($survey_id) {
			DB::table('questions')->where("survey_id", $survey_id)->delete();
			DB::table('choices')->where("survey_id", $survey_id)->delete();
			DB::table('responses')->where("survey_id", $survey_id)->delete();
		}
		
		/**
		 * generates a link to take a survey
		 * 
		 * @param  survey_id
		 * @return None
		 */
		public function generateLinkCodeDB() {
			$survey_id = Crypt::decrypt($_POST["survey_id"]);
			$survey_info = $this->getSurveyByID($survey_id);
			$link_id = str_random(16);
			$link = URL::to('/') . "/surveys/answer-survey-page/" . urlencode($survey_info->title) . "/" . $link_id;
			DB::table("surveys")->where("survey_id", $survey_id)->update(["link_code"=>$link_id]);
			return ["code"=>1, "link"=>$link];
		}
		
		/**
		 * answering survey page
		 * 
		 * @param  link_id
		 * @return None
		 */
		function answerSurveyPage($title, $link_code) {
			$survey_info = $this->getSurveyByLinkId($link_code);
			$unique_respondent_id = str_random(64);
			if ($survey_info->theme != "default") { //get the theme for this survey
				$this->color1 = $survey_info->theme;
			}
			return view("surveys/answer-survey-page", ["color1"=>$this->color1, "survey"=>$survey_info, "unique_respondent_id"=>$unique_respondent_id]);
		}
		
		/**
		 * saving of responses
		 * 
		 * @param  survey_id and reponses array
		 * @return None
		 */
		function saveResponsesDB() {
			$questions_choices = json_decode($_POST["responses"]);
			$survey_id = Crypt::decrypt($_POST["survey_id"]);
			$unique_respondent_id = $_POST["unique_respondent_id"];
			DB::table('responses')->where("unique_respondent_id", $unique_respondent_id)->delete();
			$ip_address = $_SERVER['REMOTE_ADDR'];
			foreach($questions_choices as $question) {
				$question_id = $question->question_id;
				foreach($question->answers as $response_id) {
					$new_response = [	"unique_respondent_id"		=> $unique_respondent_id, 
										"ip_address"				=> $ip_address, 
										"survey_id"					=> $survey_id,
										"question_id"				=> $question_id,
										"answer"					=> $response_id,
										"date"						=> DB::raw("NOW()")	];
					if (strlen($response_id)>0) {
						DB::table('responses')->insertGetId($new_response);
					}
				}
			}
			return ["code"=>1, "message"=>"Success"];
		}
		
		/**
		 * checks if there's already respondents in a particular survey
		 * 
		 * @param  survey_id
		 * @return Array
		 */
		function checkResponsesDB() {
			$survey_id = Crypt::decrypt($_POST["survey_id"]);
			$responses = DB::table('responses')->where("survey_id", $survey_id)->get();
			return json_encode($responses);
		}
		
		/**
		 * matches the password for answering survey
		 * 
		 * @param  survey_id, password
		 * @return Array
		 */
		public function fillPasswordDB() {
			$survey_id = Crypt::decrypt($_POST["survey_id"]);
			$my_password = md5($_POST["my_password"]);
			$result = DB::table('surveys')->where("survey_id", $survey_id)->where("password", $my_password)->get();
			if (count($result) > 0) {
				return ["code"=>1, "message"=>"Password match."];
			} else {
				return ["code"=>-1, "message"=>"Password is incorrect."];
			}
		}
		
		public function showSurveyResultsPage($title, $survey_id) {
			$results = [];
			$x = 0;
			$survey_id = Crypt::decrypt($survey_id);
			$survey_info = $this->getSurveyById($survey_id);
			if ($survey_info->theme == "default") { $survey_info->theme = "indigo"; }
			$questions = DB::table('questions')->where('survey_id', $survey_id)->get();
			foreach($questions as $question) {
				$results[$x] = $question;
				if ($question->question_type == "SINGLE_ANSWER" || $question->question_type == "MULTIPLE_ANSWER") {
					$results[$x]->responses = $this->getSingleMultipleAnswerResponses($question->question_id);
				} else if ($question->question_type == "SINGLE_RESPONSE") {
					$results[$x]->responses = $this->getSingleResponseResponses($question->question_id);
				}
				$x++;
			}
			return view("surveys/responses-survey-page", ["color1"=>$this->color1, "survey"=>$survey_info, "responses"=>$results]);
		}
		public function getSingleMultipleAnswerResponses($question_id) {
			$responses = [];
			$x = 0;
			$choices = DB::table('choices')->where('question_id', $question_id)->get();
			foreach($choices as $choice) {
				$responses[$x] = $choice;
				$responses[$x]->total = DB::table('responses')->where('answer', $choice->choice_id)->count();
				$x++;
			}
			return $responses;
		}
		public function getSingleResponseResponses($question_id) {
			$responses = [];
			$temp_array = [];
			$x = 0;
			$responses_not_unique = DB::table('responses')->select('answer')->where('question_id', $question_id)->get();
			foreach($responses_not_unique as $response) { $temp_array[] = $response->answer; }
			$responses_unique = $this->uniqueSingleResponses($temp_array);
			foreach ($responses_unique as $item) {
				$responses[$x] = array();
				$responses[$x]['answer'] = $item;
				$responses[$x]['total'] = DB::table('responses')->where('answer', $item)->count();
				$x++;
			}
			return $responses;
		}
		public function uniqueSingleResponses($array) {
			return array_intersect_key(
				$array,
				array_unique(array_map("StrToLower",$array))
			);
		}
		
		/**
		 * Generates a random survey for /example page
		 *
		 * @param  None
		 * @return None
		 */
		public function generateExample() {
			$survey_info = DB::table('surveys')->where('password', "")->orderBy(DB::raw('RAND()'))->first();
			return redirect()->to("/surveys/answer-survey-page/".urlencode($survey_info->title)."/".$survey_info->link_code);
		}
		
		/**
		 * Upload logo for surveys
			*
		 * @param  None
		 * @return Response
		 */
		public function uploadLogo() {
			if (!Input::hasFile('file')) {
				return ["code"=>-1, "full_path"=>"", "file_name"=>"", "message"=>"Upload failed."];
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