<?php

// ----------------------------------------------------------------------------------------------------------
// chart analysis in frontend of Surveys
add_shortcode('d-survey-historic-statistics', 'd_survey_historic_statistics');
function d_survey_historic_statistics(){
    global $wpdb;
    $table      = $wpdb->prefix.'d_submitted_surveys';
    $id         = $_GET['id'];
    $user_id    = get_current_user_id();

    // echo $id;

    $results['male'] = $wpdb->get_results( "SELECT (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end) age_group, count(*) count FROM wp_d_submitted_surveys WHERE survey_id= '$id' AND gender = 'M' GROUP BY (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end)");

    $results['female'] = $wpdb->get_results( "SELECT (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end) age_group, count(*) count FROM wp_d_submitted_surveys WHERE survey_id= '$id' AND gender = 'F' GROUP BY (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end)");

    $results['other'] = $wpdb->get_results( "SELECT (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end) age_group, count(*) count FROM wp_d_submitted_surveys WHERE survey_id= '$id' AND gender = 'O' GROUP BY (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end)");  

    $male   = [ 'group1'=> "" , 'group2'=> "" , 'group3'=> "" , 'group4'=> "" , 'group5'=> "" , 'group6'=> "" , 'group7'=> "" ];
    $female = [ 'group1'=> "" , 'group2'=> "" , 'group3'=> "" , 'group4'=> "" , 'group5'=> "" , 'group6'=> "" , 'group7'=> "" ];
    $other  = [ 'group1'=> "" , 'group2'=> "" , 'group3'=> "" , 'group4'=> "" , 'group5'=> "" , 'group6'=> "" , 'group7'=> "" ];

    $results['male']      = array_replace( $male   , array_column( $results['male']   ,   'count' , 'age_group'));
    $results['female']    = array_replace( $female , array_column( $results['female'] ,   'count' , 'age_group'));
    $results['other']     = array_replace( $other  , array_column( $results['other']  ,   'count' , 'age_group'));

    ?>
    <div style="width: 100%;float: right;">
        <canvas id="myChart" width="100%" height="50"></canvas>
      </div>
    <div class="info" style="width: 100%;float:left;clear: both;margin-top: 5%;">
      <div style="width: 100% !important; float: left;">
          <span>Male</span>
              <div id="Progress_Status"> 
                  <div id="myprogressBar" class="myprogressBar"></div> 
                  <span id ="elmentVal" class="count"></span>
              </div>
          <span>Female</span>
          <div id="Progress_Status"> 
              <div>
                  <div id="myprogressBar1" class="myprogressBar"></div> 
              </div>
              <span id ="elmentVal1" class="count"></span>
          </div>
          <span>Other</span>
          <div id="Progress_Status"> 
              <div>
                  <div id="myprogressBar2" class="myprogressBar"></div> 
              </div>
              <span id ="elmentVal2" class="count"></span>
          </div>
      </div>
    </div>
    
    <script>
        var male    = <?php echo json_encode( $results['male'] ); ?> ;
        var female  = <?php echo json_encode( $results['female'] ); ?> ;
        var other   = <?php echo json_encode( $results['other'] ); ?> ;

        const m = Object.values(male);
        const f = Object.values(female);
        const o = Object.values(other);

        var total_male = 0;
        for (var i = 0; i < m.length; i++) {
            total_male += m[i] << 0;
        }
        var total_female = 0;
        for (var i = 0; i < f.length; i++) {
            total_female += f[i] << 0;
        }
        var total_other = 0;
        for (var i = 0; i < o.length; i++) {
            total_other += o[i] << 0;
        }
        
        total = total_male+total_female+total_other;
        var ctx     = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
        //   type: 'line',
        //   data: {
        //     labels: [15,30,45,60,75,90,100],
        //     datasets: [{ 
        //         data: m,
        //         label: "Male",
        //         borderColor: "#3e95cd",
        //         fill: false
        //       }, { 
        //         data:f,
        //         label: "Female",
        //         borderColor: "rgba(255,99,132,1)",
        //         fill: false
        //       },{ 
        //         data:o,
        //         label: "Other",
        //         borderColor: "#FF4500",
        //         fill: false
        //       }
        //     ]
        //   },
            type: 'bar',
            data: {
                labels: [15,30,45,60,75,90,100],
                datasets: [{
                    data: m,
                    label: "Male",
                    borderColor: "#4472c4",
                    backgroundColor:"#4472c4",
                    fill: false
                }, {
                    data:f,
                    label: "Female",
                    borderColor: "#ed7d31",
                    backgroundColor:"#ed7d31",
                    fill: false
                },{
                    data:o,
                    label: "Other",
                    borderColor: "#39b7b3",
                    backgroundColor:"#39b7b3",
                    fill: false
                }
            ]
            },
          options: {
            title: {
              display: true,
              text: 'Submited surveys per title according to age and gender (in %)'
            }
          }
        });

        window.onload = function() {
          var element = document.getElementById("myprogressBar");  
          var ele_val = document.getElementById("elmentVal");  
          var width = (total_male * 100 ) / total;
          var motion = 1;
          width = (width.toFixed(2)); 
          var identity = setInterval(scene, 10); 
            function scene() { 
                if (width <= 100) {
                    if(motion <= width){
                      motion++; 
                      element.style.width = motion + '%';  
                      ele_val.innerHTML = width + '%'; 
                    }
                } else { 
                  clearInterval(identity); 
                } 
            } 

            var element1 = document.getElementById("myprogressBar1"); 
            var ele_val1 = document.getElementById("elmentVal1");  
            var width1 = (total_female * 100 ) / total; 
            width1 = (width1.toFixed(2));
            var motion1 = 1;
            var identity1 = setInterval(scene1, 10); 
                function scene1() { 
                    if (width1 <= 100) { 
                        if(motion1 <= width1){
                          motion1++; 
                          element1.style.width = motion1 + '%';  
                          ele_val1.innerHTML = width1 + '%'; 
                        }
                    } else { 
                      clearInterval(identity1); 
                } 
            } 
            var element2 = document.getElementById("myprogressBar2"); 
            var ele_val2 = document.getElementById("elmentVal2");  
            var width2 = (total_other * 100 ) / total; 
            width2 = (width2.toFixed(2));
            var motion2 = 1;
            var identity2 = setInterval(scene2, 10); 
                function scene2() { 
                    if (width2 <= 100) { 
                        if(motion2 <= width2){
                          motion2++; 
                          element2.style.width = motion2 + '%';  
                          ele_val2.innerHTML = width2 + '%'; 
                        }
                    } else { 
                      clearInterval(identity2); 
                } 
            } 
        }
    </script>
