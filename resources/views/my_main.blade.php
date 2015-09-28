<!DOCTYPE html>


<html>

	<head>
		<title>@yield('page-title') - jSurveys</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<link rel="icon" type="image/png" href="/images/logo/default.png">
		<style>
			html, body {
				height: 100%;
			}
			.error_messages {
				color: red;
				padding: 5px;
			}
			.custom-card-style {
				float: left;
				max-height: 200px;
				overflow: auto;
				width: 95%;
				margin: 10px;
			}
			.question-text {
				font-size: 24px;
				width: 100%;
			}
		</style>
		@yield('jquery-scripts')
	</head>

	<body>
		<div style="min-height:100%;margin:0 auto -120px;">
			@yield('content')
			<div style="height:170px;width:100%;"></div>
		</div>
		<footer class="w3-container w3-{{ $color1 }}" style="height:120px;padding-top:35px;width:100%;">
			<img src="/images/logo/default.png" width="40px" style="float:left;padding-top:5px;"/>
			<h4 style="float:left;margin-left:10px;">jSurveys</h4>
			<small style="float:right;padding-top:20px;">Copyright 2015</small>
		</footer>
	</body>

</html>