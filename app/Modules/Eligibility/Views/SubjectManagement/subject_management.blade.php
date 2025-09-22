@extends('layouts.admin.app')
@section('title') Subject Management @endsection
@section('content')
<style>
    .collapsible-link::before {
        content: '';
        width: 14px;
        height: 2px;
        background: #333;
        position: absolute;
        top: calc(50% - 1px);
        right: 1rem;
        display: block;
        transition: all 0.3s;
    }

    /* Vertical line */
    .collapsible-link::after {
        content: '';
        width: 2px;
        height: 14px;
        background: #333;
        position: absolute;
        top: calc(50% - 7px);
        right: calc(1rem + 6px);
        display: block;
        transition: all 0.3s;
    }

    .collapsible-link[aria-expanded='true']::after {
        transform: rotate(90deg) translateX(-1px);
    }

    .collapsible-link[aria-expanded='true']::before {
        transform: rotate(180deg);
    }
    
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Subject Management</div>
            <div class="">
                <a href="{{url('admin/Eligibility')}}" class="btn btn-sm btn-secondary" title="">Back</a>
               {{--  <a href="{{url('admin/Eligibility/trash')}}" class="btn btn-sm btn-danger" title="">Trash</a> --}}
            </div>
        </div>
    </div>
    <form action="{{$module_url}}/updateSubjectManagement" method="POST" id="subject_management" name="subject_management">
        {{csrf_field()}}
        <div class="card shadow">
            <div class="card-body">
                @include("layouts.admin.common.alerts")
                <div class="">
                    <div class="form-group">
                        <label class="control-label">Select Application : </label>
                        <div class="">
                            <select class="form-control custom-select selectApplication" name="application_id">
                                <option value="">Choose Option</option>
                                @foreach($applications as $key=>$value)
                                    <option value="{{$value->id}}" @if($value->id == $id) selected @endif>{{$value->application_name}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                @if(isset($data) && !empty($data))
                <div class="card shadow">
                    <div class="card-body">
                        <div class="container">
                            <div class="row">
                                <div class="col-lg-12 mx-auto">
                                    <div id="accordionExample" class="">
                                        <!-- Accordion item 2 -->
                                        @if(isset($data['grades']) && !empty($data['grades']))
                                        @forelse($data['grades'] as $key=>$grade)
                                            <div class="card" style="width: 100%">
                                                <div id="headingTwo" class="card-header bg-white shadow-sm border-0">
                                                    <h6 class="mb-0 font-weight-bold"><a href="#" data-toggle="collapse" data-target="#{{$grade->name}}" aria-expanded="false" aria-controls="{{$grade->name}}" class="d-block position-relative collapsed text-dark text-uppercase collapsible-link py-2">{{$grade->name}}</a></h6>
                                                </div>
                                                <input type="hidden" name="application_id" value="{{$id}}">
                                                <div id="{{$grade->name}}" aria-labelledby="heading{{$grade->name}}" data-parent="#accordionExample" class="collapse">
                                                    <div class="card-body p-5 mt-20">
                                                        <div class="row pl-10">
                                                            @if(isset($data['subjects']) && !empty($data['subjects']))
                                                            @forelse($data['subjects'] as $key=>$subject)
                                                                <div class="col-md-3 mb-20">
                                                                    <div class="row">
                                                                        <div class="col-sm-6">
                                                                            {{$subject}}
                                                                        </div>
                                                                        <div class="col-sm-6">
                                                                            @php $subject=strtolower(str_replace(' ','_',$subject)) @endphp
                                                                    {{-- !empty($data['subjectManagement']->where('grade_id',$grade->id)->where($subject,'Y')->first())--}}
                                                                            <input type="hidden" name="gradeSubject[{{$grade->name}}][{{$subject}}]" value="N">
                                                                            <input type="checkbox" value="Y" name="gradeSubject[{{$grade->name}}][{{$subject}}]" class="js-switch js-switch-1 js-switch-xs status" data-size="Small" @if(!isset($data['subjectManagement'][$grade->name][$subject])||$data['subjectManagement'][$grade->name][$subject]=='Y') checked @endif />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @empty
                                                            @endforelse
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                        @endforelse
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

            </div>
        </div>

        @if($id > 0)
        <div class="box content-header-floating" id="listFoot">
            <div class="row">
                <div class="col-lg-12 text-right hidden-xs float-right">
                   {{--  @php
                        if(isset($eligibility["template_id"]) && $eligibility["template_id"] != 0)
                            $template_type = "general";
                        else
                            $template_type = "recommendation";
                    @endphp --}}
                    {{-- <input type="hidden" name="submit-from" id="submit-from-btn" value="{{$template_type}}"> --}}
                    <button type="submit" class="btn btn-warning btn-xs" value="save" name="submit"><i class="fa fa-save"></i> Save </button>
                   <button type="Submit" name="save_exit" value="save_exit" class="btn btn-success btn-xs submit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                    <a class="btn btn-danger btn-xs" href="{{url('/admin/Eligibility')}}"><i class="fa fa-times"></i> Cancel</a>
                   {{-- <a class="btn btn-danger btn-xs" href="javascript:void(0);"><i class="far fa-trash-alt"></i> Delete</a> --}}
                </div>
            </div>
        </div>
        @endif
    </form>
@endsection
@section('scripts')
    
    <script type="text/javascript">
        
        $(document).on("change",".selectApplication",function(event)
        {
            let app_id = $(document).find(".selectApplication").val();
            var link = "{{url('admin/Eligibility/subjectManagement')}}"+"/"+app_id;
            document.location.href = link;
        });
    </script>
@endsection

       