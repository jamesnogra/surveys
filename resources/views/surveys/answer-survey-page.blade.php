@extends('my_main')


@section('page-title')
    Answer Survey - {{ $survey->title }}
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<div style="float:left;">
			<div style="float:left;margin:10px 10px 0px 0px;"><img src="/images/logo/{{ $survey->logo }}" height="40px;" /></div>
			<h3 style="float:left;">{{ $survey->title }}</h3>
			<input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
			<input type="hidden" id="unique_respondent_id" name="unique_respondent_id" value="{{ $unique_respondent_id }}" />
		</div>
		<h4 style="float:right;">
			<!--<a href="" class="w3-btn">
				<i class="material-icons w3-large">email</i> Email
			</a>-->
		</h4>
	</header>
	<div class="w3-container">&nbsp;</div>
	<div class="w3-container">
		<div class="w3-col l2">&nbsp;</div>
		<div class="w3-col l8">
			<div id="questions-choices-container"></div>
			<div style="margin-top:20px;">
				<button id="back-button" style="float:left;" class="w3-btn w3-{{ $color1 }}" onClick="changePages('BACK');">Back</button>
				<button id="next-button" style="float:right;" class="w3-btn w3-{{ $color1 }}" onClick="changePages('NEXT');">Next</button>
				<button id="submit-button" style="float:right;" class="w3-btn" onClick="confirmSubmitAnswers();">Submit</button>
			</div>
		</div>
		<div class="w3-col l2">&nbsp;</div>
	</div>
	<div id="white-full-screen" style="top:0;left:0;width:100%;height:100%;background-color:white;position:fixed;"></div>
	<div id="password-box" class="w3-card-4" style="width:350px;position:fixed;left:50%;top:50%;background-color:#FFFFFF;margin:-85px 0px 0px -175px;">
		<header class="w3-container w3-{{ $color1 }}">
			<h4 style="text-align:center;">Password</h4>
		</header>
		<h5 style="padding:5px;text-align:center;">
			<input type="password" id="my-password" />
			<div id="password-error-message" class="error_messages"></div>
			<div><button class="w3-btn" id="ok-button" onClick="submitPassword();">OK</button></div>
		</h5>
	</div>
@endsection


