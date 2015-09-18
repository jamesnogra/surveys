<?php

	namespace App\Http\Controllers;

	use Crypt;
	use DB;
	use App\User;
	use App\Http\Controllers\Controller;
	
	class UserController extends Controller
	{
		private $color1;
		
		public function __construct() {
			$this->color1 = "indigo";
			if (!empty(session("color1"))) {
				$this->color1 = session("color1");
			}
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
			$user_id = Crypt::decrypt($user_id);
			$user = DB::table('users')->where("user_id", $user_id)->first();
			return view("users/view-user-page", ["user"=>$user, "color1"=>$this->color1]);
		}	
		
	}

?>