@extends('my_main')


@section('page-title')
    Creating Survey - {{ $survey->title }}
@endsection


@section('content')
	<header class="w3-container w3-{{ $color1 }}">
		<h4 style="float:left;">
			<a href="/surveys/my-surveys-page" class="w3-btn">
				<i class="material-icons">payment</i> My Surveys
			</a>
		</h4>
		<h4 style="float:right;">
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
					<div class="w3-group">
						<input type="hidden" id="_token" name="_token" value="{{ csrf_token() }}" />
						<input type="hidden" id="survey_id" name="survey_id" value="{{ $survey_id }}" />
						<input name="the_title" id="the_title" class="w3-input" type="text" value="{{ $survey->title }}" onBlur="updateValueToDB('title', this.value);" style="width:100%;font-size:32px;">
						<label class="w3-label">Title</label>
						<div id="the_title_error" class="error_messages"></div>
					</div>
					<div class="w3-group"> 
						<textarea name="the_description" id="the_description" class="w3-input" onBlur="updateValueToDB('description', this.value);" style="width:100%;resize:none;">{{ $survey->description }}</textarea>
						<label class="w3-label">Description</label>
						<div id="the_description_error" class="error_messages"></div>
					</div>
					<div class="w3-group"> 
						<input name="the_password" id="the_password" class="w3-input" type="password" onBlur="updateValueToDB('password', this.value);" value="{{ $survey->password }}" style="width:100%;">
						<label class="w3-label">Password <small>(Leave blank if survey is not password protected.)</small></label>
						<div id="the_password_error" class="error_messages"></div>
					</div>
					<div class="w3-group"> 
						<form enctype="multipart/form-data">
							<input type="hidden" id="the_file_name_logo" name="the_file_name_logo" value="" />
							<div class="w3-card-12" style="height:50px;float:left;" id="logo_preview"><img height="100%" src="/images/logo/{{$survey->logo}}" /></div>
							<div class="w3-label" style="float:left;margin-left:100px;">Logo: </div>
							<div style="margin-left:150px;overflow:hidden;"><input name="file" type="file" id="the_logo" name="the_logo" accept="image/*" /></div>
							<div id="the_logo_error" class="error_messages"></div>
						</form>
					</div>
					<hr>
					<div class="w3-group"> 
						<p>
							<button id="generate-link-code-db" class="w3-btn"><i class="material-icons w3-small">link</i> Generate New Link</button> 
							<input style="width:400px;" type="text" readonly id="old-link-input-field" value="{{ URL::to('/') . '/surveys/answer-survey-page/' . $survey->title . '/' . $survey->link_code}}" />
							<a class="w3-btn" target="_blank" href="{{ URL::to('/') . '/surveys/answer-survey-page/' . $survey->title . '/' . $survey->link_code}}"><i class="material-icons w3-small">pageview</i> Preview</a>
						</p>
						<hr>
						<button id="add-question-button" class="w3-btn w3-{{ $color1 }}"><i class="material-icons w3-large">+</i> Add Question</button>
						<button id="save-survey-questions-choices-db" class="w3-btn"><i class="material-icons w3-small">save</i> Save</button>
						<a class="w3-btn" href="/users/change-theme-page/survey/{{ $survey_id }}"><i class="material-icons w3-small">invert_colors</i> Change Theme</a>
						<a class="w3-btn" href="/users/change-theme-page/survey/{{urlencode($survey->title)}}/{{ $survey_id }}"><i class="material-icons w3-small">trending_up</i> Show Results</a>
						<div id="has-responses" class="w3-container w3-red" style="margin-top:10px;">This survey has been answered by a couple of respondents. If you edit this, all of those responses will be cleared.</div>
					</div>
				</div>
				<hr>
				<div class="w3-group" id="questions-container"></div>
				<div class="w3-container" id="loading-form" style="text-align:center;"><i class="material-icons w3-xxxlarge w3-spin">refresh</i></div>
			</div>
			<div class="w3-col l2">&nbsp;</div>
		</div>
	</div>
	
	<style>html,body{ height:100%; }</style>
	<div id="white-full-screen" style="top:0;left:0;width:100%;height:100%;background-color:white;position:fixed;opacity:0.9;"></div>
	<div id="link-box" class="w3-card-4" style="width:350px;height:120px;position:fixed;left:50%;top:50%;background-color:#FFFFFF;margin:-60px 0px 0px -175px;">
		<header class="w3-container w3-{{ $color1 }}">
			<h4 style="float:left;">Links</h4>
			<h4 style="float:right;cursor:pointer;" onClick="closeLinkBox();"><i class="material-icons w3-large">close</i></h4>
		</header>
		<h5 style="padding:5px;text-align:center;">
			Direct Link: <input type="text" readonly id="link-input-field" />
		</h5>
	</div>
