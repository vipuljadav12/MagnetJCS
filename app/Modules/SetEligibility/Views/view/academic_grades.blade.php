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
        <input type="hidden" name="application_id" value="{{$req['application_id']}}">

                @php
                    $late_submission = 0;
                    if (isset($req['late_submission'])) {
                        $late_submission = $req['late_submission'];
                    }
                @endphp
                <input type="hidden" name="late_submission" value="{{$late_submission}}">
                
                @forelse(json_decode($eligibility->content)->subjects as $key=>$subject)
                    <label class="control-label col-12 col-md-12">{{$subjects[$subject]}}</label>
                    <div class="col-12 ">
                        <div class="form-group row">
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" name="value[{{$subjects[$subject]}}][]" value="{{$extraValue[$subjects[$subject]][0] ?? ""}}">
                            </div>
                        </div>
                    </div>
                    
                @empty
                @endforelse
            </div>
        </div>
   </form> 
</div>