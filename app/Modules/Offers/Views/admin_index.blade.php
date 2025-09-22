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
                <div class="content-wrapper-in" style="min-height: 800px">
                        <iframe src="{{url('/Offers/'.$slug)}}" style="width: 100%; height: 500px;" frameborder="0" id="idIframe"></iframe>
                </div>
            </div>
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
        else
        {
             window.location = "{{url('admin/dashboard')}}";
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
        new_password: {
            required: true,
            minlength: 6,
            // pwcheck: true
        },
        confirm_password: {
            required: true,
            minlength: 6,
            equalTo: "#new_password"
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
        old_password:{
            required:"Old password is required.",
            remote:"Please enter correct old password.",
        },
        new_password: {
            required: "New Password is required.",
            minlength: "Enter a Password of length 6 or greater than 6."
        },
        confirm_password: {
            required: "Confirm Password is required.",
            minlength: "Enter a Password of length 6 or greater than 6.",
            equalTo: "Password and Confirm  password is not match."
        }
      
    },
    // Make sure the form is submitted to the destination defined
    // in the "action" attribute of the form when valid
    submitHandler: function (form) {
        // alert();
        form.submit();
    }
});
</script>

<!-- InstanceEndEditable -->
</body>
<!-- InstanceEnd -->
</html>