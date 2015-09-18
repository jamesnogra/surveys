@extends('my_main')


@section('page-title')
    Creating Survey - {{ $survey->title }}
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h1 style="float:left;">
			<small><a href="/surveys/my-surveys-page" class="w3-btn">
				<i class="material-icons">payment</i> My Surveys
			</a></small>
		</h1>
		<h4 style="float:right;padding-top:5px;">
			<a href="/users/view-user-page/{{ urlencode($name) }}/{{ Crypt::encrypt($user_id) }}" class="w3-btn">
				<i class="material-icons w3-large">person</i> My Profile
			</a>
			<a href="/users/logout" class="w3-btn">
				<i class="material-icons w3-large">arrow_forward</i> Logout
			</a>
		</h4>
	</header>
	<div class="w3-container">
		<div class="w3-row">
			<div class="w3-col l2">&nbsp;</div>
			<div class="w3-col l8">
				<div class="w3-group">
					<h2>{{ $survey->title }}</h2>
					<p>{{ $survey->description }}</p>
					<button id="add-question-button" class="w3-btn w3-{{ $color1 }}">Add Question</button>
				</div>
				<div class="w3-group" id="questions-container">
				</div>
			</div>
			<div class="w3-col l2">&nbsp;</div>
		</div>
	</div>
@endsection


