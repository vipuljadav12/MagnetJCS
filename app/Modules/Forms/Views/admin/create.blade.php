@extends('layouts.admin.app')
@section('content')
<div class="heading-and-filters d-flex justify-content-between align-items-center">
    <div class="dashboard-top-tabs">
        <ul class="nav nav-tabs">
            <li class="page-title"><a href="{{url('/admin/Page/edit/'.$page_id)}}">General</a></li>
            <li class="page-title active"><a href="#">Create</a></li>
            <li class="page-title"><a href="{{url('/admin/form/preview/'.$page_id)}}">Preview</a></li>
            <li class="page-title"><a href="{{url('/admin/Answers/'.$page_id)}}">Submission</a></li>
        </ul>
    </div>
    <div class="text-right mb-10 mt-10 mr-10">
        <a href="{{url('/admin/Page')}}" title="" class="btn btn-orange text-white"><i class="fa fa-arrow-left"></i> Back</a>
        @if($page->page_status == "Publish")
        <a target="_blank" href="{{ url('/').'/'.$page->access_url }}" class="btn btn-blue text-white ml-5 mr-5" ><i class="fas fa-external-link-alt"></i></a>
        @endif
    </div>
</div>
@include('layouts.admin.common.alerts')
<div class="p-20 dashboard-box">
    <div class="card header-tab dashboard-top-tabs">
        <div class="card-body">
            <ul class="nav nav-tab">
                <li class="page-title pr-10 active" onClick="subheadertab(this,'fields')"><a href="#">Fields</a> |</li>
                <li class="page-title pr-10" onClick="subheadertab(this,'design')"><a href="#">Design</a> |</li>
                <li class="page-title pr-10" onClick="subheadertab(this,'share')"><a href="#">Share</a></li>
            </ul>
        </div>
    </div>
    <div class="card header-tab-content active" id="tab-fields">
        @include('Form::admin.fields')
    </div>
    <div class="card header-tab-content" id="tab-design">
        @include('Form::admin.design') 
    </div>
    <div class="card header-tab-content" id="tab-share">
        @include('Form::admin.share')
    </div>