@section('jquery-scripts')

	<script>
	
		function Response(question_id) {
			this.question_id = question_id;
			this.answers = [];
		}
		
		var password_ok = true;
		var loading_rotate = '<i style="color:#000000;" class="material-icons w3-xxxlarge w3-spin">refresh</i>';

		var at=1, total, questions_choices, responses = [];
		$(document).ready(function() {
			$("#white-full-screen").hide();
			$("#password-box").hide();
			$("#back-button").hide();
			$("#next-button").hide();
			$("#submit-button").hide();
			var survey_id = "{{ Crypt::encrypt($survey->survey_id)}}";
			var _token = $("#_token").val();
			var x;
			$.post("/surveys/get-questions-choices-db", {"survey_id":survey_id, "_token":_token}, function(all){
				all = JSON.parse(all);
				data = all["questions_choices"];
				questions_choices = data;
				total = data.length;
				var html_to_add;
				if (data.length > 0) {
					for (x=0; x<data.length; x++) {
						if (data[x]["question_type"] == "TEXT_ONLY") {
							html_to_add = createTextOnly(data[x]["question_num"], data[x]["question_id"], data[x]["question_text"]);
							$("#questions-choices-container").append(html_to_add);
						} else if (data[x]["question_type"] == "SINGLE_ANSWER") {
							html_to_add = createSingleAnswer(data[x]["question_num"], data[x]["question_id"], data[x]["question_text"], data[x]["choices"]);
							$("#questions-choices-container").append(html_to_add);
						} else if (data[x]["question_type"] == "MULTIPLE_ANSWER") {
							html_to_add = createMultipleAnswer(data[x]["question_num"], data[x]["question_id"], data[x]["question_text"], data[x]["choices"]);
							$("#questions-choices-container").append(html_to_add);
						} else if (data[x]["question_type"] == "SINGLE_RESPONSE") {
							html_to_add = createSingleResponse(data[x]["question_num"], data[x]["question_id"], data[x]["question_text"]);
							$("#questions-choices-container").append(html_to_add);
						}
					}
					prepareButtons();
					$(".question-container").hide();
					$("#card-num-"+at).show();
					if (all["password"].length > 0) {
						$("#white-full-screen").show();
						$("#password-box").show();
					}
				}
			});
		});
		
		function submitPassword() {
			var my_password = $("#my-password").val();
			var survey_id = "{{ Crypt::encrypt($survey->survey_id)}}";
			var _token = $("#_token").val();
			$("#password-error-message").html(loading_rotate);
			$("#ok-button").hide();
			$.post("/surveys/fill-password-db", {"survey_id":survey_id, "_token":_token, "my_password":my_password}, function(data){
				if (data["code"] == 1) {
					$("#white-full-screen").fadeOut();
					$("#password-box").fadeOut();
					$("#password-error-message").html("");
					password_ok = true;
				} else {
					$("#password-error-message").html("Password is incorrect.");
					$("#ok-button").show();
					password_ok = false;
				}
			});
		}
		
		
		function confirmSubmitAnswers() {
			if (confirm("Are you sure you want to submit your answers?")) {
				$("#questions-choices-container").fadeOut("fast");
				$("#back-button").hide();
				$("#next-button").hide();
				$("#submit-button").hide();
				submitAnswers();
			}
		}
		
		function submitAnswers() {
			var x, id;
			var survey_id = "{{ Crypt::encrypt($survey->survey_id)}}";
			var _token = $("#_token").val();
			var unique_respondent_id = $("#unique_respondent_id").val();
			for (x=0; x<total; x++) {
				id = questions_choices[x]["question_id"];
				responses[x] = new Response(id);
				if (questions_choices[x]["question_type"] == "SINGLE_ANSWER") {
					responses[x]["answers"] = [ $("input[name='question-id-"+id+"']:checked").val() ];
				} else if (questions_choices[x]["question_type"] == "MULTIPLE_ANSWER") {
					$('input[name=question-id-'+id+']:checked').map(function() {
						responses[x]["answers"].push($(this).val());
					});
				} else if (questions_choices[x]["question_type"] == "SINGLE_RESPONSE") {
					responses[x]["answers"] = [ $("#question-id-"+id).val() ];
				}
			}
			if (password_ok) {
				$.post("/surveys/save-responses-db", {"survey_id":survey_id, "responses":JSON.stringify(responses), "_token":_token, "unique_respondent_id":unique_respondent_id}, function(data){
					//do here
				});
			} else {
				alert("You skipped the password.");
			}
		}
		
		
		function changePages(move) {
			var prev = at;
			$("#back-button").show();
			$("#next-button").show();
			$("#submit-button").hide();
			if (move == "NEXT") {
				if (at < total) {
					at++;
				}
			} else if (move == "BACK") {
				if (at > 1) {
					at--;
				}
			}
			if (at == 1) {
				$("#back-button").hide();
			}
			if (at == total) {
				$("#next-button").hide();
				$("#submit-button").show();
			}
			submitAnswers();
			//console.log("At: " + at + "\nTotal: " + total);
			$("#card-num-"+prev).slideUp();
			$("#card-num-"+at).slideDown();
		}
		
		
		function prepareButtons() {
			$("#back-button").hide();
			$("#submit-button").hide();
			if (total > 0) {
				$("#next-button").show();
			}
			if (at == total) {
				$("#next-button").hide();
				$("#submit-button").show();
			}
		}
		
		
		function createTextOnly(num, id, text) {
			var temp_str = '<div class="question-container w3-card-4" id="card-num-'+num+'" style="position:relative">';
			temp_str += '<header class="w3-container w3-{{ $color1 }}"><h4>'+text+'</h4></header>';
			temp_str += '</div>';
			return temp_str;
		}
		
		function createSingleAnswer(num, id, text, choices) {
			var x;
			var temp_str = '<div class="question-container w3-card-4" id="card-num-'+num+'" style="position:relative">';
			temp_str += '<header class="w3-container w3-{{ $color1 }}"><h4>'+text+'</h4></header>';
			temp_str += '<div class="w3-container">';
			if (choices.length > 0) {
				for (x=0; x<choices.length; x++) {
					temp_str += "<div style='clear:both;'>";
					temp_str += "<div style='float:left;width:40px;'>";
					temp_str += '<label class="w3-checkbox">';
					temp_str += '<input type="radio" id="question-id-'+id+'-'+x+'" name="question-id-'+id+'" value="'+choices[x]["choice_id"]+'" /><div class="w3-checkmark"/>';
					temp_str += '</label>';
					temp_str += "</div>";
					temp_str += "<div style='float:left;padding-top:10px;'><label for='question-id-"+id+"-"+x+"'>"+choices[x]["choice_text"]+"</label></div>";
					temp_str += "</div>";
				}
			}
			temp_str += '</div>';
			temp_str += '</div>';
			return temp_str;
		}
		
		function createMultipleAnswer(num, id, text, choices) {
			var x;
			var temp_str = '<div class="question-container w3-card-4" id="card-num-'+num+'" style="position:relative">';
			temp_str += '<header class="w3-container w3-{{ $color1 }}"><h4>'+text+'</h4></header>';
			temp_str += '<div class="w3-container">';
			if (choices.length > 0) {
				for (x=0; x<choices.length; x++) {
					temp_str += "<div style='clear:both;'>";
					temp_str += "<div style='float:left;width:40px;'>";
					temp_str += '<label class="w3-checkbox">';
					temp_str += '<input type="checkbox" id="question-id-'+id+'-'+x+'" name="question-id-'+id+'" value="'+choices[x]["choice_id"]+'" /><div class="w3-checkmark"/>';
					temp_str += '</label>';
					temp_str += "</div>";
					temp_str += "<div style='float:left;padding-top:10px;'><label for='question-id-"+id+"-"+x+"'>"+choices[x]["choice_text"]+"</label></div>";
					temp_str += "</div>";
				}
			}
			temp_str += '</div>';
			temp_str += '</div>';
			return temp_str;
		}
		
		function createSingleResponse(num, id, text) {
			var temp_str = '<div class="question-container w3-card-4" id="card-num-'+num+'" style="position:relative">';
			temp_str += '<header class="w3-container w3-{{ $color1 }}"><h4>'+text+'</h4></header>';
			temp_str += '<div class="w3-container" style="padding:10px;">';
			temp_str += '<input class="w3-input" type="text" style="width:100%;" name="question-id-'+id+'" id="question-id-'+id+'" />';
			temp_str += '</div>';
			temp_str += '</div>';
			return temp_str;
		}
		
	
	</script>

@endsection