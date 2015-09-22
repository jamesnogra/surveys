@extends('my_main')


@section('page-title')
    Create New Survey
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h4 style="float:left;">
			Create New Survey
			<a href="/surveys/my-surveys-page" class="w3-btn">
				<i class="material-icons w3-large">payment</i> My Surveys
			</a>
		</h4>
		<h4 style="float:right;">
			<a href="/users/view-user-page/{{ urlencode($name) }}/{{ Crypt::encrypt($user_id) }}" class="w3-btn">
				<i class="material-icons w3-large">person</i> My Profile
			</a>
			<a href="/users/logout" class="w3-btn">
				<i class="material-icons w3-large">arrow_forward</i> Logout
			</a>
		</h4>
	</header>
	<div class="w3-container">&nbsp;</div>
	<div class="w3-container">
		<div class="w3-row">
			<div class="w3-col l3">&nbsp;</div>
			<div class="w3-col l6">
				<div id="loading-icon" style="text-align:center;margin-top:100px;"><i style="color:#000000;" class="material-icons w3-xxxlarge w3-spin">refresh</i></div>
				<div class="w3-container" id="create-survey-form">
					<div class="w3-group">
						<input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
						<input name="the_title" id="the_title" class="w3-input" type="text" required style="width:100%;font-size:32px;">
						<label class="w3-label">Title</label>
						<div id="the_title_error" class="error_messages"></div>
					</div>
					<div class="w3-group"> 
						<textarea name="the_description" id="the_description" class="w3-input" style="width:100%;resize:none;"></textarea>
						<label class="w3-label">Description</label>
						<div id="the_description_error" class="error_messages"></div>
					</div>
					<div class="w3-group"> 
						<input name="the_password" id="the_password" class="w3-input" type="password" required style="width:100%;">
						<label class="w3-label">Password <small>(Leave blank if survey is not password protected.)</small></label>
						<div id="the_password_error" class="error_messages"></div>
					</div>
					<div class="w3-group"> 
						<form enctype="multipart/form-data">
							<input type="hidden" id="the_file_name_logo" name="the_file_name_logo" value="" />
							<div class="w3-label">Logo: </div>
							<div style="margin-left:50px;overflow:hidden;"><input name="file" type="file" id="the_logo" name="the_logo" accept="image/*" /></div>
							<div id="the_logo_error" class="error_messages"></div>
							<div class="w3-card-12" style="width:25%" id="logo_preview"></div>
						</form>
					</div>
					<div class="w3-group"> 
						<button type="button" id="create-survey-button" class="w3-btn w3-{{ $color1 }}">Create</button>
					</div>
				</div>
			</div>
			<div class="w3-col l3">&nbsp;</div>
		</div>
	</div>
@endsection


@section('jquery-scripts')
	<script>
		$(document).ready(function() {
			
			afterLoading();
			
			$("#the_logo").on("change", function (e) {
				$("#the_logo_error").html('<i style="color:#000000;" class="material-icons w3-xxxlarge w3-spin">refresh</i>');
				$("#create-survey-button").hide();
				var file_data = $("#the_logo").prop("files")[0];   			// Getting the properties of file from file field
				var form_data = new FormData();                  			// Creating object of FormData class
				form_data.append("file", file_data)              			// Adding extra parameters to form_data
				form_data.append("_token", $("#_token").val())              // Adding extra parameters to form_data
				$.ajax({
					url: '/surveys/upload-logo',
					dataType: 'json',
					cache: false,
					contentType: false,
					processData: false,
					data: form_data,                         				// Setting the data attribute of ajax with file_data
					type: 'post',
					success: function(data) {
						$("#create-survey-button").show();
						if (data["code"] == 1) {
							$("#the_logo_error").html("");
							$("#logo_preview").html("<img width='100%' src='"+data["full_path"]+"' />");
							$("#the_file_name_logo").val(data["file_name"]);
						} else if (data["code"] == -1) {
							$("#logo_preview").html("");
							$("#the_logo_error").html("Please upload a valid image file.");
							$("#the_file_name_logo").val("");
						} else {
							$("#logo_preview").html("");
							$("#the_logo_error").html("Something went wrong during the logo upload.");
							$("#the_file_name_logo").val("");
						}
					}, error: function(err) {
						$("#the_logo_error").html("Something went wrong during the logo upload.");
						$("#logo_preview").html("");
						$("#the_file_name_logo").val("");
					}
				});
			});
			
			$("#create-survey-button").click(function() {
				var _token = $("#_token").val();
				var the_title = $("#the_title").val();
				var the_description = $("#the_description").val();
				var the_password = $("#the_password").val();
				var the_file_name_logo = $("#the_file_name_logo").val();
				var submit = true;
				beforeLoading();
				if (the_title.length < 1) {
					$("#the_title_error").html("Title is required.");
					submit = false;
				} else {
					$("#the_title_error").html("");
				}
				if (the_description.length < 1) {
					$("#the_description_error").html("Description is required.");
					submit = false;
				} else {
					$("#the_description_error").html("");
				}
				if (submit) {
					$.post("/surveys/create-survey-db", {"the_title":the_title, "the_description":the_description, "the_password":the_password, "the_file_name_logo":the_file_name_logo, "_token":_token}, function(data){
						data = JSON.parse(data);
						if (data["code"] == 1) {
							window.location = "/surveys/create-actual-survey-page/"+the_title+"/"+data["new_user_id"];
						} else {
							$("#the_name_error").html("Something is wrong. Please try again later.");
							afterLoading();
						}
					}).fail(function(error){
						$("#the_name_error").html("Something is wrong. Please try again later.");
						afterLoading();
					});
				}
			});
			
			function beforeLoading() {
				$("#loading-icon").show();
				$("#create-survey-form").hide();
			}
			function afterLoading() {
				$("#loading-icon").hide();
				$("#create-survey-form").show();
			}
			
		});
	</script>
@endsection