</div>
@endsection
@section('footscript')
<script type="text/javascript" src="{{url('/')}}/resources/plugins/unisharp/laravel-ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{{url('/resources/plugins/unisharp/laravel-ckeditor/adapters/jquery.js')}}"></script>
<script type="text/javascript">
    $(function() {
       $(".help_text_view").tooltip();
   });

    /*-- Switchery JS Start  --*/   
    var elems = Array.prototype.slice.call(document.querySelectorAll('.js-switch')); 
    elems.forEach(function(html) {
        var switchery = new Switchery(html, { size: 'small' });
    });

  /*-- Switchery JS End  --*/ 

    var length = $("#formSubmit").find('textarea[name="description"]').length;
    var i = 1;
    var BASE_URL = "{{url('/')}}";
    $("#formSubmit").find('textarea[name="description"]').each(function(){
        $(this).attr('id','description_'+i);
        CKEDITOR.replace('description_'+i,{
            height:400,
            filebrowserImageBrowseUrl: BASE_URL  + '/resources/plugins/unisharp/laravel-ckeditor/imageupload.php?type=Image&token={{csrf_token()}}&ajaxurl={{url('/')}}',
            filebrowserBrowseUrl: BASE_URL  + '/resources/plugins/unisharp/laravel-ckeditor/imageupload.php?type=Image&token={{csrf_token()}}&ajaxurl={{url('/')}}',
            filebrowserUploadUrl: BASE_URL  + '/resources/plugins/unisharp/laravel-ckeditor/imageupload.php?type=Image&token={{csrf_token()}}&ajaxurl={{url('/')}}',
            filebrowserWindowWidth: '1000',
            filebrowserWindowHeight: '700',
            basePath: BASE_URL+'/resources/assets/admin/',
            baseHref: BASE_URL+"/resources/assets/admin/"
        });
        var ck_id = $(this).attr('id');
        //CKEDITOR.instances[ck_id].destroy();
        i++;
    });
    //enableckeditor();
    function enableckeditor() {
        var length = $("#formSubmit").find('textarea[name="description"]').length;
        var i = 1;
        $("#formSubmit").find('textarea[name="description"]').each(function(){
                // $("#cke_description_"+i).remove();
                if(length == i) {
                    console.log('In:-'+i);
                    $(this).attr('id','description_'+i);
                    
                }
                var ck_id = $(this).attr('id');
                if(CKEDITOR.instances[ck_id])  {
                    CKEDITOR.instances[ck_id].destroy();
                }
                CKEDITOR.replace(ck_id,{
                    height:400,
                    filebrowserImageBrowseUrl: BASE_URL  + '/resources/plugins/unisharp/laravel-ckeditor/imageupload.php?type=Image&token={{csrf_token()}}&ajaxurl={{url('/')}}',
                    filebrowserBrowseUrl: BASE_URL  + '/resources/plugins/unisharp/laravel-ckeditor/imageupload.php?type=Image&token={{csrf_token()}}&ajaxurl={{url('/')}}',
                    filebrowserUploadUrl: BASE_URL  + '/resources/plugins/unisharp/laravel-ckeditor/imageupload.php?type=Image&token={{csrf_token()}}&ajaxurl={{url('/')}}',
                    filebrowserWindowWidth: '1000',
                    filebrowserWindowHeight: '700',
                    basePath: BASE_URL+'/resources/assets/admin/',
                    baseHref: BASE_URL+"/resources/assets/admin/"
                });
                i = i+1;
            });
    }

    function setValue(text) {
        var property = $(text).attr('property');
        switch(property){
            case "label":
                changelabel(text);
            break;
            case "placeholder":
                changeplaceholder(text);
            break;
            case "helptext":
                changehelptext(text);
            break;
            case "minimum":
                changeminimum(text);
            break;
            case "maximum":
                changemaximum(text);
            break;
            case "formatvalidation":
                changeformatvalidation(text);
            break;
            case "formatvalidationmsg":
                changeformatvalidationmsg(text);
            break;
            case "required":
                changerequire(text);
            break;
            case "requiredvalidationmsg":
                changerequiredvalidationmsg(text);
            break;
            /*404*/
            case "showhide":
                changeshowhide(text);
            break;
            case "readonly":
                changereadonly(text);
            break;
            /*end404*/
            case "choices":
                changechoice(text);
            break;
            case "selected":
                changeselected(text);
            break;
            case "layout":
                changelayout(text);
            break;
            case "rows":
                changerows(text);
            break;
            case "button":
                changebutton(text);
            case "validation":
                changeValidation(text);
            case "reqvalidation":
                changeReqValidation(text);
            break;
        }
    }

    function changelabel(text) {
        var x = $(text).val();
        if(x == '') {
            $('.cust-box.active').find('.text_label').val('');
            $('.cust-box.active').find('.label_view').text('');
        } else {
            $('.cust-box.active').find('.text_label').val(x);
            $('.cust-box.active').find('.label_view').text(x);
        }
    }
    function changeplaceholder(text) {
        var x = $(text).val();
        if(x == '') {
            $('.cust-box.active').find('.place_holder_text').val('');
        } else {
            $('.cust-box.active').find('.placeholder').attr('placeholder',x);
            $('.cust-box.active').find('.place_holder_text').val(x);
        }
    }
    function changehelptext(text) {
        var x = $(text).val();
        if(x == '') {
            $('.cust-box.active').find('.help_text').val('');
            $('.cust-box.active').find('.help_text_view').html('');
        } else {
            $('.cust-box.active').find('.help_text').val(x);
            $('.cust-box.active').find('.help_text_view').html("<i class='fas fa-info-circle'></i>");
            $('.cust-box.active').find('.help_text_view').attr('title',x);
            //$('.cust-box.active').find('.help_text_view').tooltip();
            //$('.help_text_view').tooltip();
        }
    }

    function changeminimum(text) {
        var x = $(text).val();
        if(x == '') {
            $('.cust-box.active').find('.minimum').val('');             
        } else {
            $('.cust-box.active').find('.minimum').val(x);
        }
    }

    function changemaximum(text) {
        var x = $(text).val();
        if(x == '') {
            $('.cust-box.active').find('.maximum').val('');
        } else {
            $('.cust-box.active').find('.maximum').val(x);
        }
    }

    function changeformatvalidation(text) {
        var x = $(text).val();
        if(x == '') {
            $('.cust-box.active').find('.format_validation').val('');
        } else {
            $('.cust-box.active').find('.format_validation').val(x);
        }
    }
    function changerequire(text) {
        var x = $(text).val();
        if($(text).prop("checked") == true){
            var required = "Yes";
            $('.cust-box.active').find('.field-required').text('*');
            $('.requiredvalidationmsg').show();
        } else {
            var required = "No";
            $('.cust-box.active').find('.field-required').text('');
            $('.requiredvalidationmsg').hide();
        }
        $('.cust-box.active').find('.required_field').val(required);
    }
        function changeformatvalidationmsg(text) {
            var x = $(text).val();
            if(x == '') {
               $('.cust-box.active').find('.format_validation_msg').val('');
           } else {
            $('.cust-box.active').find('.format_validation_msg').val(x);
        }
    }
      function changerequiredvalidationmsg(text) {
        var x = $(text).val();
        if(x == '') {
           $('.cust-box.active').find('.required_validation_msg').val('');
        } else {
        $('.cust-box.active').find('.required_validation_msg').val(x);
       }
    }
    /*404*/
    function changeshowhide(text) {
        var x = $(text).val();
        if($(text).prop("checked") == true){
            var show = "Yes";
        } else {
            var show = "No";
        }
        $('.cust-box.active').find('.showhide_field').val(show);
    } 
    function changereadonly(text) {
        var x = $(text).val();
        if($(text).prop("checked") == true){
            var readonly = "Yes";
        } else {
            var readonly = "No";
        }
        $('.cust-box.active').find('.readonly_field').val(readonly);
    }
    /*end404*/
    function changechoice(text) {
        var length = $(text).parent().parent().find('input[name="choice[]"]').length;
        var choices = "";
        var i = 1;
        var input_choice = "";
        if($(text).parent().parent().hasClass('radio-choice')) {
            var input = "radio";
        }
        if($(text).parent().parent().hasClass('checkbox-choice')) {
            var input = "checkbox";
        }
        if($(text).parent().parent().hasClass('dropdown-choice')) {
            var input = "dropdown";
        }

        var length = $(text).parent().parent().find('input[name="choice[]"]').length;
        $(text).parent().parent().find('input[name="choice[]"]').each(function(){
            if(input == "radio") {
                input_choice += '<label><input type="radio" class="" disabled="disabled" value="'+$(this).val()+'"> '+$(this).val()+' </label> ';
            } 
            if(input == "checkbox") {
                input_choice += '<label><input type="checkbox" class="" disabled="disabled" value="'+$(this).val()+'"> '+$(this).val()+' </label> ';
            }
            if(input == "dropdown") {
                if(i == 1) {
                    input_choice += '<select class="form-control custom-select">';
                }
                input_choice += '<option value="'+$(this).val()+'">'+$(this).val()+'</option>';
                if(length == i) {
                    input_choice += '</select>';   
                }
            }
            choices += $(this).val();
            if($(this).parent().find('input[type="checkbox"]').prop('checked')) {
                $('.cust-box.active').find('.selected').val($(this).val());
            }
            if(i != length) {
                choices += ",";
            }
            i++;
        });
        $('.cust-box.active').find('.all-choice').html(input_choice);
        $('.cust-box.active').find('.choice').val(choices);
    }
    function changeselected(text) {
        //$(text).not(text).prop('checked', false);
        $(text).parent().parent().find('input:checkbox').not(text).prop('checked', false);
        var selected_value = "";
        if($(text).prop('checked')) {
            selected_value = $(text).parent().find('.choice').val();
        }
        $('.cust-box.active').find('.selected').val(selected_value);
        $('.cust-box.active').find('.all-choice input,select > option').each(function(){
            if($(this).is('option')) {
                $(this).attr('selected',false);
                if($(this).val() == selected_value) {
                    $(this).attr('selected','selected');
                }
            } else {
                $(this).prop('checked',false);
                if($(this).val() == selected_value) {
                    $(this).prop('checked',true);
                }
            }
        });
    }

    function changelayout(text) {
        var x = $(text).val();
        var layout = "";
        var removeclass = "";
        if(x == "One Column") {
            layout = "one-column";
            removeclass = "two-column";
        } else if(x == "Two Column") {
            layout = "two-column";
            removeclass = "one-column";
        } else {
            layout = "";
            removeclass = "one-column two-column";
        }
        $('.cust-box.active').find('.layout').val(x);
        $('.cust-box.active').find('.all-choice').removeClass(removeclass).addClass(layout);
    }

    function changerows(text) {
        var x = $(text).val();
        if(x == '') {
            $('.cust-box.active').find('.rows').val('');
            $('.cust-box.active').find('textarea').attr('rows','');
        } else {
            $('.cust-box.active').find('.rows').val(x);
            $('.cust-box.active').find('textarea').attr('rows',x);
        }
    }
    
    function changebutton(text) {
        var x = $(text).val();
        if(x == '') {
            $('.button_setting').find('input').val('Save');
        } else {
            $('.button_setting').find('input').val(x);
        }
    }
    
    function changeValidation(text){
        var x = $(text).val();
        if(x == '') {
            $('.button_setting').find('input').val('Save');
        } else {
            $('.button_setting').find('input').val(x);
        }
    }

    $('#formtitle').on("change",function(){
        var x = $(this).val()
        if(x == '') {
            $('.ff-title-box').html('');
            $('#form_title').val('');
        } else {
            $('.ff-title-box').html(x);
            $('#form_title').val(x);
        }
    });

    // $('.radio-select-choice').click(function() {
    //     $('.radio-select-choice').not(this).prop('checked', false);
    //     var selected_value = $(this).parent().find('.choice').val();
    // });

    // $('.checkbox-select-choice').click(function() {
    //     $('.checkbox-select-choice').not(this).prop('checked', false);
    // });
    
    function headertab(id,id1) {
        $(id).addClass('active').siblings('.page-title').removeClass('active');
        $('.header-tab-content').removeClass('active');
        $('#tab-'+id1).addClass('active');
    };
    $('.custom-fields').on("click",".cust-box",function(){      
        // $('.ff-title-box').removeClass('active');       
        $('.cust-box').removeClass('active , spacer');
        $(this).addClass('active spacer');
        $('.form-setting').removeClass('active');
        $('.add-field-setting').addClass('active');
        if($(this).find(".cust-editor").length == 0) {
            $(editor()).appendTo('.spacer .box_control');      
        }
        
        var cust_in = $(this).find(".cust-box-in");

        var type = $('.cust-box.active').find('.type_id').val();
        if(cust_in.length == 1) {
            $(".field-type-list-detail").addClass("d-none");
            $(".field-type").removeClass("d-none");
        } else {
            if(type != undefined) {
                console.log(" type undefined ");
                setFormSettingValues(type);
            }    
        }
        
    });

    $('.button_setting').on("click",function(){      
        $('.cust-box').removeClass('active , spacer');
        $(this).addClass('active spacer');
        $('.form-setting').removeClass('active');
        $('.add-field-setting').addClass('active');
        if($(this).find(".cust-editor").length == 0) {
            $(editor()).appendTo('.spacer .box_control');      
        }
        $(".field-type-list-detail").addClass("d-none");
        $(".field-type").addClass("d-none");
        $(".button-property").removeClass("d-none");
        //var cust_in = $(this).find(".cust-box-in");

        // var type = $('.cust-box.active').find('.type_id').val();
        // if(cust_in.length == 1) {
        //     $(".field-type-list-detail").addClass("d-none");
        //     $(".field-type").removeClass("d-none");
        // } else {
        //     // if(type != undefined) {
        //     //     setFormSettingValues(type);
        //     // }    
        // }
        
    });

    $('.card-body').on("click",".ff-title-box",function(){  
        $('.cust-box').removeClass('active , spacer');  
        $(this).addClass('active');
        $('.form-setting').addClass('active');
        $('.add-field-setting').removeClass('active');
    });

    $(function() {
        $(".spacer").sortable({
            revert: false,
        });
        $("#draggable > div").each(function(){
            $(this).draggable({
                connectToSortable: '.spacer',
                containment: ".card-body",
                cursor: 'move',
                helper: 'clone',
                zIndex: 100,
                stop : function(event, ui){
                    box_control(this);
                    var curr = $('.spacer').children('.cust-box-out');
                    if(curr.length > 0) {
                        hideElement();    
                    }
                },
                revert: "invalid",
                scroll: false,
            });
        });
    });

    function hideElement() {
        $(".field-type").addClass('d-none');
    }

    function box_control(z){
        var y = $(z).children('span').html();
        var curr = $('.spacer').children('.cust-box-out');
        if(curr.length > 0) {
            $(curr).children('.cust-box-in , .box_control').remove();
            switch(y) {
                case "Text Box":
                    $(contentext()).appendTo(curr);
                    setFormSettingValues("1");
                    $(".textbox-property").removeClass('d-none');
                break;
                case "Email Address":
                    $(contenemail()).appendTo(curr);
                    setFormSettingValues("3");
                    $(".email-property").removeClass('d-none');
                break;
                case "MultiText":
                    $(contenmultitext()).appendTo(curr);
                    setFormSettingValues("8");
                    $(".multitext-property").removeClass('d-none');
                break;
                case "Textarea":
                    $(contentextarea()).appendTo(curr);
                    setFormSettingValues("9");
                    $(".textarea-property").removeClass('d-none');
                break;
                case "Check Box":
                    $(contencheckbox()).appendTo(curr);
                    setFormSettingValues("5");
                    $(".checkbox-property").removeClass('d-none');
                break;
                case "Radio Button":
                    $(contenradio()).appendTo(curr);
                    setFormSettingValues("4");
                    $(".radio-property").removeClass('d-none');
                break;
                case "Drop Down":
                    $(contendropdown()).appendTo(curr);
                    setFormSettingValues("7");
                    $(".dropdown-property").removeClass('d-none');
                break;
                case "Editor":
                    $(contenckeditor()).appendTo(curr);
                    enableckeditor();
                break;
                case "Date":
                    $(datePicker()).appendTo(curr);
                    setFormSettingValues("10");
                    $(".datepicker-property").removeClass('d-none');
                break;    
                case "Upload File":
                    $(uploadFile()).appendTo(curr);
                    setFormSettingValues("11");
                    $(".uploadfile-property").removeClass('d-none');
                break;
                case "Toggle":
                    $(toggle()).appendTo(curr);
                    setFormSettingValues("12");
                    $(".toggle-property").removeClass('d-none');
                break;         
            }
            //$(conten()).appendTo(curr);
            $(editor()).appendTo('.spacer .box_control');
            var i = 1;
            $(".spacer").parent().find('.box_control').each(function(){
                $(this).find('.sort').val(i);
                i++;
            });
        }
    };
    function contentext(){
        return '<div class="box_control"><label class="label_view"></label><span class="field-required text-danger ml-5"></span><span class="help_text_view"></span><small></small><input type="text" class="form-control placeholder" disabled="disabled"><input type="hidden" name="place_holder_text" class="place_holder_text"><input type="hidden" name="text_label" class="text_label"><input type="hidden" name="format_validation" class="format_validation"><input type="hidden" name="format_validation_msg" class="format_validation_msg"><input type="hidden" name="required_validation_msg" class="required_validation_msg"><input type="hidden" name="minimum" class="minimum"><input type="hidden" name="maximum" class="maximum"><input type="hidden" name="help_text" class="help_text"><input type="hidden" name="required_field" class="required_field"><input type="hidden" name="text_validation" class="text_validation"><input type="hidden" name="required_validation" class="required_validation"><input type="hidden" name="showhide_field" class="showhide_field"><input type="hidden" name="readonly_field" class="readonly_field"><input type="hidden" name="type_id" class="type_id" value="1"><input type="hidden" name="sort" class="sort" value=""></div>';
    };
    function contenemail(){
        return '<div class="box_control"><label class="label_view"></label><span class="field-required text-danger ml-5"></span><span class="help_text_view"></span><input type="email" class="form-control placeholder"><input type="hidden" name="place_holder_text" class="place_holder_text"><input type="hidden" name="text_label" class="text_label"><input type="hidden" name="format_validation" class="format_validation"><input type="hidden" name="help_text" class="help_text"><input type="hidden" name="required_field" class="required_field"><input type="hidden" name="required_validation" class="required_validation"><input type="hidden" name="required_validation_msg" class="required_validation_msg"><input type="hidden" name="showhide_field" class="showhide_field"><input type="hidden" name="readonly_field" class="readonly_field"><input type="hidden" name="type_id" class="type_id" value="3"><input type="hidden" name="sort" class="sort" value=""></div>';
    };
    function contentextarea(){
        return '<div class="box_control"><label class="label_view"></label><span class="field-required text-danger ml-5"></span><span class="help_text_view"></span><small></small><textarea class="form-control placeholder" disabled="disabled"></textarea><input type="hidden" name="place_holder_text" class="place_holder_text"><input type="hidden" name="text_label" class="text_label"><input type="hidden" name="minimum" class="minimum"><input type="hidden" name="maximum" class="maximum"><input type="hidden" name="help_text" class="help_text"><input type="hidden" name="rows" class="rows"><input type="hidden" name="required_field" class="required_field"><input type="hidden" name="required_validation" class="required_validation"><input type="hidden" name="required_validation_msg" class="required_validation_msg"><input type="hidden" name="showhide_field" class="showhide_field"><input type="hidden" name="readonly_field" class="readonly_field"><input type="hidden" name="type_id" class="type_id" value="9"><input type="hidden" name="sort" class="sort" value=""></div>';
    };
    function contenmultitext(){
        return '<div class="box_control"><div class="form-group"><label></label><input type="text" class="form-control"></div><div class="form-group"><label></label><input type="text" class="form-control"></div></div>';
    };
    function contencheckbox(){
        return '<div class="box_control"><label class="label_view"></label><span class="field-required text-danger ml-5">*</span><input type="hidden" name="text_label" class="text_label"><span class="help_text_view"></span><div class="all-choice"><label><input type="checkbox" class="" disabled="disabled" value="First choice"> First choice</label> <label><input type="checkbox" class="" disabled="disabled" value="Second choice"> Second choice</label> <label><input type="checkbox" class="" disabled="disabled" value="Third choice"> Third choice</label> </div><input type="hidden" name="layout" class="layout"><input type="hidden" name="choice" class="choice" value="First choice,Second choice,Third choice"><input type="hidden" name="selected" class="selected"><input type="hidden" name="help_text" class="help_text"><input type="hidden" name="required_field" class="required_field"><input type="hidden" name="required_validation_msg" class="required_validation_msg"><input type="hidden" name="showhide_field" class="showhide_field"><input type="hidden" name="readonly_field" class="readonly_field"><input type="hidden" name="type_id" class="type_id" value="5"><input type="hidden" name="sort" class="sort" value=""></div>';
    };
    function contenradio(){
        return '<div class="box_control"><label class="label_view"></label><span class="field-required text-danger ml-5">*</span><input type="hidden" name="text_label" class="text_label"><span class="help_text_view"></span><div class="all-choice"><label><input type="radio" class="" disabled="disabled" value="First choice"> First choice</label> <label><input type="radio" class="" disabled="disabled" value="Second choice"> Second choice</label> <label><input type="radio" class="" disabled="disabled" value="Third choice"> Third choice</label> </div><input type="hidden" name="layout" class="layout" value="Side by side"><input type="hidden" name="choice" class="choice" value="First choice,Second choice,Third choice"><input type="hidden" name="selected" class="selected"><input type="hidden" name="help_text" class="help_text"><input type="hidden" name="required_field" class="required_field"><input type="hidden" name="required_validation_msg" class="required_validation_msg"><input type="hidden" name="showhide_field" class="showhide_field"><input type="hidden" name="readonly_field" class="readonly_field"><input type="hidden" name="type_id" class="type_id" value="4"><input type="hidden" name="sort" class="sort" value=""></div>';
    };
    function contendropdown(){
        return '<div class="box_control"><label class="label_view"></label><span class="field-required text-danger ml-5">*</span><span class="help_text_view"></span><div class="all-choice"><select class="form-control custom-select"><option value="option 1">option 1</option><option value="option 2">option 2</option></select></div><input type="hidden" name="text_label" class="text_label"><input type="hidden" name="choice" class="choice" value="option 1,option 2"><input type="hidden" name="selected" class="selected"><input type="hidden" name="help_text" class="help_text"><input type="hidden" name="required_field" class="required_field"><input type="hidden" name="required_validation_msg" class="required_validation_msg"><input type="hidden" name="showhide_field" class="showhide_field"><input type="hidden" name="readonly_field" class="readonly_field"><input type="hidden" name="type_id" class="type_id" value="7"><input type="hidden" name="sort" class="sort" value=""></div>';
    };
    function contenckeditor(){
     return '<div class="box_control"><label></label><textarea name="description"></textarea><input type="hidden" name="type_id" class="type_id" value="6"><input type="hidden" name="sort" class="sort" value=""></div>';
 };
 function editor(){
    return  '<div class="cust-editor"><ul>' +
    '<li><a onclick="edit(\'cut\',this)" href="javascript:void(0);" title="" class="">' +
    '<div><i class="fas fa-cut"></i></div>' +
    'Cut</a></li>' +
    '<li><a onclick="edit(\'copy\',this)" href="javascript:void(0);" title="" class="">' +
    '<div><i class="fas fa-clone"></i></div>' +
    'Copy</a></li>' +
    '<li><a onclick="edit(\'paste\',this)" href="javascript:void(0);" title="" class="">' +
    '<div><i class="fas fa-paste"></i></div>' +
    'Paste</a></li>' +
    '<li><a onclick="edit(\'delete\',this)" href="javascript:void(0);" title="" class="">' +
    '<div><i class="fas fa-trash-alt"></i></div>' +
    'Delete</a></li>' +
    '<li><a onclick="dropd(this)" href="javascript:void(0);" title="" class="">' +
    '<div><i class="fas fa-plus"></i></div>' +
    'Insert field</a><div class="dropdownbox"><ul>' +
    '<li><a onclick="edit(\'addfieldbefore\',this)" href="javascript:void(0);" title="" class="">' +
    '<div><i class="fas fa-arrow-left"></i></div>' +
    'Insert Before</a></li>'+
    '<li><a onclick="edit(\'addfieldafter\',this)" href="javascript:void(0);" title="" class="">' +
    '<div><i class="fas fa-arrow-right"></i></div>' +
    'Insert After</a></li>'+
    '</ul></div></li>' +
    '<li><a onclick="edit(\'smaller\',this)" href="javascript:void(0);" title="" class="">' +
    '<div><i class="fas fa-arrow-left"></i></div>' +
    'Make smaller</a></li>' +
    '<li><a onclick="edit(\'bigger\',this)" href="javascript:void(0);" title="" class="">' +
    '<div><i class="fas fa-arrow-right"></i></div>' +
    'Make bigger</a></li>' +
    '<li><a onclick="edit(\'justify\',this)" href="javascript:void(0);" title="" class="">' +
    '<div><i class="fas fa-th"></i></div>' +
    'Justify row</a></li>' +
    '</ul></div>';
}
/*404*/
function datePicker(){
    return '<div class="box_control"><label class="label_view"></label><span class="field-required text-danger ml-5"></span><span class="help_text_view"></span><small></small><div class="input-group date"   id="datetimepicker1"><input type="text" class="form-control placeholder" disabled="disabled"/><div class="input-group-append input-group-addon"><span class="input-group-text"><i class="far fa-calendar-alt"></i></span></div></div><input type="hidden" name="place_holder_text" class="place_holder_text"><input type="hidden" name="text_label" class="text_label"><input type="hidden" name="format_validation" class="format_validation"><input type="hidden" name="help_text" class="help_text"><input type="hidden" name="required_field" class="required_field"><input type="hidden" name="showhide_field" class="showhide_field"><input type="hidden" name="readonly_field" class="readonly_field"><input type="hidden" name="required_validation_msg" class="required_validation_msg"><input type="hidden" name="type_id" class="type_id" value="10"><input type="hidden" name="sort" class="sort" value=""></div>';
};
function uploadFile(){
    return '<div class="box_control"><label class="label_view"></label><span class="field-required text-danger ml-5"></span><small></small><label></label><input type="file" class="form-control" name="fileToUpload" id="fileToUpload" disabled="disabled" style="font-size: 12px;"><input type="hidden" name="text_label" class="text_label" ><input type="hidden" name="format_validation" class="format_validation"><input type="hidden" name="help_text" class="help_text"><input type="hidden" name="required_field" class="required_field"><input type="hidden" name="required_validation_msg" class="required_validation_msg"><input type="hidden" name="showhide_field" class="showhide_field"><input type="hidden" name="readonly_field" class="readonly_field"><input type="hidden" name="type_id" class="type_id" value="11"><input type="hidden" name="sort" class="sort" value=""></div>';
}
function toggle(){
    return '<div class="box_control"><label class="label_view"></label><span class="field-required text-danger ml-5"></span><span class="help_text_view"></span><small></small><div class="form-group d-flex justify-content-between align-items-center"><div><input type="checkbox" class="js-switch1" id="toggle"  data-switchery="true" style="display: none;" disabled="disabled" checked ><span class="switchery switchery-small" style="box-shadow: rgb(223, 223, 223) 0px 0px 0px 0px inset; border-color: rgb(223, 223, 223); background-color: rgb(255, 255, 255); transition: border 0.4s ease 0s, box-shadow 0.4s ease 0s;"><small style="left: 0px; transition: background-color 0.4s ease 0s, left 0.2s ease 0s;"></small></span></div></div><input type="hidden" name="text_label" class="text_label"><input type="hidden" name="help_text" class="help_text"><input type="hidden" name="showhide_field" class="showhide_field"><input type="hidden" name="type_id" class="type_id" value="12"><input type="hidden" name="sort" class="sort" value=""></div>';



}
/*404*/
function dropd(id){
    $(id).parent('li').toggleClass('showdrop');
}   
function edit(id,id1) { 
    var x = id;
    var curr = $(id1).parents('.cust-box');
    var y = $(curr).attr('class');

    var classis = getcolclass(y);
    classis= parseInt(classis)
    var oldclass = classis;
    switch(x){
        case "cut":
        cut(id1);
        break;
        case "copy":
        copy(id1);
        break;
        case "paste":
        paste(id1);
        break;
        case "delete":
        del(id1);
        break;
        case "addfieldbefore":
        addnew1(id1);
        break;
        case "addfieldafter":
        addnew(id1);
        break;
        case "smaller":
        if(classis<=12 && classis>2){
            classis--;
        }
        else {
            classis;
        }
        break;
        case "bigger":
        if(classis<12 && classis>=2){
            classis++;
        }
        else {
            classis;
        }
        break;
        case "justify":     
        classis = 12;       
        break;
    }
    $(curr).removeClass('col-'+oldclass).addClass('col-'+classis);
}
var defaultstrucure = '<div class="cust-box-out"><div class="cust-box-in"><div class="box_control text-center p-5 fa-2x">+</div></div></div>';  
function cut(z){
    var curr = $(z).parents('.cust-box');
    var y = $(curr).clone();    
    $(curr).html(defaultstrucure);
}   
function copy(z){
    var curr = $(z).parents('.cust-box');
    var y = $(curr).clone();
}
function paste(z){

}
function del(z){
    var curr = $(z).parents('.cust-box');
    var main_div = $(curr).parent();
    var y = $(main_div).find('.cust-box').length;
    var i = 1;
    if(y>2){
        $(curr).remove();
    }
    else {      
        $(curr).html(defaultstrucure);
    }
    $(main_div).find('.box_control').each(function(){
        $(this).find('.sort').val(i);
        i++;
    });


}
function addnew1(z) {
    var curr = $(z).parents('.cust-box');
    var x = '<div class="col-12 cust-box">'+defaultstrucure+'</div>';
    $(x).insertBefore(curr);
}
function addnew(z) {
    var curr = $(z).parents('.cust-box');
    var x = '<div class="col-12 cust-box">'+defaultstrucure+'</div>';
    $(x).insertAfter(curr);
}
function getcolclass(classstring){
    var return1 = '';
    var result = classstring.split(' ');
    jQuery.each( result, function( i, val ) {
        var current = result[i];
        if (current.indexOf('col-') >= 0){
            return1 = current.replace("col-",'');
        }
    });
    return return1;
}
function drop(x){
    $(x).siblings('.field-type-list-detail').slideToggle();
}

