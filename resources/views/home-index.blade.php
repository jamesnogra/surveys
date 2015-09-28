@extends('my_main')


@section('page-title')
    Home
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<div style="float:left;font-size:18px;padding-top:10px;">
			<nav class="w3-topnav">
				<a href="/"><i class="material-icons w3-large">home</i> Home</a>
				<a href="/example"><i class="material-icons w3-large">book</i> Example</a>
				<a href="/contact-us"><i class="material-icons w3-large">phone</i> Contact Us</a>
				<a href="/about"><i class="material-icons w3-large">info</i> About</a>
			</nav>
		</div>
		<h4 style="float:right;">
			<?php
				echo '<a href="/users/change-theme-page/home/" class="w3-btn"><i class="material-icons w3-large">invert_colors</i> Theme</a> ';
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
		<img id="slideshow_image_1" src="/images/slideshow/1.JPG" width="800" alt="Create Survey" class="slideshow_images" />
		<img id="slideshow_image_2" src="/images/slideshow/2.JPG" width="800" alt="Publish Survey" class="slideshow_images" />
		<img id="slideshow_image_3" src="/images/slideshow/3.JPG" width="800" alt="View Survey Results" class="slideshow_images" />
	</div>
	<div style="width:100%;height:20px;clear:both;"></div>
	<div style="text-align:center;max-width:832px;margin:0 auto;">
		<div class="w3-container w3-third" style="margin-bottom:20px;">
			<div class="w3-card-4">
				<header class="w3-container w3-{{ $color1 }}">
					<h4>Create</h4>
				</header>
				<div class="w3-container">
					<p><i class="material-icons w3-text-{{ $color1 }}" style="font-size:128px;">border_color</i></p>
					<p>Create surveys from various question types and templates. You can even upload your own company or organization logo in your surveys!</p>
				</div>
			</div>			
		</div>
		<div class="w3-container w3-third" style="margin-bottom:20px;">
			<div class="w3-card-4">
				<header class="w3-container w3-{{ $color1 }}">
					<h4>Publish</h4>
				</header>
				<div class="w3-container">
					<p><i class="material-icons w3-text-{{ $color1 }}" style="font-size:128px;">cloud_upload</i></p>
					<p>Publish your survey so everyone can respond to it. You can have it password protected so that only invited respondents can answer it.</p>
				</div>
			</div>			
		</div>
		<div class="w3-container w3-third" style="margin-bottom:20px;">
			<div class="w3-card-4">
				<header class="w3-container w3-{{ $color1 }}">
					<h4>View Results</h4>
				</header>
				<div class="w3-container">
					<p><i class="material-icons w3-text-{{ $color1 }}" style="font-size:128px;">assignment_turned_in</i></p>
					<p>View the results of your surveys. Whether you have hundereds, thousands, or even millions of respondents, we will graph the results for you instantly.</p>
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
		<img src="/images/company-logos/laravel-logo.png" alt="Laravel Logo" height="50" style="margin:10px;" />
		<img src="/images/company-logos/jquery-logo.gif" alt="jQuery Logo" height="50" style="margin:10px;" />
		<img src="/images/company-logos/html-5-logo.png" alt="HTML5 Logo" height="50" style="margin:10px;" />
		<img src="/images/company-logos/mysql-logo.png" alt="MySQL Logo" height="50" style="margin:10px;" />
		<img src="/images/company-logos/w3-schools-logo.png" alt="W3Schools Logo" height="50" style="margin:10px;" />
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