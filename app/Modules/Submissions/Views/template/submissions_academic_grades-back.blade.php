@php $academic_calc = array() @endphp
@foreach($eligibilities as $ekey=>$evalue)
    @if(strtolower($evalue->eligibility_ype) == "academic grades")
        @php $academic_calc = getEligibilityContent1($evalue->assigned_eigibility_name) @endphp
    @endif
    @if(strtolower($evalue->eligibility_ype) == "academic grade calculation")
        @php 
            $academic_grade_calc = getEligibilityContent1($evalue->assigned_eigibility_name)
        @endphp
    @endif
@endforeach
@if(empty($academic_calc))
    @foreach($eligibilities_2 as $ekey=>$evalue)
        @if(strtolower($evalue->eligibility_ype) == "academic grades")
            @php $academic_calc = getEligibilityContent1($evalue->assigned_eigibility_name) @endphp
        @endif
        @if(strtolower($evalue->eligibility_ype) == "academic grade calculation")
            @php 
                $academic_grade_calc = getEligibilityContent1($evalue->assigned_eigibility_name)
            @endphp
        @endif
    @endforeach
@endif
@if(!empty($academic_calc))
    @if($academic_grade_calc->scoring->type == 'DD' || $academic_grade_calc->scoring->type == 'GA')
        @php $content = $academic_calc ?? null @endphp
        @php $scoring = $academic_calc->scoring ?? null @endphp
        @include("Submissions::template.submission_academic_grades_with_calc")
    @else
        @include("Submissions::template.submission_academic_grades_without_calc")
    @endif
@else
    @include("Submissions::template.submission_academic_grades_without_calc")


@endif
