@extends('my_main')


@section('page-title')
    {{ $user->name }} - View User
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h1 style="float:left;">
			{{ $user->name }}
			<small><a href="/surveys/my-surveys-page" class="w3-btn">
				<i class="material-icons">payment</i> My Surveys
			</a></small>
		</h1>
		<h4 style="float:right;padding-top:5px;">
			<a href="/users/logout" class="w3-btn">
				<i class="material-icons w3-large">arrow_forward</i> Logout
			</a>
		</h4>
	</header>
	<div class="w3-container">	
		<h3>{{ $user->email }}</h3>
	</div>
@endsection