function formSubmit(submit_data,page_content_id){
        //var arrText = new Array();

        var i = 0;
        var form_title = $("#form_title").val();
        var id;
        var layout = new Object();
        $("#formSubmit").find('.custom-fields').each(function() {
            var i = 0;
            id = $(this).attr('id');
            var form_id = $(this).find('input[name="form_id"]').val();
            var p_con = new Object();
            $(this).find('.box_control').each(function() {
                var  values = new Object();
                $(this).find('input,textarea').each(function() {
                    var disabled = $(this).attr('disabled');
                    if(disabled == undefined) {
                        if($(this).is('textarea')) {
                            var name = $(this).attr('name');
                            var ck_id = $(this).attr('id');
                            var ck_value = CKEDITOR.instances[ck_id].getData();
                            values[name] = ck_value;
                        } else {
                         // console.log($(this));
                         var name = $(this).attr('class');
                         values[name] = $(this).val();    
                     }

                 }
                    //val = JSON.stringify(values);
                });
                // console.log(values);
                p_con[i] = values;
                // i = i+1;
                //alert(id);
                layout[id] = p_con;
                layout[id]['form_id'] = form_id;
                i++;
            });
            //arrText.push(layout);
        });
        // var form_value = layout;
        $.ajax({
            url : "{{ url('admin/form/store') }}",
            
            data : {_token:'{{ csrf_token() }}',formValue:layout,form_title:form_title,submit_data:submit_data,page_content_id:page_content_id},
            type : 'post',
            success : function(response) {
                // console.log(response);
                location.href = "{{ url('admin/form/') }}"+"/"+response;
            }
        });
    }

    function setFormSettingValues(type){
        switch(type){
            case "1":
            setTextValue();
            break;
            case "3":
            setEmailValue();
            break;
            case "4":
            setRadioValue();
            break;
            case "5":
            setCheckboxValue();
            break;
            /*404editor*/
            case "6":
            setCkeditorValue();
            break;
            /*end*/
            case "7":
            setDropdownValue();
            break;
            case "8":
            setMultitextValue();
            break;
            case "9":
            setTextareaValue();
            break;
            case "10":
            setDatePickerValue();
            break;
            case "11":
            setUploadFileValue();
            break;
            case "12":
            setToggleValue();
            break;        
        }
    }



    function setTextValue(){
        $(".field-type-list-detail").addClass('d-none');
        $(".textbox-property").removeClass("d-none");
        $(".field-type").addClass("d-none");
        $('#text_label_setting').val($('.cust-box.active').find('.text_label').val()); 
        $('#text_placeholder_setting').val($('.cust-box.active').find('.place_holder_text').val()); 
        $('#text_format_validation_setting').val($('.cust-box.active').find('.format_validation').val());
        $('#text_format_validation_msg_setting').val($('.cust-box.active').find('.format_validation_msg').val()); 
        $('#text_required_validation_msg_setting').val($('.cust-box.active').find('.required_validation_msg').val()); 
        $('#text_minimum_setting').val($('.cust-box.active').find('.minimum').val()); 
        $('#text_maximum_setting').val($('.cust-box.active').find('.maximum').val()); 
        $('#help_text_setting').val($('.cust-box.active').find('.help_text').val()); 
        /*toggle Require //Show/Hide//Read Only*/
        toggleswitch('required_field','text_required_setting');
        toggleswitchon('showhide_field','text_showhide_setting');
        toggleswitch('readonly_field','text_readonly_setting');
        /*end*/
        
    }

    function setEmailValue(){
        $(".field-type-list-detail").addClass('d-none');
        $(".email-property").removeClass("d-none");
        $(".field-type").addClass("d-none");
        $('#email_label_setting').val($('.cust-box.active').find('.text_label').val()); 
        $('#email_placeholder_setting').val($('.cust-box.active').find('.place_holder_text').val()); 
        $('#email_help_text_setting').val($('.cust-box.active').find('.help_text').val()); 
        $('#email_required_validation_msg_setting').val($('.cust-box.active').find('.required_validation_msg').val());      
        /*toggle Require //Show/Hide//Read Only*/
        toggleswitch('required_field','email_required_setting');
        toggleswitchon('showhide_field','email_showhide_setting');
        toggleswitch('readonly_field','email_readonly_setting');
        /*end*/
    }

    function setTextareaValue(){
        $(".field-type-list-detail").addClass('d-none');
        $(".textarea-property").removeClass("d-none");
        $(".field-type").addClass("d-none");
        $('#textarea_label_setting').val($('.cust-box.active').find('.text_label').val()); 
        $('#textarea_placeholder_setting').val($('.cust-box.active').find('.place_holder_text').val()); 
        $('#textarea_minimum_setting').val($('.cust-box.active').find('.minimum').val()); 
        $('#textarea_maximum_setting').val($('.cust-box.active').find('.maximum').val()); 
        $('#textarea_help_text_setting').val($('.cust-box.active').find('.help_text').val());
        $('#textarea_rows_setting').val($('.cust-box.active').find('.rows').val());
        $('#textarea_required_validation_msg_setting').val($('.cust-box.active').find('.required_validation_msg').val());
        /*toggle Require //Show/Hide//Read Only*/
        toggleswitch('required_field','textarea_required_setting');
        toggleswitchon('showhide_field','textarea_showhide_setting');
        toggleswitch('readonly_field','textarea_readonly_setting');
        /*end*/
        
    }

    function setRadioValue(){
        $(".field-type-list-detail").addClass('d-none');
        $(".radio-property").removeClass("d-none");
        $(".field-type").addClass("d-none");
        $('#radio_help_text_setting').val($('.cust-box.active').find('.help_text').val());
        $('.radio-layout').val($('.cust-box.active').find('.layout').val());
        $("#radio_label_setting").val($('.cust-box.active').find('.text_label').val());
        $('#radio_required_validation_msg_setting').val($('.cust-box.active').find('.required_validation_msg').val());
        var selected = $('.cust-box.active').find('.selected').val();
        var choices = $('.cust-box.active').find('.choice').val();
        var choice = choices.split(',');
        var length = choice.length;
        var choice_html = "";
        var add_remove = "";
        $.each(choice,function(index,value){
            if(index == length-1) {
                add_remove = '<a href="javascript:void(0);" title="" class="mr-10 removechoice"><i class="far fa-trash-alt"></i></a><a href="javascript:void(0);" title="" class="addchoice"><i class="far fa-plus-square"></i></a>';
            } else {
                add_remove = '<a href="javascript:void(0);" title="" class="mr-10 removechoice"><i class="far fa-trash-alt"></i></a><a href="javascript:void(0);" title="" class="addchoice d-none"><i class="far fa-plus-square"></i></a>';
            }
            if(value == selected) {
                var checked = "checked='checked'";
            } else {
                var checked = "";
            }
            choice_html += '<div class="mb-5 d-flex align-items-center"><input type="checkbox" class="mr-10" property="selected" onchange="setValue(this)" '+checked+'><input type="text" class="form-control w-50 mr-10 choice" name="choice[]" value="'+value+'" property="choices" onchange="setValue(this)">'+add_remove+'</div>';
        });
        $(".radio-choice").html(choice_html);
        /*toggle Require //Show/Hide//Read Only*/
        toggleswitch('required_field','radio_required_setting');
        toggleswitchon('showhide_field','radio_showhide_setting');
        toggleswitch('readonly_field','radio_readonly_setting');
        /*end*/
    }

    function setCheckboxValue() {
        $(".field-type-list-detail").addClass('d-none');
        $(".checkbox-property").removeClass("d-none");
        $(".field-type").addClass("d-none");
        $('#checkbox_help_text_setting').val($('.cust-box.active').find('.help_text').val());
        $("#checkbox_label_setting").val($('.cust-box.active').find('.text_label').val());
        $("#checkbox_layout_setting").val($('.cust-box.active').find('.layout').val());
        $('#checkbox_required_validation_msg_setting').val($('.cust-box.active').find('.required_validation_msg').val());
        var selected = $('.cust-box.active').find('.selected').val();
        
        var choices = $('.cust-box.active').find('.choice').val();
        var choice = choices.split(',');
        var length = choice.length;
        var choice_html = "";
        var add_remove = "";
        $.each(choice,function(index,value){
            if(index == length-1) {
                add_remove = '<a href="javascript:void(0);" title="" class="mr-10 removechoice"><i class="far fa-trash-alt"></i></a><a href="javascript:void(0);" title="" class="addchoice"><i class="far fa-plus-square"></i></a>';
            } else {
                add_remove = '<a href="javascript:void(0);" title="" class="mr-10 removechoice"><i class="far fa-trash-alt"></i></a><a href="javascript:void(0);" title="" class="addchoice d-none"><i class="far fa-plus-square"></i></a>';
            }
            if(value == selected) {
                var checked = "checked='checked'";
            } else {
                var checked = "";
            }
            choice_html += '<div class="mb-5 d-flex align-items-center"><input type="checkbox" class="mr-10" property="selected" onchange="setValue(this)" '+checked+'><input type="text" class="form-control w-50 mr-10 choice" name="choice[]" value="'+value+'" property="choices" onchange="setValue(this)">'+add_remove+'</div>';
        });
        $(".checkbox-choice").html(choice_html);
        /*toggle Require //Show/Hide//Read Only*/
        toggleswitch('required_field','checkbox_required_setting');
        toggleswitchon('showhide_field','checkbox_showhide_setting');
        toggleswitch('readonly_field','checkbox_readonly_setting');
        /*end*/
    }
    /*setCkeditorValue()*/
    function setCkeditorValue() {
       $(".field-type-list-detail").addClass('d-none');
       $(".ckeditor-property").removeClass("d-none");
       $(".field-type").addClass("d-none");
       $('#ckeditor_label_setting').val($('.cust-box.active').find('.text_label').val()); 
       
   }
   /* */
   function setDropdownValue() {
    $(".field-type-list-detail").addClass('d-none');
    $(".dropdown-property").removeClass("d-none");
    $(".field-type").addClass("d-none");
    $('#dropdown_help_text_setting').val($('.cust-box.active').find('.help_text').val());
    $('#dropdown_required_validation_msg_setting').val($('.cust-box.active').find('.required_validation_msg').val());
    var selected = $('.cust-box.active').find('.selected').val();

    var choices = $('.cust-box.active').find('.choice').val();
    var choice = choices.split(',');
    var length = choice.length;
    var choice_html = "";
    var add_remove = "";
    $.each(choice,function(index,value){
        if(index == length-1) {
            add_remove = '<a href="javascript:void(0);" title="" class="mr-10 removechoice"><i class="far fa-trash-alt"></i></a><a href="javascript:void(0);" title="" class="addchoice"><i class="far fa-plus-square"></i></a>';
        } else {
            add_remove = '<a href="javascript:void(0);" title="" class="mr-10 removechoice"><i class="far fa-trash-alt"></i></a><a href="javascript:void(0);" title="" class="addchoice d-none"><i class="far fa-plus-square"></i></a>';
        }
        if(value == selected) {
            var checked = "checked='checked'";
        } else {
            var checked = "";
        }
        choice_html += '<div class="mb-5 d-flex align-items-center"><input type="checkbox" class="mr-10" property="selected" onchange="setValue(this)" '+checked+'><input type="text" class="form-control w-50 mr-10 choice" name="choice[]" value="'+value+'" property="choices" onchange="setValue(this)">'+add_remove+'</div>';
    });
    $(".dropdown-choice").html(choice_html);
    /*toggle Require //Show/Hide//Read Only*/
    toggleswitch('required_field','dropdown_required_setting');
    toggleswitchon('showhide_field','dropdown_showhide_setting');
    toggleswitch('readonly_field','dropdown_readonly_setting');
    /*end*/
}
/*404*/
function setDatePickerValue(){
   $(".field-type-list-detail").addClass('d-none');
   $(".datepicker-property").removeClass("d-none");
   $(".field-type").addClass("d-none");
   $('#date_label_setting').val($('.cust-box.active').find('.text_label').val()); 
   $('#date_placeholder_setting').val($('.cust-box.active').find('.place_holder_text').val()); 
   $('#date_help_text_setting').val($('.cust-box.active').find('.help_text').val());
   $('#date_required_validation_msg_setting').val($('.cust-box.active').find('.required_validation_msg').val()); 
   /*toggle Require //Show/Hide//Read Only*/
   toggleswitch('required_field','date_required_setting');
   toggleswitchon('showhide_field','date_showhide_setting');
   toggleswitch('readonly_field','date_readonly_setting');
   /*end*/
}


