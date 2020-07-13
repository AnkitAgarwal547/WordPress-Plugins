// <!-- <script type="text/javascript"> -->
    // -------------------------------------------------------------------------------------------------------------------------------
    //  For Creating Survey Validation
    jQuery(document).ready(function( $ ) {
        var i = 0;
        var j = 0;
        var k = 0;
        // -------------------------------------
        // Add New Question
        $('#add-new-survey').click(function(){
            var isValid;
            var first = '';
            var loop = 0;
            $('.error-messages-d').html('');
            $("#survey-form-cat input").each(function() {
                var element = $(this);
                if(loop == 0){
                    first = element;
                    loop ++;
                }
                if (element.val() == "") {

                    isValid = false;
                    // $(this).focus();
                    $(this).css("border", "1px solid red");
                    $('.error-messages-d').append('<li>' + element.attr("name") + ' is required</li>');
                }
            });

            if(isValid == false){
                first.focus()

                return true;
            }
            
            $html = '<div class="add-new-element"><p class="delete-element"><i class="fa fa-trash"> Delete</i></p>' +
                '<input type="text" id="v-survey-question'+ i +'" placeholder="Enter Question" name="v-survey-question'+ i +'">' +
            
                '<div class="type-of-survey-answers">' +
                    '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-input'+ i +'" class="survey-option-input" required checked><label for="survey-option-input'+ i +'">Input Type</label>' +
                    '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-radio'+ i +'" class="survey-option-radio" required><label for="survey-option-radio'+ i +'">One Choice</label>' +
                    '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-checkbox'+ i +'" class="survey-option-checkbox" required><label for="survey-option-checkbox'+ i +'">Multiple Choice</label>' +
                '</div>' + 

                '<div id="survey-answer-options"></div>' +
                '</div>';

            $('.main-survey-container').append($html);
            i++;
        });
        //  Ends
        // -------------------------------------

        // -------------------------------------
        // Add Input Button Click options and clear previous if any

        $(document).on('click', '.survey-option-input', function(){
            var par = $(this).parent().parent().children('#survey-answer-options');
            $(document).find(par).empty();
            j = 0;
            k = 0;
        })

        //  Ends
        // -------------------------------------

        // -------------------------------------
        // Add Radio Button Click options
        $(document).on('click', '.survey-option-radio', function(){
            var par = $(this).parent().parent().children('#survey-answer-options');
            $(document).find(par).html('');
            j = 0;
            k = 0;

            if(j==0){
                $html = '<div class="radio-button-option-style"><input type="text" class="option-radio-options" name="option-radio-options'+ j +'">' +
                    '<a class="radio-button-option-button">Add option</a></div>';
            
                var par = $(this).parent().parent().children('#survey-answer-options');
                $(document).find(par).html($html);
                j++;
            }           
        });

        $(document).on('click', '.radio-button-option-button', function(){
            $html = '<div class="radio-button-option-style"><input type="text" class="option-radio-options" name="option-radio-options'+ j +'">'+
            '<span class="delete-option-radio">Delete Option</span></div>';
            $(this).parent().append($html);
            j++;
        });

        $(document).on('click', '.delete-option-radio', function(){
            $(this).parent().remove();
        });
        //  Ends
        // -------------------------------------

        // -------------------------------------
        // Add Checkbox Button Click options
        $(document).on('click', '.survey-option-checkbox', function(){
            var par = $(this).parent().parent().children('#survey-answer-options');
            $(document).find(par).html('');
            j = 0;
            k = 0;

            if(k==0){
                $html = '<div class="checkbox-button-option-style"><input type="text" class="option-checkbox-options" name="option-checkbox-options'+ k +'">' +
                    '<a class="checkbox-button-option-button">Add option</a></div>';
            
                var par = $(this).parent().parent().children('#survey-answer-options');
                $(document).find(par).html($html);
                k++;
            }
        });

        $(document).on('click', '.checkbox-button-option-button', function(){
            $html = '<div class="checkbox-button-option-style"><input type="text" class="option-checkbox-options" name="option-checkbox-options'+ k +'">'+
            '<span class="delete-option-checkbox">Delete Option</span></div>';
            $(this).parent().append($html);
            k++;
        });

        $(document).on('click', '.delete-option-checkbox', function(){
            $(this).parent().remove();
        });
        //  Ends
        // -------------------------------------
    });
    // </script>
    // <script>


    // -----------------------------------------------------------------------------------------------------
    // Saving Category into database
    jQuery(document).ready(function( $ ) {
        $(document).on('click', '.add-cat', function(e){
            e.preventDefault();
            $(this).find('.dean-loader').show();
            var cat_name = $('#newcategory').val();
            if(cat_name == ""){
                Swal.fire('Error', 'Category Name is required', 'error');
                $('.dean-loader').hide();
                return true;
            }
            var parent_cat = $('#parent_cat').val();
            // var file = $("#category_icon")[0].files[0];

                    
            // var FR = new FileReader();
            // var fileData123 = "";
            // FR.addEventListener("load", function(e) {
            //     document.getElementById("category-icon-preview").src = e.target.result;
            //     $('#category-icon-preview').css('width', '100px');
            //     document.getElementById("b64").innerHTML = e.target.result;
            //     fileData123 = e.target.result;
            //     console.log($('#b64').html());
            // }); 
            
            // FR.readAsDataURL( $("#category_icon")[0].files[0] );
           
            // return true;

            $.ajax({
                url: ajaxurl,
                type : 'POST',
                dataType: 'json',
                data : {
                    action : 'create_cat_dean',
                    cat_name: cat_name,
                    parent_cat: parent_cat,
                    // file: fileData
                    // form : obj
                },
                success : function( resp ) {
                    if(resp.status == 'success'){
                        Swal.fire('Success', resp.msg, 'success');
                        setTimeout(() => {
                            location.reload(true);    
                        }, 1000);
                    }
                    else{
                        Swal.fire('Error', resp.msg, 'error');
                    }
                    $('.dean-loader').hide();
                }
            })
        });

        $('#category-add-toggle').on('click', function(){
            $('#category-add').removeClass('wp-hidden-child');
        });
    });

    // Category Deletion in Backend
    jQuery(document).ready(function( $ ) {
        $(document).on('click', '.delete-category', function(e){
            e.preventDefault();
            $(this).find('.dean-loader').show();
            var cat_id = $(this).attr('data-id');
            var parent_id = $(this).attr('parent-id');

            $.ajax({
                url: ajaxurl,
                type : 'POST',
                dataType: 'json',
                data : {
                    action : 'delete_cat_dean',
                    cat_id: cat_id,
                    parent_id: parent_id,
                    // form : obj
                },
                success : function( resp ) {
                    if(resp.status == 'success'){
                        Swal.fire('Success', resp.msg, 'success');
                        setTimeout(() => {
                            location.reload(true);    
                        }, 1000);
                    }
                    else{
                        Swal.fire('Error', resp.msg, 'error');
                    }
                    $('.dean-loader').hide();
                }
            })
        });
    });
    
	// ------------------------------------------------------------------------------------------------------
	// Saving Default Questions of Category into database using ajax
	jQuery(document).ready(function( $ ) {
    // $(document).on('click', '#create-survey', function(){

        $('#survey-form-cat').submit(function(e){
            e.preventDefault();
            $('.dean-loader').show();
            var isValid;
            var first = '';
            var loop = 0;
            $('.error-messages-d').html('');
            $("#survey-form-cat input").each(function() {
                var element = $(this);
                if(loop == 0){
                    first = element;
                    loop ++;
                }
                if (element.val() == "") {
                    isValid = false;
                    // $(this).focus();
                    $(this).css("border", "1px solid red");
                    $('.error-messages-d').append('<li>' + element.attr("name") + ' is required</li>');
                }
            });

            if(isValid == false){
                first.focus();
                return true;
            }
            const form = document.querySelector("#survey-form-cat");
            const data = new FormData(form);
            // console.log(Array.from(data));
            var x = 0;
            var q = []; 
            var obj = [];
            $('.add-new-element').each(function(){
                var y = 0;
                var o = [];
                q[x] = $(this).children('input').val();
                // check radio button click and apply below loop accordingly
                val = $(this).find('input[type=radio]:checked').attr('class');
                if(val == 'survey-option-input'){
                    o[y] = '';
                }
                if(val == 'survey-option-radio'){
                    $(this).find('.radio-button-option-style').each(function(){
                        o[y] = $(this).children('input').val();
                    y++;
                    })
                }
                if(val == 'survey-option-checkbox'){
                    $(this).find('.checkbox-button-option-style').each(function(){
                        o[y] = $(this).children('input').val();  
                    y++;
                    })   
                }
                obj[x] = { 'question': q[x],  'type': val, 'options': o };
                x++;
            })
            // console.log(obj);
            // return true;

            var cat = $('#d_survey_category').val();
            var formData = $(this).serializeArray();
            // return true;        
            $.ajax({
                url: ajaxurl,
                type : 'post',
                dataType: 'json',
                data : {
                    action : 'create_survey_cat_dean',
                    form : obj,
                    cat: cat
                },
                success : function( resp ) {
                    if(resp.status == 'success'){
                        Swal.fire('Success', resp.msg, 'success');
                    }
                    else{
                        Swal.fire('Error', resp.msg, 'error');
                    }
                    $('.dean-loader').hide();
                }
            })
        })
	})
    
    // ------------------------------------------------------------------------------------------------------
	// Saving Default Questions of Category into database using ajax
	jQuery(document).ready(function( $ ) {
        // Validation
        var i = 0;
        var j = 0;
        var k = 0;
        // -------------------------------------
        // Add New Question
        $('#udpate-default-question').click(function(){
            var isValid;
            var first = '';
            var loop = 0;
            $('.error-messages-d').html('');
            $("#update-default-question-cat input").each(function() {
                var element = $(this);
                if(loop == 0){
                    first = element;
                    loop ++;
                }
                if (element.val() == "") {

                    isValid = false;
                    // $(this).focus();
                    $(this).css("border", "1px solid red");
                    $('.error-messages-d').append('<li>' + element.attr("name") + ' is required</li>');
                }
            });

            if(isValid == false){
                first.focus()

                return true;
            }
            
            $html = '<div class="add-new-element"><p class="delete-element"><i class="fa fa-trash"> Delete</i></p>' +
                '<input type="text" id="v-survey-question'+ i +'" placeholder="Enter Question" name="v-survey-question'+ i +'">' +
            
                '<div class="type-of-survey-answers">' +
                    '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-input'+ i +'" class="survey-option-input" required><label for="survey-option-input'+ i +'">Input Type</label>' +
                    '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-radio'+ i +'" class="survey-option-radio" required><label for="survey-option-radio'+ i +'">One Choice</label>' +
                    '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-checkbox'+ i +'" class="survey-option-checkbox" required><label for="survey-option-checkbox'+ i +'">Multiple Choice</label>' +
                '</div>' + 

                '<div id="survey-answer-options"></div>' +
                '</div>';

            $('.main-survey-container').append($html);
            i++;
        });

        // --------------------------------------------------------
        // form submit
        $('#update-default-question-cat').submit(function(e){
            e.preventDefault();
            $(this).find('.dean-loader').show();
            var isValid;
            var first = '';
            var loop = 0;
            $('.error-messages-d').html('');
            $("#update-default-question-cat input").each(function() {
                var element = $(this);
                if(loop == 0){
                    first = element;
                    loop ++;
                }
                if (element.val() == "") {
                    isValid = false;
                    // $(this).focus();
                    $(this).css("border", "1px solid red");
                    $('.error-messages-d').append('<li>' + element.attr("name") + ' is required</li>');
                }
            });

            if(isValid == false){
                first.focus();
                $(this).find('.dean-loader').hide();
                return true;
            }
            const form = document.querySelector("#update-default-question-cat");
            const data = new FormData(form);
            // console.log(Array.from(data));
            var x = 0;
            var q = []; 
            var obj = [];
            $('.add-new-element').each(function(){
                var y = 0;
                var o = [];
                q[x] = $(this).children('input').val();
                // check radio button click and apply below loop accordingly
                val = $(this).find('input[type=radio]:checked').attr('class');
                if(val == 'survey-option-input'){
                    o[y] = '';
                }
                if(val == 'survey-option-radio'){
                    $(this).find('.radio-button-option-style').each(function(){
                        o[y] = $(this).children('input').val();
                    y++;
                    })
                }
                if(val == 'survey-option-checkbox'){
                    $(this).find('.checkbox-button-option-style').each(function(){
                        o[y] = $(this).children('input').val();  
                    y++;
                    })   
                }
                obj[x] = { 'question': q[x],  'type': val, 'options': o };
                x++;
            })
            // console.log(obj);
            // return true;

            var cat = $('#update_default_question').val();
            var success_url = $('#url_after_success').val();
            var formData = $(this).serializeArray();
            // return true;        
            $.ajax({
                url: ajaxurl,
                type : 'post',
                dataType: 'json',
                data : {
                    action: 'update_default_question_cat',
                    form: obj,
                    cat: cat,
                    success_url: success_url
                },
                success : function( resp ) {
                    if(resp.status == 'success'){
                        Swal.fire('Success', resp.msg, 'success');
                        setTimeout(() => {
                            location.replace(resp.url);
                        }, 1000);
                    }
                    else{
                        Swal.fire('Error', resp.msg, 'error');
                    }
                    $('.dean-loader').hide();
                }
            })
        })
    })

    // ------------------------------------------------------------------------------------------------------
    // Delete the Items added on survey/default question
    jQuery(document).ready(function( $ ) {
        $(document).on('click', '.delete-element', function(){
            $(this).parent().remove();
        })
    })