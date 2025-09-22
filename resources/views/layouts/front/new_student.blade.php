@extends('layouts.front.app')

@section('content')
    <form class="p-20 border mt-20 mb-20">
            <div class="box-1">
                <div class="back-box" style="">
                    <div class="form-group text-right">
                        <!--<label class="control-label col-12 col-md-4 col-xl-3"></label>-->
                        <div class="">
                            <a href="{{url('/')}}" class="btn btn-secondary back-btn" title="">Back</a>
                        </div>
                    </div>    
                </div>
                <div class="card">
                    <div class="card-header">Please enter your student's requested information</div>
                    <div class="card-body">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-4 col-xl-3">State ID Number (10 Digit) : </label>
                            <div class="col-12 col-md-6 col-xl-6">
                                <input type="text" class="form-control" maxlength="10">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-4 col-xl-3">Date of Birth : </label>
                            <div class="col-12 col-md-6 col-xl-6">
                                <input type="text" class="form-control mydatepicker01">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-4 col-xl-3">Best Contact phone number : </label>
                            <div class="col-12 col-md-6 col-xl-6">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-4 col-xl-3">Alternate phone number : </label>
                            <div class="col-12 col-md-6 col-xl-6">
                                <input type="text" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-4 col-xl-3">Parent Email address : </label>
                            <div class="col-12 col-md-6 col-xl-6">
                                <input type="email" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-4 col-xl-3">Confirm Email address : </label>
                            <div class="col-12 col-md-6 col-xl-6">
                                <input type="email" class="form-control">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-4 col-xl-3"></label>
                            <div class="col-12 col-md-6 col-xl-6">
                                <a href="javascript:void(0);" class="btn btn-secondary" title="">Submit</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </form>  

@endsection
