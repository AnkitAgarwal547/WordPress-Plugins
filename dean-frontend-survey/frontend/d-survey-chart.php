<?php

// ----------------------------------------------------------------------------------------------------------
// chart analysis in frontend of Surveys
add_shortcode('d-survey-chart', 'd_survey_analysis');
function d_survey_analysis(){
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
        <!-- <div>
            <h4>Total Surveys</h4> <span id ="total_sur" style="font-size: 100px;"><?php echo $cat_count; ?></span>
            <h4>Total Category</h4> <span id ="total_cat" style="font-size: 100px;">0</span>
            <h4 style="margin-top:0;">Total Submitted Surveys</h4> <span id ="total_val" style="font-size:100px;">0</span>
        </div> -->
        <!-- <div class="all-survey-type">
          <div class="all-survey">
            <span id ="total_sur" style="font-size: 100px;"><?php echo $cat_count; ?></span>
            <h4>Total Surveys</h4> 
          </div>
          <div class="all-category">
            <span id ="total_cat" style="font-size: 100px;">0</span>
             <h4>Total Category</h4> 
          </div>
           <div class="all-submitted">
             <span id ="total_val" style="font-size:100px;">0</span>
             <h4 style="margin-top:0;">Total Submitted Surveys</h4> 
           </div>
        </div> -->




        <!-- <div class="survey_content">
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
        </div> -->
    </div>
    <script>
        // jQuery(document).ready(function($){
        //     var male    = <?php echo json_encode( $results['male'] ); ?> ;
        //     var female  = <?php echo json_encode( $results['female'] ); ?> ;
        //     var other   = <?php echo json_encode( $results['other'] ); ?> ;
        //     var cat = <?php echo "$cat_count"; ?>;

        //     const m = Object.values(male);
        //     const f = Object.values(female);
        //     const o = Object.values(other);

        //     var total_male = 0;
        //     for (var i = 0; i < m.length; i++) {
        //         total_male += m[i] << 0;
        //     }
        //     var total_female = 0;
        //     for (var i = 0; i < f.length; i++) {
        //         total_female += f[i] << 0;
        //     }
        //     var total_other = 0;
        //     for (var i = 0; i < o.length; i++) {
        //         total_other += o[i] << 0;
        //     }
            
        //     total = total_male+total_female+total_other;
        //     $({ countNum: $('#total_val').html() }).animate({ countNum: total }, {
        //         duration: 1000,
        //         easing: 'swing',
        //         step: function () {
        //         $('#total_val').html(Math.floor(this.countNum));
        //     },
        //     complete: function () {
        //         $('#total_val').html(this.countNum);
        //     }
        //     });
        //     $({ countNum1: $('#total_cat').html() }).animate({ countNum1: cat }, {
        //         duration: 1000,
        //         easing: 'swing',
        //         step: function () {
        //         $('#total_cat').html(Math.floor(this.countNum1));
        //     },
        //     complete: function () {
        //         $('#total_cat').html(this.countNum1);
        //     }
        //     });
        //     // console.log(total);
        //     var ctx     = document.getElementById('myChart').getContext('2d');
        //     var myChart = new Chart(ctx, {
        //     type: 'line',
        //     data: {
        //         labels: [15,30,45,60,75,90,100],
        //         datasets: [{ 
        //             data: m,
        //             label: "Male",
        //             borderColor: "#3e95cd",
        //             fill: false
        //         }, { 
        //             data:f,
        //             label: "Female",
        //             borderColor: "rgba(255,99,132,1)",
        //             fill: false
        //         },{ 
        //             data:o,
        //             label: "Other",
        //             borderColor: "#FF4500",
        //             fill: false
        //         }
        //         ]
        //     },
        //     options: {
        //         title: {
        //         display: true,
        //         text: 'Submited surveys per title according to age and gender (in %)'
        //         }
        //     }
        //     });

        //     window.onload = function() {
        //     var element = document.getElementById("myprogressBar");  
        //     var ele_val = document.getElementById("elmentVal");  
        //     var width = (total_male * 100 ) / total;
        //     var motion = 1;
        //     width = (width.toFixed(2)); 
        //     var identity = setInterval(scene, 10); 
        //         function scene() { 
        //             if (width <= 100) {
        //                 if(motion <= width){
        //                 motion++; 
        //                 element.style.width = motion + '%';  
        //                 ele_val.innerHTML = width + '%'; 
        //                 }
        //             } else { 
        //             clearInterval(identity); 
        //             } 
        //         } 

        //         var element1 = document.getElementById("myprogressBar1"); 
        //         var ele_val1 = document.getElementById("elmentVal1");  
        //         var width1 = (total_female * 100 ) / total; 
        //         width1 = (width1.toFixed(2));
        //         var motion1 = 1;
        //         var identity1 = setInterval(scene1, 10); 
        //             function scene1() { 
        //                 if (width1 <= 100) { 
        //                     if(motion1 <= width1){
        //                     motion1++; 
        //                     element1.style.width = motion1 + '%';  
        //                     ele_val1.innerHTML = width1 + '%'; 
        //                     }
        //                 } else { 
        //                 clearInterval(identity1); 
        //             } 
        //         } 
        //         var element2 = document.getElementById("myprogressBar2"); 
        //         var ele_val2 = document.getElementById("elmentVal2");  
        //         var width2 = (total_other * 100 ) / total; 
        //         width2 = (width2.toFixed(2));
        //         var motion2 = 1;
        //         var identity2 = setInterval(scene2, 10); 
        //             function scene2() { 
        //                 if (width2 <= 100) { 
        //                     if(motion2 <= width2){
        //                     motion2++; 
        //                     element2.style.width = motion2 + '%';  
        //                     ele_val2.innerHTML = width2 + '%'; 
        //                     }
        //                 } else { 
        //                 clearInterval(identity2); 
        //             } 
        //         } 
        //     }
        // })
    </script>
    <?php

    global $wpdb;      
    $user_id    = get_current_user_id();
    $table_survey      = $wpdb->prefix.'d_surveys';
    // $categories = $wpdb->get_results( "SELECT id FROM $table_survey WHERE user_id = '$user_id'");
    // foreach( $categories as $key => $category )
    // {
    //   $categories[$key] = $category->id;
    // }
    // $surveyCatID    = implode(",", $categories);
    // $table          = $wpdb->prefix.'d_submitted_surveys';
    // if($surveyCatID)
    // // $results        = $wpdb->get_results( "SELECT id,name,email,phone,age,gender,created_at FROM $table WHERE survey_id in ($surveyCatID) ");

    $result_survey  = $wpdb->get_results("SELECT * FROM $table_survey WHERE user_id = '$user_id' AND temp = 0 AND is_active = 0 or is_stopped=1");

    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')
        $url = "https://";   
    else  
        $url = "http://";   
    // Append the host(domain name, ip) to the URL.   
    $url.= $_SERVER['HTTP_HOST'];   
    // Append the requested resource location to the URL   
    $url.= $_SERVER['REQUEST_URI'];
    $copy_url = $url;
    $url = parse_url($url, PHP_URL_PATH);
    ?>
    <div class="remove-this-later">
    <button name="export_csv" id="dean_export_survey_csv" class="btn btn-primary pull-right">Export CSV</button>
        <div class="v-survey-heading">Survey List</div>
            <!-- <div id="error" class="alert alert-danger" role="alert"></div> -->
        <div class="table-responsive">
            <table border="1" style="width:100%" class="table table-striped" id="dataTable">
                <thead>
                    <tr>
                        <!-- <th>Name</th>
                        <th>Email</th>
                        <th>Gender</th>
                        <th>Age</th> -->
                        <!-- <th>Phone Number</th> -->
                        <!-- <th>Created at</th>
                        <th>Action</th> -->
                        <th>Title</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php
                if($result_survey) :
                    foreach($result_survey as $indexing=>$result)
                    { ?>
                        <tr>
                            <td><a href="/cur/school-dashboard/?survey=d-survey-statistics&id=<?php echo $result->id ?>"><?php echo $result->title; ?></a></td>
                            <td><?php if($result->is_active == 1 && $result->is_stopped){ echo "<strong style='color: #569bca';>Completed</strong>"; }else{ echo "<strong style='color: red';>Inactive</strong>"; } ?></strong></td>
                            <td><?php echo get_userdata($result->user_id)->display_name; ?></td>
                            <td><?php echo $result->start_date; ?></td>
                            <!-- <td><?php //echo $result->phone; ?></td> -->
                            <td><?php echo ($result->end_date == $result->start_date) ? "-" : $result->end_date; ?></td>
                            <td>

                            <?php if($result->is_stopped == 0){ ?>
                                <a href="<?php echo $url.'?survey=d-disable-survey&action=enable&id='. $result->id .'&url='. $url .'?survey=d-show-single-survey&id='.$result->id ?>" class="btn">Activate</a>
                            <?php }
                            else{ ?>
                                <h4>N/A</h4>
                            <?php } ?>

                            </td>
                            <!-- <td>
                                <a href="<?php //echo $url.'?survey=d-survey-answers&id='. $result->id ?>" class="btn">View Answers</a>
                            </td> -->
                        </tr>
                    <?php } else: ?>
                            <td colspan=7 style="text-align: center;">No Data Found </td>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <script>
        var ajaxurl = "<?php echo admin_url('admin-ajax.php'); ?>";
        jQuery(document).ready(function($){
            $('#dean_export_survey_csv').click(function(){
                $(this).html('Downloading...');
                $.ajax({
                    type: "GET",
                    url: ajaxurl,
                    dataType: 'html',
                    data: ({ 
                        action: 'dean_survey_export_csv_report'
                    }),
                    success: function(data){
                        document.location.href = '<?php echo admin_url('admin-ajax.php?action=dean_survey_export_csv_report'); ?>';
                    },
                    error: function(data){ 
                        alert("No Data Found!");
                        // document.getElementById('error').innerHTML="No Data Found!";
                        return false;
                    }
                }); 
                $('#dean_export_survey_csv').html('Export CSV');
            })
            // ----------------------------------------------------------
            // Data Table init
            $('#dataTable').DataTable();
        });
    </script>
<?php
}
}

