@extends('my_main')


@section('page-title')
    Add User
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h4 style="float:left;">Sign Up</h4>
		<h4 style="float:right;"><a class="w3-btn" href="/users/login-user-page">Login</a></h4>
	</header>
	<div class="w3-row">
		<div class="w3-col l3">&nbsp;</div>
		<div class="w3-col l6">
			<div class="w3-container" id="add-user-form">
				<div class="w3-group">
					<input type="hidden" id="_token" name="_token" value="{{{ csrf_token() }}}" />
					<input name="the_email" id="the_email" class="w3-input" type="text" required style="width:100%;">
					<label class="w3-label">Email</label>
					<div id="the_email_error" class="error_messages"></div>
				</div>
				<div class="w3-group"> 
					<input name="the_password" id="the_password" class="w3-input" type="password" required style="width:100%;">
					<label class="w3-label">Password</label>
					<div id="the_password_error" class="error_messages"></div>
				</div>
				<div class="w3-group"> 
					<input name="the_name" id="the_name" class="w3-input" type="text" required style="width:100%;">
					<label class="w3-label">Name</label>
					<div id="the_name_error" class="error_messages"></div>
				</div>
				<div class="w3-group"> 
					<button type="button" id="add-user-button" class="w3-btn w3-{{ $color1 }}">Sign Up</button>
				</div>
			</div>
			<div class="w3-container" id="loading-form" style="text-align:center;margin-top:100px;"><i class="material-icons w3-xxxlarge w3-spin">refresh</i></div>
		</div>
		<div class="w3-col l3">&nbsp;</div>
	</div>
@endsection


@section('jquery-scripts')
	<script>
		$(document).ready(function() {
			
			$('#loading-form').hide();
			
			$("#add-user-button").click(function() {
				var _token = $("#_token").val();
				var the_email = $("#the_email").val();
				var the_password = $("#the_password").val();
				var the_name = $("#the_name").val();
				var submit = true;
				if (!isEmail(the_email)) {
					submit = false;
					$("#the_email_error").html("Please enter a valid email.");
				} else {
					$("#the_email_error").html("");
				}
				if (the_password.length < 6) {
					submit = false;
					$("#the_password_error").html("Password length must be greater than or equal to 6 characters.");
				} else {
					$("#the_password_error").html("");
				}
				if (the_name.length < 1) {
					submit = false;
					$("#the_name_error").html("Please enter your name.");
				} else {
					$("#the_name_error").html("");
				}
				if (submit) {
					$('#add-user-form').hide();
					$('#loading-form').show();
					$.post("/users/add-user-db", {"the_email":the_email, "the_password":the_password, "the_name":the_name, "_token":_token}, function(data){
						data = JSON.parse(data);
						if (data["code"] == 1) {
							window.location = "/users/view-user-page/" + the_name.replace(/[^a-zA-Z ]/g, "") + "/" + data["new_user_id"];
						} else if (data["code"] == -1) {
							$("#the_email_error").html("This email address is already registered.");
						} else {
							$("#the_name_error").html("Something is wrong. Please try again later.");
						}
						$('#add-user-form').show();
						$('#loading-form').hide();
					}).fail(function(error){
						$("#the_name_error").html("Something is wrong. Please try again later.");
					});
				}
			});
			
			function isEmail(email) {
				var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
				return regex.test(email);
			}
			
		});
	</script>
@endsection