<?php
// ---------------------------------------------------------------------------------------------------------
// Survey List and Actions
    $user_id = get_current_user_id();
    $id         = $_GET['id'];
    global $wpdb;
    $table = $wpdb->prefix.'d_surveys';
    $results = $wpdb->get_results( "SELECT * FROM $table WHERE user_id = $user_id  AND id='$id'" );
    // print_r($results);
    $table = $wpdb->prefix.'d_categories_surveys';
    $categories = $wpdb->get_results( "SELECT * FROM $table");

    $tableSub = $wpdb->prefix.'d_submitted_surveys';
    $submitted = $wpdb->get_results("SELECT * FROM $tableSub WHERE survey_id = '$id'");

    if(count($results) > 0 ){
  ?>
  <br><br>
  <div class="remove-this-later">
  <div class="v-survey-heading">Submitted Surveys List</div>
  <?php
  foreach($results as $indexing=>$result){

      $title = $result->title;
      // print_r($result);
      $cat = $result->category_id;
      foreach($categories as $k => $res){
          if($res->id == $cat){
              $cat = $res->category_name;
          }
      } ?>
  <?php }?>
        <div class="table-responsive">
            <table border="1" style="width:100%" class="table table-striped">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>User Name</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if($results) :
                    foreach($results as $indexing=>$result){
                        $title = $result->title;

                        $cat = $result->category_id;
                        foreach($categories as $k => $res){
                            if($res->id == $cat){
                                $cat = $res->category_name;
                            }
                        }     
                        if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
                            $url = "https://";   
                        else  
                            $url = "http://";   
                        // Append the host(domain name, ip) to the URL.   
                        $url.= $_SERVER['HTTP_HOST'];   

                        // Append the requested resource location to the URL   
                        $url.= $_SERVER['REQUEST_URI'];
                        $copy_url = home_url().'/survey';
                        $url = parse_url($url, PHP_URL_PATH);
                    ?>
                        <tr>
                            <td><?php echo $title ?></td>
                            <td><?php echo $cat ?></td>
                            <td><?php echo get_userdata($result->user_id)->display_name; ?></td>
                            <td><?php if($result->is_active == 1){ echo "<strong style='color: green';>Active</strong>"; }else{ echo "<strong style='color: red';>Inactive</strong>"; } ?></strong></td>
                            <td>
                                <?php if($result->is_stopped == 0){ ?>
                                <a href="<?php echo $url.'?survey=d-edit-survey&id='. $result->id ?>" class="btn">Edit</a>
                                <?php } ?>
                                <!-- <a href="<?php //echo $url.'?survey=d-remove-survey&id='. $result->id ?>" class="btn">Disable</a> -->
                                <?php if($result->is_active == 1){ ?>
                                <a href="<?php echo $url.'?survey=d-disable-survey-historic&action=disable&id='. $result->id .'&url='. $url .'?survey=d-show-single-survey&id='.$result->id ?>" class="btn btn-danger">Deactivate</a>
                                <?php }
                                else{ ?>
                                <a href="<?php echo $url.'?survey=d-disable-survey-historic&action=enable&id='. $result->id .'&url='. $url .'?survey=d-show-single-survey&id='.$result->id ?>" class="btn">Activate</a>
                                <?php }
                                if($result->is_stopped == 0){ ?>
                                <a href="<?php echo $url.'?survey=d-stop-survey-historic&id='. $result->id .'&url='. $url .'?survey=d-show-single-survey' ?>" class="btn btn-danger">Stop</a>
                                <?php }
                                else{
                                    echo "<span>Stopped</span>";
                                } ?>
                                <!-- <a href="<?php //echo base64_url_encode("$url.'?survey=d-survey-link&id='.$result->id") ?>" id="d-survey-copy-link" class="btn">Link</a> -->
                                <?php if($result->is_stopped == 0){ ?>
                                <a href="javascript:void(0)" data-url="<?php echo $copy_url.base64_url_encode("id=$result->id&school_id=$user_id&urlencode=stronghass") ?>" id="d-survey-copy-link<?= $indexing ?>" class="btn d-survey-copy-link" onclick="copyToClipboard('#d-survey-copy-link<?= $indexing ?>')">Copy Link</a>
                                <?php } ?>
                                <?php if(count($submitted) > 0) { ?>
                                <a href="<?php echo $url.'?survey=d-survey-answers&id='. $result->id ?>" class="btn">View Answers</a>
                                <a href="<?php echo $url.'?survey=d-survey-compare&id='. $result->id ?>" class="btn">Compare</a>
                                <?php } ?>
                            </td>
                        </tr>
                <?php } else: ?>
                    <td colspan=5 style="text-align: center;">No Data Found </td>
                <?php endif; ?>
                </tbody>
            </table>
      <!-- <a href="<?php //echo $url.'?survey=d-add-user' ?>">Add Employee</a> -->
        </div>
  </div>
  <script>
    
        function copyToClipboard(element) {
            var $temp = jQuery("<input>");
            jQuery("body").append($temp);
            $temp.val(jQuery(element).attr('data-url')).select();
            document.execCommand("copy");
            $temp.remove();
            jQuery(element).html('Copied');
        }

    jQuery(document).ready(function($) {
      $('.tabs a').click(function(e) {
          e.preventDefault();
          var tab_id = $(this).attr('id'); 
          $.ajax({
              type: "GET",
              url: "wp-admin/admin-ajax.php", 
              dataType: 'html',
              data: ({ action: 'yourFunction', id: tab_id}),
              success: function(data){
                        $('#tab'+tab_id).html(data);
              },
              error: function(data)  
              {  
              alert("Error!");
              return false;
              }  

          }); 

          });
      });
  </script>
<?php
    }
}

