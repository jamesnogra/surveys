@extends('my_main')


@section('page-title')
    Login
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h1>Login</h1>
	</header>
	<div class="w3-container">	
		<div class="w3-row">
			<div class="w3-col l3">&nbsp;</div>
			<div class="w3-col l6">
				<div class="w3-container" id="login-user-form">
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
					<div class="w3-group" style="text-align:center;"> 
						<button type="button" id="login-button" class="w3-btn w3-{{ $color1 }}">Login</button>
						<a class="w3-btn" href="/users/add-user-page">Sign Up</a>
					</div>
				</div>
				<div class="w3-container" id="loading-form" style="text-align:center;margin-top:100px;"><i class="material-icons w3-xxxlarge w3-spin">refresh</i></div>
			</div>
			<div class="w3-col l3">&nbsp;</div>
		</div>
	</div>
@endsection


@section('jquery-scripts')
	<script>
		$(document).ready(function() {
			
			$('#loading-form').hide();
			
			$("#login-button").click(function() {
				var _token = $("#_token").val();
				var the_email = $("#the_email").val();
				var the_password = $("#the_password").val();
				$('#login-user-form').hide();
				$('#loading-form').show();
				$.post("/users/login-user-db", {"the_email":the_email, "the_password":the_password, "_token":_token}, function(data){
					data = JSON.parse(data);
					if (data["code"] == 1) {
						$("#the_password_error").html("");
						window.location = "/surveys/my-surveys-page"
					} else if (data["code"] == -1) {
						$("#the_password_error").html("Incorrect username and password.");
					} else {
						$("#the_password_error").html("Something went wrong.");
					}
					$('#login-user-form').show();
					$('#loading-form').hide();
				});
			});
			
		});
	</script>
@endsection