@extends('layouts.admin.app')
@section('title') Create User Role @endsection
@section("styles")
<style type="text/css">
    .error
    {
        color:#721c24;
    }
</style>
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Add User Role</div>
        <div class=""><a href="{{url('admin/Role')}}" class="btn btn-success btn-sm" title="Back">Go Back</a></div>
    </div>
</div>
@include('layouts.admin.common.alerts')
<form method="POST" action="{{url('/admin/Role/store')}}" id="roleSubmitForm">
 {{csrf_field()}}
    <div class="card shadow">
        <div class="card-body">
            <div class="form-group">
                <label class="control-label">User Role : </label>
                <div class="">
                   <input type="text" name="name" id="role" class="form-control" value="">
                   @if ($errors->has('name'))
                    <div class="error col-sm-4 col-lg-8">{{ $errors->first('name') }}</div>
                    @endif
                </div>
            </div>
            @if(!empty($data['permission']))
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($data['display_name'] as $key=>$permission)
                            @if(isset($permission))
                            <tr>
                                <td class="b-600">{{$key}}</td>
                                @foreach($permission as $key => $permission)
                                <td class="text-center">
                                    <label class="" style="height:auto !important;">{{\App\Modules\Module\Models\Module::Show($permission)->display_name ?? \App\Modules\Module\Models\Module::Show($permission)->name}}</label>
                                    <div>
                                    <input type="checkbox" name="permission[{{$key}}]" class="js-switch js-switch-1 js-switch-xs @if(strpos(\App\Modules\Module\Models\Module::Show($permission)->name, 'Dashboard') !== false) roles-permission @endif" data-size="Small" value="Y">
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
<div class="box content-header-floating" id="listFoot">
<div class="row">
        <div class="col-lg-12 text-right hidden-xs float-right">
            <button class="btn btn-warning btn-xs" type="submit" name="save" value="save"><i class="fa fa-save mr-2"></i>Save </button>
            <button class="btn btn-success btn-xs" type="submit" name="save_exit" value="save_exit"><i class="fa fa-save mr-2"></i>Save &amp; Exit</button>
                
            <a class="btn btn-danger btn-xs" href="{{url('admin/Role')}}">
                <i class="far fa-trash-alt"></i> Cancel
            </a> 
        </div>
</div>
</div>
</form>
@endsection
@section('scripts')
<script type="text/javascript">
    $("#roleSubmitForm").validate({
        
       
        rules: {
            role: {required: true},
        },
        messages: {
            role: {
                required: "User Role is required.",
            },
         
        },
        submitHandler: function (form) {
            form.submit();
        }
    });

    $(document).on('change','.chngepermission',function(){
        var data = this.value;
        if(this.checked == true){
        $("#id_"+data).val(data);             
        }else{
        $("#id_"+data).val(""); 
        }
    });

    $('.roles-permission').click(function() {
        console.log("hi");
        $(this).siblings('input:checkbox').prop('checked', false);
    });
</script>
@endsection