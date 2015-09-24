@extends('my_main')


@section('page-title')
    Change Theme
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<div style="float:left;">
			<h3>
				Change Theme
			</h3>
		</div>
		<h4 style="float:right;">
			<a href="/" class="w3-btn">
				<i class="material-icons w3-large">home</i> Home
				<input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
				<input type="hidden" id="page" name="page" value="{{ $page }}" />
				<input type="hidden" id="survey_id" name="survey_id" value="{{ $survey_id }}" />
			</a>
		</h4>
	</header>
	<div class="w3-container">	
		<div class="w3-row">
			@foreach($available_themes as $color)
				<div class="w3-container w3-third" style="text-align:center;margin-top:20px;">
					<div class="w3-card-8">
						<header class="w3-{{ $color }} w3-container"><h4>{{ ucfirst($color) }}</h4></header>
						<div class="w3-container" style="padding:15px;"><button onClick="selectColor('{{ $color }}');" class="w3-btn w3-{{ $color }}">Select</button></div>
					</div>
				</div>
			@endforeach
		</div>
	</div>
@endsection


@section('jquery-scripts')

	<script>
	
		function selectColor(color1) {
			var _token = $("#_token").val();
			var page = $("#page").val();
			var survey_id = $("#survey_id").val();
			$.post("/users/set-theme-session", {"_token":_token, "color1":color1, "page":page, "survey_id":survey_id}, function(data){
				if (data["code"] == 1) {
					var prev = document.referrer;
					window.location = prev;
				}
			});
		}
	
		$(document).ready(function() {
			
			
			
		});
		
	</script>
	
@endsection