// chart analysis in frontend of Surveys Ends
// ----------------------------------------------------------------------------------------------------------
// add_action( 'wp_ajax_dean_survey_export_csv_report', 'dean_survey_export_csv_report' );
// function dean_survey_export_csv_report(){
//     global $wpdb;
//     $table       = $wpdb->prefix.'d_surveys';
//     $user_table  = $wpdb->prefix.'users';
//     $user_id     = get_current_user_id();

//     $survey_table   = $wpdb->prefix.'d_submitted_surveys';
//     $cat_table      = $wpdb->prefix.'d_categories_surveys';

//     $filename   = 'd_survey_report_sheet-'.date('Y-m-d'); 
//     $header_row = array( 'S NO.', 'Title', 'category_name' ,'Created By', 'Male', 'Female','Other', 'Submitted Survey', 'status','start_date' ,'end_date',  );
//     $data_rows  = array();

//     $results    = $wpdb->get_results("SELECT u.display_name,c.category_name, valu.title, valu.male_count ,valu.female_count , valu.other_count, valu.total_cnt, valu.is_stopped,valu.start_date,valu.end_date  FROM ( SELECT * FROM (SELECT survey_id, COUNT(CASE WHEN gender='M' then 1 end) AS male_count, COUNT(CASE WHEN gender='F' then 1 end) AS female_count, COUNT(CASE WHEN gender='O' then 1 end) AS other_count, COUNT(*) AS total_cnt FROM $survey_table group by survey_id ) AS val LEFT JOIN $table ON $table.id = val.survey_id ) AS valu LEFT JOIN $user_table u on u.id = valu.user_id AND u.id = $user_id JOIN $cat_table c ON valu.category_id = c.id","ARRAY_A");

