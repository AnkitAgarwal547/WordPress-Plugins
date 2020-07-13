
// ------------------------------------------------------------------------------------------------------------------
//  For Creating Survey Validation
// ------------------------------------------------------------------------------------------------------------------
jQuery(document).ready(function( $ ) {
    var i = 0;
    var j = 0;
    var k = 0;
    // -------------------------------------
    // Add New Question
    $(document).on('click', '#add-new-element', function(){

        var isValid;
        var first = '';
        var loop = 0;
        $('.error-messages-d').html('');
        $("#survey-form input").each(function() {
            var element = $(this);
            if(loop == 0){
                first = element;
                loop ++;
            }
            if (element.val() == "") {

                isValid = false;
                // $(this).focus();
                $(this).css("border", "1px solid red");
                $('.error-messages-d').append('<li>All fields are required</li>');
            }
        });

        if(isValid == false){
            first.focus()
            return true;
        }
        
        $html = '<div class="v-add-new-survey"><a class="delete-element"><i class="fa fa-trash"></i> Delete</a>' +
            '<input type="text" id="v-survey-question'+ i +'" placeholder="Enter Question" name="v-survey-question'+ i +'">' +
        
            '<div class="type-of-survey-answers">' +
                '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-input'+ i +'" class="survey-option-input" checked><label for="survey-option-input'+ i +'">Input Type</label>' +
                '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-radio'+ i +'" class="survey-option-radio" required><label for="survey-option-radio'+ i +'">One Choice</label>' +
                '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-checkbox'+ i +'" class="survey-option-checkbox" required><label for="survey-option-checkbox'+ i +'">Multiple Choice</label>' +
            '</div><i class="fa fa-plus collapse-answers-d" style="float: right;"></i>' + 

            '<div id="v-survey-answer-options" style="display:block"></div>' +
            '</div>';

        $('.main-survey-container').append($html);
        i++;
        $('.create-survey-without-load').resize();
    });
    //  Ends
    // -------------------------------------

    // -------------------------------------
    // Add Input Button Click options and clear previous if any

    $(document).on('click', '.survey-option-input', function(){
        var par = $(this).parent().parent().children('#v-survey-answer-options');
        $(document).find(par).empty();
        j = 0;
        k = 0;
    })

    //  Ends
    // -------------------------------------

    // -------------------------------------
    // Add Radio Button Click options
    $(document).on('click', '.survey-option-radio', function(){
        var par = $(this).parent().parent().children('#v-survey-answer-options');
        $(document).find(par).html('');
        j = 0;
        k = 0;

        if(j==0){
            $html = '<div class="radio-button-option-style"><input type="text" class="option-radio-options" name="option-radio-options'+ j +'">' +
                '<a class="radio-button-option-button">Add option</a></div>';
        
            var par = $(this).parent().parent().children('#v-survey-answer-options');
            $(document).find(par).html($html);
            j++;
        }
        $('.create-survey-without-load').resize();  
    });

    $(document).on('click', '.radio-button-option-button', function(){
        $html = '<div class="radio-button-option-style"><input type="text" class="option-radio-options" name="option-radio-options'+ j +'">'+
        '<span class="delete-option-radio">Delete Option</span></div>';
        $(this).parent().append($html);
        j++;
        $('.create-survey-without-load').resize();
    });

    $(document).on('click', '.delete-option-radio', function(){
        $(this).parent().remove();
    });
    //  Ends
    // -------------------------------------

    // -------------------------------------
    // Add Checkbox Button Click options
    $(document).on('click', '.survey-option-checkbox', function(){
        var par = $(this).parent().parent().children('#v-survey-answer-options');
        $(document).find(par).html('');
        j = 0;
        k = 0;

        if(k==0){
            $html = '<div class="checkbox-button-option-style"><input type="text" class="option-checkbox-options" name="option-checkbox-options'+ k +'">' +
                '<a class="checkbox-button-option-button">Add option</a></div>';
        
            var par = $(this).parent().parent().children('#v-survey-answer-options');
            $(document).find(par).html($html);
            k++;
        }
        $('.create-survey-without-load').resize();
    });

    $(document).on('click', '.checkbox-button-option-button', function(){
        $html = '<div class="checkbox-button-option-style"><input type="text" class="option-checkbox-options" name="option-checkbox-options'+ k +'">'+
        '<span class="delete-option-checkbox">Delete Option</span></div>';
        $(this).parent().append($html);
        k++;
        $('.create-survey-without-load').resize();
    });

    $(document).on('click', '.delete-option-checkbox', function(){
        $(this).parent().remove();
    });
    //  Ends
    // -------------------------------------
});

