// Survey chart
jQuery('#asd').on('change', function(e) {
    e.preventDefault(); 
    post_id =  jQuery('select :selected').val();
    jQuery.ajax({ 
    url: ajaxurl, 
    dataType: 'json',
    data : {
        action : 'vendor_survey_report',
        id : post_id,
    },
    context: document.body, 
    success: function(survey){
        jQuery("#report_anl").slideDown( "slow", function() {
            var m ;
            var f ;
            var o ;

            var male    = new Array(survey.male);
            var female  = new Array(survey.female);
            var other   = new Array(survey.other);

            jQuery.each(male, function(i, val) {
                 m = Object.values(val);
            });

            jQuery.each(female, function(i, val) {
                 f = Object.values(val);
            });

            jQuery.each(other, function(i, val) {
                 o = Object.values(val);
            });
            
            var ctx     = document.getElementById('myChart').getContext('2d');
            var myChart = new Chart(ctx, {
              type: 'line',
              data: {
                labels: [15,30,45,60,75,90,100],
                datasets: [{ 
                    data: m,
                    label: "Male",
                    borderColor: "#3e95cd",
                    fill: false
                  }, { 
                    data: f,
                    label: "Female",
                    borderColor: "rgba(255,99,132,1)",
                    fill: false
                  }, { 
                    data: o,
                    label: "Other",
                    borderColor: "#FF4500",
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
        });
    }});
});

// frontend Survey chart question
jQuery( "#question-compare" ).submit(function(e) {
    if(jQuery("#filed-first-input").val() ===  jQuery("#filed-second-input").val())
    {
      jQuery('#filed-first-input').css("border", "1px solid red");
      jQuery('#filed-second-input').css("border", "1px solid red");
      jQuery('.error-messages-d').append('<li> Questions must be different on both fields!!</li>');
      return true;
    }
    e.preventDefault(); 
    jQuery(this).find('.dean-loader').show(200);
    jQuery("#myChart_first").remove();
    jQuery("#myChart_second").remove();
    var isValid;
    var first = '';
    var loop = 0;
    jQuery(".inp").each(function() {
        var element = jQuery(this);
        if(loop == 0){
            first = element;
            loop ++;
        }
        if(element.val() == "") {
            isValid = false;
            jQuery(this).css("border", "1px solid red");
            jQuery('.error-messages-d').append('<li>' + element.attr("name") + ' is required</li>');
        }
    });

    if(isValid == false){
        first.focus()
        return true;
    }

    jQuery.ajax({ 
    url: ajaxurl, 
    type: 'POST',
    dataType: 'json',
    data : {
        action : 'question_compare',
        q1: jQuery('#filed-first-input').val(),
        q2: jQuery('#filed-second-input').val(),
    },
    context: document.body, 
    success: function(survey){
      jQuery("#report_analysis").append("<canvas id='myChart_first' class='chart-comp' width='300' height='200' ></canvas><canvas id='myChart_second' width='300' height='200' class='chart-comp'></canvas>");
      jQuery("#report_analysis").slideDown( "slow", function() {
            // first chart start
            var male    = new Array(survey.male);
            var female  = new Array(survey.female);
            var other   = new Array(survey.other);
            var answer  = new Array(survey.answer);
            console.log(male);
            console.log(female);
            console.log(other);

            var type    = jQuery('#form-type').val();

            jQuery.each(male, function(i, val) {
                 m = Object.values(val);
            });
            jQuery.each(female, function(i, val) {
                 f = Object.values(val);
            });
            jQuery.each(other, function(i, val) {
                 o = Object.values(val);
            });
            jQuery.each(answer, function(i, val) {
                 A = Object.values(val);
            });

            var ctx     = document.getElementById('myChart_first').getContext('2d');
            var myChart = new Chart(ctx, {
              type: type,
              data: {
                labels: A,
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
                  text: 'Submited surveys answers of first question according to age and gender'
                }
              }
            }); 
            // first chart end

            // second chart start 
            var male1    = new Array(survey.male1);
            var female1  = new Array(survey.female1);
            var other1  = new Array(survey.other1);
            var answer1  = new Array(survey.answer1);
          
            var type    = jQuery('#form-type').val();

            jQuery.each(male1, function(i, val) {
                 m1 = Object.values(val);
            });
            jQuery.each(female1, function(i, val) {
                 f1 = Object.values(val);
            });
            jQuery.each(other1, function(i, val) {
                 o1 = Object.values(val);
            });
            jQuery.each(answer1, function(i, val) {
                 A1 = Object.values(val);
            });

            var ctx2     = document.getElementById('myChart_second').getContext('2d');
            var myChart2 = new Chart(ctx2, {
              type: type,
              data: {
                labels: A1,
                datasets: [{ 
                    data: m1,
                    label: "Male",
                    borderColor: "#4472c4",
                    backgroundColor:"#4472c4",
                    fill: false
                  }, { 
                    data:f1,
                    label: "Female",
                    borderColor: "#ed7d31",
                    backgroundColor:"#ed7d31",
                    fill: false
                  },{ 
                    data:o1,
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
                  text: 'Submited surveys answers of second question according to age and gender'
                }
              }
            }); 
            // second chart end
        });
      jQuery('.dean-loader').hide(200);
    }
    });
});

