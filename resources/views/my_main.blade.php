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
		@yield('content')
	</body>

</html>