<table>
    <thead>
        <!-- First Header row -->
        @php
            $ts_index = array_search('Test Scores', $fields);
            $ts_count = count($ts_name_fields);
        @endphp
        @if($ts_count > 0)
            <tr>
                <th colspan="{{$ts_index}}"></th>
                <th colspan="{{(count($ts_name_fields))}}">Test Scores</th>
            </tr>
        @endif
        <!-- Second Header row -->
        <tr>
            @foreach($fields as $field)
                @if($field == 'Test Scores' && $ts_count > 0)
                    <!-- Test Scores -->
                    @foreach($ts_name_fields as $ts_name)
                        <th class="align-middle">{{ $ts_name }}</th>
                    @endforeach
                @else
                    <th class="align-middle">{{ $field }}</th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        <!-- Data rows -->
        @foreach($data as $s_key => $submission)
            {{-- {{dd($submission)}} --}}
            <tr>
                <td class="text-center">{{$submission['id']}}</td>
                <td class="">{{$submission['student_id']}}</td>
                <td class="">{{($submission['student_id'] != "" ? "Current" : "Non-Current")}}</td>
                <td class="">{{$submission['last_name']}}</td>
                <td class="">{{$submission['first_name']}}</td>
                <td class="text-center">{{$submission['next_grade']}}</td>
                <td class="">{{$submission['current_school']}}</td>
                <td class="">{{$submission['first_program']}}</td>
                <td class="">{{$submission['second_program']}}</td>
                <!-- Test Scores -->
                @if($ts_count > 0)
                    @foreach($ts_name_fields as $ts_name)
                        @php
                            $ts_rank = $submission['test_scores']['data'][$ts_name] ?? '';
                            $ts_rank = ($ts_rank === 0) ? 'No' : $ts_rank;
                        @endphp
                        <td class="">{{ $ts_rank }}</td>
                    @endforeach
                @else
                    <td class=""></td>
                @endif
                <td>{{$submission['missing_recommendation']}}</td>
                <td>{{$submission['first_choice_committee_score']}}</td>
                <td>{{$submission['second_choice_committee_score']}}</td>
                <td>{{$submission['first_choice_missing_writing_sample']}}</td>
                <td>{{$submission['second_choice_missing_writing_sample']}}</td>
                <td>{{$submission['agc_rank']}}</td>
                <td>{{$submission['created_at']}}</td>
            </tr>
        @endforeach
    </tbody>
</table>