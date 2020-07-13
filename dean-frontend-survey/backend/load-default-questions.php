<?php 

function dean_load_default_questions(){ 
    global $wpdb;
    $table_name = $wpdb->prefix.'d_categories_surveys';
    $results = $wpdb->get_results("SELECT * FROM $table_name");    
?> 

<!-- --------------------------------------------------------------------------- -->
<!-- Load Category based Default Questions -->

<h3>Load Default Questions of Category</h3>
<div class="container surveBG">
	<div>
		<?php if($results) { ?>
		<form method="GET">
			<input type="hidden" name="page" value="<?= $_GET['page'] ?>">
			<select name="load-default-questions-cat" id="load-default-questions-cat">
				<option value="0">None</option>
				<?php foreach($results as $result){ ?>
				<option value="<?php echo $result->id ?>" <?php if(isset($_GET['load-default-questions-cat'])): ?><?php echo ($result->id == $_GET['load-default-questions-cat']) ? "selected" : "" ?> <?php endif; ?> ><?php echo $result->category_name ?></option>
				<?php } ?>
			</select>
			<button type="submit" class="button button-primary" id="load-default-questions">Load Default Questions</button>
		</form>
		<?php } ?>
	</div>

	<div>
		<?php if(isset($_GET['load-default-questions-cat']) && $_GET['load-default-questions-cat'] != 0) {
			$cat_id = $_GET['load-default-questions-cat'];
			global $wpdb;
			$table_name = $wpdb->prefix.'d_categories_default_questions';
			$results = $wpdb->get_results("SELECT * FROM $table_name WHERE cat_id = $cat_id");
            
            if(count($results) > 0):
                $survey = json_decode($results[0]->questions, true);
		?>
			<?php //print_r($results); ?>

			<div class="survey-container" style="margin-bottom: 20px;">
                <div class="error-messages-d"></div>
                <form id="update-default-question-cat">
					<input type="hidden" id="update_default_question" value="<?= $_GET['load-default-questions-cat'] ?>">
					<input type="hidden" id="url_after_success" value="<?php echo strtok($_SERVER["REQUEST_URI"], '&'); ?>">
                    <div class="main-survey-container">
                        <!-- <div class="v-add-new-survey"> -->

					<?php
					if(count($survey) > 0){
						foreach($survey as $key => $sur){ 
						$key = $key+100;
						?>
						<div class="add-new-element">
							<p class="delete-element"><i class="fa fa-trash"> Delete</i></p>
							<input type="text" placeholder="Enter Question" name="v-survey-question" value="<?= $sur['question'] ?>">
						
							<div class="type-of-survey-answers">
								<input type="radio" name="survey-option-input<?= $key ?>" class="survey-option-input" id="survey-option-input<?= $key ?>" required <?= ($sur['type'] == 'survey-option-input') ? 'checked=checked' : '' ?> ><label for="survey-option-input">Input Type</label>
								<input type="radio" name="survey-option-input<?= $key ?>" class="survey-option-radio" id="survey-option-radio<?= $key ?>" required <?= ($sur['type'] == 'survey-option-radio') ? 'checked=checked' : '' ?>><label for="survey-option-radio">One Choice</label>
								<input type="radio" name="survey-option-input<?= $key ?>" class="survey-option-checkbox" id="survey-option-checkbox<?= $key ?>" required <?= ($sur['type'] == 'survey-option-checkbox') ? 'checked=checked' : '' ?>><label for="survey-option-checkbox">Multiple Choice</label>
							</div>

							<div id="survey-answer-options">
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



                        <div class="add-new-element">
							<p class="delete-element"><i class="fa fa-trash"> Delete</i></p>
                            <input id="v-survey-question" class="form-control" type="text" placeholder="Enter Question"
                                name="v-survey-question">
                            <div class="type-of-survey-answers">
                                <input type="radio" name="survey-option-input" class="survey-option-input"
                                    id="survey-option-input" required><label for="survey-option-input">Input
                                    Type</label>
                                <input type="radio" name="survey-option-input" class="survey-option-radio"
                                    id="survey-option-radio" required><label for="survey-option-radio">One
                                    Choice</label>
                                <input type="radio" name="survey-option-input" class="survey-option-checkbox"
                                    id="survey-option-checkbox" required><label for="survey-option-checkbox">Multiple
                                    Choice</label>
                            </div>
                            <div id="survey-answer-options"></div>
                        </div>
                    </div>
                    <div class="submit-survey">
                        <a class="button button-primary" id="udpate-default-question">Add Element</a>
                        <button type="submit" class="button button-primary">Update <i
                                class="fa fa-spin fa-spinner dean-loader" style="display:none"></i></button>
                    </div>
                </form>
            </div>
		<?php 
			else:
				echo '<div style="margin-top: 20px; text-align: left; font-size: 15px;">No Default Questions found for this Category !</div>';
			endif;
		}
		?>
	</div>
	
</div>

<!-- Load Category based Default Questions Ends -->
<!-- --------------------------------------------------------------------------- -->
<script>
	var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>
<?php }

// ------------------------------------------------------------------------------------------
// Update Default questions of a Category
add_action( 'wp_ajax_update_default_question_cat', 'dean_update_default_question_cat', 0 );
function dean_update_default_question_cat(){
	$formData = $_POST['form'];
    $formData = json_encode($formData);
    
	$cat_id   = $_POST['cat'];
	$url = $_POST['success_url'];
    // print_r($formData); die();
    $user_id = get_current_user_id();

    global $wpdb;
    $table = $wpdb->prefix.'d_categories_default_questions';

	$results = $wpdb->get_results( "SELECT * FROM $table WHERE cat_id = $cat_id AND user_id = $user_id ");
    if(count($results) > 0){
		$data = array('questions' => $formData, 'updated_at' => date('Y-m-d H:i:s'));
		
		$where = array(
			'id'	=> $results[0]->id
		);

		$wpdb->update($table, $data, $where);
		
		wp_send_json(array('status'=> 'success', 'msg' => 'Default Questions updated successfully', 'url' => $url));
		exit;
	}
	wp_send_json(array('status'=> 'failed', 'msg' => 'Something went wrong!'));
	exit;
}