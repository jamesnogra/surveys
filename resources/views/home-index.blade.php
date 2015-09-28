@extends('my_main')


@section('page-title')
    Home
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<div style="float:left;">
			<h5>
				<nav class="w3-topnav">
					<a href="/"><i class="material-icons w3-large">home</i> Home</a>
					<a href="/example"><i class="material-icons w3-large">book</i> Example</a>
					<a href="/contact-us"><i class="material-icons w3-large">phone</i> Contact Us</a>
					<a href="/about"><i class="material-icons w3-large">info</i> About</a>
				</nav>
			</h5>
		</div>
		<h4 style="float:right;">
			<?php
				if (session("user_id") !== null) {
					echo '<a href="/users/view-user-page/'.urlencode(Crypt::decrypt(session("name"))).'/'.session("user_id").'" class="w3-btn"><i class="material-icons w3-large">home</i> My Profile</a> ';
					echo '<a href="/users/logout" class="w3-btn"><i class="material-icons w3-large">arrow_forward</i> Logout</a> ';
				} else {
					echo '<a href="/users/login-user-page" class="w3-btn"><i class="material-icons w3-large">lock</i> Login</a> ';
					echo '<a href="/users/add-user-page" class="w3-btn"><i class="material-icons w3-large">assignment</i> Signup</a>';
				}
			?>
		</h4>
	</header>
	<div style="width:100%;height:20px;clear:both;"></div>
	<div id="my_slideshow" style="width:800px;height:300px;margin:0 auto;overflow:hidden;">
		<img id="slideshow_image_1" src="/images/slideshow/1.JPG" width="800px" class="slideshow_images" />
		<img id="slideshow_image_2" src="/images/slideshow/2.JPG" width="800px" class="slideshow_images" />
		<img id="slideshow_image_3" src="/images/slideshow/3.JPG" width="800px" class="slideshow_images" />
	</div>
	<div style="width:100%;height:20px;clear:both;"></div>
	<div style="text-align:center;max-width:832px;margin:0 auto;">
		<div class="w3-container w3-third" style="margin-bottom:20px;">
			<div class="w3-card-4">
				<header class="w3-container w3-{{ $color1 }}">
					<h4>Create</h4>
				</header>
				<div class="w3-container">
					<p><i class="material-icons" style="font-size:128px;">border_color</i></p>
					<p>Create surveys from various question types and templates.</p>
				</div>
			</div>			
		</div>
		<div class="w3-container w3-third" style="margin-bottom:20px;">
			<div class="w3-card-4">
				<header class="w3-container w3-{{ $color1 }}">
					<h4>Publish</h4>
				</header>
				<div class="w3-container">
					<p><i class="material-icons" style="font-size:128px;">cloud_upload</i></p>
					<p>Publish your surveys so everyone can respond to it.</p>
				</div>
			</div>			
		</div>
		<div class="w3-container w3-third" style="margin-bottom:20px;">
			<div class="w3-card-4">
				<header class="w3-container w3-{{ $color1 }}">
					<h4>View Results</h4>
				</header>
				<div class="w3-container">
					<p><i class="material-icons" style="font-size:128px;">assignment_turned_in</i></p>
					<p>View the results of your surveys.</p>
				</div>
			</div>
		</div>
	</div>
	<div style="width:100%;height:20px;clear:both;"></div>
	<div class="w3-container" style="text-align:center;">
		<a href="/users/add-user-page" class="w3-btn w3-{{ $color1 }} w3-xxxlarge">Sign Up</a>
	</div>
	<div style="width:100%;height:40px;clear:both;"></div>
	<div style="text-align:center;max-width:832px;margin:0 auto;height:50px;opacity:0.5;">
		<img src="/images/company-logos/laravel-logo.png" height="100%" style="margin:10px;" />
		<img src="/images/company-logos/jquery-logo.gif" height="100%" style="margin:10px;" />
		<img src="/images/company-logos/html-5-logo.png" height="100%" style="margin:10px;" />
		<img src="/images/company-logos/mysql-logo.png" height="100%" style="margin:10px;" />
		<img src="/images/company-logos/w3-schools-logo.png" height="100%" style="margin:10px;" />
	</div>
@endsection


@section('jquery-scripts')
	<script>
		$(document).ready(function() {
			
			$(function() { setInterval( "slideSwitch()", 5000 ); });
			
		});
		
		var count = 2; //begin sliding at the second image
		function slideSwitch() {
			if (count > 3) { count = 1; }
			$(".slideshow_images").hide();
			$("#slideshow_image_"+count).fadeIn();
			count++;
		}
	</script>
@endsection