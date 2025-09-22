@if(!empty($choice_ary))
    @foreach($choice_ary as $choice => $cvalue)

        @php
            $program_id = $submission->{$choice.'_choice_program_id'};
            // Entery in writing prompt table
            $wp_entry = App\Modules\WritingPrompt\Models\WritingPrompt::where('submission_id', $submission->id)->where('program_id', $program_id)->first();

            if ($choice == 'first' || count($choice_ary) == 1) {
                $wp_data = getWritingPromptDetails($submission->id, $value, $submission->late_submission);
            } else{
                $wp_data = getWritingPromptDetails($submission->id, $value_2, $submission->late_submission);
                // $wp_data = getWritingPromptDetails($submission->id, $value_2, $submission->late_submission, $choice);
                $value = $value_2;
            }
            if (!empty($wp_data)) {
                $wp_data = json_decode($wp_data, true);
            }

            $submission_data = \DB::table('submission_data')
                ->where('submission_id', $submission->id)
                ->where('config_name', 'wp_'.$choice.'_choice_link')
                ->first();
            $wp_link = $submission_data->config_value ?? '';

            // $data = getSubmissionWritingPrompt($submission->id);
        @endphp

        {{-- <form class="form" id="writing_prompt_form_{{$choice}}" method="post" action="{{url('admin/Submissions/update/WritingPrompt/'.$submission->id)}}">   --}}
            {{-- {{csrf_field()}} --}}
            <div class="card shadow">
                <div class="card-header">{{$value->eligibility_ype}} {{$cvalue}} [{{getProgramName($submission->{$choice.'_choice_program_id'})}}]</div>
                <div class="card-body">
                    <div class="">
                        @forelse($wp_data as $wp_value)
                            <div class="form-group row">
                                <label class="control-label col-12 col-md-12 font-weight-bold">{{$wp_value['writing_prompt'] or 'Writing Prompt'}}</label>
                                <div class="col-12 col-md-12">
                                    <textarea class="form-control" disabled>{{$wp_value['writing_sample'] or ''}}</textarea>
                                </div>
                            </div>
                        @endforeach

                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">Student Email</label>
                            <div class="col-12 col-md-12">
                                <input type="text" class="form-control" value="{{$submission->parent_email or ''}}">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">Student LInk</label>
                            <div class="col-12 col-md-12">
                                <span style="color: blue;">{{url('/WritingPrompt/').'/'.$wp_link}}</span>
                            </div>
                        </div>
                        <div class="input-group">
                            @if(isset($wp_entry))
                            {{-- <form id="frm_ws_print_{{$choice}}" method="get" action="{{url('admin/WritingPrompt/print')}}">
                                {{csrf_field()}}
                                <input type="hidden" name="submission_id" value="{{$submission->id}}">
                                <input type="hidden" name="program_id" value="{{$program_id}}"> --}}
                                <a href="{{url('admin/WritingPrompt/print/'.$submission->id.'/'.$program_id)}}" class="btn btn-sm btn-primary mr-10" title=""><i class="far fa-file-pdf"></i> Print Writing Sample</a>
                            {{-- </form> --}}
                                <a href="javascript:void(0);" data-s_id="{{$submission->id}}" data-p_id="{{$program_id}}" class="btn btn-sm btn-primary" title="" onclick="clearWritingPrompt(this)"><i class="fas fa-eraser"></i> Clear Writing Prompt</a>
                            @else
                                <a href="javascript:void(0);" data-s_id="{{$submission->id}}" data-s_choice="{{$choice}}" data-parent_email="{{$submission->parent_email or ''}}" class="btn btn-sm btn-primary" title="" onclick="sendWritingPromptMail(this)"><i class="far fa-paper-plane"></i> Resend Email</a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        {{-- </form> --}}
    @endforeach
@endif
