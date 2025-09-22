<div class="card ">
    {{-- <div class="card-header">{{$eligibility->name}}</div> --}}
   <form id="extraValueForm" action="{{url('admin/SetEligibility/extra_values/save')}}" method="post">
        {{csrf_field()}}
        <div class="card-body">
            @php
                $subjects = array("re"=>"Reading","eng"=>"English","math"=>"Math","sci"=>"Science","ss"=>"Social Studies","o"=>"Other");
            @endphp
            <div class="row">
                <input type="hidden" name="program_id" value="{{$req['program_id']}}">
                <input type="hidden" name="eligibility_id" value="{{$req['eligibility_id']}}">
                <input type="hidden" name="eligibility_type" value="{{$req['eligibility_type']}}">
                @forelse(json_decode($eligibility->content)->subjects as $key=>$subject)
                    <label class="control-label col-12 col-md-12">{{$subjects[$subject]}}</label>
                    <div class="col-12 @if(json_decode($eligibility->content)->scoring->type=='DD') col-md-12 @else col-md-6 @endif">
                        <div class="form-group row">
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="value[{{$subjects[$subject]}}][]" value="{{$extraValue[$subjects[$subject]][0] ?? ""}}">
                               {{--  {{$extraValue[$subjects[$subject]][0] ?? ""}}
                                @if(isset($extraValue))
                                    @foreach($extraValue as $k=>$v)
                                        @php
                                            print_r($v);
                                        @endphp
                                    @endforeach
                                @endif --}}
                            </div>
                        </div>
                    </div>
                    @if(json_decode($eligibility->content)->scoring->type!='DD')
                        <div class="col-12 col-lg-6">
                            <div class="form-group row">
                                {{-- <label class="control-label col-12 col-md-12">  </label> --}}
                                <div class="col-12 col-md-12">
                                    <select class="form-control custom-select" name="value[{{$subjects[$subject]}}][]">
                                        <option value="">Select Option</option>
                                        @if(json_decode($eligibility->content)->scoring->method=='YN')
                                            @forelse(json_decode($eligibility->content)->scoring->YN as $yn)
                                                <option @if(isset($extraValue[$subjects[$subject]][1]) && $extraValue[$subjects[$subject]][1] == $yn) selected @endif>{{$yn}}</option>
                                            @empty
                                            @endforelse
                                        @elseif(json_decode($eligibility->content)->scoring->method=='NR')
                                            @forelse(json_decode($eligibility->content)->scoring->NR as $nr)
                                                <option @if(isset($extraValue[$subjects[$subject]][1]) && $extraValue[$subjects[$subject]][1] == $nr) selected @endif>{{$nr}}</option>
                                            @empty
                                            @endforelse
                                        @else
                                            @if(json_decode($eligibility->content)->scoring->CO=='NP')
                                                <option value="NP"
                                                    @if(isset($extraValue[$subjects[$subject]][1]) && $extraValue[$subjects[$subject]][1] == "NP") selected @endif>
                                                    National Percentage 
                                                </option>
                                            @else
                                                <option value="RS"
                                                    @if(isset($extraValue[$subjects[$subject]][1]) && $extraValue[$subjects[$subject]][1] == "RS") selected @endif>
                                                    Raw Score 
                                                </option>
                                            @endif
                                            {{-- <option value="NP"
                                                @if(isset($extraValue[$subjects[$subject]][1]) && $extraValue[$subjects[$subject]][1] == "NP") selected @endif>
                                                @if(json_decode($eligibility->content)->scoring->CO=='NP')
                                                    National Percentage 
                                                @else
                                                    Raw Score 
                                                @endif
                                            </option> --}}
                                        @endif
                                    </select>
                                </div>
                            </div>
                        </div>
                    @endif
                @empty
                @endforelse
            </div>
        </div>
   </form> 
</div>