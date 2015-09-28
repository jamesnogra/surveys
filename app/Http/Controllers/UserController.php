<?php

	namespace App\Http\Controllers;

	use Crypt;
	use DB;
	use App\User;
	use App\Http\Controllers\Controller;
	use Illuminate\Support\Facades\Input;
	use URL;
	use Illuminate\Routing\Redirector;
	
	class UserController extends Controller
	{
		private $color1;
		
		public $available_themes = ["red", "pink", "purple", "deep-purple", "indigo", "blue", "light-blue", "cyan", "teal", "green", "light-green", "lime", "khaki", "yellow", "amber", "orange", "deep-orange", "blue-grey", "brown", "dark-grey", "black"];
		
		public function __construct() {
			$this->color1 = "indigo";
			if (!empty(session("color1"))) {
				$this->color1 = session("color1");
			}
		}
		
		/**
		 * Main homepage
		 *
		 * @param  None
		 * @return None
		 */
		public function homeIndex() {
			return view("home-index", ["color1"=>$this->color1]);
		}
		
		/**
		 * Lists all the users
		 *
		 * @param  None
		 * @return Response
		 */
		public function index() {
			$users = DB::table('users')->get();
			return view("users/view-all-users-page", ["users"=>$users, "color1"=>$this->color1]);
		}
		
		/**
		 * Login page for users
		 *
		 * @param  None
		 * @return Response
		 */
		public function loginUserPage() {
			return view("users/login-page", ["color1"=>$this->color1]);
		}
		
		/**
		 * Login page for users
		 *
		 * @param  None
		 * @return Response
		 */
		public function loginUserDB() {
			$user = DB::table('users')->where("email", $_POST["the_email"])->where("password", md5($_POST["the_password"]))->first();
			if (!empty($user)) {
				session([
					'user_id' => Crypt::encrypt($user->user_id),
					'email' => Crypt::encrypt($_POST["the_email"]),
					'name' => Crypt::encrypt($user->name),
					'type' => Crypt::encrypt($user->type)
				]);
				return json_encode(array("code"=>"1", "message"=>"Login successful."));
			} else {
				return json_encode(array("code"=>"-1", "message"=>"Incorrect username and password."));
			}
		}
		
		/**
		 * Logout a user, destroy sessions
		 *
		 * @param  None
		 * @return Response
		 */
		public function logout() {
			session()->flush();
			return redirect()->action('UserController@homeIndex');
		}
		
		/**
		 * Add a user to the database page
		 * 
		 * @param None
		 * @return None
		 */
		public function addUserPage() {
			return view("users/add-user-page", ["color1"=>$this->color1]);
		}
		
		/**
		 * Add a user to the database (mass insert)
		 * 
		 * @param Array
		 * @return None
		 */
		public function addUserDB() {
			if (session("record_time") !== null) {
				$last_registered_time = Crypt::decrypt(session("record_time"));
				if (time()-$last_registered_time < 60) { //check if this user registered many times within a minute
					$result = array("code"=>-2, "new_user_id"=>"", "message"=>"You registered like few minutes ago.");
					return json_encode($result);
				}
			}
			$user = DB::table('users')->where("email", $_POST["the_email"])->get();
			if (empty($user)) {
				$new_user_id = DB::table('users')->insertGetId(
					["email" => $_POST["the_email"],
					"password" => md5($_POST["the_password"]),
					"name" => $_POST["the_name"],
					"type" => "USER",
					"picture" => "default.jpg"]
				);
				$result = array("code"=>1, "new_user_id"=>Crypt::encrypt($new_user_id));
				session(["record_time"=>Crypt::encrypt(time())]); //let's record when this PC last registered
				return json_encode($result);
			} else {
				$result = array("code"=>-1, "new_user_id"=>"");
				return json_encode($result);
			}
		}
		
		/**
		 * View a certain user
		 * 
		 * @param Array
		 * @return None
		 */
		public function viewUserPage($name, $user_id) {
			if (empty(session('email'))) {
				return view("errors/unauthorized-page", ["color1"=>$this->color1]);
			}
			$user_id = Crypt::decrypt($user_id);
			$user = DB::table('users')->where("user_id", $user_id)->first();
			$surveys = DB::table('surveys')->where("user_id", $user_id)->get();
			return view("users/view-user-page", ["user"=>$user, "color1"=>$this->color1, "surveys"=>$surveys]);
		}
		
		/**
		 * changes the theme of the main page or a survey
		 * 
		 * @param page
		 * @return None
		 */
		public function changeThemePage($page, $survey_id=0) {
			return view("users/change-theme-page", ["color1"=>$this->color1, "available_themes"=>$this->available_themes, "page"=>$page, "survey_id"=>$survey_id]);
		}
		
		public function setThemeSession() {
			$color1 = $_POST["color1"];
			$page = $_POST["page"];
			if ($page == "main") { //general theme
				session(["color1"=>$color1]);
				return ["code"=>1, "message"=>"General theme has been changed to " . $color1 . "."];
			} else if ($page == "survey") {
				$survey_id = Crypt::decrypt($_POST["survey_id"]);
				DB::table("surveys")->where("survey_id", $survey_id)->update(["theme"=>$color1]);
				return ["code"=>1, "message"=>"Survey theme has been changed to " . $color1 . "."];
			} else if ($page == "home") {
				session(["color1"=>$color1]);
				return ["code"=>1, "message"=>"Survey theme has been changed to " . $color1 . "."];
			}
		}
		
		/**
		 * uploads an profile image to user
		 * 
		 * @param image
		 * @return Array
		 */
		public function uploadPicture() {
			$user_id = Crypt::decrypt(session("user_id"));
			if (!Input::hasFile('file')) {
				return ["code"=>-1, "full_path"=>"", "file_name"=>"", "message"=>"Upload failed."];
			}
			$uploaddir = "images/profile/";
			$new_file_info = $this->uploadImg('file', $uploaddir);
			if (getimagesize($new_file_info["full_path"])) {
				DB::table("users")->where("user_id", $user_id)->update(["picture"=>$new_file_info["file_name"]]);
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