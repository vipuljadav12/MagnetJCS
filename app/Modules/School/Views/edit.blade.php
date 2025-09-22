@extends("layouts.admin.app")
@section('title')
Edit School | {{config('APP_NAME',env("APP_NAME"))}}
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Edit School Master</div>
        <div class="">
            <a href="{{url('admin/School')}}" class="btn btn-sm btn-secondary" title="Go Back">Go Back</a>

        </div>
    </div>
</div>
<form action="{{url('admin/School/update',$school->id)}}" method="post" id="editschool">
    {{csrf_field()}}
    <input type="hidden" name="id" id="id" value="{{$school->id}}">
    <div class="card shadow">
        <div class="card-body">
            <div class="form-group">
                <label for="">School Name : </label>
                <div class="">
                    <input type="text" class="form-control name" id="name" name="name" value="{{$school->name}}">
                </div>
                @if ($errors->any())
                <div class="text-danger">
                    <ul>
                        <li>{{ $errors->first('name') }}</li>
                    </ul>
                </div>
                @endif    
            </div>
            <div class="form-group">
                <label for="">Grade Levels : </label>
                <div class="row flex-wrap grade_id">
                    @foreach($grades as $grade)
                    @if(in_array($grade->name , $list))
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="">
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" value="{{$grade->name}}" name="grade[]" id="{{$grade->id}}" checked>
                            <label for="{{$grade->id}}" class="custom-control-label">{{$grade->name}}</label>
                        </div>
                        </div>
                    </div>
                    @else
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="">
                            <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" value="{{$grade->name}}" name="grade[]" id="{{$grade->id}}">
                            <label for="{{$grade->id}}" class="custom-control-label">{{$grade->name}}</label>
                        </div>
                        </div>
                    </div>
                    @endif

                    @endforeach
                    @if ($errors->any())
                    <div class="text-danger">
                        <ul>
                            <li>{{ $errors->first('grade') }}</li>
                        </ul>
                    </div>
                    @endif    
                </div>
            </div>
            <div class="form-group">
                <label for="">Magnet : </label>
                <div class="">
                    <select class="form-control custom-select" name="magnet">
                       <option value="">Select</option>
                       <option value="Yes" {{$school->magnet=='Yes'?'selected':''}} >Yes</option>
                       <option value="No" {{$school->magnet== 'No'?'selected':''}}>No</option>
                   </select>
               </div>
           </div>
           <div class="form-group">
            <label for="">Zoning API Name if different : </label>
            <div class=""><input type="text" class="form-control" name="zoning_api_name" value="{{$school->zoning_api_name}}">
            </div>
            @if ($errors->any())
            <div class="text-danger">
                <ul>
                    <li>{{ $errors->first('zoning_api_name') }}</li>
                </ul>
            </div>
            @endif    
        </div>
        <div class="form-group">
            <label for="">SIS Name if different : </label>
            <div class=""><input type="text" class="form-control" name="sis_name" value="{{$school->sis_name}}">
            </div>
        </div>
        @if ($errors->any())
        <div class="text-danger">
            <ul>
                <li>{{ $errors->first('sis_name') }}</li>
            </ul>
        </div>
        @endif    
    </div>
</div>
<div class="box content-header-floating" id="listFoot">
    <div class="row">
        <div class="col-lg-12 text-right hidden-xs float-right">
            <div>
                {{-- <button class="btn btn-warning btn-xs" type="submit"><i class="fa fa-save"></i> Save </button> --}}
                {{-- <a class="btn btn-success btn-xs" href="school-master.html"><i class="fa fa-save"></i> Save &amp; Exit</a>--}}
                {{-- <a class="btn btn-primary btn-xs" href="javascript:void(0);" onclick="deletefunction({{$school->id}})"><i class="far fa-trash-alt"></i> Delete</a> --}}
                <button type="submit" class="btn btn-warning btn-xs" name="submit" value="Save" title="Save"><i class="fa fa-save"></i> Save </button>
               <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                <a class="btn btn-danger btn-xs" href="{{url('/admin/School')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
            </div>
        </div>
    </div>
</div>
</form>    
@endsection
@section('scripts')
{{-- <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
<script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script> --}}
<script type="text/javascript">
    $(document).ready(function(){

$('#editschool').validate({ // initialize the plugin
    ignore:[],

    rules: {
        name: 
        {
            required: true,
            maxlength:255,
            /*remote:{
                url:"{{url('admin/School/checkunique')}}",
                type:"get",
                data:{
                    id:$('#id').val(),
                }
            }*/
        },
        'grade[]':
        {
            required:true,
        },
        magnet:
        {
            required:true,
        },
       /* zoning_api_name:
        {
            required:true,
            maxlength:255
        },
        sis_name:
        {
            required:true,
            maxlength:255
        },*/
    },
    messages:
    {
        name:
        {
            required: 'The Name field is required.',
            maxlength:'The Name may not be greater than 100 characters.',
            remote:'The Name has already been taken.',
        },
        'grade[]':
        {
            required:"The Grade field is required."
        },
        magnet:
        {
            required:"The Magnet field is required"
        },
        zoning_api_name:
        {
            required:'The Zoning API name field is required.',
            maxlength:'The Zoning API name name may not be greater than 255 characters.'
        },
        sis_name:
        {
           required:'The SIS name field is required.',
           maxlength:'The SIS name name may not be greater than 255 characters.'
       }
   },
   errorPlacement: function(error, element) 
   {
    if ( element.is(":checkbox") ) 
    {
        error.appendTo( element.parents('.form-group'));
    }
    else 
    { 
        error.insertAfter( element );
    }
    error.css('color','red');
},
submitHandler: function(form) {
    form.submit();
}
});
});

    var deletefunction = function(id){
        swal({
            title: "Are you sure you would like to move this School to trash?",
            text: "",
                // type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes",
                closeOnConfirm: false
            }).then(function() {
                window.location.href = '{{url('/')}}/admin/School/delete/'+id;
            });
        };    
    </script> 
    @endsection