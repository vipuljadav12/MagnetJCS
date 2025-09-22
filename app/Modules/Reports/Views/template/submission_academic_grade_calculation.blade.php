    @php 

        
        $eligibility_data = getEligibilityContent1($value->assigned_eigibility_name);

        $content = $eligibility_data ?? null;
        $scoring = $eligibility_data->scoring ?? null;

        
    @endphp

    @if(isset($scoring->type) && $scoring->type == "DD")

            @if(isset($content->terms_pulled[0]) && isset($content->subjects))
                @php $total_year = $content->terms_pulled[0] @endphp
                @php $subjects = Config::get('variables.subjects') @endphp

                @for($i=1; $i <= $total_year; $i++)
                    <div class="card shadow">
                            <div class="card-header">Year - {{(date("Y")-$i)."-".(date("Y")-($i-1))}}</div>
                            <div class="card-body d-flex">
                                @foreach($content->subjects as $value)
                                    @foreach($content->terms_calc as $value1)
                                        <div class="form-group row mr-10">
                                            <label class="control-label col-12 col-md-12">{{$subjects[$value]}} - {{$value1}} </label>
                                            <div class="col-12 col-md-12">
                                                <input type="text" class="form-control" value="{{getAcademicScore($submission->student_id, $subjects[$value], $value1, (date("Y")-$i)."-".(date("Y")-($i-1)), (date("Y")-$i)."-".(date("y")-($i-1)))}}">
                                            </div>
                                        </div>
                                    @endforeach
                                @endforeach
                            </div>
                        </div>
                    
                @endfor
            @endif

        @endif