<form class="form" id="submission_comment_form" method="post" action="{{url('admin/Submissions/store/comments/'.$submission->id)}}">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-header">Comments</div>
        <div class="card-body">
            <div class="form-group">
                <label for="">Comment : </label>
                <div class="">
                    <textarea name="comment" class="form-control"></textarea>
                    @if($errors->has('comment'))
                        <div class="error">{{$errors->first('comment')}}</div>
                    @endif
                </div>
            </div>
            <div class="form-group text-right">
                <button type="submit" class="btn btn-secondary" title="Submit">Submit</button>
            </div>
            <div class="border-top mt-30 pb-10">
            @isset($data['comments'])
                @foreach($data['comments'] as $value)
                    @php
                        $user_name = getUserName($value->user_id);
                        $explode_name = explode(" ", $user_name);
                        $name_initials = "";
                        foreach ($explode_name as $word) {
                            $name_initials .= $word[0] ?? '';
                        }
                    @endphp
                    <div class="d-flex mt-20 mb-0 card p-15 flex-row">
                        <div class="mr-20">
                            <div class="bg-secondary text-white rounded-circle comment-short-name">{{$name_initials}}</div>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between mb-10">
                                <div class="">{{$user_name}}</div>
                                <div class="">{{getDateTimeFormat($value->created_at)}}</div>
                            </div>
                            <div class="">{{$value->comment}}</div>
                            <div class="">{!! $value->submission_event !!}</div>

                        </div>
                    </div>
                @endforeach
            @endisset
            </div>
        </div>
    </div>
</form>