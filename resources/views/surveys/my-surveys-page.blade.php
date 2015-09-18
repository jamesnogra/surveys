@extends('my_main')


@section('page-title')
    My Surveys - {{ $name }} 
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h1 style="float:left;">
			My Surveys
			<small><a href="/surveys/create-survey-page" class="w3-btn">
				<i class="material-icons">payment</i> New Survey
			</a></small>
		</h1>
		<h4 style="float:right;padding-top:5px;">
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
		@foreach ($surveys as $survey)
			<div class="w3-card-4 custom-card-style">
				<header class="w3-container w3-{{ $color1 }}">
					<h3>{{ $survey->title }}</h3>
				</header>
				<div class="w3-container">
					<p>{{ $survey->description }}</p>
				</div>
			</div>
		@endforeach
	</div>
@endsection