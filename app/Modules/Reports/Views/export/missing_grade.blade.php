@php 
    $config_subjects = Config::get('variables.subjects');
    $subject_count = count($subjects) ?? 0;
    $colspan = 10;
@endphp

@if(!empty($firstdata))
    <table class="table table-striped mb-0 w-100" id="datatable">
        <thead>
            <tr>
                <th class="align-middle" colspan="{{$colspan}}" rowspan="2"></th>
                @foreach ($terms as $tyear => $tvalue)
                    <th class="align-middle text-center" colspan="{{$subject_count}}">{{$tyear}}</th>
                @endforeach
            </tr>
            <tr>
                {{-- <th class="align-middle" colspan="{{$colspan}}"></th> --}}
                @foreach ($terms as $tyear => $tvalue)
                    @foreach($subjects as $value)
                        @php
                            $sub = $config_subjects[$value] ?? $value;
                        @endphp
                        <th class="align-middle">{{$sub}}</th>
                    @endforeach
                @endforeach
            </tr>
            <tr>
                <th class="align-middle">Submission ID</th>
                <th class="align-middle">Submission Status</th>
                <th class="align-middle">State ID</th>
                <th class="align-middle">Last Name</th>
                <th class="align-middle">First Name</th>
                <th class="align-middle">Current Grade</th>
                <th class="align-middle">Next Grade</th>
                <th class="align-middle">Current School</th>
                <th class="align-middle">First Choice</th>
                <th class="align-middle">Second Choice</th>
                @foreach ($terms as $tyear => $tvalue)
                    @foreach($subjects as $value)
                        @foreach($tvalue as $value1)
                            <th class="align-middle">{{$value1}}</th>
                        @endforeach
                    @endforeach
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach($firstdata as $key=>$value)
                <tr id="row{{$value['submission_id']}}">
                    <td class="text-center">{{$value['id']}}</td>
                    <td class="text-center">{{$value['submission_status']}}</td>
                    <td class="">{{$value['student_id']}}</td>
                    <td class="">{{$value['last_name']}}</td>
                    <td class="">{{$value['first_name']}}</td>
                    <td class="text-center">{{$value['current_grade']}}</td>
                    <td class="text-center">{{$value['next_grade']}}</td>
                    <td class="">{{$value['current_school']}}</td>
                    <td class="">{{$value['first_program']}}</td>
                    <td class="">{{$value['second_program']}}</td>
                    @foreach ($terms as $tyear => $tvalue)
                        @foreach($subjects as $svalue)
                            @foreach($tvalue as $tvalue1)
                                <td class="align-middle">
                                    @php
                                        $marks = $value['score'][$tyear][$svalue][$tvalue1] ?? '';
                                    @endphp
                                    <div class="text-center">
                                        @if(is_numeric($marks) || $marks == 'NA')
                                            <span>
                                                {!! $marks !!}
                                            </span> 
                                        @else
                                            <span>
                                                {{'0'}}
                                            </span> 
                                        @endif
                                    </div>
                                </td>
                            @endforeach
                        @endforeach
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
@endif