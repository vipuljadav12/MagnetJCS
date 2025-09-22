@extends("layouts.admin.app")
@section('title')
	Edit Users | {{config('APP_NAME',env("APP_NAME"))}}
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Edit User</div>
        <div class=""><a href="{{url('admin/Users/')}}" class="btn btn-sm btn-primary" title="Go Back">Go Back</a></div>
    </div>
</div>
@include('layouts.admin.common.alerts')

<form class="" id="UserForm" action="{{url('admin/Users/update/'.$user->id)}}" method="post">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-body">
            <div class="form-group">
                <label for="" class="control-label">First Name : </label>
                <div class="">
                    <input type="text" class="form-control" value="{{$user->first_name ?? old("first_name")}}" name="first_name">
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
                    <input type="text" class="form-control" value="{{ $user->last_name ?? old("last_name")}}" name="last_name">
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
                    <input type="email" class="form-control" value="{{ $user->email ?? old("email")}}" name="email" disabled="">
                </div>
                @if($errors->has("email"))
                    <div class="alert alert-danger m-t-5">
                       {{$errors->first('email')}}
                    </div>
                @endif
            </div>
            <div class="form-group">
                <label for="" class="control-label">User Type : </label>
                <div class="">
                    <select class="form-control custom-select" name="role_id">
                        <option value="">Select</option>
                        @forelse($roles as $r=>$role)
                            <option value="{{$role->id}}" @if($user->role_id == $role->id) selected @endif>{{($role->name)}}</option>
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
            <div class="form-group">
              <label class="">Change Password </label>
              <div class="">
                  <input type="checkbox" class="js-switch js-switch-1 js-switch-xs" id="changePassword" data-plugin="switchery" data-size="small"  data-color="#236cb9"/>
              </div>
            </div>

            <div class="form-group changePassword">
              <label for="" class="">Password <span class="required">*</span> </label>
              <div class="">
                <input type="password" class="form-control" name="password" id="id_password" value="{{old('password')}}" maxlength="20">
                @if ($errors->has('password'))
                <span class="help-block">
                  <strong>{{ $errors->first('password') ?? ''}}</strong>
                </span>
                @endif
              </div>
            </div>
            <div class="form-group changePassword">
                <label for="" class="">Confirm Password  <span class="required">*</span> </label>
                <div class="">
                  <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" value="{{old('password_confirmation')}}">
                  @if ($errors->has('password_confirmation'))
                  <span class="help-block">
                    <strong>{{ $errors->first('password_confirmation') ?? ''}}</strong>
                  </span>
                  @endif
                </div>
            </div>

        </div>
    </div>
    <div class="card shadow d-none">
        <div class="card-header">Program Access</div>
        <div class="card-body">
            <div class="row flex-wrap">
                <div class="col-12 col-sm-4 col-lg-2">
                    <div class=""><input type="checkbox" name="all_programs" id="all_programs" value="All" class="" onchange="checkAllProgram()">
                        <label for="table09" class="">All Programs</label></div>
                </div>
            </div>
            <div class="form-group" id="programlist">
                <label for="" class="control-label">Programs : </label>
                <div class="school-list">
                    @php 
                        $count = count($user->programs) ?? 0;
                    @endphp
                    @for($i=0;$i < $count;$i++)
                        <div class="mb-10 d-flex align-items-center" id="first-school">
                            <a href="javascript:void(0);" class="remove-school @if( $count == 1) d-none @endif mr-20" title=""><i class="fas fa-minus-circle"></i></a>
                            <a href="javascript:void(0);" class="add-school mr-20" title=""><i class="fas fa-plus-circle"></i></a>
                            <select class="form-control custom-select" name="programs[]">
                                <option value="">Select Programs</option>
                                @foreach($programs as $p=>$program)
                                    <option value="{{$program->id}}" @if($program->id == $user->programs[$i]) selected @endif>{{$program->name}}</option>

                                @endforeach
                            </select>
                        </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>
    <div class="box content-header-floating" id="listFoot">
        <div class="row">
            <div class="col-lg-12 text-right hidden-xs float-right">
                <button type="submit" class="btn btn-warning btn-xs"><i class="fa fa-save"></i> Save </button>
                   <button type="submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                   <a class="btn btn-danger btn-xs" href="{{url('/admin/Users')}}"><i class="fa fa-times"></i> Cancel</a>
            </div>
        </div>
    </div>
</form>
@endsection
@section('scripts')
    <script type="text/javascript">
         $(function(){
          $('.changePassword').css("display", "none");
      });

      $(document).on('change','#changePassword',function(){
          $('.changePassword').toggle();
      });

        var deletefunction = function(id){
            swal({
                title: "Are you sure you would like to move this User to trash?",
                text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/Users/trash/'+id;
            });
        };

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
                role_id:{
                    required:true,
                },
                password:{
                    required:true,
                    minlength:8
                },
                password_confirmation:{
                    minlength:8,
                    required: true,
                    equalTo : "#id_password",

                },

            },
            messages:{
                first_name:{
                    required: 'First Name is required.',
                    maxlength:'The first name may not be greater than 255 characters.'
                },
                last_name:{
                    required: 'Last Name is required.',
                    maxlength:'The last name may not be greaterr than 255 characters.'
                },
                role_id:{
                    required:'Please select User Type',
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