//     // die($results[0]->survey_id);

//     if(empty($results)){
//         wp_send_json(array( 'status'=> 'error', 'msg' => 'No data Found'),403);
//     }else{
//         $i = 1;
//         foreach ( $results as $result ) 
//         {
//             if($result['is_stopped'] == 0){
//                 $result['is_stopped'] = 'Active';
//             }else{
//                 $result['is_stopped'] = 'Inactive';
//             }
//             $row = array(
//             $i++,
//             $result['title'],
//             $result['category_name'],
//             $result['display_name'],
//             $result['male_count'],
//             $result['female_count'],
//             $result['other_count'],
//             $result['total_cnt'],
//             $result['is_stopped'],
//             $result['start_date'],
//             $result['end_date']
//             );
//             $data_rows[] = $row;
//         }
//         ob_end_clean ();
//         $fh = @fopen( 'php://output', 'w' ); 
//         fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) ); 
//         header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' ); 
//         header( 'Content-Description: File Transfer' ); 
//         header( 'Content-type: text/csv' ); 
//         header( "Content-Disposition: attachment; filename=$filename".'.csv' ); 
//         header( 'Expires: 0' ); 
//         header( 'Pragma: public' ); 
//         fputcsv( $fh, $header_row ); 
//         foreach ( $data_rows as $data_row ) { 
//             fputcsv( $fh, $data_row ); 
//         } 
//         fclose( $fh ); 
//         ob_end_flush();
//         exit;
//         wp_send_json(array('data' =>'Sucess' ,'status'=> 'success', 'msg' => 'Downloaded'));
//     }
// }

