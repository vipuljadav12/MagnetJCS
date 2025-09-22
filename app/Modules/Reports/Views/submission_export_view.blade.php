<table>
    <thead>
        @php
            $ts_index = array_search('Test Scores', $fields);
            $ts_count = count($ts_field_ary);
        @endphp
        <!-- First Header row -->
        @if($ts_index !== false && $ts_count>0)
            <tr>
                @php
                    $ts_index = array_search('Test Scores', $fields);
                @endphp
                <th colspan="{{$ts_index}}"></th>
                <th colspan="{{($ts_count*2)}}">Test Scores</th>
            </tr>
        @endif
        <!-- Second Header row -->
        <tr>
            @foreach($fields as $field)
                @if($ts_count>0 && $field == 'Test Scores')
                    @for($i=0; $i<2; $i++)
                        @foreach($ts_field_ary as $ts_field)
                            @php
                                $score_type = ($i==0 ? 'Value' : 'Rank');
                                $ts_field .= ' - '.$score_type;
                            @endphp
                            <th>
                                {{ $ts_field or '' }}
                            </th>
                        @endforeach
                    @endfor
                @else
                    <th>{{ $field or '' }}</th>
                @endif
            @endforeach
        </tr>
    </thead>
    <tbody>
        <!-- Data rows -->
        @foreach($submissions as $s_key => $submission)
            <tr>
                @foreach($submission as $field => $value)
                    @if($ts_count>0 && $field == 'ts_data')
                        @for($i=0; $i<2; $i++)
                            @foreach($ts_field_ary as $ts_field)
                                @php
                                    $score_type = ($i==0 ? 'value' : 'rank');
                                @endphp
                                <th>
                                    {{ ($value[$score_type][$ts_field] ?? '') }}
                                </th>
                            @endforeach
                        @endfor
                    @else
                        @php
                            if (!isset($value) || is_array($value)) {
                                $value = '';
                            }
                        @endphp
                        <td>{{$value}}</td>
                    @endif
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>