<?php

// ----------------------------------------------------------------------------------------------------------
// chart analysis in frontend of Surveys
add_shortcode('d-survey-compare', 'd_survey_question_caomparision');
function d_survey_question_caomparision(){
    
    global $wpdb; 
    $id             = $_REQUEST['id'];
    $user_id        = get_current_user_id();
    
    $survey_tbl     = $wpdb->prefix.'d_surveys';
    $survey_id      = $wpdb->get_row("SELECT id FROM $survey_tbl WHERE user_id = '$user_id' AND id = $id");

    $sub_survey_tbl = $wpdb->prefix.'d_submitted_surveys';
    $cat            = json_decode(json_encode($wpdb->get_results("SELECT submitted_survey FROM $sub_survey_tbl WHERE survey_id in ($survey_id->id)")),true);

    foreach ( $cat as $key => $category ){
        $qns[$key] = json_decode($category['submitted_survey'],TRUE);
    }

    // Questions array
    $i = 0;
    foreach ($qns as $key => $value) {
        $va = array(); 
        foreach ($value as $k => $v) {
            $va[$k] = $v['Question'];
        } 
        $val[$key] = $va;
    }
    foreach ($val as $quest) {
        foreach ($quest as $qu) {
            $q[$i] = $qu;
            $i++;
        }
    }
    $q = array_unique($q);
    natcasesort($q);
    ?>
    <div class="error-messages-d">
    </div>   
    <form id="question-compare">
        <div style="float: left; margin-bottom:50px; ">
            <div style="width: 360px; float: left;" class="d-first-question">
                <select id="question-area" multiple size="9" style="width:350px;" >
                    <?php
                        foreach ($q as $Question) {
                        echo "<option>$Question</option>";
                     } ?>
                </select>
            </div>
            <div style="width: 200px; float: left;" class="d-second-question">
                <div class="question-one-d">
                    <button type="button" class="btn btn-default btn-circle btn-sm" onclick="filed_first_button()">
                        <span class="glyphicon glyphicon-arrow-right"></span>
                    </button>
                    <textarea readonly="" class="inp" id="filed-first-input" name="question1" placeholder="First Question"></textarea><br>
                </div>
                <div class="question-two-d">
                    <button type="button" class="btn btn-default btn-circle btn-sm" onclick="filed_second_button()">
                        <span class="glyphicon glyphicon-arrow-right"></span>
                    </button>
                    <textarea readonly="" class="inp" id="filed-second-input" name="question2" placeholder="Second Question"></textarea><br>
                </div>
            </div> 
            <div style="width: 150px; float: left;"  class="d-choose-chart">
		        <select class="inp" name="type" id="form-type" size="5" required>
		            <option value="horizontalBar" >Horizontal bar graph</option>
		            <option value="bar" >Vertical bar graph</option>
		        </select>
        	</div>
	        <button type="submit" id="create-comparision" class="btn btn-primary pull-right">Submit 
	        	<i class="fa fa-spin fa-spinner dean-loader" style="display:none"></i>
	        </button>
        </div>
    </form>
    <div id="report_analysis">
    </div>
        <script>
            var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
            function filed_first_button() {
                var selected_question   = document.getElementById('filed-first-input');
                var question_select     = jQuery("#question-area").val();
                var clone_question      = question_select.join("\n\n");
                selected_question.value = clone_question;
            }
            function filed_second_button() {
                var selected_question   = document.getElementById('filed-second-input');
                var question_select     = jQuery ("#question-area").val();
                var clone_question      = question_select.join("\n\n");
                selected_question.value = clone_question;
            }
            jQuery(document).ready(function (){
                jQuery("#change").on('click',function(){
                    var pickup = jQuery('#filed-first-input').val();
                    jQuery('#filed-first-input').val(jQuery('#filed-second-input').val());
                    jQuery('#filed-second-input').val(pickup);
                });
            });
    </script>
<?php }

