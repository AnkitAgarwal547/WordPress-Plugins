<?php

// ----------------------------------------------------------------------------------------------------------
// ----------------------------------------- Code For Survey ------------------------------------------------

add_shortcode('d-create-survey-form', 'd_create_survey_form');
function d_create_survey_form(){ 
	if(!is_user_logged_in()){
		echo '<div class="container">You are not Allowed to see this Page</div>';
	}else{
		if(wp_get_current_user()->roles[0] == 'school'){
			
			$user_id = get_current_user_id();

			global $wpdb;
			$table = $wpdb->prefix.'d_categories_default_questions';
			
			$cat_id = $_GET['d_load_cat_survey'];
			$survey = $wpdb->get_results( "SELECT * FROM $table WHERE cat_id = '$cat_id'");

			$table_cat = $wpdb->prefix.'d_categories_surveys';
			$category = $wpdb->get_results( "SELECT * FROM $table_cat WHERE id='$cat_id'");
			
			if($survey)
				$survey = json_decode($survey[0]->questions, true);
	?>
	<!-- ------------------------------------- -->

	<div class="remove-this-later">
		<div class=" ">
			<div class="v-survey-heading">Create <?php echo '<strong style="text-transform: uppercase;">'.$category[0]->category_name.'</strong>' ?> Survey</div>
			<div class="survey-container add-survey-club" style="margin-bottom: 20px;">
				<div class="error-messages-d"></div>
				
				<form id="survey-form">
					<div class="survey-title">
						<input type="hidden" id="d_category_id" name="d_category_id" value="<?= $cat_id ?>">
						<label for="survey_title" class="survey_title">Title</label>
						<input type="text" class="form-control" placeholder="Enter Survey Title" name="survey-title" id="survey_title">
					</div>
					<h4 class="survey-heading">Survey</h4>
					<div class="main-survey-container">
					<?php
					if(count($survey) > 0){
					foreach($survey as $key => $sur){ 
						$key = $key+100;
						?>
						<div class="v-add-new-survey">
							<input type="text" placeholder="Enter Question" name="v-survey-question" value="<?= $sur['question'] ?>">
						
							<div class="type-of-survey-answers">
								<input type="radio" name="survey-option-input<?= $key ?>" class="survey-option-input" id="survey-option-input<?= $key ?>" required <?= ($sur['type'] == 'survey-option-input') ? 'checked=checked' : '' ?> ><label for="survey-option-input">Input Type</label>
								<input type="radio" name="survey-option-input<?= $key ?>" class="survey-option-radio" id="survey-option-radio<?= $key ?>" required <?= ($sur['type'] == 'survey-option-radio') ? 'checked=checked' : '' ?>><label for="survey-option-radio">One Choice</label>
								<input type="radio" name="survey-option-input<?= $key ?>" class="survey-option-checkbox" id="survey-option-checkbox<?= $key ?>" required <?= ($sur['type'] == 'survey-option-checkbox') ? 'checked=checked' : '' ?>><label for="survey-option-checkbox">Multiple Choice</label>
							</div>

							<div id="v-survey-answer-options">
								<?php foreach($sur['options'] as $k => $opt)
								{ 
									if($sur['type'] == 'survey-option-radio'){ ?>
										<div class="radio-button-option-style"><input type="text" class="option-radio-options" value="<?= $opt ?>" name="option-radio-options<?= $k ?>" id="survey-option-radio<?= $key ?>">
									<?php if($k == 0): ?><a class="radio-button-option-button">Add option</a> <?php else: ?>
										<span class="delete-option-radio">Delete Option</span>
									<?php endif; ?>
										</div>
									<?php }
									elseif($sur['type'] == 'survey-option-checkbox'){ ?>
										<div class="checkbox-button-option-style">
										<input type="text" class="option-checkbox-options" name="option-checkbox-options<?= $k ?>" value="<?= $opt ?>" id="survey-option-checkbox<?= $key ?>">
										<?php if($k == 0): ?>
											<a class="checkbox-button-option-button">Add option</a>
										<?php else: ?>
											<span class="delete-option-checkbox">Delete Option</span>
										<?php endif; ?>											
										</div>
									<?php
									}
								} ?>
							</div>

						</div>
					<?php } }?>
						<div class="v-add-new-survey">
							<input type="text" placeholder="Enter Question" name="v-survey-question">
							<div class="type-of-survey-answers">
								<input type="radio" name="survey-option-input" class="survey-option-input" id="survey-option-input" required><label for="survey-option-input">Input Type</label>
								<input type="radio" name="survey-option-input" class="survey-option-radio" id="survey-option-radio" required><label for="survey-option-radio">One Choice</label>
								<input type="radio" name="survey-option-input" class="survey-option-checkbox" id="survey-option-checkbox" required><label for="survey-option-checkbox">Multiple Choice</label>
							</div>
							<div id="v-survey-answer-options"></div>
						</div>
					</div>
					<a id='add-new-element' class=" add-element">Add Element</a>
					<div class="submit-survey">
						<button type="submit" id="create-survey">Create <i class="fa fa-spin fa-spinner dean-loader" style="display:none"></i></button>
					</div>	
				</form>
			</div>
		</div>
	</div>
	<script>
		var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
	</script>
<?php }
	else{
		return 'You have no Privilage to view this page';
	}
}
}

// ---------------------------------------------------------------------------------
// Ajax For saving the survey creation Data to Database

add_action( 'wp_ajax_create_survey_dean', 'dean_create_survey_and_save' );
// add_action( 'wp_ajax_nopriv_create_survey_dean', 'dean_create_survey_and_save' );
function dean_create_survey_and_save(){
	$formData = $_POST['form'];	
	$formData = json_encode($formData);

	$cat_id = $_POST['cat'];
	
	$title = $_POST['title'];
	
	$user_id = get_current_user_id();
	
	global $wpdb;
	$table = $wpdb->prefix.'d_surveys';

	$results = $wpdb->get_results( "SELECT * FROM $table WHERE title = '$title' AND user_id = $user_id" );
	if(count($results) > 0){
		wp_send_json(array('status'=> 'failed', 'msg' => 'You have already created this Survey'));
		exit;
	}

	$data = array('user_id' => $user_id, 'title' => $title, 'survey' => $formData, 'category_id' => $cat_id, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'), 'start_date' => date('Y-m-d H:i:s'), 'end_date' => date('Y-m-d H:i:s'));
	$wpdb->insert($table,$data);
	// $my_id = $wpdb->insert_id;
	wp_send_json(array('status'=> 'success', 'msg' => 'Survey created Successfully'));
	exit;
}