@extends('my_main')


@section('page-title')
    Results
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h3 style="float:left;">
			Results
		</h3>
		<h4 style="float:left;margin-left:10px;">
			<a href="/surveys/my-surveys-page" class="w3-btn">
				<i class="material-icons w3-large">payment</i> My Surveys
			</a>
		</h4>
		<h4 style="float:right;">
			<a class="w3-btn" href="/"><i class="material-icons w3-large">home</i> Home</a>
			<a href="/users/view-user-page/{{ urlencode(Crypt::decrypt(session('name'))) }}/{{ session('user_id') }}" class="w3-btn">
				<i class="material-icons w3-large">person</i> My Profile
			</a>
			<a href="/users/logout" class="w3-btn">
				<i class="material-icons w3-large">arrow_forward</i> Logout
			</a>
		</h4>
	</header>
	<div class="w3-container">
		<div class="w3-row">
			<div class="w3-col l3">&nbsp;</div>
			<div class="w3-col l6">
				<h2>{{ $survey->title }}</h2>
				@foreach($responses as $item)
					<div style="margin-bottom:20px;" class="w3-card-4">
						<header class="w3-container w3-{{ $survey->theme }}"><h4>{{ $item->question_num }}) {{ $item->question_text }}</h4></header>
						<div class="w3-container" id="chart-{{ $item->question_num }}">Cannot interpret item {{ $item->question_num }}.</div>
					</div>
				@endforeach
			</div>
			<div class="w3-col l3">&nbsp;</div>
		</div>
	</div>
@endsection


@section('jquery-scripts')
	<script type="text/javascript" src="https://www.google.com/jsapi"></script>
	<script>
		// Load the Visualization API and the piechart package.
		google.load('visualization', '1.0', {'packages':['corechart']});
	
		var responses;
		$(document).ready(function() {
			
			
			// Set a callback to run when the Google Visualization API is loaded.
			//google.setOnLoadCallback(drawChart);
			
			var x;
			responses = "{{ json_encode($responses) }}";
			responses = JSON.parse(htmlDecode(responses));
			console.log(responses);
			for (x=0; x<responses.length; x++) {
				if (responses[x]["question_type"] == "SINGLE_RESPONSE") {
					createSingleResponseChart(responses[x]["question_num"], responses[x]["question_id"], responses[x]["question_text"], responses[x]["responses"]);
				}
				else if ((responses[x]["question_type"] == "SINGLE_ANSWER") || (responses[x]["question_type"] == "MULTIPLE_ANSWER")) {
					createSingleAndMultipleAnswerChart(responses[x]["question_num"], responses[x]["question_id"], responses[x]["question_text"], responses[x]["responses"]);
				}
			}
			
		});
		
		function createSingleAndMultipleAnswerChart(num, qid, text, responses_temp) {
			var x, temp_rows = [];
			temp_rows[0] = ["Option", "Total", { role: "style" } ];
			for (x=0; x<responses_temp.length; x++) {
				temp_rows[x+1] = [responses_temp[x]["choice_text"], responses_temp[x]["total"], getRandomColor()];
			}
			
			var data = google.visualization.arrayToDataTable(temp_rows);
			
			var view = new google.visualization.DataView(data);
			view.setColumns([0, 1,
			   { calc: "stringify",
				 sourceColumn: 1,
				 type: "string",
				 role: "annotation" },
			   2]);

			var options = {
				title: "",
				width: "100%",
				height: 400,
				bar: {groupWidth: "95%"},
				legend: { position: "none" },
			};
			var chart = new google.visualization.ColumnChart(document.getElementById("chart-"+num));
			chart.draw(view, options);
		}
		
		function createSingleResponseChart(num, qid, text, responses_temp) {
			// Create the data table.
			var data = new google.visualization.DataTable();
			var x, temp_rows = [];
			data.addColumn('string', 'Responses');
			data.addColumn('number', 'Total');
			for (x=0; x<responses_temp.length; x++) {
				temp_rows[x] = [responses_temp[x]["answer"], responses_temp[x]["total"]];
			}
			data.addRows(temp_rows);
			
			// Set chart options
			var options = {'title': '',
				'width':"100%",
				'height':400};
			
			// Instantiate and draw our chart, passing in some options.
			var chart = new google.visualization.PieChart(document.getElementById('chart-'+num));
			chart.draw(data, options);
		}
		
		function htmlDecode(input){
			var e = document.createElement('div');
			e.innerHTML = input;
			return e.childNodes[0].nodeValue;
		}
		
		function getRandomColor() {
			var letters = '0123456789ABCDEF'.split('');
			var color = '#';
			for (var i = 0; i < 6; i++ ) {
				color += letters[Math.floor(Math.random() * 16)];
			}
			return color;
		}
	</script>
@endsection