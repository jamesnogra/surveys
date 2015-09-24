@extends('my_main')


@section('page-title')
    {{ $user->name }} - View User
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h4 style="float:left;">
			<input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
			<a href="/surveys/my-surveys-page" class="w3-btn">
				<i class="material-icons w3-large">payment</i> My Surveys
			</a>
		</h4>
		<h4 style="float:right;">
			<a href="/users/logout" class="w3-btn">
				<i class="material-icons w3-large">arrow_forward</i> Logout
			</a>
		</h4>
	</header>
	<div class="w3-container">	
		<div class="w3-col l3">&nbsp;</div>
		<div class="w3-col l6">
			<div class="w3-container">
				<div style="float:left;width:120px;padding:10px;">
					<div id="picture_preview" style="height:120px;width:120px;overflow:hidden;"><img src="/images/profile/{{$user->picture}}" height="120px;" /></div>
					<div class="file_upload w3-btn">
						<form enctype="multipart/form-data" style="margin-top:5px;">
							<span>Change</span>
							<input type="hidden" id="the_file_name_picture" name="the_file_name_picture" value="" />
							<input name="file" type="file" id="the_picture" name="the_picture" accept="image/*" class="upload" />
						</form>
					</div>
					<center style="font-size:9px;"><a class="w3-btn w3-{{ $color1 }}" href="/users/change-theme-page/main">Change Theme</a></center>
				</div>
				<div style="float:left;margin-left:20px;margin-top:40px;width:70%;">
					<h3>{{ $user->name }}</h3>
					<div style="width:1px;height:50px;"></div>
					@foreach ($surveys as $survey)
						<hr />
						<div class="survey-container" style="width:100%;">
							<div class="survey-logo" style="float:left;width:100px;height:100px;overflow:hidden;margin-right:10px;"><img src="/images/logo/{{ $survey->logo }}" height="100px" width="100px" /></div>
							<div style="float:left;width:65%;">
								<div style="margin-top:10px;font-size:28px;overflow:hidden;">{{ $survey->title }}</div>
								<div style="overflow:hidden;"><a class="w3-btn" href="/surveys/create-actual-survey-page/{{ urlencode($survey->title) }}/{{ Crypt::encrypt($survey->survey_id) }}">Edit</a></div>
							</div>
							<div style="width:1px;height:10px;clear:both;"></div>
						</div>
					@endforeach
				</div>
			</div>
			<div class="w3-container" id="loading-form" style="text-align:center;margin-top:100px;"><i class="material-icons w3-xxxlarge w3-spin">refresh</i></div>
		</div>
		<div class="w3-col l3">&nbsp;</div>
	</div>
@endsection


@section('jquery-scripts')
	<style>
		.file_upload {
			position: relative;
			overflow: hidden;
			margin: 10px;
			text-align: center;
		}
		.file_upload input.upload {
			position: absolute;
			top: 0;
			right: 0;
			margin: 0;
			padding: 0;
			font-size: 20px;
			cursor: pointer;
			opacity: 0;
			filter: alpha(opacity=0);
		}
	</style>
	
	<script>		
	
		$(document).ready(function() {
			
			
			$("#loading-form").hide();
			$("#the_picture").on("change", function (e) {
				$("#loading-form").show();
				$(".file_upload").hide();
				var file_data = $("#the_picture").prop("files")[0];   			// Getting the properties of file from file field
				var form_data = new FormData();                  			// Creating object of FormData class
				form_data.append("file", file_data)              			// Adding extra parameters to form_data
				form_data.append("_token", $("#_token").val())              // Adding extra parameters to form_data
				$.ajax({
					url: '/users/upload-picture',
					dataType: 'json',
					cache: false,
					contentType: false,
					processData: false,
					data: form_data,                         				// Setting the data attribute of ajax with file_data
					type: 'post',
					success: function(data) {
						$("#loading-form").hide();
						$("#create-survey-button").show();
						if (data["code"] == 1) {
							$("#the_picture_error").html("");
							$("#picture_preview").html("<img height='120px' src='"+data["full_path"]+"' />");
							//updateValueToDB("logo", data["file_name"]);
						} else if (data["code"] == -1) {
							$("#picture_preview").html("");
							$("#the_picture_error").html("Please upload a valid image file.");
						} else {
							$("#picture_preview").html("");
							$("#the_picture_error").html("Something went wrong during the logo upload.");
						}
						$(".file_upload").show();
					}, error: function(err) {
						$("#the_picture_error").html("Something went wrong during the logo upload.");
						$("#picture_preview").html("");
						$("#loading-form").hide();
					}
				});
			});
			
		});
			
	</script>	
@endsection