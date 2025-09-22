<!doctype html>
<html lang="en"><!-- InstanceBegin template="/Templates/grandmaster-master.dwt" codeOutsideHTMLIsLocked="false" -->
<head>
    @include("layouts.admin.common.head")
    @yield("styles")
</head>
<body data-bg="theme01" class="dark-theme">
<div class="body-wrapper theme-customizer-page">
    <main datanavbar="sticky">
        <div class="main-wrapper">
            @include("layouts.admin.common.sidebar")
            @include("layouts.admin.common.header")
            <div class="content-wrapper">
                <div class="content-wrapper-in">
                <!-- InstanceBeginEditable name="Content-Part" -->
                    @yield("content")
                <!-- InstanceEndEditable --> 
                </div>
            </div>
            @include("layouts.admin.common.footer")
        </div>
    </main>
</div>
<!-- InstanceBeginEditable name="Footer Extra Slot" --> <!-- InstanceEndEditable --> 
@include("layouts.admin.common.js")
@yield("scripts")
<script type="text/javascript">

     $('#switch_dashboard').change(function() {
        if ($(this).val() == "magnet") {
            window.location = "{{url('admin/magnet_dashboard')}}";
        }
        else if($(this).val()=="mcpss")
        {
             window.location = "{{url('admin/dashboard')}}";
        }
        else if($(this).val()=="superoffer")
        {
             window.location = "{{url('admin/superadmin/offer')}}";
        }
        else if($(this).val()=="districtoffer")
        {
             window.location = "{{url('admin/districtadmin/offer')}}";
        }
    });

    function changeDistrict(district_id)
    {
        var url  = '{{ url("/admin/changedistrict/")}}/'+district_id;
        $.ajax({
            url:url,
            method:'get',
            success:function(response){
                location.reload();
            }
        });
    }

    function changeEnrollment(enrollment_id)
    {
        var url  = '{{ url("/admin/changeenrollment/")}}/'+enrollment_id;
        $.ajax({
            url:url,
            method:'get',
            success:function(response){
                location.reload();
            }
        });
    }

    $(document).on('click','#changePassword',function(){
        // $('#changePassFields').toggle();
        // alert();
        $(document).find(".change_pass").toggleClass("d-none");
    });

$("#update_profile").validate({
    // Specify validation rules
    rules: {
        first_name: {
            required: true,                   
        },
        last_name: {
            required: true,                   
        },
        username: {
            required: true,                   
        },
        old_password: {
            required: true,
            remote: {
                url: '{{url("/")}}/admin/Users/checkoldpass',                            
                data: {'current': function () {
                        return $('input[id="u_email"]').val()
                    }},
                asynce: false
            },
        },
        password: {
            required: true,
            minlength: 6,
            // pwcheck: true
        },
        // confirm_password: {
        password_confirmation: {
            required: true,
            minlength: 6,
            equalTo: "#password"
        }
       
    },

    // Specify validation error messages
    messages: {
        first_name: {
            required: "First name is required.",
        },
        last_name: {
            required: "Last name is required.",
        },
        username: {
            required: "User Name is required.",
        },
        old_password:{
            required:"Old password is required.",
            remote:"Please enter correct old password.",
        },
        password: {
            required: "New Password is required.",
            minlength: "Enter a Password of length 6 or greater than 6."
        },
        password_confirmation: {
            required: "Confirm Password is required.",
            minlength: "Enter a Password of length 6 or greater than 6.",
            equalTo: "Password and Confirm  password is not match."
        },
        profile: {
            accept: "Please insert image file only"
        }
    },
    errorPlacement: function(error, element)
    {
        error.appendTo( element.parent());
        error.css('color','red');
    },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function (form) {
        // alert();
        form.submit();
    }
});

$(document).on("click",".side-menu-btn",function(){
    $(this).find("i").toggleClass("fa-times");
    $(".side-menu , .content-wrapper , footer").toggleClass("active");
});

function showMissingReport()
{
    if($("#enrollment_option").val() == "")
    {
        alert("Please select enrollment year");
    }
    else if($("#reporttype").val() == "")
    {
        alert("Please select report type");
    }
    else
    {
        var link = "{{url('/')}}/admin/Reports/missing/"+$("#enrollment_option").val()+"/"+$("#reporttype").val();
        document.location.href = link;
    }
}
</script>

<style>
     .side-menu .slimscroll-menu {height: calc(100vh - 70px); position: relative;}
    .side-menu .mCSB_container {margin-right: 0 !important;}
    .side-menu .mCSB_dragger_bar {width: 13px !important; background-color: rgb(158, 165, 171) !important;}
    .side-menu .mCSB_draggerRail {background-color: transparent !important;}
    .mCSB_scrollTools {margin: 0!important;}
    .side-menu-btn {width: 30px; position: absolute; left: 240px;}
    .logo-box {height: 69px !important;}
    .side-menu {left: 0px !important; transition: all 0.3s !important; box-shadow: none !important; z-index: 1001 !important; background-color: transparent !important;}
    .side-menu .slimscroll-menu {box-shadow: 4px 0 5px -2px #dedede; left: 0px !important; transition: all 0.3s !important; background: #fff !important;}
    .side-menu.active .slimscroll-menu {left: -260px !important; transition: all 0.3s !important;}
    .content-wrapper {margin-left: 260px !important; transition: all 0.3s !important; position: relative;}
    .content-wrapper.active {margin-left: 0px !important; transition: all 0.3s !important;}
    footer {margin-left: 260px !important; transition: all 0.3s !important; position: relative;}
    footer.active {margin-left: 0px !important; transition: all 0.3s !important;}
</style>

<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd -->
</html>