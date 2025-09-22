@extends('layouts.admin.app')
@section('title')
    Import Gifted Students
@endsection
@section('content')

    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Import Gifted Students</div>
            <div class=""><a class=" btn btn-secondary btn-sm" href="#">Go Back</a></div>
        </div>
    </div>
    <div class="tab-content bordered" id="myTabContent">
        <div class="content-wrapper-in" id="importmissinggrade">
            @include('layouts.admin.common.alerts')
            <div class="card shadow">
                <div class="card-body">
                    <div class="">Before uploading data (Gifted Students), please ensure that there is consistency with the naming of column fields in your "XLSX" file:<br></div>
                    <div class="pt-10">
                        <a href="{{url('/resources/assets/admin/ImportGiftedStudents.xlsx')}}" class="btn btn-secondary">Download Template</a>
                    </div>
                </div>
            </div>
            <form id="" method="post" action="{{url('admin/import/gifted_students/save')}}" enctype="multipart/form-data" novalidate="novalidate">
                {{csrf_field()}}   
                <div class="card shadow">
                    <div class="card-header">Upload</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 mb-15">
                                <input type="file" id="upload_csv" name="upload_csv" class="form-control font-12" value="" required="">
                                
                            </div>
                            <div class="col-lg-12 pt-5 mt-5">
                                <button class="btn btn-success btn-xs" id="file_submit" type="submit" name="save" value="save"><i class="fa fa-save ml-5 mr-5"></i>Upload</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@section('scripts')


@endsection