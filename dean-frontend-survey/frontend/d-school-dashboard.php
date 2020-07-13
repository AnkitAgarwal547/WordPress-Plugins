<?php

add_shortcode('dean-school-dashboard', 'd_school_dashboard');
function d_school_dashboard(){

  if(!is_user_logged_in()){
    echo '<div class="container">Not Illagable</div>';
}else{
    // if(wp_get_current_user()->roles[0] == 'administrator'){
global $wpdb;
$table      = $wpdb->prefix.'d_submitted_surveys';
$user_id    = get_current_user_id();

$table1     = $wpdb->prefix.'d_surveys';
$cat        = $wpdb->get_results( "SELECT id FROM $table1 WHERE user_id = '$user_id'");
$cat_count  = count($cat);

$cat_tbl    = $wpdb->prefix.'d_categories_surveys';
$sur_count  = $wpdb->get_row( "SELECT count(*)count FROM $cat_tbl");

foreach ( $cat as $key => $category )
{
  $cat[$key] = $category->id;
}
$surveyCatID = implode(",", $cat);

$results['male'] = $wpdb->get_results( "SELECT (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end) age_group, count(*) count FROM $table WHERE survey_id in ($surveyCatID) and gender = 'M' GROUP BY (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end)");

$results['female'] = $wpdb->get_results( "SELECT (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end) age_group, count(*) count FROM $table WHERE survey_id IN ($surveyCatID) and gender = 'F' GROUP BY (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end)");

$results['other'] = $wpdb->get_results( "SELECT (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end) age_group, count(*) count FROM $table WHERE survey_id In ($surveyCatID) and gender = 'O' GROUP BY (case when age <= 15 then 'group1' when age > 15 and age <= 30 then 'group2' when age > 30 and age <= 45 then 'group3' when age > 45 and age <= 60 then 'group4' when age > 60 and age <= 75 then 'group5' when age > 75 and age <= 90 then 'group6' else 'group7' end)");


$male=$female=$other = ['group1'=>"", 'group2'=>"" ,'group3'=>"" ,'group4'=>"" ,'group5'=>"" ,'group6'=>"" ,'group7'=>""];

$results['male']      = array_replace( $male   , array_column( $results['male']   ,   'count' , 'age_group'));
$results['female']    = array_replace( $female , array_column( $results['female'] ,   'count' , 'age_group'));
$results['other']     = array_replace( $other  , array_column( $results['other']  ,   'count' , 'age_group'));

?>
<div class="survey_dash">
    <div class="all-survey-type">
      <div class="all-survey">
        <span id ="total_sur" style="font-size: 100px;"><?php echo $cat_count; ?></span>
        <h4>Total Surveys</h4> 
      </div>
      <div class="all-category">

        <?php
        $user = wp_get_current_user();
        if($roles = $user->roles[0] == "employee"){
          $user_id = get_user_meta($user->ID, '_user_parent', true);
        }

        $args  = array(
          'meta_key' => '_user_parent',
          'meta_value' => $user_id,
          'meta_compare' => '=' // everything but the exact match
        );
 
        $user_query = new WP_User_Query( $args );
        $user_count = count($user_query->get_results());
        ?>

        <span style="font-size: 100px;"><?= $user_count; ?></span>
         <h4>Users</h4> 
      </div>
       <div class="all-submitted">
         <span id ="total_val" style="font-size:100px;">0</span>
         <h4 style="margin-top:0;">Total Submitted Surveys</h4> 
       </div>
    </div>
    <div class="survey_content">
      <div class="survey-progress">
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
        <canvas id="myChart" style="border: 1px dotted ; margin-top: 3em;"></canvas>
    </div>
</div>
<script>
    var male    = <?php echo json_encode( $results['male'] ); ?> ;
    var female  = <?php echo json_encode( $results['female'] ); ?> ;
    var other   = <?php echo json_encode( $results['other'] ); ?> ;
    var cat = <?php echo "$cat_count"; ?>;

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
    jQuery(document).ready(function($){
      $({ countNum: $('#total_val').html() }).animate({ countNum: total }, {
          duration: 1000,
          easing: 'swing',
          step: function () {
          $('#total_val').html(Math.floor(this.countNum));
      },
      complete: function () {
          $('#total_val').html(this.countNum);
      }
      });
      $({ countNum1: $('#total_cat').html() }).animate({ countNum1: cat }, {
          duration: 1000,
          easing: 'swing',
          step: function () {
          $('#total_cat').html(Math.floor(this.countNum1));
      },
      complete: function () {
          $('#total_cat').html(this.countNum1);
      }
      });
      // console.log(total);
      var ctx     = document.getElementById('myChart').getContext('2d');
      var myChart = new Chart(ctx, {
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
    });
</script>
<?php 
  }
}