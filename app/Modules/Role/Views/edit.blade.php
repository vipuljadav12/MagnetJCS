@extends('layouts.admin.app')
@section('title') Edit User Role @endsection
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
        <div class="page-title mt-5 mb-5">Edit User Role</div>
        <div class=""><a href="{{url('admin/Role')}}" class="btn btn-success btn-sm" title="Back"> Go Back</a></div>
    </div>
</div>
@include('layouts.admin.common.alerts')
<form method="POST" action="{{url('/admin/Role/update')}}" id="roleSubmitForm">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-body">
            <div class="form-group">
                <label class="control-label">User Role *</label>
                <div class="">
                    <input type="hidden" name="id" value="{{ $data['role']->id }}">
                    <input type="text" name="name" id="role" class="form-control" value="{{$data['role']->name}}">
                    @if ($errors->has('name'))
                    <div class="error col-sm-4 col-lg-8">{{ $errors->first('name') }}</div>
                    @endif
                </div>
            </div>
            @if(isset($data['permission']))
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- <tr>
                            <td class="b-600">
                                <input name="" type="checkbox" class="checkAll" data-size="Small"  value="Y" id="Dashboard">
                                <label for="Dashboard">  Dashboard </label>
                            </td>
                            <td class="text-center">
                                <label class="" style="height: auto;">All</label>
                                <div>
                                    <input type="checkbox" name="permission[]" class="js-switch js-switch-1 js-switch-xs checkboxSingle" data-size="Small" value="Y" {{(isset($data["permission_id"]) && in_array($key, $data["permission_id"])) ? 'checked' : ''}}>
                                </div>
                            </td>
                        </tr> --}}
                        @foreach($data['display_name'] as $key=>$permission)
                            @if(isset($permission))
                            <tr>
                                <td class="b-600">
                                    <input name="" type="checkbox" class="checkAll" data-size="Small"  value="Y" id="{{"all".$key}}">
                                    <label for="{{"all".$key}}">{{" ".ucfirst($key)}} </label>
                                </td>
                                @foreach($permission as $key => $permission)
                                <td class="text-center">
                                    <label class="" style="height: auto;">{{\App\Modules\Module\Models\Module::Show($permission)->display_name ?? \App\Modules\Module\Models\Module::Show($permission)->name}}</label>
                                    <div>
                                        <input type="checkbox" name="permission[{{$key}}]" class="js-switch js-switch-1 js-switch-xs checkboxSingle" data-size="Small" value="Y" {{(isset($data["permission_id"]) && in_array($key, $data["permission_id"])) ? 'checked' : ''}}>
                                    </div>
                                </td>
                                @endforeach
                            </tr>
                            @endif
                        @endforeach
                        {{-- @foreach($data['display_name'] as $key=>$row)
                            @if(isset($row))
                            <tr class="checkboxRow">
                                <td class="b-600">
                                    <input name="" type="checkbox" class="checkAll" data-size="Small"  value="Y" id="{{"all".$key}}">
                                    <label for="{{"all".$key}}">{{" ".ucfirst($key)}} </label>
                                </td>
                                @foreach($data['modules'] as $modkey => $module)
                                <td class="text-center">
                                    @if(in_array($module, $row))
                                        <label>{{$module}}</label>
                                        <input name="permission[{{array_search($module, $row)}}]" type="checkbox" class="js-switch js-switch-1 js-switch-xs checkboxSingle" data-size="small"  value="Y" {{(isset($data["permission_id"]) && in_array(array_search($module, $row), $data["permission_id"])) ? 'checked' : ''}}>
                                    @endif
                                </td>
                                @endforeach
                            </tr>
                            @endif
                        @endforeach --}}
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
$(function()
{
    adjCheckbox();
});
$(document).on("change",".checkboxSingle",function()
{
    adjCheckbox();
});
function adjCheckbox()
{
    $(document).find(".checkboxRow").each(function(){
        var numberofcheckbox = $(this).find(".checkboxSingle").length;
        var checkedLength = $(this).find(".checkboxSingle:checked").length;
        // console.log(numberofcheckbox+" -- "+checkedLength);
        var switchry = new Switchery( $(this).find(".checkAll"), { size: 'small' });
        if(numberofcheckbox == checkedLength)
        {
            // $(this).find(".checkAll").prop("checked",true).change("no");
            $(this).find(".checkAll").prop("checked",true);
            // switchry.handleOnchange(true);   
        }
        else{
            $(this).find(".checkAll").prop("checked",false);
            // switchry.handleOnchange(false);  
        }
    });
}
    $(document).on("change",".checkAll",function(){

        let checked = $(this).prop("checked");
        // console.log( $(this).parent().parent().find(".checkboxSingle"));
        $(this).parent().parent().find(".checkboxSingle").prop("checked",(checked = !checked)).trigger("click");
        // $(this).parent().parent().find(".checkboxSingle").trigger("click");
    });
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
    
</script>
@endsection