@extends("layouts.admin.app")
@section('title')
Add Text | {{config('APP_NAME',env("APP_NAME"))}}
@endsection 
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Add Text</div>
        <div class="">
            <a href="{{url('admin/Configuration')}}" class="btn btn-sm btn-secondary" title="Back">Back</a>

        </div>
    </div>
</div>
@include("layouts.admin.common.alerts")
<form action="{{url('admin/Configuration/store')}}" method="post" id="addTranslation">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-body">
            <div class="form-group">
                <label for="">Short Code : </label>
                <div class="">
                    {{-- <input type="text" id="config_name" class="form-control" value="{{old("config_name")}}" name="config_name"> --}}
                    {{-- <input type="text" id="config_name" class="form-control" value="{{old("config_name")}}" name="config_name"> --}}
                    <select id="config_name" class="form-control"  name="config_name">
                      @foreach(getConfigNameArray() as $k=>$config)
                        <option value="{{$k}}">{{$config}}</option>
                      @endforeach
                    </select>
                </div>
                @if ($errors->any())
                <div class="text-danger">
                    <strong>{{ $errors->first('config_name') }}</strong>
                </div>
                @endif    
            </div>
 
            <div class="form-group">
                <label for="">Text Description : </label>
                <div class="">
                    <textarea class="form-control" name="config_value" id="config_value">{{old('config_value') ?? ''}}</textarea>
                </div>
                @if ($errors->any())
                 <div class="text-danger">
                    <strong>{{ $errors->first('config_value') }}</strong>
                </div>
                @endif 
            </div>
            <div class="box content-header-floating" id="listFoot">
                <div class="row">
                    <div class="col-lg-12 text-right hidden-xs float-right">
                        <button type="submit" class="btn btn-warning btn-xs submitBtn" name="save" value="save">
                            <i class="fa fa-save"></i> Save
                        </button>
                        <button type="submit" class="btn btn-success btn-xs" name="save_exit" value="save_exit">
                            <i class="fa fa-save"></i> Save &amp; Exit</button> 
                            <a class="btn btn-danger btn-xs" href="{{url('/admin/Configuration')}}"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection
@section('scripts')
<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/plugins/laravel-ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="{{url('/resources/assets/admin/plugins/laravel-ckeditor/adapters/jquery.js')}}"></script>
<script type="text/javascript" src="{{url('/')}}/resources/assets/admin/js/additional-methods.min.js"></script>
<script type="text/javascript">
 CKEDITOR.replace('config_value');
 $.validator.addMethod(
    "regex",
    function(value, element, regexp) {
      return this.optional(element) || regexp.test(value);
    },      
    "Invalid number."
  );
   $("#addTranslation").validate({
    ignore: [],
    rules: {
      config_name:{
        required: true
      },
      config_value:{
      required: function(textarea) {
       CKEDITOR.instances[textarea.id].updateElement();
       var editorcontent = textarea.value.replace(/<[^>]*>/gi, '');
       return editorcontent.length === 0;
       }
      },
    }, 
    messages:{
    config_name:{
      required:"Description is required.",
    },
    config_value:{
      required:"Text is required.",
    },
  },
  errorPlacement: function(error, element)
    {
    error.appendTo( element.parents('.form-group'));
    error.css('color','red');
    }, 
  submitHandler: function(form){
   form.submit();
 }
});  

</script> 
@endsection