function setUploadFileValue(){
 $(".field-type-list-detail").addClass('d-none');
 $(".uploadfile-property").removeClass("d-none");
 $(".field-type").addClass("d-none");
 $('#uploadfile_label_setting').val($('.cust-box.active').find('.text_label').val()); 
 $('#uploadfile_format_validation_setting').val($('.cust-box.active').find('.format_validation').val()); 
 $('#uploadfile_help_text_setting').val($('.cust-box.active').find('.help_text').val()); 
 $('#uploadfile_required_validation_msg_setting').val($('.cust-box.active').find('.required_validation_msg').val()); 
 /*toggle Require //Show/Hide//Read Only*/
 toggleswitch('required_field','uploadfile_required_setting');
 toggleswitchon('showhide_field','uploadfile_showhide_setting');
 toggleswitch('readonly_field','uploadfile_readonly_setting');
 /*end*/

}

function setToggleValue(){
               // alert("hello");
               $(".field-type-list-detail").addClass('d-none');
               $(".toggle-property").removeClass("d-none");
               $(".field-type").addClass("d-none");
               $('#toggle_label_setting').val($('.cust-box.active').find('.text_label').val()); 
               $('#toggle_help_text_setting').val($('.cust-box.active').find('.help_text').val()); 
               /*toggle Require //Show/Hide//Read Only*/
               
                toggleswitchon('showhide_field','toggle_showhide_setting');
              
               /*end*/
               
           }

           /*FUNCTION*/
           function toggleswitch(className,idName){
             if($('.cust-box.active').find("."+className).val() == 'Yes'){
                var special = document.querySelector('#'+idName);
                special.checked = true;
                var event = document.createEvent('HTMLEvents');
                event.initEvent('change', true, true);
                special.dispatchEvent(event);
            }
            else {
             var special = document.querySelector('#'+idName);
             special.checked = false;
             var event = document.createEvent('HTMLEvents');
             event.initEvent('change', true, true);
             special.dispatchEvent(event);   
         }
     }
     function toggleswitchon(className,idName){
            if($('.cust-box.active').find("."+className).val() == 'No'){
                var special = document.querySelector('#'+idName);
                special.checked = false;
                var event = document.createEvent('HTMLEvents');
                event.initEvent('change', true, true);
                special.dispatchEvent(event);
            }
            else{
                var special = document.querySelector('#'+idName);
                special.checked = true;
                var event = document.createEvent('HTMLEvents');
                event.initEvent('change', true, true);
                special.dispatchEvent(event);
             }   
        }
     /*END*/
     /*404*/
     $(document).on("click", ".addchoice", function() {
        $(this).parent().find(".removechoice").removeClass('d-none');
        var clonnableObj = $(this).parent();

        var newObj = clonnableObj.clone();
        // var div = $('.card-box:last').find('.helpimage');
        // var number = parseInt( div.prop("id").match(/\d+/g), 10 ) ;
        // var num = parseInt( div.prop("id").match(/\d+/g), 10 ) +1;

        newObj.appendTo(clonnableObj.parent());
        newObj.find("input[name='choice[]']").val('');
        newObj.find("input:checkbox").prop('checked',false);
        $(this).addClass('d-none');
    });

     $(document).on("click", ".removechoice", function() {
        var clonnableObj = $(this).parent();
        var parentobj = $(this).parent().parent();
        clonnableObj.remove();
        var choices = "";
        var i = 1;
        var input_choice = "";
        if(parentobj.hasClass('radio-choice')) {
            var input = "radio";
        }
        if(parentobj.hasClass('checkbox-choice')) {
            var input = "checkbox";
        }
        if(parentobj.hasClass('dropdown-choice')) {
            var input = "dropdown";
        }
        var length = parentobj.find('input[name="choice[]"]').length;
        parentobj.find('input[name="choice[]"]').each(function(){
            if(input == "radio") {
                input_choice += '<label><input type="radio" class="" disabled="disabled" value="'+$(this).val()+'"> '+$(this).val()+' </label> ';
            } 
            if(input == "checkbox") {
                input_choice += '<label><input type="checkbox" class="" disabled="disabled" value="'+$(this).val()+'"> '+$(this).val()+' </label> ';
            }
            if(input == "dropdown") {
                if(i == 1) {
                    input_choice += '<select class="form-control custom-select">';
                }
                input_choice += '<option value="'+$(this).val()+'">'+$(this).val()+'</option>';
                if(length == i) {
                    input_choice += '</select>';   
                }
            }

            choices += $(this).val();
            if($(this).parent().find('input[type="checkbox"]').prop('checked')) {
                $('.cust-box.active').find('.selected').val($(this).val());
            }
            if(i != length) {
                choices += ",";
            }
            i++;
        });
        $('.cust-box.active').find('.all-choice').html(input_choice);
        $('.cust-box.active').find('.choice').val(choices);        
        //$(this).hide();
        //console.log($(this).parent().parent().find('.addchoice:last'));
        parentobj.find('.addchoice:last').removeClass('d-none');
        //alert($(this).parent().parent().find('.addchoice').length);
        if(parentobj.find('.addchoice').length < 2) {
            parentobj.find('.removechoice:last').addClass('d-none');
        }
        
        
    });

     function subheadertab(id,id1) {
        $(id).addClass('active').siblings('.page-title').removeClass('active');
        $('.header-tab-content').removeClass('active');
        $('#tab-'+id1).addClass('active');
    }

    /**************Styling of the Fields.*****************/
    $('.custom-fields').on("click",".des-box", function(){
        $('.des-box').removeClass('active , spacer');
        $(this).addClass('active');
        var type = $('.des-box.active').find('.type_id').val();
        $(".field-type-list-detail").addClass("d-none");
        if(type != undefined){
           setFormStyling(type);
       }

   });
    /**Depending on Type 
    * form styling
    *
    **/
    function setFormStyling(type){
       switch(type){
        case "1":
        setTextStyle();
        break;
        case "3":
        setEmailStyle();
        break;
        case "4":
        setRadioStyle();
        break;
        case "5":
        setCheckboxStyle();
        break;
        case "7":
        setDropdownStyle();
        break;
        case "8":
        setMultitextStyle();
        break;
        case "9":
        setTextareaStyle();
        break;
    }
}