@endsection


@section('jquery-scripts')
	<script>
	
		function Question(question_id, question_text, question_type)  {
			this.question_id = question_id;
			this.question_text = question_text;
			this.question_type = question_type;
			this.choices = [];
		};
		function Choice(choice_id, choice_text) {
			this.choice_id = choice_id;
			this.choice_text = choice_text;
		}
		var all_questions = [];
		
	
		var survey_theme = "indigo";
		$(document).ready(function() {
			
			beforeLoading();
			getQuestionsChoices();
			closeLinkBox();
			checkResponses();
			function getQuestionsChoices() {
				var _token = $("#_token").val();
				var survey_id = $("#survey_id").val();
				$.post("/surveys/get-questions-choices-db", {"survey_id":survey_id, "_token":_token}, function(all){
					all = JSON.parse(all);
					data = all["questions_choices"];
					setSurveyTheme(all["theme"]);
					if (data.length > 0) {
						var x, y;
						var temp_array = [];
						for (x=0; x<data.length; x++) {
							temp_array[x] = new Question("question-id-"+x, data[x]["question_text"], data[x]["question_type"]);
							if (data[x]["choices"].length>0) {
								for (y=0; y<data[x]["choices"].length; y++) {
									temp_array[x]["choices"][y] = new Choice("choice-id-"+y, data[x]["choices"][y]["choice_text"]);
								}
							}
						}
						all_questions = temp_array;
						resetQuestionsIncludingUI();
					}
					afterLoading();
				});
			}
			
			function setSurveyTheme(color) {
				if (color != "default") {
					survey_theme = color;
				}
			}
			
			function beforeLoading() {
				$("#loading-form").show();
				$("#questions-container").hide();
				$("#save-survey-questions-choices-db").hide();
			}
			function afterLoading() {
				$("#loading-form").hide();
				$("#save-survey-questions-choices-db").show();
				$("#questions-container").show();
			}
			
			$("#save-survey-questions-choices-db").click(function() {
				var _token = $("#_token").val();
				var temp_array = JSON.stringify(all_questions);
				var survey_id = $("#survey_id").val();
				beforeLoading();
				$.post("/surveys/save-survey-questions-choices-db", {"survey_id":survey_id, "questions_choices":temp_array, "_token":_token}, function(data){
					afterLoading();
				});
			});
			
			$("#add-question-button").click(function() {
				var at_question = all_questions.length;
				all_questions[at_question] = new Question();
				updateQuestionNumberDropDownList()
				$("#questions-container").append(addTextOnlyTemplate(at_question));
				$('#question-container-'+at_question).hide();
				$('#question-container-'+at_question).fadeIn('fast');
				//console.log("Number of existing questions: " + question_num + "\nNumber of questions including deleted ones: " + question_num_with_deleted + "\nNumber of Deleted Questions: " + (question_num_with_deleted-question_num));
			});
			
			$("#generate-link-code-db").click(function() {
				var _token = $("#_token").val();
				var survey_id = $("#survey_id").val();
				if (confirm("Are you sure to create new link for this survey? The old one will be deleted.")) {
					$("#generate-link-code-db").hide();
					$.post("/surveys/generate-link-code-db", {"survey_id":survey_id, "_token":_token}, function(data){
						//data = JSON.parse(data);
						$("#generate-link-code-db").show();
						if (data["code"] == 1) {
							$("#link-input-field").val(data["link"]);
							openLinkBox();
						}
					});
				}
			});
			
			$("#the_logo").on("change", function (e) {
				$("#the_logo_error").html('<i style="color:#000000;" class="material-icons w3-xxxlarge w3-spin">refresh</i>');
				$("#create-survey-button").hide();
				var file_data = $("#the_logo").prop("files")[0];   			// Getting the properties of file from file field
				var form_data = new FormData();                  			// Creating object of FormData class
				form_data.append("file", file_data)              			// Adding extra parameters to form_data
				form_data.append("_token", $("#_token").val())              // Adding extra parameters to form_data
				$.ajax({
					url: '/surveys/upload-logo',
					dataType: 'json',
					cache: false,
					contentType: false,
					processData: false,
					data: form_data,                         				// Setting the data attribute of ajax with file_data
					type: 'post',
					success: function(data) {
						$("#create-survey-button").show();
						if (data["code"] == 1) {
							$("#the_logo_error").html("");
							$("#logo_preview").html("<img height='100%' src='"+data["full_path"]+"' />");
							$("#the_file_name_logo").val(data["file_name"]);
							updateValueToDB("logo", data["file_name"]);
						} else if (data["code"] == -1) {
							$("#logo_preview").html("");
							$("#the_logo_error").html("Please upload a valid image file.");
							$("#the_file_name_logo").val("");
						} else {
							$("#logo_preview").html("");
							$("#the_logo_error").html("Something went wrong during the logo upload.");
							$("#the_file_name_logo").val("");
						}
						}, error: function(err) {
						$("#the_logo_error").html("Something went wrong during the logo upload.");
						$("#logo_preview").html("");
						$("#the_file_name_logo").val("");
					}
				});
			});
			
		});
		
		
		function closeLinkBox() {
			$("#white-full-screen").fadeOut("fast");
			$("#link-box").fadeOut("fast");
		}
		function openLinkBox() {
			$("#white-full-screen").fadeIn("fast");
			$("#link-box").fadeIn("fast");
		}
		
		function checkResponses() {
			var _token = $("#_token").val();
			var survey_id = $("#survey_id").val();
			$("#has-responses").hide();
			$.post("/surveys/check-responses-db", {"survey_id":survey_id, "_token":_token}, function(data){
				data = JSON.parse(data);
				if (data.length > 0) {
					$("#has-responses").show();
				}
			});
		}
		
		//this template already contains the card container, only the contents of this will be replaced when question type is changed
		function addTextOnlyTemplate(q, question_text) {
			var temp_str = "";
			temp_str += '<div class="w3-card-4" style="width:100%;margin-bottom:20px;" id="question-container-'+q+'"> 						\
							<header class="w3-container w3-'+survey_theme+'">																\
								<h5 style="float:left;">																					\
									<div style="margin-right:20px;float:left;">Question Number: '+addQuestionNumbersDropDown(q)+'</div>												\
									<div style="margin-right:20px;float:left;">Question Type: '+addQuestionTypeDropDown(q)+'</div>										\
								</h5>																											\
								<h5 style="float:right;">																				\
									<button class="w3-btn" onClick="deleteQuestion('+q+');">										\
										<i class="material-icons w3-tiny">clear</i> Delete												\
									</button>																							\
								</h5>																											\
							</header>																									\
							<div class="w3-container" id="question-choices-'+q+'">													\
								<div class="w3-group"> 																					\
									<input id="question-'+q+'-type" type="hidden" value="TEXT_ONLY">';
			if (typeof question_text === 'undefined') {
				temp_str +=			'<input id="question-'+q+'-text" class="w3-input question-text" type="text" onBlur="changeQuestionValue('+q+');">';
			} else {
				temp_str +=			'<input id="question-'+q+'-text" class="w3-input question-text" type="text" onBlur="changeQuestionValue('+q+');" value="'+question_text+'">';
			}
			temp_str +=				'<label class="w3-label">Text</label>																\
								</div>																						\
							</div>																										\
						</div>';
			return temp_str;
		}
		
		//this function will be called onBlur of any type of question
		//this function is called when a particular question textbox is edited
		function changeQuestionValue(q) {
			all_questions[q].question_id = "question-id-"+q;
			all_questions[q].question_text = $("#question-"+q+"-text").val();
			all_questions[q].question_type = $("#question-"+q+"-type").val();
			//console.log(JSON.stringify(all_questions, null, 4));
		}
		
		//this function will be called when a particular choice is edited
		function changeChoiceValue(q, c) {
			//console.log("At question "+q+" and choice "+c+" .");
			all_questions[q].choices[c].choice_id = "choice-id-"+q+"-"+c;
			all_questions[q].choices[c].choice_text = $("#text-"+q+"-"+c).val();
		}
		
		function addTextOnlyTemplateContent(q) {
			var temp_str = '<div class="w3-group">';
			temp_str += '<input id="question-'+q+'-type" type="hidden" value="TEXT_ONLY">';
			temp_str += '<input id="question-'+q+'-text" class="w3-input question-text" type="text" onBlur="changeQuestionValue('+q+');">';
			temp_str += '<label class="w3-label">Text</label>';
			temp_str += '</div>';
			return temp_str;
		}
		
		function addSingleAnswerQuestionTemplate(q, question_text) {
			var temp_str = '<div class="w3-group">';
					temp_str += '<input id="question-'+q+'-type" type="hidden" value="SINGLE_ANSWER">';
					if (typeof question_text === 'undefined') {
						all_questions[q].choices[0] = new Choice(); //initialize first choice if this is not edit
						temp_str += '<input id="question-'+q+'-text" class="w3-input question-text" type="text" onBlur="changeQuestionValue('+q+');">';
					} else {
						temp_str += '<input id="question-'+q+'-text" class="w3-input question-text" type="text" onBlur="changeQuestionValue('+q+');" value="'+question_text+'">';
					}
					temp_str += '<label class="w3-label">Question</label>';
					temp_str += '<div id="choices-container-'+q+'" style="margin-top:10px;">';
					temp_str += '<label class="w3-checkbox" style="width:100%;">';
						temp_str += '<input type="radio" checked="checked" name="for-question-'+q+'" id="choice-'+q+'-0">';
						temp_str += '<div class="w3-checkmark" style="float:left;"></div>';
						temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="text-'+q+'-0" type="text" onBlur="changeChoiceValue('+q+', 0);"></div>';
					temp_str += '</label><br>';
				temp_str += '</div>';
				temp_str += '<div class="w3-group">';
					temp_str += '<button onClick="addAnotherSingleAnswerResponse('+q+');" class="w3-btn w3-'+survey_theme+'">Add Another Answer</button>';
				temp_str += '</div>';
			temp_str += '</div>';
			return temp_str;
		}
		function addAnotherSingleAnswerResponse(q, choice_text) {
			var at_choice_index = all_questions[q].choices.length;
			all_questions[q].choices[at_choice_index] = new Choice(); //initialize another choice when user adds another choice
			var temp_str = addAnotherSingleAnswerResponseUI(q, at_choice_index, choice_text);
			$("#choices-container-"+q).append(temp_str);
		}
		function addAnotherSingleAnswerResponseUI(q, at_choice_index, choice_text) {
			var temp_str = '<label class="w3-checkbox" style="width:100%;">';
						temp_str += '<input type="radio" name="for-question-'+q+'" id="choice-'+q+'-'+at_choice_index+'">';
						temp_str += '<div class="w3-checkmark" style="float:left;"></div>';
						if (typeof choice_text === 'undefined') {
							temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="text-'+q+'-'+at_choice_index+'" type="text" onBlur="changeChoiceValue('+q+', '+at_choice_index+');"></div>';
						} else {
							temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="text-'+q+'-'+at_choice_index+'" type="text" onBlur="changeChoiceValue('+q+', '+at_choice_index+');" value="'+choice_text+'"></div>';						
						}
					temp_str += '</label><br>';
			return temp_str;
		}
		
		function addMultipleAnswerQuestionTemplate(q, choice_text) {
			var temp_str = '<div class="w3-group">';
					temp_str += '<input id="question-'+q+'-type" type="hidden" value="MULTIPLE_ANSWER">';
					if (typeof choice_text === 'undefined') {
						all_questions[q].choices[0] = new Choice(); //initialize first choice
						temp_str += '<input id="question-'+q+'-text" class="w3-input question-text" type="text" onBlur="changeQuestionValue('+q+');">';
					} else {
						temp_str += '<input id="question-'+q+'-text" class="w3-input question-text" type="text" onBlur="changeQuestionValue('+q+');" value="'+choice_text+'">';
					}
					temp_str += '<label class="w3-label">Question</label>';
					temp_str += '<div id="choices-container-'+q+'" style="margin-top:10px;">';
					temp_str += '<label class="w3-checkbox" style="width:100%;">';
						temp_str += '<input type="checkbox" checked="checked" name="for-question-'+q+'" id="choice-'+q+'-0">';
						temp_str += '<div class="w3-checkmark" style="float:left;"></div>';
						temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="text-'+q+'-0" type="text" onBlur="changeChoiceValue('+q+', 0);"></div>';
					temp_str += '</label><br>';
				temp_str += '</div>';
				temp_str += '<div class="w3-group">';
					temp_str += '<button onClick="addAnotherMultipleAnswerResponse('+q+');" class="w3-btn w3-'+survey_theme+'">Add Another Answer</button>';
				temp_str += '</div>';
			temp_str += '</div>';
			return temp_str;
		}
		function addAnotherMultipleAnswerResponse(q, choice_text) {
			var at_choice_index = all_questions[q].choices.length;
			all_questions[q].choices[at_choice_index] = new Choice(); //initialize another choice when user adds another choice
			var temp_str = addAnotherMultipleAnswerResponseUI(q, at_choice_index, choice_text);
			$("#choices-container-"+q).append(temp_str);
		}
		function addAnotherMultipleAnswerResponseUI(q, at_choice_index, choice_text) {
			var temp_str = '<label class="w3-checkbox" style="width:100%;">';
					temp_str += '<input type="checkbox" name="for-question-'+q+'" id="choice-'+q+'-'+at_choice_index+'">';
					temp_str += '<div class="w3-checkmark" style="float:left;"></div>';
					if (typeof choice_text === 'undefined') {
						temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="text-'+q+'-'+at_choice_index+'" type="text" onBlur="changeChoiceValue('+q+', '+at_choice_index+');"></div>';
					} else {
						temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="text-'+q+'-'+at_choice_index+'" type="text" onBlur="changeChoiceValue('+q+', '+at_choice_index+');" value="'+choice_text+'"></div>';						
					}
				temp_str += '</label><br>';
			return temp_str;
		}
		
		function addSingleResponseQuestionTemplate(q, choice_text) {
			var temp_str = '<div class="w3-group">';
					temp_str += '<input id="question-'+q+'-type" type="hidden" value="SINGLE_RESPONSE">';
					if (typeof choice_text === 'undefined') {
						all_questions[q].choices[0] = new Choice(); //initialize first choice
						temp_str += '<input id="question-'+q+'-text" class="w3-input question-text" type="text" onBlur="changeQuestionValue('+q+');">';
					} else {
						temp_str += '<input id="question-'+q+'-text" class="w3-input question-text" type="text" onBlur="changeQuestionValue('+q+');" value="'+choice_text+'">';
					}
					temp_str += '<label class="w3-label">Question</label>';
					temp_str += '<div id="choices-container-'+q+'" style="margin-top:10px;">';
					temp_str += '<label class="w3-checkbox" style="width:100%;">';
					temp_str += '<div style="float:left;">Answer: </div>';
					temp_str += '<div style="float:right;width:90%;"><input style="width:100%;" id="text-'+q+'-0" type="text" disabled></div>';
					temp_str += '</label><br>';
					temp_str += '</div>';
				temp_str += '</div>';
			return temp_str;
		}
		
		function addQuestionTypeDropDown(q) {
			var temp_str = "<select onChange='changeQuestionType("+q+");' style='color:#000000;' class='w3' id='drop-down-question-"+q+"'>";
				temp_str += "<option value='TEXT_ONLY' selected>Text Only</option>";
				temp_str += "<option value='SINGLE_ANSWER'>Single Answer</option>";
				temp_str += "<option value='MULTIPLE_ANSWER'>Multiple Answer</option>";
				temp_str += "<option value='SINGLE_RESPONSE'>Single Response</option>";
				/*temp_str += "<option value='4'>Single Image</option>";
				temp_str += "<option value='5'>Multiple Image</option>";*/
			temp_str += "</select>";
			return temp_str;
		}
		
		function addQuestionNumbersDropDown(q) {
			var x;
			var temp_str = "<select class='all-drop-down-question-number' onChange='changeQuestionNumber("+q+");' style='color:#000000;' class='w3' id='drop-down-question-number-"+q+"'>";
				for (x=1; x<=all_questions.length; x++) {
					if (q == (x-1)) {
						temp_str += "<option selected value='"+x+"'>"+x+"</>";
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
			for (x=0; x<all_questions.length; x++) {
				if ($("#drop-down-question-number-"+x).length) { //check if this question card still exists
					$("#drop-down-question-number-"+x).empty();
					for (y=0; y<all_questions.length; y++) {
						if (x == y) {
							$("#drop-down-question-number-"+x).append("<option selected value='"+(y+1)+"'>"+(y+1)+"</option>");
						} else {
							$("#drop-down-question-number-"+x).append("<option value='"+(y+1)+"'>"+(y+1)+"</option>");
						}
					}
				}
			}
		}
		
		//q is the index to be changed to what has been selected in its dropdown
		function changeQuestionNumber(q) {
			var index_from = q;
			var index_to = $("#drop-down-question-number-"+q).val()-1;
			//console.log("Move question " + (index_from+1) + " to question " + (index_to+1));
			all_questions.splice(index_to, 0, all_questions.splice(index_from, 1)[0]);
			$("#questions-container").fadeOut('fast', function() {
				resetQuestionsIncludingUI();
				$("#questions-container").fadeIn('fast');
			});
		}
		
		function changeQuestionType(q) {
			var new_type = $("#drop-down-question-"+q).val();
			if (new_type == "TEXT_ONLY") {
				$("#question-choices-"+q).html(addTextOnlyTemplateContent(q));
			} else if (new_type == "SINGLE_ANSWER") {
				$("#question-choices-"+q).html(addSingleAnswerQuestionTemplate(q));
			} else if (new_type == "MULTIPLE_ANSWER") {
				$("#question-choices-"+q).html(addMultipleAnswerQuestionTemplate(q));
			} else if (new_type == "SINGLE_RESPONSE") {
				$("#question-choices-"+q).html(addSingleResponseQuestionTemplate(q));
			}
		}
		
		function deleteQuestion(q) {
			all_questions[q].question_id = "DELETED";
			all_questions[q].question_text = "DELETED";
			all_questions[q].question_type = "DELETED";
			$("#questions-container").fadeOut('fast', function() {
				resetQuestionsIncludingUI();
				updateQuestionNumberDropDownList();
				$("#questions-container").fadeIn('fast');
			});
		}
		
		//this will clear all the DELETED items in the all_questions array
		//and creates the array again
		function resetQuestionsIncludingUI() {
			$("#questions-container").html(""); //empty the question cards
			var x, y;
			var counter = 0; //will be the index incremented if a question is not type of DELETED
			var temp_array_questions = [];
			var html_to_add = "";
			for (x=0; x<all_questions.length; x++) {
				if (all_questions[x].question_text != "DELETED") {
					html_to_add = addTextOnlyTemplate(counter, all_questions[x].question_text);
					$("#questions-container").append(html_to_add);
					$("#drop-down-question-number-"+counter).val(); //set the page number
					temp_array_questions[temp_array_questions.length] = new Question("question-id-"+counter, all_questions[x].question_text, all_questions[x].question_type);
					if (all_questions[x].question_type == "TEXT_ONLY") {
						//nothing
					} else if (all_questions[x].question_type == "SINGLE_ANSWER") {
						$("#question-choices-"+counter).html(addSingleAnswerQuestionTemplate(counter, all_questions[x].question_text));
						if (all_questions[x].choices.length > 0) {
							temp_array_questions[counter].choices = all_questions[x].choices;
							$("#choices-container-"+counter).html(""); //if there are already choices for this question, we will reinsert all of them
							for (y=0; y<all_questions[x].choices.length; y++) {
								html_to_add = addAnotherSingleAnswerResponseUI(counter, y, all_questions[x].choices[y].choice_text);
								$("#choices-container-"+counter).append(html_to_add);
							}
						}
					} else if (all_questions[x].question_type == "MULTIPLE_ANSWER") {
						$("#question-choices-"+counter).html(addMultipleAnswerQuestionTemplate(counter, all_questions[x].question_text));
						if (all_questions[x].choices.length > 0) {
							temp_array_questions[counter].choices = all_questions[x].choices;
							$("#choices-container-"+counter).html(""); //if there are already choices for this question, we will reinsert all of them
							for (y=0; y<all_questions[x].choices.length; y++) {
								html_to_add = addAnotherMultipleAnswerResponseUI(counter, y, all_questions[x].choices[y].choice_text);
								$("#choices-container-"+counter).append(html_to_add);
							}
						}
					} else if (all_questions[x].question_type == "SINGLE_RESPONSE") {
						$("#question-choices-"+counter).html(addSingleResponseQuestionTemplate(counter, all_questions[x].question_text));
					}
					counter++;
				}
			}
			all_questions = temp_array_questions;
			//console.log("DELETE:\n" + JSON.stringify(all_questions, null, 4));
		}
		
		//this function will be called when the title, description, password, or logo has been changed
		function updateValueToDB(field, value) {
			var survey_id = $("#survey_id").val();
			var _token = $("#_token").val();
			//alert("Change the " + field + " to " + value + ".");
			$.post("/surveys/update-value-survey-db", {"survey_id":survey_id, "field":field, "value":value, "_token":_token}, function(data){
				data = JSON.parse(data);
				//nothing
			});
		}
		
	</script>
@endsection