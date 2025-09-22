<style type="text/css">
    .mailpreview {
        padding: 26px;
        border: 2px solid #ccc;
        background: #fff !important;
        height: 500px;
        overflow: scroll;
    }
</style>
<form class="form" id="manual_grade_form" method="post" action="{{url('admin/Submissions/store/manual/gradechange/'.$submission->id)}}">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-header">Manual Process</div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-xl-6">
                        <div class="form-group col-12">
                            <label for="">Select Next Grade : </label>
                            <div class="">
                                <select class="form-control custom-select" name="manual_next_grade" id="manual_next_grade">
                                    @foreach($nxt_grades as $value)
                                        <option value="{{$value}}" @if($value == $submission->next_grade) selected="" @endif>{{$value}}</option> 
                                    @endforeach
                                </select>
                            </div>
                        </div>

                </div>
            </div> 

                <div class="form-group text-right">
                    <button type="submit" class="btn btn-secondary" title="Submit">Submit</button>
                </div>

            
        </div>
    </div>
</form>