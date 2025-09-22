@extends('layouts.admin.app')
@section('title')
    Import AGT priority to New Century
@endsection
@section('content')

    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Import Student Data for Program</div>
            {{-- <div class=""><a class=" btn btn-secondary btn-sm" href="#">Go Back</a></div> --}}
        </div>
    </div>
    <div class="tab-content bordered" id="myTabContent">
        <div class="content-wrapper-in" id="importagtnch">
            @include('layouts.admin.common.alerts')
            <div class="card shadow">
                <div class="card-body">
                    <div class="">Before uploading data, please ensure that there is consistency with the naming of column fields in your "XLS / XLSX" file:<br></div>
                    <div class="pt-10">
                        <a href="{{url('/resources/assets/admin/ImportAGTNewCentury.xlsx')}}" class="btn btn-secondary">Download Template</a>
                    </div>
                </div>
            </div>
            <form method="post" action="{{url('admin/import/agt_nch/save')}}" enctype="multipart/form-data" novalidate="novalidate" id="agt_nch">
                {{csrf_field()}}   
                <div class="card shadow">
                    <div class="card-header">Upload</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="control-label"><strong>Select Program :</strong> </label>
                            <div class="">
                                <select class="form-control custom-sel2" name="program_name">
                                    <option value="">Choose an Option</option>
                                    @foreach($programs as $key=>$value)
                                        @if($value->name != '')
                                            <option value="{{$value->name}}">{{$value->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 mb-15">
                                <input type="file" id="upload_agt_nch" name="upload_agt_nch" class="form-control font-12">
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
            $("#agt_nch").validate({
                rules: {
                    program_name: {
                        required: true,
                    },
                    upload_agt_nch: {
                        required: true,
                    },
                },
                messages: {
                    program_name:{
                        required: 'Select program.',
                    },
                    upload_agt_nch:{
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