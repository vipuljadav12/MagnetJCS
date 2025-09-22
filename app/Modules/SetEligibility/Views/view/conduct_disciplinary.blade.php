
    {{-- <div class="card-header">{{$eligibility->name}}</div> --}}
    <p>Student's CDI should be below or equal to entered CDI</p>
<div class="card shadow">
    <form id="extraValueForm" action="{{url('admin/SetEligibility/extra_values/save')}}" method="post">
        {{csrf_field()}}
        <input type="hidden" name="program_id" value="{{$req['program_id']}}">
        <input type="hidden" name="eligibil`ity_id" value="{{$req['eligibility_id']}}">
        <input type="hidden" name="eligibility_type" value="{{$req['eligibility_type']}}">
        @php
            $late_submission = 0;
            if (isset($req['late_submission'])) {
                $late_submission = $req['late_submission'];
            }
        @endphp
        <input type="hidden" name="late_submission" value="{{$late_submission}}">
        <div class="card-body">
            @php
                $selectors = array(
                    "B","C","D","E"
                );
            @endphp
            <div class="row">
                @foreach($selectors as $s=>$selector)
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12">{{$selector}} info</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="value[{{$selector}}][]" value="{{$extraValue[$selector][0] ?? ""}}">
                        </div>
                    </div>
                </div>
                @endforeach
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"># Susp</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="value[Susp][]" value="{{$extraValue['Susp'][0] ?? ""}}">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="form-group row">
                        <label class="control-label col-12 col-md-12"># Days Susp</label>
                        <div class="col-12 col-md-12">
                            <input type="text" class="form-control" name="value[SuspDays][]" value="{{$extraValue['SuspDays'][0] ?? ""}}">
                        </div>
                    </div>
                </div>
                {{-- @if(json_decode($eligibility->content)->scoring->type!='DD')
                <div class="col-12 col-lg-6">
                    <div class="form-group custom-none row">
                        <label class="control-label col-12 col-md-12">Select : </label>
                        <div class="col-12 col-md-12">
                            <select class="form-control custom-select template-type" name="extra[eligibility_type][type]">
                                <option value="">Select Option</option>
                                @if(json_decode($eligibility->content)->scoring->method=='YN')
                                    @forelse(json_decode($eligibility->content)->scoring->YN as $yn)
                                        <option>{{$yn}}</option>
                                    @empty
                                    @endforelse
                                @else
                                    @forelse(json_decode($eligibility->content)->scoring->NR as $nr)
                                        <option>{{$nr}}</option>
                                    @empty
                                    @endforelse
                                @endif
                            </select>
                        </div>
                    </div>
                </div>
                @endif   --}}
            </div>
        </div>
    </form>
</div>



<script type="text/javascript">
    $(document).ready(function(){

                $(document).find("#exampleModalLabel1").html("Conduct Disciplinary Info");
        }
        );
</script>