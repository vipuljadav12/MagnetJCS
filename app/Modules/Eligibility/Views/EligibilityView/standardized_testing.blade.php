<div class="card shadow">
    <div class="card-header">{{$eligibility->name}}</div>
    <div class="card-body">
        @php
            $subjects = array("re"=>"Reading","eng"=>"English","math"=>"Math","sci"=>"Science","ss"=>"Social Studies","o"=>"Other");
        @endphp
        <div class="row">
            @forelse(json_decode($eligibility->content)->subjects as $key=>$subject)
                <label class="control-label col-12 col-md-12">{{$subjects[$subject]}}</label>
                <div class="col-12 @if(json_decode($eligibility->content)->scoring->type=='DD') col-md-12 @else col-md-6 @endif">
                    <div class="form-group row">
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" value="">
                        </div>
                    </div>
                </div>
                @if(json_decode($eligibility->content)->scoring->type!='DD')
                    <div class="col-12 col-lg-6">
                        <div class="form-group row">
{{--                            <label class="control-label col-12 col-md-12">  </label>--}}
                            <div class="col-12 col-md-12">
                                <select class="form-control custom-select">
                                    <option value="">Select Option</option>
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
                                    @else
                                        <option>@if(json_decode($eligibility->content)->scoring->CO=='NP')National Percentage @else Raw Score @endif</option>
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
</div>