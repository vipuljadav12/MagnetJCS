@extends('layouts.admin.app')
@section('title')Gifted Students | {{config('APP_NAME',env("APP_NAME"))}} @endsection
@section('styles')
    <!-- DataTables -->
    <link href="{{asset('resources/assets/admin/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('resources/assets/admin/plugins/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{asset('resources/assets/admin/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    {{-- <link rel="stylesheet" href="{{url('/resources/assets/admin/css/jquery-ui.css?rand()')}}"> --}}

    <style type="text/css">
        .error{
            color: red;
        }
    </style>
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Add Gifted Student</div>
            <div class="">
                <a href="{{url('admin/GiftedStudents')}}" class="btn btn-sm btn-secondary" title="">Back</a>
            </div>
        </div>
    </div>

    @include('layouts.admin.common.alerts')
    <form id="add-giftedstudent" method="post" action="{{url('admin/GiftedStudents/store')}}">
        {{csrf_field()}}
        <div class="card shadow">
            <div class="card-body">
                <div class="form-group">
                    <label class="control-label">Student State ID : </label>
                    <div class="">
                        <input type="text" onblur="checkStudentID(this)" class="form-control" name="stateID" placeholder="Student ID (10 Digit)" value="{{old('stateID')}}">
                        <span class="d-none">Checking Student ID <img src="{{url('/resources/assets/front/images/loader.gif')}}"></span>
                    </div>
                    @if($errors->first('stateID'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('stateID')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label">First Name : </label>
                    <div class=""><input type="text" class="form-control" name="first_name" id="first_name" value="{{old('first_name')}}"></div>
                    @if($errors->first('first_name'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('first_name')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label">Last Name : </label>
                    <div class=""><input type="text" class="form-control" name="last_name" id="last_name" value="{{old('last_name')}}"></div>
                    @if($errors->first('last_name'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('last_name')}}
                        </div>
                    @endif
                </div>
                <div class="form-group">
                    <label class="control-label">Admin : </label>
                    <div class=""><input type="text" class="form-control" name="admin" value="{{old('admin')}}"></div>
                    @if($errors->first('admin'))
                        <div class="mb-1 text-danger">
                            {{ $errors->first('admin')}}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                    <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save" title="Save"><i class="fa fa-save"></i> Save </button>
                    <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="{{url('/admin/GiftedStudents')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
<script type="text/javascript">
    function checkStudentID(obj)
    {
        var val = $.trim($(obj).val());
        if(val != "")
        { 
            $(obj).siblings("span").removeClass("d-none");
            $(obj).addClass("d-none");
            var url = "{{url('/check/sibling')}}/"+val;
            $.ajax({
                url:url,
                method:'get',
                success:function(response){
                    if($.trim(response) == "")
                    {
                        swal({
                            text: "Invalid Student ID",
                            type: "warning",
                            confirmButtonText: "OK",
                            confirmButtonColor: "#d62516"
                        });
                        //alert("");
                        $(obj).val("");
                        $(obj).focus();

                    }else{
                        var detail = response.split(' ');
                        $('#first_name').val(detail[0]);
                        $('#last_name').val(detail[1]);
                    }
                    $(obj).removeClass("d-none");
                    $(obj).siblings("span").addClass("d-none");
                }
            });
        }
    }
    $( function() {
        $("#add-giftedstudent").validate({
            rules:{
                stateID:{
                    required:true,
                }
            },
            messages:{
                stateID:{
                    required: 'Student ID is required',
                }
            }
        });
    });
</script>

@endsection