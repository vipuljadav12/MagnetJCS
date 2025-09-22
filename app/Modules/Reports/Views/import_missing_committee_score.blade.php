@extends('layouts.admin.app')
@section('title')
    Import Missing Committee Score | {{config('APP_NAME',env("APP_NAME"))}}
@endsection

@section('styles')
<style type="text/css">
    .error { color: red; }
    .alert1 {
        position: relative;
        padding: 0.75rem 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid transparent;
            border-top-color: transparent;
            border-right-color: transparent;
            border-bottom-color: transparent;
            border-left-color: transparent;
        border-radius: 0.25rem;
    }
    .alert-error {
        color: #721c24;
        background-color: #f8d7da;
        border-color: #f5c6cb;
    }
</style>
@endsection

@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Import Missing Committee Score</div><div class=""><a class=" btn btn-secondary btn-sm" href="{{url('/admin/Reports/missing/'.$enrollment_id.'/committee_score')}}">Go Back</a></div>
    </div>
</div>
<div class="">
    <div class="tab-content bordered" id="myTabContent">
        <div class="content-wrapper-in" id="importmissinggrade">
            @include('layouts.admin.common.alerts')

            <div class="card shadow">
                <div class="card-body">
                    <div class="">Before uploading data (missing committee score), please ensure that there is consistency with the naming of column fields in your "XLSX" file:<br></div>
                    <div class="pt-10">
                        <a href="{{url('/resources/assets/admin/ImportMissingCommitteeScoreNew.xlsx')}}" class="btn btn-secondary">Download Template</a>
                    </div>
                </div>
            </div>

            <form id="frm_mcs_import" method="post" action="{{url('admin/Reports/import/missing/committee_score')}}" enctype="multipart/form-data" novalidate="novalidate">
                {{csrf_field()}}   
                <div class="card shadow">
                    <div class="card-header">Upload</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-lg-12 mb-15">
                                <input type="file" id="imp_file" name="imp_file" class="form-control font-12" value="" required="">
                                @if($errors->first('imp_file'))
                                    <div class="mb-1 text-danger">
                                        {{ $errors->first('imp_file')}}
                                    </div>
                                @endif
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
</div>
@endsection
@section('scripts')

<script type="text/javascript">
    $('#frm_mcs_import').validate({
        rules: {
            imp_file: {
                required: true,
                extension: 'xlsx'
            }    
        },
        messages: {
            imp_file: {
                required: 'File is required.',
                extension: 'Only .xlsx file allowed.'
            }  
        }
    });
</script>

@endsection