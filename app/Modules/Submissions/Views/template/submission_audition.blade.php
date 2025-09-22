@if(!empty($choice_ary))
    @foreach($choice_ary as $choice => $cvalue)

        @php
            // dd($value_2->assigned_eigibility_name);
            if ($choice == 'first' || count($choice_ary) == 1) {
                $eligibility_data = getEligibilityConfig($submission->first_choice_program_id, $value->assigned_eigibility_name, "email");
            } else{
                $eligibility_data = getEligibilityConfig($submission->second_choice_program_id, $value->assigned_eigibility_name, "email");
            }
            
        @endphp
            <div class="card shadow">
                <div class="card-header">{{$value->eligibility_ype}} {{$cvalue}} [{{getProgramName($submission->{$choice.'_choice_program_id'})}}]</div>
                <div class="card-body">
                    {!! $eligibility_data !!}
                </div>
            </div>
    @endforeach
@endif