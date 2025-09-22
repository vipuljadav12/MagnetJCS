@extends('layouts.admin.app')
@section('title')
Edit Permission
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Edit Permission</div>
        <div class="">
            <a href="{{url('admin/Permission')}}" class="btn btn-success btn-sm" title="Back">Go Back</a>
        </div>
    </div>
</div>
@include('layouts.admin.common.alerts')
<form id="permissionSubmitForm" method="POST" action="{{url('/admin/Permission/update/'.$data['permission']->id)}}">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-body">
             <div class="form-group">
                <label for="" class="">Slug Name *  </label>
                <div class="">
                    <input type="text" class="form-control" name="slug"  value="{{$data['permission']->slug ?? ''}}">
                    @if ($errors->has('slug'))
                    <div class="error col-sm-4 col-lg-8">{{ $errors->first('slug') }}</div>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="" class="">Display Name * </label>
                <div class="">
                    <input type="text" class="form-control" name="display_name"  value="{{$data['permission']->display_name ?? ''}}">
                    @if ($errors->has('display_name'))
                    <div class="error col-sm-4 col-lg-8">{{ $errors->first('display_name') }}</div>
                    @endif
                </div>
            </div>
            <div class="form-group">
                <label for="" class="">Module * </label>
                <div class="">
                    <select name="module_id" class="form-control custom-select">
                        <option value="">Select Module</option>
                        @foreach($data['module'] as $key => $module)
                            <option value="{{ $module->id }}" {{($module->id == $data['permission']->module_id) ? 'selected' : ''}}>{{ $module->name }}</option>
                        @endforeach
                    </select>
                    @if ($errors->has('module_id'))
                    <div class="error col-sm-4 col-lg-8">{{ $errors->first('module_id') }}</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
<div class="box content-header-floating" id="listFoot">
<div class="row">
        <div class="col-lg-12 text-right hidden-xs float-right">
            <button class="btn btn-warning btn-xs" type="submit" name="save" value="save"><i class="fa fa-save mr-2"></i>Save </button>
            <button class="btn btn-success btn-xs" type="submit" name="save_exit" value="save_exit"><i class="fa fa-save mr-2"></i>Save &amp; Exit</button>
                
            <a class="btn btn-danger btn-xs" href="{{url('admin/Permission')}}">
                <i class="far fa-trash-alt"></i> Cancel
            </a> 
        </div>
</div>
</div>
</form>
<!-- InstanceEndEditable --> 
@endsection
@section('script')
<script type="text/javascript">
    $("#permissionSubmitForm").validate({
        
        // Specify validation rules for required
        rules: {
            slug: {required: true},
            display_name:{required:true},
            module_name:{required:true},         
        },
        // Specify validation error messages
        messages: {
            slug: {
                required: "Slug is required.",
            },
            display_name: {
                required: "Display Name is required.",
            },
            module_name: {
                required: "Module Name is required.",
            },
         
        },
        // Make sure the form is submitted to the destination defined
        submitHandler: function (form) {
            form.submit();
        }
    });
</script>
@endsection