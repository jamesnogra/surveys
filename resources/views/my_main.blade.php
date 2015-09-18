<!DOCTYPE html>


<html>

	<head>
		<title>@yield('page-title') - Surveys</title>
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
		<link rel="stylesheet" href="http://www.w3schools.com/lib/w3.css">
		<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<style>
			.error_messages {
				color: red;
				padding: 5px;
			}
			.custom-card-style {
				width: 31%;
				float: left;
				margin: 5px;
				max-height: 200px;
				overflow: auto;
				min-width: 200px;
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