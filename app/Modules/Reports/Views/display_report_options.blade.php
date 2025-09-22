<div class="card-body">
    <form class="">
        <div class="form-group">
            <label for="">Enrollment Year : </label>
            <div class="">
                <select class="form-control custom-select" id="enrollment_option">
                    <option value="">Select Enrollment Year</option>
                    @foreach($enrollment as $key=>$value)
                        <option value="{{$value->id}}" @if($enrollment_id == $value->id) selected @endif>{{$value->school_year}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="">Report : </label>
            <div class="">
                @php $report_arr = Config::get('variables.reportArr') @endphp
				<select class="form-control custom-select" id="reporttype">
				    <option value="">Select Report</option>
				    @foreach($report_arr as $rk=>$rv)
				    	<option value="{{$rk}}" @if($selection == $rk) selected @endif>{{$rv}}</option>
				    @endforeach
				</select>
            </div>
        </div>
        <div class=""><a href="javascript:void(0);" onclick="showMissingReport()" title="Generate Report" class="btn btn-success generate_report">Generate Report</a></div>
    </form>
</div>