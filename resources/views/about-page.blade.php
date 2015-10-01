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
	<div class="w3-container w3-col s2"></div>
	<div class="w3-container w3-col s8" style="text-align:center;">
		<h2 style="margin-bottom:40px;">About jSurveys</h2>
		<img src="/images/logo/default.png" width="250" alt="jSurveys Logo" />
		<p>jSurvey is a survey tool that is free to use for small or even large scale businesses or organizations that wants to gather data from people. Currently, there are three types of questions that can be added in jSurvey tool and a text-only page that does not require respondents to answer.</p>
		<p>Surveys can be shared through an automatically generated link and can be protected by a password so that only the ones invited can respond. Respondents of jSurveys can answer surveys in a computer or mobile device. All the responses are tabulated and graphed using Google Charts. Only the survey creator can view the results.</p>
	</div>
	<div class="w3-container w3-col s2"></div>
@endsection