@extends('my_main')


@section('page-title')
    Unauthorized
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h4 style="float:left;margin-top:17px;">Unauthorized</h4>
		<h4 style="float:right;padding-top:5px;">
			<a href="/users/login-user-page" class="w3-btn">
				<i class="material-icons w3-large">https</i> Login
			</a>
		</h4>
	</header>
	<div class="w3-container">	
		<div class="w3-row">
			<div class="w3-col l3">&nbsp;</div>
			<div class="w3-col l6" style="text-align:center;">
				<h3>This page is only accessible by registered users.</h3>
			</div>
			<div class="w3-col l3">&nbsp;</div>
		</div>
	</div>
@endsection