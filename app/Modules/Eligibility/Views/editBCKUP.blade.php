@extends('layouts.admin.app')
@section('title')Edit Eligibility @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Edit Eligibility</div>
            <div class=""><a href="{{url('admin/Eligibility')}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
        </div>
    </div>
    <form class="">
        <!--<div class="card shadow">
            <div class="card-header">Interview Score</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="">How Interview Score will be entered ?</label>
                    <div class="">
                        <select class="form-control custom-select form-control-sm">
                            <option value="">Manually by District Administrator</option>
                            <option value="">via Spreadsheet Upload</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="">How interview score eligibility method will work ?</label>
                    <div class="">
                        <select class="form-control custom-select form-control-sm">
                            <option value="">Labels</option>
                            <option value="">Numerical Ranking</option>
                            <option value="">Customized by School</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>-->
        <div class="card shadow">
            <div class="card-header">Basic Eligibility</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="">Do you want to map this eligibility under Basic Eligibility Method ?</label>
                    <div class="">
                        <select class="form-control custom-select form-control-sm">
                            <option value="">Yes</option>
                            <option value="">No</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">Combined Eligibility</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="">Do you want to map this eligibility under Combined Eligibility Method ?</label>
                    <div class="">
                        <select class="form-control custom-select form-control-sm">
                            <option value="">Yes</option>
                            <option value="">No</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">Additional Eligibility</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="">Do you want to map this eligibility under Additional Eligibility Method ?</label>
                    <div class="">
                        <select class="form-control custom-select form-control-sm">
                            <option value="">Yes</option>
                            <option value="">No</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card shadow">
            <div class="card-header">Final Eligibility</div>
            <div class="card-body">
                <div class="form-group">
                    <label for="">Do you want to map this eligibility under Final Eligibility Method ?</label>
                    <div class="">
                        <select class="form-control custom-select form-control-sm">
                            <option value="">Yes</option>
                            <option value="">No</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>

        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right"><a class="btn btn-warning btn-xs" href="javascript:void(0);"><i class="fa fa-save"></i> Save </a> <a class="btn btn-success btn-xs" href="eligibility-master.html"><i class="fa fa-save"></i> Save &amp; Exit</a> <a class="btn btn-primary btn-xs" href="javascript:void(0);"><i class="far fa-file-pdf"></i> Print First Choice</a> </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts')
    <script src="{{asset('resources/assets/common/js/jquery.validate.min.js')}}"></script>
    <script src="{{asset('resources/assets/common/js/additional-methods.min.js')}}"></script>
    <script>

    </script>
@endsection
