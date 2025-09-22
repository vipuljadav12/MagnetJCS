@extends("layouts.admin.app")
@section('title')
	Add Users | {{config('app.name',env("APP_NAME"))}}
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Add User</div>
        <div class=""><a href="{{url('admin/Users/')}}" class="btn btn-sm btn-primary" title=""><i class="fa fa-arrow-left"></i> Back</a></div>
    </div>
</div>
<form class="" id="UserForm" action="{{url('admin/Users/store')}}" method="post" >
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-body">
            {{-- @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif --}}
            <div class="form-group">
                <label for="" class="control-label">First Name : </label>
                <div class="">
                    <input type="text" class="form-control" value="{{old("first_name")}}" name="first_name">
                </div>
                @if($errors->has("first_name"))
                    <div class="alert alert-danger m-t-5">
                       {{$errors->first('first_name')}}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label for="" class="control-label">Last Name : </label>
                <div class="">
                    <input type="text" class="form-control" value="{{old("last_name")}}" name="last_name">
                </div>
                @if($errors->has("last_name"))
                    <div class="alert alert-danger m-t-5">
                       {{$errors->first('last_name')}}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label for="" class="control-label">Email : </label>
                <div class="">
                    <input type="email" class="form-control" value="{{old("email")}}" name="email" id="email">
                </div>
                @if($errors->has("email"))
                    <div class="alert alert-danger m-t-5">
                       {{$errors->first('email')}}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label for="" class="control-label">Confirm Email : </label>
                <div class="">
                    <input type="email" class="form-control" value="{{old("email_confirmation")}}" name="email_confirmation">
                </div>
                {{-- @if($errors->has("confirm_email"))
                    <div class="alert alert-danger m-t-5">
                       {{$errors->first('confirm_email')}}
                    </div>
                @endif --}}
            </div>
            <div class="form-group">
                <label for="" class="control-label">Plain Password : </label>
                <div class="">
                    <input type="text" class="form-control" value="{{old("password")}}" name="password" >
                </div>
                <div class="small">To update a user's password, provide one here</div>
                @if($errors->has("password"))
                    <div class="alert alert-danger m-t-5">
                       {{$errors->first('password')}}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label for="" class="control-label">User Type : </label>
                <div class="">
                    <select class="form-control custom-select" name="role_id">
                        <option value="">Select</option>
                        @forelse($roles as $r=>$role)
                            <option value="{{$role->id}}">{{($role->name)}}</option>
                        @empty
                        @endforelse
                    </select>
                </div>
                @if($errors->has("role_id"))
                    <div class="alert alert-danger m-t-5">
                       {{$errors->first('role_id')}}
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="card shadow d-none">
        <div class="card-header">Program Access
        </div>
        <div class="card-body">
             <div class="row flex-wrap">
            <div class="col-12 col-sm-4 col-lg-2">
                <div class=""><input type="checkbox" name="all_programs" id="all_programs" value="All" class="" onchange="checkAllProgram()">
                    <label for="table09" class="">All Programs</label></div>
            </div>
        </div>
        


            <div class="form-group d-none" id="programlist">
                <label for="" class="control-label">Programs : </label>
                <div class="school-list">
                    <div class="mb-10 d-flex align-items-center" id="first-school">
                        <a href="javascript:void(0);" class="remove-school d-none mr-20" title=""><i class="fas fa-minus-circle"></i></a>
                        <a href="javascript:void(0);" class="add-school mr-20" title=""><i class="fas fa-plus-circle"></i></a>
                        <select class="form-control custom-select" name="programs[]">
                            <option value="">Select Programs</option>
                            @foreach($programs as $p=>$program)
                                <option value="{{$program->id}}">{{$program->name}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="box content-header-floating" id="listFoot">
        <div class="row">
            <div class="col-lg-12 text-right hidden-xs float-right">
                <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save"><i class="fa fa-save"></i> Save </button>
                   <button type="submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                   <a class="btn btn-danger btn-xs" href="{{url('/admin/Users')}}"><i class="fa fa-times"></i> Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
@section('scripts')
<script type="text/javascript">
    jQuery.validator.addMethod("emailfull", function(value, element) {
             return this.optional(element) || /^([a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+(\.[a-z\d!#$%&'*+\-\/=?^_`{|}~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]+)*|"((([ \t]*\r\n)?[ \t]+)?([\x01-\x08\x0b\x0c\x0e-\x1f\x7f\x21\x23-\x5b\x5d-\x7e\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|\\[\x01-\x09\x0b\x0c\x0d-\x7f\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))*(([ \t]*\r\n)?[ \t]+)?")@(([a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\d\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.)+([a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]|[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF][a-z\d\-._~\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]*[a-z\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])\.?$/i.test(value);
            }, "Please enter valid email address!");
    $("#UserForm").validate({
        rules:{
            first_name:{
                required:true,
                maxlength:100,
            },
            last_name:{
                required:true,
                maxlength:100,
            },
            email: {
                required: true,
                emailfull: true
            },
            email_confirmation:{
                required:true,
                equalTo:"#email",
                emailfull: true
            },
            password:{
                required:true,
                minlength: 8,
                maxlength:255,
            },
            role_id:{
                required:true,
            }

        },
        messages:{
            first_name:{
                required: 'First Name is required.',
                maxlength:'The first name may not be greater than 255 characters.'
            },
            last_name:{
                required: 'Last Name is required.',
                maxlength:'The last name may not be greater than 255 characters.'
            },
            email:{
                required: 'Email is required.',
                remote:'The email has already been taken.',
                maxlength:'The Email may not be greater than 255 characters.',
            },
            email_confirmation:{
                required:'Email Confirmation is required.',
                equalTo:"Email Confirmation is not match.",
            },
            password:{
                required:'Password is required.',
                minlength: "The password must be at least 8 characters long",
                maxlength:'The password may not be greater than 255 characters.',
            },
            role_id:{
                required:'Please select User Type.',
            }
        },errorPlacement: function(error, element)
        {
            error.appendTo( element.parents('.form-group'));
            error.css('color','red');
        }
    });
    $(document).on("click",".add-school",function()
    {
        var obj =  $(this).parent().clone();
        $(this).parent().after(obj);
        showHideBtn();
    });
    $(document).on("click",".remove-school",function()
    {
        $(this).parent().remove();
        showHideBtn();
    });
    function showHideBtn()
    {
        var count = $(".add-school").length;
        if(count > 1)
        {
            $(document).find(".remove-school").removeClass("d-none");
        }
        else
        {
            $(document).find(".remove-school").addClass("d-none");
        }
        // alert(count);
    }

    function checkAllProgram()
    {
        if($("#all_programs").is(":checked")) 
        {
            $("#programlist").addClass("d-none");
        }
        else {
            $("#programlist").removeClass("d-none");
        }
    }
</script>
@endsection