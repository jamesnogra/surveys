@extends('my_main')


@section('page-title')
    My Surveys - {{ $name }} 
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<div style="float:left;">
			<h3 style="float:left;">My Surveys</h3>
			<h4 style="float:left;margin-left:10px;">
				<a href="/surveys/create-survey-page" class="w3-btn">
					<i class="material-icons w3-large">payment</i> New Survey
				</a>
			</h4>
		</div>
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
		<input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
		<?php $x = 0; ?>
		@foreach ($surveys as $survey)
			<div class="w3-third" id="card-{{ $x }}">
				<div class="w3-card-4 custom-card-style">
					<header class="w3-container w3-{{ $color1 }}">
						<h4 style="float:left;">{{ $survey->title }}</h4>
						<h4 style="float:right;">
							<i class="material-icons w3-large" style="cursor:pointer;" onClick="editSurvey('{{ urlencode($survey->title) }}', '{{ Crypt::encrypt($survey->survey_id) }}');">edit</i>
							<i class="material-icons w3-large" style="cursor:pointer;" onClick="deleteSurveyDB('{{ Crypt::encrypt($survey->survey_id) }}', {{ $x }});">delete</i>
						</h4>
					</header>
					<div class="w3-container">
						<p>{{ $survey->description }}</p>
					</div>
				</div>
			</div>
			<?php $x++; ?>
		@endforeach
	</div>
@endsection


@section('jquery-scripts')

	<script>

		function editSurvey(title, survey_id) {
			window.location = "/surveys/create-actual-survey-page/"+title+"/"+survey_id;
		}
		
		function deleteSurveyDB(survey_id, id) {
			var survey_id = survey_id;
			var _token = $("#_token").val();
			if (confirm("Are you sure you want to delete this survey?")) {
				$("#card-"+id).fadeOut("fast", function() {
					$.post("/surveys/delete-survey-db", {"survey_id":survey_id, "_token":_token}, function(data){
						//data = JSON.parse(data);
						//nothing
					});
				});
			}
		}
	
	</script>

@endsection