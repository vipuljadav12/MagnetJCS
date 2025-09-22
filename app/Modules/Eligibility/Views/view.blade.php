@extends('layouts.admin.app')
@section('title')View Eligibility @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">View Eligibility</div>
            <div class=""><a href="{{$module_url}}" class="btn btn-sm btn-secondary" title="">Back</a></div>
        </div>
    </div>
{{--    @if(isset(json_decode($eligibility->content)->eligibility_type))
        <div class="card shadow">
            <div class="card-body">
                    <div class="form-group custom-none">
                        <label class="control-label">{{$eligibility->name}}</label>
                        <div class="">
                                <select class="form-control custom-select template-type" name="extra[eligibility_type][type]">
                                <option value="">Select Option</option>
                                    @if(json_decode($eligibility->content)->eligibility_type->type=='YN')
                                        @forelse(json_decode($eligibility->content)->eligibility_type->YN as $yn)
                                            <option>{{$yn}}</option>
                                        @empty
                                        @endforelse
                                    @else
                                        @forelse(json_decode($eligibility->content)->eligibility_type->NR as $nr)
                                            <option>{{$nr}}</option>
                                        @empty
                                        @endforelse
                                    @endif
                            </select>
                        </div>
                    </div>
                --}}{{-- <div class="form-group text-right"><a href="javascript:void(0);" class="btn btn-secondary btn-sm first-click" title="">Submit</a></div> --}}{{--
            </div>
         </div>
    @endif
    @if(isset(json_decode($eligibility->content)->scoring))
        <div class="card shadow">
        <div class="card-header">{{$eligibility->name}}</div>
        <div class="card-body">
            @php
                $subjects = array("re"=>"Reading","eng"=>"English","math"=>"Math","sci"=>"Science","ss"=>"Social Studies","o"=>"other");
            @endphp
            <div class="row">
                @forelse(json_decode($eligibility->content)->subjects as $key=>$subject)
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">{{$subjects[$subject]}}</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" value="41">
                            </div>
                        </div>
                    </div>
                @empty
                @endforelse
                @if(json_decode($eligibility->content)->scoring->type!='DD' && json_decode($eligibility->content)->scoring->method !='CO'  )
                    <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">Select : </label>
                        <div class="col-12 col-md-12">
                            <select class="form-control custom-select">
                                @if(json_decode($eligibility->content)->scoring->method=='YN')
                                    @forelse(json_decode($eligibility->content)->scoring->YN as $yn)
                                        <option>{{$yn}}</option>
                                    @empty
                                    @endforelse
                                @elseif(json_decode($eligibility->content)->scoring->method=='NR')
                                    @forelse(json_decode($eligibility->content)->scoring->NR as $nr)
                                        <option>{{$nr}}</option>
                                    @empty
                                    @endforelse
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
    @endif--}}
    
    @if(isset($eligibility->template_id) && $eligibility->template_id != 0)
        @include("Eligibility::EligibilityView.".$eligibilityTemplate->content_html)
    @else
        @include("Eligibility::EligibilityView.template2")
    @endif
@endsection
@section('scripts')
@endsection