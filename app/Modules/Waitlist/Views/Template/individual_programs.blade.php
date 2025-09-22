<div class="tab-pane fade show active" id="preview03" role="tabpanel" aria-labelledby="preview03-tab">
    <div class="form-group">
                                        <label for="">Add a Program to Individual Process : </label>
                                        <div class="">
                                            <select class="form-control custom-select" onchange="showProgramsWaitlist(this.value)">
                                                <option value="">Choose an option</option>
                                                @foreach($programs as $key=>$value)
                                                    @php $tmp = explode(",", $value->grade_lavel) @endphp
                                                    @foreach($tmp as$k=>$v)
                                                        <option value="{{$value->id.'--'.$v}}">{{$value->name." - Grade ".$v}}</option>
                                                    @endforeach
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <div id="responseprogram">
                                    </div>
                                    <div id="waitingprograms1" class="d-none">
                                    </div>

</div>