// chart analysis in frontend of Surveys Ends
// ----------------------------------------------------------------------------------------------------------
add_action( 'wp_ajax_dean_survey_export_csv_report', 'dean_survey_export_csv_report' );
function dean_survey_export_csv_report(){
    global $wpdb;
    $table       = $wpdb->prefix.'d_surveys';
    $user_table  = $wpdb->prefix.'users';
    $user_id     = get_current_user_id();

    $survey_table   = $wpdb->prefix.'d_submitted_surveys';
    $cat_table      = $wpdb->prefix.'d_categories_surveys';
    
    $filename   = 'd_survey_report_sheet-'.date('Y-m-d'); 
    $header_row = array( 'S NO.', 'Title', 'category_name' ,'Created By', 'Male', 'Female','Other', 'Submitted Survey', 'status','start_date' ,'end_date', 'Question','Answers' );
    $data_rows  = array();

    $results    = $wpdb->get_results("SELECT u.display_name,c.category_name, valu.title, valu.id, valu.male_count ,valu.female_count , valu.other_count, valu.total_cnt, valu.is_stopped, valu.start_date, valu.end_date  FROM ( SELECT * FROM (SELECT survey_id, COUNT(CASE WHEN gender='M' then 1 end) AS male_count, COUNT(CASE WHEN gender='F' then 1 end) AS female_count, COUNT(CASE WHEN gender='O' then 1 end) AS other_count, COUNT(*) AS total_cnt FROM $survey_table group by survey_id ) AS val LEFT JOIN $table ON $table.id = val.survey_id ) AS valu LEFT JOIN $user_table u on u.id = valu.user_id AND u.id = $user_id JOIN $cat_table c ON valu.category_id = c.id","ARRAY_A");

    if(empty($results)){
        wp_send_json(array( 'status'=> 'error', 'msg' => 'No data Found'),403);
    }else{
        $i = 1;
        foreach ( $results as $result ) 
        {

            if($result['is_stopped'] == 0){
                $result['is_stopped'] = 'Active';
            }else{
                $result['is_stopped'] = 'Inactive';
            }

            $id = $result["id"];
            $categories = json_decode(json_encode($wpdb->get_results('SELECT submitted_survey FROM '."$survey_table".' WHERE survey_id = '."$id".'')),true);

            foreach ($categories as $key => $surv) {
                $values[$key] = json_decode($surv['submitted_survey'],TRUE);
            }

            // Questions array
            $i = 0;
            $va = [];
            foreach ($values as $key => $value) {
                foreach ($value as $k => $v) {
                    $va[$k] = $v;
                } 
                $val[$key] = $va;
            }
            foreach ($val as $quest) {
                foreach ($quest as $qu) {
                    $Ques[$i] = $qu;
                    $i++;
                }
            }
            
            $q = $a = 1;

            foreach ($Ques as $key => $v) { 
                $que = $v['Question'];
                foreach ($v['Answers'] as $key => $an){
                    $answ = $answ.$an.', ';
                }
            $questions  = $questions.('Q'.$q++.') '.$que.' ?');
            $answers    = $answers.('Ans'.$a++.') '.$answ.' ');
            $answ       = '';
            }

            $row = array(
                $i++,
                $result['title'],
                $result['category_name'],
                $result['display_name'],
                $result['male_count'],
                $result['female_count'],
                $result['other_count'],
                $result['total_cnt'],
                $result['is_stopped'],
                $result['start_date'],
                $result['end_date'],
                $questions,
                $answers
            );
            $data_rows[] = $row;
            unset($Ques);
            unset($questions);
            unset($answers);
        }
        // die();
        ob_end_clean ();
        $fh = @fopen( 'php://output', 'w' ); 
        fprintf( $fh, chr(0xEF) . chr(0xBB) . chr(0xBF) ); 
        header( 'Cache-Control: must-revalidate, post-check=0, pre-check=0' ); 
        header( 'Content-Description: File Transfer' ); 
        header( 'Content-type: text/csv' ); 
        // header('Content-type: "application/vnd.ms-excel"; charset=utf-8');
        header( "Content-Disposition: attachment; filename=$filename".'.csv' ); 
        header( 'Expires: 0' ); 
        header( 'Pragma: public' ); 
        fputcsv( $fh, $header_row ); 
        foreach ( $data_rows as $data_row ) { 
            fputcsv( $fh, $data_row ); 
        } 
        fclose( $fh ); 
        ob_end_flush();
        exit;
        wp_send_json(array('data' =>'Sucess' ,'status'=> 'success', 'msg' => 'Downloaded'));
    }
}