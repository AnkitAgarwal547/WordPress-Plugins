<?php

// ----------------------------------------------------------------------------------------------------------
// chart analysis in frontend of Surveys
add_shortcode('d-submit-survey-answers', 'd_survey_answers');
function d_survey_answers(){
	global $wpdb;      
	$id = $_GET['id'];

    $user_id    = get_current_user_id();
    $table      = $wpdb->prefix.'d_submitted_surveys';
    $categories = $wpdb->get_results( "SELECT submitted_survey FROM $table WHERE survey_id = '$id'");
    if(!empty($categories)){

    $ans = json_decode($categories[0]->submitted_survey,TRUE);
        foreach ($ans as $key => $value) {
    	$val[$key] = $value;
    } 

    $cat_first            = json_decode(json_encode($categories),true);

    foreach ( $cat_first as $key => $category_first ){
        $qns_first[$key] = json_decode($category_first['submitted_survey'],TRUE);
    }

    $i = 0;
    foreach ($qns_first as $key => $value_first) {
        $va_first = array(); 
        foreach ($value_first as $k => $v_first) {
            $va_first[$k] = $v_first['Answers'];
        } 
        $ans_first[$key] = $va_first;
    }
    ?>
    <div class="remove-this-later">
    <div class="v-survey-heading">Submitted Answers</div>
   	 <div class = "table-responsive">
   		<table class = "table table-striped table-bordered">
	        <thead>
		        <tr>
		        	<?php 
	        		foreach ($val as $v){ ?>
		        		<th><?php echo $v['Question']; ?></th>
		        	<?php }?>
		        </tr>
	     	</thead>
	      	<tbody>
	      		<?php 
					 foreach ($ans_first as $v) {?>
		            	<tr>
		            		<?php foreach ($v as $a) {?> <td> <?php foreach ($a as $ans); echo $ans?></td><?php }?>
		        		</tr>
        		<?php }?>	
		   	</tbody>
	   	</table>
      </div>
    </div>
<?php }

else { ?>
    <div class="remove-this-later">
        <div class="v-survey-heading">Submitted Answers</div>
            <div>
                <center style="margin:50px;"><h2>No answers Submitted Yet !!</h2><center>
            </div>    
        </div>
    </div>
<?php }}