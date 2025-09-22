@extends("layouts.admin.app")
@section("title")
    Import Racial Composition Data | {{config('APP_NAME',env("APP_NAME"))}}
@endsection
@section('styles')
    <style type="text/css">
        .error{
            color: #e33d2d;
        }
    </style>
@stop

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Import Racial Composition Data</div>
        <div class=""><a href="{{url("/admin/Enrollment/edit/".$id)}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
    </div>
</div>
<form id="frm_import" method="post" action="{{url("/admin/Enrollment/import/store/".$id)}}" enctype="multipart/form-data">
{{  csrf_field() }}
    <div class="form-list">
        @include("layouts.admin.common.alerts")
    </div>
    
    <div class="card shadow">
        <div class="card-body">
            <div class="">Before uploading data, please ensure that there is consistency with the naming of column fields in your "XLSX" file:</div>
            <a href="{{url('admin/Enrollment/import/template/')}}" title="" class="btn btn-secondary mt-10">Download Template</a>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-header">Upload</div>
        <div class="card-body">
            <div class=""> 
                <input type="file" id="upload_csv" name="file" class="form-control h-auto" value="">
                @if($errors->first('file'))
                    <div class="mb-1 text-danger">
                        {{ $errors->first('file')}}
                    </div>
                @endif
            </div>
            <button type="submit" name="save_exit" value="save_exit" class="btn btn-success mt-10">Upload</button>
        </div>
    </div>
</form>
@stop

@section('scripts')

    <script type="text/javascript">
        const file_limit = 10; //in mb
        /*-- form validation start --*/    
        $.validator.addMethod("file_validation", function(value, element) {
            return fileValidation(element);
        }, "Please upload a file less than "+file_limit+" MB.");

        $('#frm_import').validate({
            rules:{
                file: {
                    required: true,
                    extension: 'xls|xlsx',
                    file_validation: true
                }
            },messages:{
                file:{
                    required:"File is required.",
                    extension:'The file must be a file of type: xls|xlsx.'
                }         
            }
        });
        function fileValidation(element) {
            const fi = element;
            if (fi.files.length > 0) {
                const max_limit = (file_limit * 1024); // in Bytes
                const fsize = fi.files.item(0).size;
                const file = Math.round((fsize / 1024));
                if (file > max_limit) {
                    return false;
                }
            }
            return true;
        }
        /*-- form validation end --*/
    </script>
@stop

