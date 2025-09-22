<div class="card shadow">
    <form id="extraValueForm1" action="{{url('admin/SetEligibility/configurations/save')}}" method="post">
        {{csrf_field()}}
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
        <div class="card-body">
            @php $box_count = json_decode($eligibility->content)->eligibility_type->box_count @endphp
            @php
                if(empty($extraValue['ts_scores'])){
                    $extraValue['ts_scores'] = [''];
                }
            @endphp
            @php $count = 1 @endphp
            <p>Select Test Scores to be Used in Preliminary Score Calculation:</p>
            <div class="col-12 row wp_row"> 
            @foreach($extraValue['ts_scores'] as $key=>$value)
                <div class="col-12 row wp_row ml-10">
                    <div class="col-12"> 
                        <input type="checkbox" class="custom-control-input" id="table{{$key}}" name="use_calculation[]" @if(in_array($value, explode(",", getEligibilityConfigDynamic($req['program_id'], $req['eligibility_id'], 'use_calculation', $req['application_id'])))) checked @endif value="{{$value}}"><label for="table{{$key}}" class="custom-control-label">{{$value}}</label>
                    </div>
                </div>
            @endforeach
            </div>



            <div class="row">
                <div class="form-group col-12 text-right">
                    <button type="submit" id="extraValueFormBtn2" {{-- form="extraValueForm" --}} class="btn btn-success extraValueFormBtn">Save</button>
                </div>
            </div>

        </div>

    </form>
</div>
