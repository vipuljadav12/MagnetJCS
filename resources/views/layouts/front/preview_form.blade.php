@extends('layouts.front.app')

@section('content')
    @include("layouts.front.common.district_header")
        <div class="box-2" style="">
            <div class="box-2-1" style="">
                <div class="back-box" style="">
                    <div class="form-group text-right">
                        <div class="">
                            
                        </div>
                    </div>    
                </div>
                <div class="card">
                    <div class="card-header">Step {{$page_id}} - {{ getFormPageTitle($data[0]->form_id, $page_id) }}</div>
                     <div class="card-body">
                        @include("layouts.front.preview_form_fields")
                        <div class="form-group row">
                                <label class="control-label col-12 col-md-4 col-xl-3"></label>
                                <div class="col-12 col-md-6 col-xl-6">
                                    <a href="javascript:void(0)" onclick="alert('This functionality does not work in preview mode')" class="btn btn-secondary step-2-1-btn">Submit</a>
                                </div>
                            </div>
                     </div>
                </div>
                
            </div>
        </div>
@endsection