// data analysis for analysis
add_filter('wp_ajax_question_compare','dean_question_caompare');
function dean_question_caompare(){

    global $wpdb;
    $table      = $wpdb->prefix.'d_submitted_surveys';

    $question1 = $_POST["q1"];
    $question2 = $_POST["q2"];

    $user_id    = get_current_user_id();

    $qns_first  = $wpdb->get_results("SELECT * FROM $table WHERE submitted_survey LIKE '%$question1%' ");
    $qns_second = $wpdb->get_results("SELECT * FROM $table WHERE submitted_survey LIKE '%$question2%' ");

    $cat_first            = json_decode(json_encode($qns_first),true);
    $cat_second           = json_decode(json_encode($qns_second),true);

    foreach ( $cat_first as $key => $category_first ){
        $qns_first[$key] = json_decode($category_first['submitted_survey'],TRUE);
    }
    foreach ( $cat_second as $key => $category_second ){
        $qns_second[$key] = json_decode($category_second['submitted_survey'],TRUE);
    }

    // Answers array first
    $i = 0;
    foreach ($qns_first as $key => $value_first) {
        $va_first = array(); 
        foreach ($value_first as $k => $v_first) {
            $va_first[$k] = $v_first;
        } 
        $ans_first[$key] = $va_first;
    }
    foreach ($ans_first as $quest_first) {          
        foreach ($quest_first as $qu_first) {
            $a_first[$i] = $qu_first;
            $i++;
        }
    }
     // Answers array second
    $i = 0;
    foreach ($qns_second as $key => $value_second) {
        $va_second = array(); 
        foreach ($value_second as $k => $v_second) {
            $va_second[$k] = $v_second;
        } 
        $ans_second[$key] = $va_second;
    }
    foreach ($ans_second as $quest_second) {          
        foreach ($quest_second as $qu_second) {
            $a_second[$i] = $qu_second;
            $i++;
        }
    }

    $filter_ans_first = array_filter($a_first, function ($var_first) use ($question1){
        return ($var_first['Question'] == $question1);
    });
    $filter_ans_second = array_filter($a_second, function ($var_second) use ($question2){
        return ($var_second['Question'] == $question2);
    });

    $i = 0;
    foreach ($filter_ans_first as $key => $value_first) {
        $answer_first[$i] = $value_first['Answers'];
        $i++;
    }
    foreach ($answer_first as $qu_first) {          
        foreach ($qu_first as $qw_first) {
            $an_first[$i] = $qw_first;
            $i++;
        }
    }
    foreach ($filter_ans_second as $key => $value_second) {
        $answer_second[$i] = $value_second['Answers'];
        $i++;
    }
    foreach ($answer_second as $qu_second) {          
        foreach ($qu_second as $qw_second) {
            $an_second[$i] = $qw_second;
            $i++;
        }
    }

    $answer_first   = array_unique($an_first);
    $answer_second  = array_unique($an_second);

    foreach ($answer_first as $key => $value_first) {
        $m_first[$key]  = $wpdb->get_row( "SELECT count(*) count FROM (SELECT * FROM $table WHERE submitted_survey LIKE '%$value_first%' and submitted_survey LIKE '%$question1%')as qns_first WHERE gender = 'M' ");

        $f_first[$key]  = $wpdb->get_row( "SELECT count(*) count FROM (SELECT * FROM $table WHERE submitted_survey LIKE '%$value_first%' and submitted_survey LIKE '%$question1%' )as qns_first WHERE gender = 'F'");

        $o_first[$key]  = $wpdb->get_row( "SELECT count(*) count FROM (SELECT * FROM $table WHERE submitted_survey LIKE '%$value_first%' and submitted_survey LIKE '%$question1%' )as qns_first WHERE gender = 'O'");          
    }
    foreach ($answer_second as $key => $value_second) {
        $m_second[$key] = $wpdb->get_row( "SELECT count(*) count FROM (SELECT * FROM $table WHERE submitted_survey LIKE '%$value_second%' and submitted_survey LIKE '%$question2%')as qns_first WHERE gender = 'M' ");

        $f_second[$key] = $wpdb->get_row( "SELECT count(*) count FROM (SELECT * FROM $table WHERE submitted_survey LIKE '%$value_second%' and submitted_survey LIKE '%$question2%' )as qns_first WHERE gender = 'F'");

        $o_second[$key] = $wpdb->get_row( "SELECT count(*) count FROM (SELECT * FROM $table WHERE submitted_survey LIKE '%$value_second%' and submitted_survey LIKE '%$question2%' )as qns_first WHERE gender = 'O'");          
    }
    // first
    foreach ($m_first as $males_first)
    { 
        foreach ($males_first as $m_first) 
        { 
        $male_first[] = $m_first; 
        } 
    }
    foreach ($f_first as $females_first)
    { 
        foreach ($females_first as $f_first) 
        { 
        $female_first[] = $f_first; 
        } 
    }
    foreach ($o_first as $others_first)
    { 
        foreach ($others_first as $o_first) 
        { 
        $other_first[] = $o_first; 
        } 
    }
    // second
    foreach ($m_second as $males_second)
    { 
        foreach ($males_second as $m_second) 
        { 
        $male_second[] = $m_second; 
        } 
    }
    foreach ($f_second as $females_second)
    { 
        foreach ($females_second as $f_second) 
        { 
        $female_second[] = $f_second; 
        } 
    }
    foreach ($o_second as $others_second)
    { 
        foreach ($others_second as $o_second) 
        { 
        $other_second[] = $o_second; 
        } 
    }

    if(($male_first > 0 || $female_first > 0 || $other_first > 0)&&($male_second > 0 || $female_second > 0 || $other_second > 0)){
        wp_send_json(array('male' => $male_first, 'female' => $female_first , 'other' =>$other_first , 'answer' => $answer_first,
            'male1' => $male_second, 'female1' => $female_second , 'other1' =>$other_second , 'answer1' => $answer_second, 
            'status'=> 'success', 'msg' => 'Report exist'));
        exit;
    }
    wp_send_json(array('status'=> 'failed', 'msg' => 'Report does not exist'));
    exit;
}

