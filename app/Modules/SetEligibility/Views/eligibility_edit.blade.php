<div class="tab-content bordered" id="myTab_eligibilityContent">
    <div class="tab-pane fade show active" id="submissions" role="tabpanel" aria-labelledby="submissions-tab">
        @if(count($applications) > 0)
        <div class="">
            <div class="card shadow">
                <div class="card-header d-flex flex-wrap justify-content-between">
                    <div class="">Eligibility Determination Method</div>
                    
                </div>
                <div class="card-body">
                    <div class="">
                            <div class="form-group">
                                <label class="control-label">Select Application : </label>
                                <div class="">
                                    <select class="form-control custom-select" id="application_id" name="application_id" onchange="changeApplication(this.value)">
                                        @foreach($applications as $key=>$value)
                                            <option value="{{$value->id}}" @if($application_id == $value->id) selected @endif>{{$value->application_name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                    <div class="pb-10 d-flex flex-wrap justify-content-center align-items-center">
                        <div class="d-flex mb-10 mr-30">
                            <div class="mr-10">Basic Method Active : </div>
                            <input disabled="" id="basic_method_only" type="checkbox" class="js-switch js-switch-1 js-switch-xs" name="basic_method_only"  data-size="Small" {{$program->basic_method_only=='Y'?'checked':''}}>
                        </div>
                        <div class="d-flex mb-10 mr-30">
                            <div class="mr-10">Combined Scoring Active : </div>
                            <input disabled="" id="combined_scoring" type="checkbox" class="js-switch js-switch-1 js-switch-xs"  name="combined_scoring" data-size="Small" {{$program->combined_scoring=='Y'?'checked':''}}>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0">
                            <thead>
                            <tr>
                                <th class="align-middle">Eligibility Type</th>
                                <th class="align-middle">Used in Determination Method</th>
                                <th class="align-middle text-center">Eligibility Value Required?</th>
                                <th class="align-middle text-center">Minimum Eligibility Value</th>
                                <th class="align-middle text-center d-none">Active/Inactive</th>
                                <th class="align-middle text-center w-120">Applied to Grades</th>
                            </tr>
                            </thead>
                            <tbody>
                                @forelse($eligibilities as $key=>$eligibility)
                                    <tr>
                                        <td class="">
                                            {{$eligibility['name']}}
                                            {{-- {{$setEligibility[$eligibility['id']]}} --}}
                                            <input type="hidden" id="" name="eligibility_type[]" value="{{$eligibility['id']}}">
                                            <input type="hidden" id="" name="eligibility_id[{{$eligibility['id']}}][]" value="@if(isset($eligibility['program_eligibility']['assigned_eigibility_name'])) {{$eligibility['program_eligibility']['assigned_eigibility_name']}} @endif">
                                            @php
                                                if(isset($eligibility['program_eligibility']['assigned_eigibility_name']))
                                                {
                                                    if(isset($setEligibility[$eligibility['id']]->eligibility_value))
                                                    {
                                                        $methodCheck  = getEligibilityContentType($eligibility['program_eligibility']['assigned_eigibility_name'],$setEligibility[$eligibility['id']]->eligibility_value) ?? null ;
                                                    }
                                                    else
                                                    {
                                                        $methodCheck = getEligibilityContentType($eligibility['program_eligibility']['assigned_eigibility_name']) ?? null;
                                                    }
                                                }
                                                $isST = getEligibilityTypeById($eligibility['id']);
                                                 //print_r($isST->content_html);
                                            @endphp
                                            {{-- {{$methodCheck}} --{{$eligibility['id']}}-- --}}
                                            @php
                                                $required = isset($setEligibility[$eligibility['id']]->required) ? $setEligibility[$eligibility['id']]->required : null;
                                            @endphp
                                           
         
                                         <td class="">
                                            <select class="form-control custom-select" name="determination_method[]" disabled>
                                               <option value="">Choose an Option</option>
                                                <option value="Basic" {{isset($eligibility['program_eligibility']['determination_method']) && $eligibility['program_eligibility']['determination_method']=='Basic'?'selected':''}}>Basic</option>
                                                <option value="Combined" {{isset($eligibility['program_eligibility']['determination_method']) &&    $eligibility['program_eligibility']['determination_method']=='Combined'?'selected':''}}>Combined</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <select class="form-control custom-select valueRequiredSelect" name="required[{{$eligibility['id']}}][]">
                                                @php
                                                    $required = isset($setEligibility[$eligibility['id']]->required) ? $setEligibility[$eligibility['id']]->required : null;
                                                @endphp
                                                <option value="X">Choose an Option</option>
                                                <option value="Y" @if(isset($required) && $required == "Y") selected="" @endif>Yes</option>
                                                <option value="N" @if(isset($required) && $required == "N") selected="" @endif>No</option>
                                            </select>

                                        </td>
                                        <td class="text-center">                                    
                                            <div class="MinimumEligibility ForSelectedY @if(isset($required) && $required == "N" || $required == "X") d-none @endif">
                                                <div class="align-items-center text-center">
                                                    <div class="mr-10 d-none">
                                                         <select class="form-control d-none" name="eligibility_value[{{$eligibility['id']}}][]" @if(isset($required) && $required == "N") disabled="" @endif style="width: 200px !important">
                                                            @if(isset($eligibility['program_eligibility']['assigned_eigibility_name']))
                                                                @if(isset($setEligibility[$eligibility['id']]->eligibility_value))
                                                                    {!! getEligibilityContent($eligibility['program_eligibility']['assigned_eigibility_name'],$setEligibility[$eligibility['id']]->eligibility_value) ?? "" !!}
                                                                @else
                                                                    {!! getEligibilityContent($eligibility['program_eligibility']['assigned_eigibility_name']) ?? "" !!}
                                                                @endif
                                                            @endif
                                                        </select>
                                                    </div>
                                                    <div class="">

                                                        @if(isset($eligibility['program_eligibility']))
                                                             @if(isset($methodCheck) && $methodCheck == "NA" || $isST->content_html == "standardized_testing" || $isST->content_html == "conduct_disciplinary" || $isST->content_html == "academic_grade_calculation" || $isST->content_html == "writing_prompt" || $isST->content_html == "recommendation_form" || $isST->content_html == "test_score" || $isST->content_html == "audition" || $isST->content_html == "committee_score")
                                                                    
                                                                <a class="openPopUpForData editPopBtn  @if(!isset($required) || $required != "Y") d-none @endif" data-id="{{$eligibility['id']}}" data-eligibility-id="{{$eligibility['program_eligibility']['assigned_eigibility_name']}}" data-program-id="{{$program->id}}"> <i class="font-18 far fa-edit"></i></a>&nbsp;&nbsp;<a class="openPopUpForSetting editPopBtn  @if(!isset($required) || $required != "Y") d-none @endif" data-id="{{$eligibility['id']}}" data-eligibility-id="{{$eligibility['program_eligibility']['assigned_eigibility_name']}}" data-program-id="{{$program->id}}"> <i class="font-18 fas fa-cog"></i></a>

                                                                @if($isST->content_html == "recommendation_form" || $isST->content_html == "writing_prompt")
                                                                    @php
                                                                        $url['recommendation_form'] = url('/recommendation/preview/gift.'.$eligibility['program_eligibility']['assigned_eigibility_name'].'.'.$program->id).'.'.$application_id;

                                                                        $url['writing_prompt'] = url('/WritingPrompt/preview/'.$eligibility['program_eligibility']['assigned_eigibility_name'].'.'.$program->id).'.'.$application_id;
                                                                    @endphp

                                                                    &nbsp;<a href="{{ $url[$isST->content_html] }}" target="_blank" class="editPopBtn @if(!isset($required) || $required != "Y") d-none @endif" data-id="{{$eligibility['id']}}" data-eligibility-id="{{$eligibility['program_eligibility']['assigned_eigibility_name']}}" data-program-id="{{$program->id}}"> <i class="font-18 fas fa-eye"></i></a>
                                                                @endif
                                                            @endif
                                                        @endif
                                                    </div>
                                                </div>
                                               
                                            
                                            </div>
                                            <div class="MinimumEligibility ForSelectedX  @if(isset($required) && $required == "Y" || $required == "N" || !isset($required)) d-none @endif">
                                                
                                            </div>
                                            <div class="MinimumEligibility ForSelectedN  @if(isset($required) && $required == "Y" || !isset($required) || $required == "X") d-none @endif d-none">
                                                N/A
                                            </div>
                                            {{-- <select class="form-control custom-select assigned_eigibility_name" id="interview_score_eligi_name" name=" assigned_eigibility_name[]">
                                                <option value="">Choose an Option</option>
                                                @forelse($eligibility['eligibility_types'] as $k=>$eligibility_types)
                                                    <option value="{{$eligibility_types['name']}}" {{isset($eligibility['program_eligibility']['assigned_eigibility_name'])&&$eligibility['program_eligibility']['assigned_eigibility_name']==$eligibility_types['name']?'selected':''}}>{{$eligibility_types['name']}}</option>
                                                @empty
                                                @endforelse
                                            </select> --}}
                                        </td>
                                        <td class="text-center d-none">
                                            @php
                                                $status = isset($setEligibility[$eligibility['id']]->status) ? $setEligibility[$eligibility['id']]->status : null;
                                            @endphp
                                            <input id="chk_09" name="status[{{$eligibility['id']}}][]" type="checkbox" value="Y" class="js-switch js-switch-1 js-switch-xs eligibility_status" data-size="Small" @if(isset($status) && $status == "Y") checked  @endif>
                                        </td>
                                        <td class="text-center">
                                            <input type="text" disabled="" class="form-control" name="" value="{{$eligibility['program_eligibility']['grade_lavel_or_recommendation_by'] ?? ""}}">                                    
                                        </td>
                                    </tr>
                                @empty
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </div>

    
</div>