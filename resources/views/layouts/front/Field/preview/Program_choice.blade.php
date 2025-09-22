<div class="form-group" >
    <div class="card">
        <div class="card-header">
            {{-- Please select your Magnet Program choices below --}}
            {{$data['label'] ?? ""}}
        </div>
        <div class="card-body">
            <div class="b-600 font-14 mb-10">First Program Choice</div>
            <div class="border p-20 mb-20">
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Program : </label>
                    <div class="col-12 col-lg-6">
                        @php $progam_data = getProgramDropdown() @endphp
                        @php $next_grade_field_id = getNextGradeField($field_id) @endphp
                        
                        @php ($next_grade_name = "") @endphp
                        
                        <select class="form-control custom-select" name="first_choice" id="first_choice">

                            <option value="">Choose an option</option>
                            @foreach($progam_data as $key=>$value)
                                @if($value->grade_name == $next_grade_name)
                                    <option value="{{$value->id}}">{{$value->program_name." - Grade ".$value->grade_name}}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Will a sibling of this applicant attend this school for the upcoming school year?</label>
                    <div class="col-12 col-lg-6">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline1" name="customRadioInline1" class="custom-control-input" value="Yes">
                            <label class="custom-control-label" for="customRadioInline1">Yes</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline2" name="customRadioInline1" class="custom-control-input" checked="" value="No">
                            <label class="custom-control-label" for="customRadioInline2">No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row" style="display: none;" id="first_sibling">
                    <label class="col-12 col-lg-4">Sibling State ID# : </label>
                    <div class="col-12 col-lg-6">
                        <input type="text" class="form-control" name="first_sibling" id="first_sibling">
                    </div>
                </div>
            </div>
            <div class="b-600 font-14 mb-10">Second Program Choice</div>
            <div class="border p-20 mb-20">
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Program : </label>
                    <div class="col-12 col-lg-6">
                        <select class="form-control custom-select" name="second_choice" id="second_choice">
                            <option value="">Choose an option</option>
                            @foreach($progam_data as $key=>$value)
                                @if($value->grade_name == $next_grade_name)
                                    <option value="{{$value->id}}">{{$value->program_name." - Grade ".$value->grade_name}}</option>
                                @endif
                            @endforeach
                        </select>

                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Will a sibling of this applicant attend this school for the upcoming school year?</label>
                    <div class="col-12 col-lg-6">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline3" name="customRadioInline" class="custom-control-input" value="Yes">
                            <label class="custom-control-label" for="customRadioInline3">Yes</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline4" name="customRadioInline" class="custom-control-input" checked value="No">
                            <label class="custom-control-label" for="customRadioInline4" >No</label>
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
