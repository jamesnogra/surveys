@extends('my_main')


@section('page-title')
    Home
@endsection

<?php
	$color1 = "indigo";
	if (session("color1") !== null) { $color1 = session("color1"); }
?>
@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<div style="float:left;font-size:18px;padding-top:10px;">
			<nav class="w3-topnav">
				<a href="/"><i class="material-icons w3-large">home</i> Home</a>
				<a href="/help"><i class="material-icons w3-large">find_in_page</i> Help</a>
				<a href="/example"><i class="material-icons w3-large">book</i> Example</a>
				<a href="http://www.therookieblog.com/#four"><i class="material-icons w3-large">phone</i> Contact Us</a>
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
	<div class="w3-container w3-col s1"></div>
	<div class="w3-container w3-col s10">
		<h2 style="margin-bottom:40px;">Guide for Creating Surveys</h2>
		<div class="w3-container">
			<div class="w3-container w3-col s4"><img class="w3-image w3-card-4" src="/images/home/single-answer.jpg" style="width:100%;" /></div>
			<div class="w3-container w3-col s8"><h4>Single Answer Type Question</h4>Single answer type questions are for questions that require only one answer. This type of question is perfect for surveys that requires absolute one answers from respondents.</div>
		</div>
		<hr>
		<div class="w3-container">
			<div class="w3-container w3-col s8"><h4>Multiple Answer Type Question</h4>Multiple answer type questions are perfect for survey questions that require multiple responses. Respondents can select as many answers as they want for a certain question.</div>
			<div class="w3-container w3-col s4"><img class="w3-image w3-card-4" src="/images/home/multiple-answer.jpg" style="width:100%;" /></div>
		</div>
		<hr>
		<div class="w3-container">
			<div class="w3-container w3-col s4"><img class="w3-image w3-card-4" src="/images/home/single-response.jpg" style="width:100%;" /></div>
			<div class="w3-container w3-col s8"><h4>Single Response Type Question</h4>Single answer type questions are questions that requires the respondents to input their responses manually. This is perfect for survey questions that have varied answers.</div>
		</div>
	</div>
	<div class="w3-container w3-col s1"></div>
@endsection