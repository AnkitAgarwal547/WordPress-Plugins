<?php

function d_default_questions(){ 
    
global $wpdb;
$table_name = $wpdb->prefix.'d_categories_surveys';
$results = $wpdb->get_results("SELECT * FROM $table_name");

?>

<div id="post-body" class="metabox-holder columns-2">
    <div id="post-body-content" style="position: relative;">
        <div class="row">
            <h1 class="wp-heading-inline">Default Questions</h1>
            <div class="survey-container" style="margin-bottom: 20px;">
                <div class="error-messages-d"></div>
                <form id="survey-form-cat">
                    <div class="form-field">
                        <label for="tag-name">Category</label>
                        <select name="d_survey_category" id="d_survey_category" class="form-control">
                            <?php if($results):
                                foreach($results as $result) : ?>
                                    <option value="<?= $result->id ?>"><?= $result->category_name ?></option>
                            <?php endforeach;
                            endif; ?>
                        </select>
                    </div>
                    <h3 class="form-field">Default Questions</h3>
                    <div class="main-survey-container">
                        <!-- <div class="v-add-new-survey"> -->
                        <div class="add-new-element">
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
                        <a class="button button-primary" id="add-new-survey">Add Element</a>
                        <button type="submit" class="button button-primary">Create <i
                                class="fa fa-spin fa-spinner dean-loader" style="display:none"></i></button>
                    </div>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
	var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
</script>
<?php }

// ---------------------------------------------------------------------------------
// Ajax For saving the survey Category Data to Database

add_action( 'wp_ajax_create_survey_cat_dean', 'dean_create_survey_cat_dean_save', 0 );
// add_action( 'wp_ajax_nopriv_create_cat_dean', 'dean_create_category_save' );
function dean_create_survey_cat_dean_save(){

    $formData = $_POST['form'];
    $formData = json_encode($formData);
    
    $cat_id   = $_POST['cat'];
    // print_r($formData); die();
    $user_id = get_current_user_id();

    global $wpdb;
    $table = $wpdb->prefix.'d_categories_default_questions';

    $results = $wpdb->get_results( "SELECT * FROM $table WHERE cat_id = $cat_id AND user_id = $user_id ");
    if(count($results) > 0){
        wp_send_json(array('status'=> 'failed', 'msg' => 'It already Exists'));
        exit;
    }
    $data = array( 'cat_id' => $cat_id, 'user_id' => $user_id, 'questions' => $formData, 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s'));
    $wpdb->insert($table,$data);
    wp_send_json(array('status'=> 'success', 'msg' => 'Default Questions saved successfully'));
    exit;
}