function setTextStyle(){
    console.log("Text");
    $(".field-type-list-detail.textbox-styling").removeClass("d-none")
};
function setEmailStyle(){
    console.log("Email");
    $(".field-type-list-detail.email-styling").removeClass("d-none")
};
function setRadioStyle(){
    console.log("Radio");
    $(".field-type-list-detail.radio-styling").removeClass("d-none")
};
function setCheckboxStyle(){
    console.log("checkbox");
    $(".field-type-list-detail.checkbox-styling").removeClass("d-none")
};
function setDropdownStyle(){
    console.log("DropDown");
    $(".field-type-list-detail.dropdown-styling").removeClass("d-none")
};
function setMultitextStyle(){
    console.log("Multitext");
    $(".field-type-list-detail.multitext-styling").removeClass("d-none")
};
function setTextareaStyle(){
    console.log("Textarea");
    $(".field-type-list-detail.textarea-styling").removeClass("d-none")
};



/**************End Styling of the Fields.************/

$(function(){
    
    changeFormElementStyle();

    $('#frmDesignSetting select').on('change',function(){        
        changeFormElementStyle();
    });

    $(document).on('click','.fontdisplay', function(){

        // console.log($(this).find('input').prop('checked'));

        if($(this).find('input[type=checkbox]').prop('checked') == true){
            $(this).find('input[type=checkbox]').prop('checked',false);
            $(this).removeClass('boxchecked');
        }else{
            $(this).find('input[type=checkbox]').prop('checked',true);
            $(this).addClass('boxchecked');
        }
        changeFormElementStyle();
    });
});

