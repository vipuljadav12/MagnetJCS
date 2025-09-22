@extends("layouts.admin.app")
@section('title')
Edit Text | {{config('APP_NAME',env("APP_NAME"))}}
@endsection 
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Edit Text</div>
        <div class="">
            <a href="{{url('admin/AlertMsg')}}" class="btn btn-sm btn-secondary" title="Back">Back</a>

        </div>
    </div>
</div>
@include("layouts.admin.common.alerts")
<form action="{{url('admin/AlertMsg/update',$msg->id)}}" method="post" id="editTranslation">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-body">
            <div class="form-group">
                <label for="">Title : </label>
                <div class="">
                    <label>{{$msg->msg_title}}
                    
                </div>
                   
            </div>
 
            <div class="form-group">
                <label for="">Text : </label>
                <div class="">
                    <textarea class="form-control" name="msg_txt" id="msg_txt">{{ $msg->msg_txt ?? ''}}</textarea>
                </div>
                @if ($errors->any())
                <div class="text-danger">
                  <strong>{{ $errors->first('msg_txt') }}</strong>
                </div>
                @endif 
            </div>
            <div class="box content-header-floating" id="listFoot">
                <div class="row">
                    <div class="col-lg-12 text-right hidden-xs float-right">
                        <button type="submit" class="btn btn-warning btn-xs submitBtn" name="save"  value="save">
                            <i class="fa fa-save"></i> Save
                        </button>
                        
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
   $("#editTranslation").validate({
    ignore: [],
    rules: {
      msg_txt:{
      required: true,
    }, 
    messages:{
    config_name:{
      required:"Description is required.",
    },
    msg_txt:{
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