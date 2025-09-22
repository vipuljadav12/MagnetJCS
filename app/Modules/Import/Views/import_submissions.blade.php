@extends('layouts.admin.app')
@section('title')
    Import Submissions
@endsection
@section('content')

    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Import Submissions</div>
            {{-- <div class=""><a class=" btn btn-secondary btn-sm" href="#">Go Back</a></div> --}}
        </div>
    </div>
    <div class="tab-content bordered" id="myTabContent">
        <div class="content-wrapper-in" id="importagtnch">
            @include('layouts.admin.common.alerts')
            <div class="card shadow">
                <div class="card-body">
                    <div class="">Before uploading data, please ensure that there is consistency with the naming of column fields in your "XLS / XLSX" file:<br></div>
                     <div class="text-danger">Student's birthday must be in m/d/Y format.<br></div>
                    <div class="pt-10">
                        <a href="{{url('/admin/import/submissions/sample')}}" class="btn btn-secondary">Download Template</a>
                        {{-- <a href="{{url('/resources/assets/admin/ImportSubmissions.xlsx')}}" class="btn btn-secondary">Download Template</a> --}}
                    </div>
                </div>
            </div>
            <form method="post" action="{{url('admin/import/submissions/save')}}" enctype="multipart/form-data" novalidate="novalidate" id="submissions">
                {{csrf_field()}}   
                <div class="card shadow">
                    <div class="card-header">Upload</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 mb-15">
                                <input type="file" id="import_file" name="import_file" class="form-control font-12">
                            </div>
                            <div class="col-lg-12 pt-5 mt-5">
                                <button class="btn btn-success btn-xs" type="submit"><i class="fa fa-save ml-5 mr-5"></i>Upload</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(function() {
            $("#submissions").validate({
                rules: {
                    import_file: {
                        required: true,
                    },
                },
                messages: {
                    import_file:{
                        required: 'File is required.',
                    },
                },
                errorPlacement: function(error, element)
                {
                    error.appendTo( element.parent());
                    error.css('color','red');
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });
        });
        
    </script>
@endsection