function changeFormElementStyle(){       

    $('#frmDesignSetting select').each(function(){
        // console.log($(this).attr('data-name'));
        if($(this).attr('data-name'))
        {
            var elementName = $(this).attr('data-name');
            var fontStyle = $('#'+elementName+'_font_style').val();
            var fontSize = $('#'+elementName+'_font_size').val();
            var fontBoldValue = "normal";
            var fontItalicValue = "normal";
            var fontUnderlineValue = "";
            
            if($('#'+elementName+'_isBold').prop('checked') == true){
                fontBoldValue = "bold";
                $('#'+elementName+'_isBold').parent().addClass('boxchecked');
            }

            if($('#'+elementName+'_isItalic').prop('checked') == true){
                fontItalicValue = "italic";
                $('#'+elementName+'_isItalic').parent().addClass('boxchecked');
            }

            if($('#'+elementName+'_isUnderline').prop('checked') == true){
                fontUnderlineValue = "underline";
                $('#'+elementName+'_isUnderline').parent().addClass('boxchecked');
            }


            if(elementName == 'text')
            {
                // $(document).find('#putStyleHere').html("<style>::placeholder{font-family:" +  fontStyle + ";font-size:"+fontSize+";font-weight:"+fontBoldValue+";font-style:"+fontItalicValue+";text-decoration:"+fontUnderlineValue+"}</style>");
                $('#frmStyleChange').find('.form-show'+elementName).css({'font-size':fontSize, 'font-family':fontStyle, 'font-weight':fontBoldValue, 'font-style':fontItalicValue, 'text-decoration':fontUnderlineValue});
            }else{
                $('#frmStyleChange').find('.form-'+elementName).css({'font-size':fontSize, 'font-family':fontStyle, 'font-weight':fontBoldValue, 'font-style':fontItalicValue, 'text-decoration':fontUnderlineValue});
            }


        }
    });        
}


</script>
@endsection