// ------------------------------------------------------------------------------------------------------------------
// Saving Survey into database using ajax
// ------------------------------------------------------------------------------------------------------------------
jQuery(document).ready(function( $ ) {
    // $(document).on('click', '#create-survey', function(){
        $(document).on('submit', '#survey-form', function(e){
            e.preventDefault();
            var isValid;
            var first = '';
            var loop = 0;
            $('.error-messages-d').html('');
            $("#survey-form input").each(function() {
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
            $('.dean-loader').show();
            const form = document.querySelector("#survey-form");
            const data = new FormData(form);
            // console.log(Array.from(data));
            var x = 0;
            var q = []; 
            var obj = [];
            $('.v-add-new-survey').each(function(){
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
            var cat = $('#d_category_id').val();

            var title = $('#survey_title').val();
            var formData = $(this).serializeArray();
            $.ajax({
                url: ajaxurl,
                type : 'post',
                dataType: 'json',
                data : {
                    action : 'create_survey_dean',
                    title: title,
                    form : obj,
                    cat: cat
                    // value: obj
                },
                success : function( resp ) {
                    if(resp.status == 'success'){
                        Swal.fire('Success', resp.msg, 'success');
                        setTimeout(() => {
                            window.location.href = window.location.pathname;
                        }, 1000);
                    }
                    else{
                        Swal.fire('Error', resp.msg, 'error');
                    }
                    $('.dean-loader').hide();
                }
            })
        })
    // })
})

// ------------------------------------------------------------------------------------------------------------------
//  Update functionality
// ------------------------------------------------------------------------------------------------------------------
    var i = 0;
    var j = 0;
    var k = 0;
    // -------------------------------------
    // Add New Question
    jQuery('#add-update-element').click(function($){

        var isValid;
        var first = '';
        var loop = 0;
        jQuery('.error-messages-d').html('');
        jQuery("#survey-form-update input").each(function() {
            var element = jQuery(this);
            if(loop == 0){
                first = element;
                loop ++;
            }
            if (element.val() == "") {

                isValid = false;
                // $(this).focus();
                jQuery(this).css("border", "1px solid red");
                jQuery('.error-messages-d').append('<li>All fields are required</li>');
            }
        });

        if(isValid == false){
            first.focus()
            return true;
        }
        
        $html = '<div class="v-add-new-survey"><a class="delete-element"><i class="fa fa-trash"></i> Delete</a>' +
            '<input type="text" id="v-survey-question'+ i +'" placeholder="Enter Question" name="v-survey-question'+ i +'">' +
        
            '<div class="type-of-survey-answers">' +
                '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-input'+ i +'" class="survey-option-input" required checked><label for="survey-option-input'+ i +'">Input Type</label>' +
                '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-radio'+ i +'" class="survey-option-radio" required><label for="survey-option-radio'+ i +'">One Choice</label>' +
                '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-checkbox'+ i +'" class="survey-option-checkbox" required><label for="survey-option-checkbox'+ i +'">Multiple Choice</label>' +
            '</div>' + 

            '<div id="v-survey-answer-options"></div>' +
            '</div>';

        jQuery('.main-survey-container').append($html);
        i++;
    });


// ------------------------------------------------------------------------------------------------------
// updating Survey into database using ajax
// ------------------------------------------------------------------------------------------------------
jQuery(document).ready(function( $ ) {
    // $(document).on('click', '#create-survey', function(){
        $('#survey-form-update').submit(function(e){
            e.preventDefault();
            $('.dean-loader').show();
            var isValid;
            var first = '';
            var loop = 0;
            $('.error-messages-d').html('');
            $("#survey-form-update input").each(function() {
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
            const form = document.querySelector("#survey-form-update");
            const data = new FormData(form);
            // console.log(Array.from(data));
            var x = 0;
            var q = []; 
            var obj = [];
            $('.v-add-new-survey').each(function(){
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
            console.log(obj);
            // return true;

            var title = $('#survey_title').val();
            var id = $('#survey_id').val();
            var formData = $(this).serializeArray();
            $.ajax({
                url: ajaxurl,
                type : 'post',
                dataType: 'json',
                data : {
                    action : 'update_survey_dean',
                    title: title,
                    form : obj,
                    id : id
                    // value: obj
                },
                success : function( resp ) {
                    if(resp.status == 'success'){
                        Swal.fire('Success', resp.msg, 'success');
                        setTimeout(() => {
                            window.location.href = window.location.pathname;
                        }, 1000);
                    }
                    else{
                        Swal.fire('Error', resp.msg, 'error');
                    }
                    $('.dean-loader').hide();
                }
            })
        })
    // })
    $(document).on('click', '.add-cat', function(){
         $.ajax({
            url: ajaxurl,
            type : 'POST',
            dataType: 'json',
            data : {
                action : 'create_cat_dean',
                cat_name: $('#newcategory').val(),
                // form : obj
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
    });    
})



// ------------------------------------------------------------------------------------------------------------------
// Saving Survey Submitted by User
// ------------------------------------------------------------------------------------------------------------------
jQuery(document).ready(function( $ ) {
    $('#survey-form-submit').submit(function(e){
        e.preventDefault();
        $(this).find('.dean-loader').show();
        var x = 0;
        var q = []; 
        var obj = [];


        // -------------------
        var loop = 0;
        var isValid = true;
        $('.error-messages-d').html('');
        $("#survey-form-submit input").each(function() {
            var element = $(this);
            if(loop == 0){
                first = element;
                loop ++;
            }
            if($(this).attr('id') != 'd_user_phone'){
                if (element.val() == "") {
                    // $(this).focus();
                    isValid = false;
                    $(this).css("border", "1px solid red");
                    $('.error-messages-d').append('<li>' + element.attr("name") + ' is required</li>');
                }
            }
        });

        if(isValid == false){
            $('html, body').animate({
                scrollTop: $("div.error-messages-d").offset().top
            }, 500)

            $(this).find('.dean-loader').hide();
            return true;
        }
        // --------------------

        $('.v-add-new-survey').each(function(){
            var y = 0;
            var o = [];
            if($(this).find('input[type=text]').attr('class') == 'input-survey-submit'){
                q[x] = $(this).children('input[type=hidden]').val();
                o[y] = $(this).find('input[type=text]').val();
            }
            if($(this).find('input[type=radio]:checked').attr('class') == 'radio-survey-submit'){
                q[x] = $(this).children('input[type=hidden]').val();
                $(this).find('.options-survey').each(function(){
                    if($(this).children('input[type=radio]:checked').val()){
                        o[y] = $(this).children('input[type=radio]:checked').val(); 
                        y++;
                    }
                })
            }
            if($(this).find('input[type=checkbox]:checked').attr('class') == 'checkbox-survey-submit'){
                q[x] = $(this).children('input[type=hidden]').val();
                $(this).find('.options-survey').each(function(){
                    if($(this).children('input[type=checkbox]:checked').val()){
                        o[y] = $(this).children('input[type=checkbox]:checked').val(); 
                        y++;
                    }
                })   
            }
            obj[x] = { 'Answers': o };
            x++;
        })
        // console.log(obj);
        // return true;
        var u_name   = $('.dean-basic-detail').find('#d_user_name').val();
        var u_gender = $('.dean-basic-detail').find('#d_user_gender').val();
        var u_age    = $('.dean-basic-detail').find('#d_user_age').val();
        var u_email  = $('.dean-basic-detail').find('#d_user_email').val();
        var u_phone  = $('.dean-basic-detail').find('#d_user_phone').val();

         $.ajax({
                url: ajaxurl,
                type : 'post',
                dataType: 'json',
                data : {
                    action : 'user_submit_survey',
                    form : obj,
                    name: u_name,
                    gender: u_gender,
                    age: u_age,
                    email: u_email,
                    phone: u_phone,
                    id : $('.token').val(),
                    school_id: school_id
                },
                success : function( resp ) {
                    if(resp.status == 'success'){
                        Swal.fire('Success', resp.msg, 'success');
                        setTimeout(() => {
                            window.location.href = base_url;
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

// ----------------------------------------------------------------------------------------------
//  ADD Employee by School Script
jQuery(document).ready(function ($) {
    $('#d_add_employee').submit(function (e) {
        e.preventDefault();
        $(this).find('.dean-loader').show();
        var x = 0;
        var q = [];
        var obj = [];


        // -------------------
        var loop = 0;
        var isValid = true;
        $('.error-messages-d').html('');
        $("#d_add_employee input").each(function () {
            var element = $(this);
            if (loop == 0) {
                first = element;
                loop++;
            }
            if ($(this).attr('id') != 'd_user_phone') {
                if (element.val() == "") {
                    // $(this).focus();
                    isValid = false;
                    $(this).css("border", "1px solid red");
                    $('.error-messages-d').append('<li>' + element.attr("name") + ' is required</li>');
                }
            }
        });

        if (isValid == false) {
            $('html, body').animate({
                scrollTop: $("div.error-messages-d").offset().top
            }, 500)

            $(this).find('.dean-loader').hide();
            return true;
        }
        // -------------------------------------------------
        var fname = $('#emp_fname').val();
        var lname = $('#emp_lname').val();
        var dname = $('#emp_dname').val();
        var email = $('#emp_email').val();
        var url   = $('#success_url').val();

        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'json',
            data: {
                action: 'd_add_employee',
                fname: fname,
                lname: lname,
                dname: dname,
                email: email,
                id: $('.token').val(),
            },
            success: function (resp) {
                if (resp.status == 'success') {
                    Swal.fire('Success', resp.msg, 'success');
                    setTimeout(() => {
                        window.location.href = url;
                    }, 1000);
                } else {
                    Swal.fire('Error', resp.msg, 'error');
                }
                $('.dean-loader').hide();
            }
        })

    });
})

// ----------------------------------------------------------------------------------------------
// Show Survey in the category page
jQuery(document).ready(function ($) {
    $(document).on('click', '#cat-survey-add', function(){
        var id = $(this).attr('data-id');
        $.ajax({
            url: ajaxurl,
            type: 'post',
            dataType: 'html',
            data: {
                action: 'd_append_create_survey',
                catid: id,
            },
            success: function (resp) {
                $('.create-survey-without-load').html(resp);
                $('.create-survey-without-load').resize();
                $('#bucket-counter').html(jQuery('.v-add-new-survey').length);
                $('.dean-loader').hide();
            }
        })
    })
})

// ------------------------------------------------------------------------------------------------------------------
// Saving Survey into database using ajax and give a preview
// ------------------------------------------------------------------------------------------------------------------
jQuery(document).ready(function( $ ) {
    // $(document).on('click', '#create-survey', function(){
        $(document).on('click', '#add-new-question', function(){

            var isValid;
            var first = '';
            var loop = 0;
            $('.error-messages-d').html('');
            $("#survey-form-preview input").each(function() {
                var element = $(this);
                if(loop == 0){
                    first = element;
                    loop ++;
                }
                if (element.val() == "") {
    
                    isValid = false;
                    // $(this).focus();
                    $(this).css("border", "1px solid red");
                    $('.error-messages-d').append('<li>All fields are required</li>');
                }
            });
    
            if(isValid == false){
                first.focus()
                return true;
            }
            
            $html = '<div class="v-add-new-survey"><a class="delete-element"><i class="fa fa-trash"></i> Delete</a>' +
                '<input type="text" id="v-survey-question'+ i +'" placeholder="Enter Question" name="v-survey-question'+ i +'">' +
            
                '<div class="type-of-survey-answers">' +
                    '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-input'+ i +'" class="survey-option-input" required checked><label for="survey-option-input'+ i +'" >Input Type</label>' +
                    '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-radio'+ i +'" class="survey-option-radio" required><label for="survey-option-radio'+ i +'">One Choice</label>' +
                    '<input type="radio" name="survey-option-input-'+ i +'" id="survey-option-checkbox'+ i +'" class="survey-option-checkbox" required><label for="survey-option-checkbox'+ i +'">Multiple Choice</label>' +
                '</div><i class="fa fa-plus collapse-answers-d" style="float: right;"></i>' + 
    
                '<div id="v-survey-answer-options" style="display:block"></div>' +
                '</div>';
    
            $('.main-survey-container').append($html);
            i++;
            $('.create-survey-without-load').resize();
            $('#bucket-counter').html(jQuery('.v-add-new-survey').length);
        });

        $(document).on('click', '#create-survey-next', function(e){
            e.preventDefault();
            var isValid;
            var first = '';
            var loop = 0;
            $('.error-messages-d').html('');
            $("#survey-form-preview input").each(function() {
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
            $('.dean-loader').show();
            const form = document.querySelector("#survey-form-preview");
            const data = new FormData(form);
            // console.log(Array.from(data));
            var x = 0;
            var q = []; 
            var obj = [];
            $('.v-add-new-survey').each(function(){
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
            var cat = $('#d_category_id').val();

            var title = $('#survey_title').val();
            var formData = $(this).serializeArray();
            $.ajax({
                url: ajaxurl,
                type : 'post',
                dataType: 'json',
                data : {
                    action : 'create_survey_dean_preview',
                    title: title,
                    form : obj,
                    cat: cat
                    // value: obj
                },
                success : function( resp ) {
                    if(resp.status == 'success'){
                        // Swal.fire('Success', resp.msg, 'success');
                        setTimeout(() => {
                            // window.location.href = window.location.pathname;
                            window.open(resp.link);
                        }, 1000);
                    }
                    else{
                        Swal.fire('Error', resp.msg, 'error');
                    }
                    $('.dean-loader').hide();
                }
            })
        })
    // })
})

// -----------------------------------------------------------------------------------
// preview save survey
jQuery(document).ready(function($){
    $(document).on('click', '#save-preview-survey', function(e){
        e.preventDefault();
        var id = $('.token').val();
        $.ajax({
            url: ajaxurl,
            type : 'post',
            dataType: 'json',
            data : {
                action : 'save_previewed_survey',
                id: id,
            },
            success: function ( resp ){
                Swal.fire('Success', resp.msg, 'success');
                setTimeout(() => {
                    window.close();
                }, 1000);
            }
        })
    })
})
// ------------------------------------------------------------------------------------------------------
// Delete the Items added on survey/default question
jQuery(document).ready(function( $ ) {
    $(document).on('click', '.delete-element', function(){
        $(this).parent().remove();
        $('#bucket-counter').html(jQuery('.v-add-new-survey').length);
    })
})

jQuery(document).ready(function($){
    $(document).on('click', '.collapse-answers-d', function(){
        $(this).next('#v-survey-answer-options').toggle();
        $(this).toggleClass('fa-plus fa-minus');
        $('.create-survey-without-load').resize();
    })
})


// ------------------------------------------------------------------------------------------------------------------
// Update User Details
// ------------------------------------------------------------------------------------------------------------------
jQuery(document).ready(function( $ ) {
    $('#user_info_update').submit(function(e){
        e.preventDefault();
        $('.dean-loader').show();
        var ajaxurl     = $(this).attr("action");
        $.ajax({
            url     : ajaxurl,
            type    : 'post',
            datatype: 'html',
            data    : {
                action          : 'edit_user_form',
                name            : $('#d_user_name').val(),
                age             : $('#d_user_age').val(),
                phone           : $('#d_user_phone').val(),
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

// ------------------------------------------------------------------------------------------------------------------
// Update User Password
// ------------------------------------------------------------------------------------------------------------------
jQuery(document).ready(function( $ ) {
    $('#user_password_update').submit(function(e){
        e.preventDefault();
        $('.dean-loader').show();
        var ajaxurl     = $(this).attr("action");
        $.ajax({
            url     : ajaxurl,
            type    : 'post',
            datatype: 'html',
            data    : {
                action          : 'update_user_password',
                pass1           : $('#new_password').val(),
                pass2           : $('#confirm_password').val(),
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