// chart analysis in frontend of Surveys Ends
// ----------------------------------------------------------------------------------------------------------

add_shortcode('d-disable-survey-historic', 'd_disable_survey_historic');
function d_disable_survey_historic(){
    $id = $_GET['id'];
    
    $url = $_GET['url'];

    $url = $url.'&id='.$id;
    // die($url);
    $action = $_GET['action'];
    
    $user_id = get_current_user_id();
	global $wpdb;
	$table = $wpdb->prefix.'d_surveys';
    if($action == 'disable'){
        $data = [ 'is_active' => 0 ];
    }
    else{
        $data = [ 'is_active' => 1 ];
    }
    $where = [ 'id' => $id ];
    $result = $wpdb->update( $table, $data, $where ); 
    if($result){ ?>
        <script> location.replace("<?= $url ?>"); </script>
		<!-- // exit; -->
    <?php exit(); }
}

add_shortcode('d-stop-survey-historic', 'd_stop_survey_historic');
function d_stop_survey_historic(){
    $id = $_GET['id'];
    
    $url = $_GET['url'].'&id='.$id;

    $user_id = get_current_user_id();
	global $wpdb;
	$table = $wpdb->prefix.'d_surveys';

    $data = [ 'is_stopped' => 1, 'end_date' => date('Y-m-d H:i:s') ];
    $where = [ 'id' => $id ];
    $result = $wpdb->update( $table, $data, $where ); 
    if($result){ ?>
        <script> location.replace("<?= $url ?>"); </script>
		<!-- // exit; -->
    <?php exit(); }
}