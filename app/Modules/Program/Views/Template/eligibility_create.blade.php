<div class="">
                    <div class="card shadow">
                        <div class="card-header d-flex flex-wrap justify-content-between">
                            <div class="">Eligibility Determination Method</div>
 
                        </div>
                        <div class="card-body">
                            <div class="pb-10 d-flex flex-wrap justify-content-center align-items-center">
                                <div class="d-flex mb-10 mr-30">
                                    <div class="mr-10">Basic Method Only Active : </div>
                                    <input id="basic_method_only" type="checkbox" class="js-switch js-switch-1 js-switch-xs" name="basic_method_only"  data-size="Small" checked>
                                </div>
                                <div class="d-flex mb-10 mr-30">
                                    <div class="mr-10">Combined Scoring Active : </div>
                                    <input id="combined_scoring" type="checkbox" class="js-switch js-switch-1 js-switch-xs"  name="combined_scoring" data-size="Small" checked>
                                </div>
                                <div class="d-flex mb-10">
                                    <div class="mr-10 mt-5">Select Combined Eligibility : </div>
                                    <div class="">
                                        <select class="form-control custom-select" id="combined_eligibility" name="combined_eligibility">
                                            <option value="">Choose an Option</option>
                                            <option value="Sum Scores" {{old('combined_eligibility')=='Sum Scores'?'selected':''}}>Sum Scores</option>
                                            <option value="Average Scores" {{old('combined_eligibility')=='Average Scores'?'selected':''}}>Average Scores</option>
                                            <option value="Weighted Scores" {{old('combined_eligibility')=='Weighted Scores'?'selected':''}}>Weighted Scores</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-striped mb-0">
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
                                                    <input type="hidden" id="grade{{$eligibility['id']}}" class="gradeval" value="">
                                                    {{$eligibility['name']}}
                                                    <input type="hidden" id="" name="eligibility_type[]" value="{{$eligibility['id']}}">
                                                </td>
                                                <td class="">
                                                    <select class="form-control custom-select determination_method" id="interview_score_deter_meth" name="determination_method[]">
                                                        <option value="">Choose an Option</option>
                                                        <option value="Basic">Basic</option>
                                                        <option value="Combined" {{old('determination_method[]')=='Combined'?'selected':''}} >Combined</option>
                                                    </select>
                                                </td>
                                                <td class="text-center">
                                                    <div class="max-width-35 ml-auto mr-auto tooltip1">
                                                        <img src="{{url('resources/assets/admin/images')}}/close.png"   id="interview_score_img" class="statusimg"  alt="Not Applicable"><span class="tooltiptext">Not Applicable</span>
                                                        <input type="hidden" name="eligibility_define[]" value="close">
                                                    </div>
                                                </td>
                                                <td class="">
                                                    <select class="form-control custom-select assigned_eigibility_name" id="interview_score_eligi_name" name=" assigned_eigibility_name[]">
                                                        <option value="">Choose an Option</option>
                                                        @forelse($eligibility['eligibility_types'] as $k=>$eligibility_types)
                                                            <option value="{{$eligibility_types['name']}}" {{old('assigned_eigibility_name')==$eligibility_types['name']?'selected':''}}>{{$eligibility_types['name']}}</option>
                                                        @empty
                                                        @endforelse
                                                    </select>
                                                </td>
                                                <td class="">
                                                    <input type="text" id="interview_score_weight" name="weight[]" class="form-control weight" value="">
                                                </td>
                                                <td class="text-center">
                                                    <input id="chk_00" type="checkbox" name="status[{{$eligibility['id']}}][]" class="js-switch js-switch-1 js-switch-xs eligibility_status" data-size="Small"  checked>
                                                </td>
                                                <td class="text-center"><!-- data-toggle="modal" data-target="#modal_1"-->
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
                    </div>
                </div>