@section('jquery-scripts')
	<script>
	
		var question_num = 0;
		var question_num_with_deleted = 0;
		var choices_num = new Array();
	
		$(document).ready(function() {
			
			$("#add-question-button").click(function() {
				question_num++;
				question_num_with_deleted++;
				updateQuestionNumberDropDownList()
				$("#questions-container").append(addTextOnlyTemplate());
				$('#question-container-'+question_num).hide();
				$('#question-container-'+question_num).fadeIn('fast');
				console.log("Number of existing questions: " + question_num + "\nNumber of questions including deleted ones: " + question_num_with_deleted + "\nNumber of Deleted Questions: " + (question_num_with_deleted-question_num));
			});
			
		});
		
		//this template already contains the card container, only the contents of this will be replaced when question type is changed
		function addTextOnlyTemplate() {
			var temp_str = "";
			temp_str += '<div class="w3-card-4" style="width:100%;margin-bottom:10px;" id="question-container-'+question_num_with_deleted+'"> 						\
							<header class="w3-container w3-{{ $color1 }}">																\
								<h5 style="float:left;">																					\
									<div style="margin-right:20px;float:left;">Question Number: '+addQuestionNumbersDropDown(question_num_with_deleted)+'</div>												\
									<div style="margin-right:20px;float:left;">Question Type: '+addQuestionTypeDropDown(question_num_with_deleted)+'</div>										\
								</h5>																											\
								<h5 style="float:right;">																				\
									<button class="w3-btn" onClick="deleteQuestion('+question_num_with_deleted+');">										\
										<i class="material-icons w3-tiny">clear</i> Delete												\
									</button>																							\
								</h5>																											\
							</header>																									\
							<div class="w3-container" id="question-choices-'+question_num_with_deleted+'">													\
								<div class="w3-group"> 																					\
									<input id="question-'+question_num_with_deleted+'-type" type="hidden" value="TEXT_ONLY">													\
									<input id="question-'+question_num_with_deleted+'-text" class="w3-input question-text" type="text">													\
									<label class="w3-label">Text</label>																\
								</div>																						\
							</div>																										\
						</div>';
			return temp_str;
		}
		
		function addTextOnlyTemplateContent(q) {
			choices_num[question_num_with_deleted] = 0;
			var temp_str = '<div class="w3-group">';
			temp_str += '<input id="question-'+q+'-type" type="hidden" value="TEXT_ONLY">';
			temp_str += '<input id="question-'+q+'-text" class="w3-input question-text" type="text">';
			temp_str += '<label class="w3-label">Text</label>';
			temp_str += '</div>';
			return temp_str;
		}
		
		function addSingleAnswerQuestionTemplate(q) {
			choices_num[q] = 1;
			var temp_str = '<div class="w3-group">';
					temp_str += '<input id="question-'+q+'-type" type="hidden" value="SINGLE_ANSWER">';
					temp_str += '<input id="question-'+q+'-text" class="w3-input question-text" type="text">'
					temp_str += '<label class="w3-label">Question</label>';
					temp_str += '<div id="choices-container-'+q+'" style="margin-top:10px;">';
					temp_str += '<label class="w3-checkbox" style="width:100%;">';
						temp_str += '<input type="radio" checked="checked" name="for-question-'+q+'" id="radio-choice-'+q+'-'+choices_num[q]+'">';
						temp_str += '<div class="w3-checkmark" style="float:left;"></div>';
						temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="radio-text-'+q+'-'+choices_num[q]+'" type="text"></div>';
					temp_str += '</label><br>';
				temp_str += '</div>';
				temp_str += '<div class="w3-group">';
					temp_str += '<button onClick="addAnotherSingleAnswerResponse('+q+');" class="w3-btn w3-{{ $color1 }}">Add Another Answer</button>';
				temp_str += '</div>';
			temp_str += '</div>';
			return temp_str;
		}
		function addAnotherSingleAnswerResponse(q) {
			choices_num[q]++;
			var temp_str = '<label class="w3-checkbox" style="width:100%;">';
					temp_str += '<input type="radio" name="for-question-'+q+'" id="radio-choice-'+q+'-'+choices_num[q]+'">';
					temp_str += '<div class="w3-checkmark" style="float:left;"></div>';
					temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="radio-text-'+q+'-'+choices_num[q]+'" type="text"></div>';
			temp_str += '</label><br>';
			$("#choices-container-"+q).append(temp_str);
		}
		
		function addMultipleAnswerQuestionTemplate(q) {
			choices_num[q] = 1;
			var temp_str = '<div class="w3-group">';
					temp_str += '<input id="question-'+q+'-type" type="hidden" value="MULTIPLE_ANSWER">';
					temp_str += '<input id="question-'+q+'-text" class="w3-input question-text" type="text">'
					temp_str += '<label class="w3-label">Question</label>';
					temp_str += '<div id="choices-container-'+q+'" style="margin-top:10px;">';
					temp_str += '<label class="w3-checkbox" style="width:100%;">';
						temp_str += '<input type="checkbox" checked="checked" name="for-question-'+q+'" id="checkbox-choice-'+q+'-'+choices_num[q]+'">';
						temp_str += '<div class="w3-checkmark" style="float:left;"></div>';
						temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="checkbox-text-'+q+'-'+choices_num[q]+'" type="text"></div>';
					temp_str += '</label><br>';
				temp_str += '</div>';
				temp_str += '<div class="w3-group">';
					temp_str += '<button onClick="addAnotherMultipleAnswerResponse('+q+');" class="w3-btn w3-{{ $color1 }}">Add Another Answer</button>';
				temp_str += '</div>';
			temp_str += '</div>';
			return temp_str;
		}
		function addAnotherMultipleAnswerResponse(q) {
			choices_num[q]++;
			var temp_str = '<label class="w3-checkbox" style="width:100%;">';
					temp_str += '<input type="checkbox" name="for-question-'+q+'" id="checkbox-choice-'+q+'-'+choices_num[q]+'">';
					temp_str += '<div class="w3-checkmark" style="float:left;"></div>';
					temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="checkbox-text-'+q+'-'+choices_num[q]+'" type="text"></div>';
			temp_str += '</label><br>';
			$("#choices-container-"+q).append(temp_str);
		}
		
		function addQuestionTypeDropDown(q) {
			var temp_str = "<select onChange='changeQuestionType("+q+");' style='color:#000000;' class='w3' id='drop-down-question-"+q+"'>";
				temp_str += "<option value='1' selected>Text Only</option>";
				temp_str += "<option value='2'>Single Answer</option>";
				temp_str += "<option value='3'>Multiple Answer</option>";
				temp_str += "<option value='4'>Single Image</option>";
				temp_str += "<option value='5'>Multiple Image</option>";
			temp_str += "</select>";
			return temp_str;
		}
		
		function addQuestionNumbersDropDown(q) {
			var x;
			var temp_str = "<select class='all-drop-down-question-number' onChange='' style='color:#000000;' class='w3' id='drop-down-question-number-"+q+"'>";
				for (x=1; x<=question_num_with_deleted; x++) {
					if (q == x) {
						temp_str += "<option selected value='"+x+"'>"+x+"</option>";
					} else {
						temp_str += "<option value='"+x+"'>"+x+"</option>";
					}
				}
				temp_str += "</select>";
			return temp_str;
		}
		
		function updateQuestionNumberDropDownList() {
			var x, y;
			var temp_str;
			for (x=1; x<=question_num_with_deleted; x++) {
				if ($("#drop-down-question-number-"+x).length) { //check if this question card still exists
					$("#drop-down-question-number-"+x).empty();
					for (y=1; y<=question_num_with_deleted; y++) {
						if (x == y) {
							$("#drop-down-question-number-"+x).append("<option selected value='"+y+"'>"+y+"</option>");
						} else {
							$("#drop-down-question-number-"+x).append("<option value='"+y+"'>"+y+"</option>");
						}
					}
				}
			}
		}
		
		function changeQuestionType(q) {
			var new_type = $("#drop-down-question-"+q).val();
			if (new_type == 1) {
				$("#question-choices-"+q).html(addTextOnlyTemplateContent(q));
			} else if (new_type == 2) {
				$("#question-choices-"+q).html(addSingleAnswerQuestionTemplate(q));
			} else if (new_type == 3) {
				$("#question-choices-"+q).html(addMultipleAnswerQuestionTemplate(q));
			}
		}
		
		function deleteQuestion(q) {
			question_num--;
			$("#question-container-"+q).fadeOut('fast', function() {
				$("#question-container-"+q).remove();
			});
			updateQuestionNumberDropDownList();
		}
		
	</script>
@endsection