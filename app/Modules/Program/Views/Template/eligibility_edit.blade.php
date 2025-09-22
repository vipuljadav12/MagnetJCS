<div class="tab-content bordered" id="myTabContent">
    <div class="tab-pane fade show active" id="submissions" role="tabpanel" aria-labelledby="submissions-tab">
        <div class="">
            <div class="card shadow">
                <div class="card-header d-flex flex-wrap justify-content-between">
                    <div class="">Eligibility Determination Method</div>
                    
                </div>
                @if(count($applications) > 0)
                <div class="card-body">
                    <div class="">
                            <div class="form-group">
                                <label class="control-label">Select Application : </label>
                                <div class="">
                                    <select class="form-control custom-select" name="application_id" onchange="changeApplication(this.value)">
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
                            <input id="basic_method_only" type="checkbox" class="js-switch js-switch-1 js-switch-xs" name="basic_method_only"  data-size="Small" {{$program->basic_method_only=='Y'?'checked':''}}>
                        </div>
                        <div class="d-flex mb-10 mr-30">
                            <div class="mr-10">Combined Scoring Active : </div>
                            <input id="combined_scoring" type="checkbox" class="js-switch js-switch-1 js-switch-xs"  name="combined_scoring" data-size="Small" {{$program->combined_scoring=='Y'?'checked':''}}>
                        </div>
                       
                        <div class="d-flex mb-10">
                            <div class="mr-10 mt-5">Select Combined Eligibility : </div>
                            <div class="">
                                <select class="form-control custom-select" id="combined_eligibility" name="combined_eligibility">
                                    <option value="">Choose an Option</option>
                                    <option value="Sum Scores" {{$program->combined_eligibility=='Sum Scores'?'selected':''}}>Sum Scores</option>
                                    <option value="Average Scores" {{$program->combined_eligibility=='Average Scores'?'selected':''}}>Average Scores</option>
                                    <option value="Weighted Scores" {{$program->combined_eligibility=='Weighted Scores'?'selected':''}}>Weighted Scores</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped mb-0 tbl_eligibility_determination_method">
                            <thead>
                            <tr>
                                <th class="align-middle">Eligibility Type</th>
                                <th class="align-middle">Used in Determination Method</th>
                                <th class="align-middle text-center">Eligibility Defined</th>
                                <th class="align-middle text-center">Assigned Eligibility Name</th>
                                <th class="align-middle text-center"><div class="tooltip1">Weight<span class="tooltiptext tooltiptext-btm">If combined and weighted is selected</span></div></th>
                                <th class="align-middle text-center w-120">Active</th>
                                <th class="align-middle text-center w-120">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @forelse($eligibilities as $key=>$eligibility)
                                
                                <tr>
                                        <td class="">
                                            {{$eligibility['name']}}
                                            <input type="hidden" id="grade{{$eligibility['id']}}" class="gradeval" value="{{(isset($eligibility['program_eligibility']['grade_lavel_or_recommendation_by']) ? str_replace(',','-',$eligibility['program_eligibility']['grade_lavel_or_recommendation_by']) : '')}}" name="grade_lavel_or_recommendation_by[]">
                                            <input type="hidden" id="" name="eligibility_type[]" value="{{$eligibility['id']}}">
                                        </td>
                                        <td class="">
                                            <select class="form-control custom-select determination_method" id="interview_score_deter_meth" name="determination_method[]">
                                               <option value="">Choose an Option</option>
                                                <option value="Basic" {{isset($eligibility['program_eligibility']['determination_method'])&&$eligibility['program_eligibility']['determination_method']=='Basic'?'selected':''}}>Basic</option>
                                                <option value="Combined" {{isset($eligibility['program_eligibility']['determination_method'])&&$eligibility['program_eligibility']['determination_method']=='Combined'?'selected':''}}>Combined</option>
                                            </select>
                                        </td>
                                        <td class="text-center">
                                            <div class="max-width-35 ml-auto mr-auto tooltip1">
                                                <img src="{{url('resources/assets/admin/images')}}/close.png" class="statusimg"   id="interview_score_img"  alt="Not Applicable"><span class="tooltiptext">Not Applicable</span>
                                                <input type="hidden" name="eligibility_define[]" value="close">
                                            </div>
                                        </td>
                                        <td class="">
                                            <select class="form-control custom-select assigned_eigibility_name" id="interview_score_eligi_name" name=" assigned_eigibility_name[]">
                                                <option value="">Choose an Option</option>
                                                @forelse($eligibility['eligibility_types'] as $k=>$eligibility_types)
                                                    <option value="{{$eligibility_types['id']}}" {{isset($eligibility['program_eligibility']['assigned_eigibility_name'])&&$eligibility['program_eligibility']['assigned_eigibility_name']==$eligibility_types['id']?'selected':''}}>{{$eligibility_types['name']}}</option>
                                                    {{-- <option value="{{$eligibility_types['name']}}" {{isset($eligibility['program_eligibility']['assigned_eigibility_name'])&&$eligibility['program_eligibility']['assigned_eigibility_name']==$eligibility_types['name']?'selected':''}}>{{$eligibility_types['name']}}</option> --}}
                                                @empty
                                                @endforelse
                                            </select>
                                        </td>
                                        <td class="">
                                            <input type="text" id="interview_score_weight"  name="weight{{$eligibility['id']}}" class="form-control weight" value="{{isset($eligibility['program_eligibility']['weight'])?$eligibility['program_eligibility']['weight']:''}}">
                                        </td>
                                        <td class="text-center">
                                            <input id="chk_09" name="status[{{$eligibility['id']}}][]" type="checkbox" class="js-switch js-switch-1 js-switch-xs eligibility_status" data-size="Small" {{isset($eligibility['program_eligibility']['status'])&&$eligibility['program_eligibility']['status']=='Y'?'checked':''}}>
                                        </td>
                                        <td class="text-center">
                                                <a href="javascript:void(0);" class="font-18 ml-5 mr-5" title="" onclick="showGradePopup('{{$eligibility['id']}}', '{{$eligibility['name']}}')"><i class="far fa-edit"></i></a>
                                            <a href="javascript:void(0);" class="font-18 ml-5 mr-5 text-danger" title=""><i class="far fa-trash-alt"></i></a>
                                        </td>
                                    </tr>
                                
                            @empty
                            @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @else
                    <p class="text-center">There is no Application setup yet.</p>
                @endif
            </div>
        </div>
    </div>

</div>
