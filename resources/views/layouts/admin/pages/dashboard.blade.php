@extends('layouts.admin.app')
@section('title')
	Dashboard
@endsection
@section('styles')
	<link rel="stylesheet" href="{{asset('resources/assets/admin/css/Chart.min.css')}}"> 
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Dashboard</div>
        <div class="">
            <select class="form-control custom-select w-200" id="switch_dashboard">
                <option value="magnet">Magnet</option>
                <option value="mcpss" selected>MCPSS</option>
            </select>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
        <div class="border shadow p-20 text-center">
            <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="far fa-address-card"></i></div>
            <div class="font-16 font-weight-bold">Total Number of Applicants Per Enrollment Period</div>
            <hr class="border-3 max-width-100">
            <div class="font-24 font-weight-bold">{{$data['total_applicants_per_enrollment_period'] or 0}}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
        <div class="border shadow p-20 text-center">
            <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="far fa-address-book"></i></div>
            <div class="font-16 font-weight-bold">Total Number of Applicants Per Application Period</div>
            <hr class="border-3 max-width-100">
            <div class="font-24 font-weight-bold">{{$data['total_applicants_per_application_period'] or 0}}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
        <div class="border shadow p-20 text-center">
            <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="fas fa-user-check"></i></div>
            <div class="font-16 font-weight-bold">Total Number of Current Students Per Application Period</div>
            <hr class="border-3 max-width-100">
            <div class="font-24 font-weight-bold">{{$data['total_current_students_per_application_period'] or 0}}</div>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-md-4 col-lg-3 mb-30">
        <div class="border shadow p-20 text-center">
            <div class="border border-3 border-secondary rounded-circle d-inline-block w-90 h-90 d-flex align-items-center justify-content-center ml-auto mr-auto mb-20 font-28 font-weight-bold"><i class="fas fa-user-times"></i></div>
            <div class="font-16 font-weight-bold">Total Number of Non Current Students Per Application Period</div>
            <hr class="border-3 max-width-100">
            <div class="font-24 font-weight-bold">{{$data['total_noncurrent_students_per_application_period'] or 0}}</div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-12 col-lg-6 mb-20">
        <div class="card shadow h-100 mb-0">
            <div class="card-header">Submission Status</div>
            <div class="card-body">
                <canvas id="myChart1" width="100%" height="70"></canvas>
                <div class="custom-legends" id="js-legend1"></div>
            </div>
        </div>
    </div>
    <div class="col-12 col-lg-6 mb-20">
        <div class="card shadow h-100 mb-0">
            <div class="card-header">Applicants by Race</div>
            <div class="card-body">
                <canvas id="myChart2" width="100%" height="70"></canvas>
                <div class="custom-legends" id="js-legend2"></div>
            </div>
        </div>
    </div>
</div>
<div class="">
    <div class="card shadow">
        <div class="card-header">Number of Applicants for Each Program</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th class="align-middle">Program Name</th>
                            @isset($data['all_grades'])
                            	@foreach($data['all_grades'] as $value)
                            		<th class="align-middle text-center w-90">{{$value->name or ''}}</th>
                            	@endforeach
                            @endisset
                            <th class="align-middle text-center w-120">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                    	@php
                    		$skip_programs = [];
                    		$applicants_each_program = [];
                    		if (!empty($data['first_choice_grade_count'])) {
                    			$applicants_each_program[] = $data['first_choice_grade_count'];
                    		}
                    		if (!empty($data['second_choice_grade_count'])) {
                    			$applicants_each_program[] = $data['second_choice_grade_count'];
                    		}
                    	@endphp
                    	@isset($applicants_each_program)
                    		@foreach($applicants_each_program as $grades_choice)
                    			@foreach($grades_choice as $key => $value)
                    			@if(in_array($key, $skip_programs) == false)
		                        <tr>
		                            <td class="">{{getProgramName($key)}} - Choice 
		                            	@if($loop->parent->iteration == 1)
		                            	1
		                            	@else
		                            	2
		                            	@endif
		                            </td>
		                            @isset($data['all_grades'])
		                            	@php
	                            			$total_value = 0;
	                            		@endphp
		                            	@foreach($data['all_grades'] as $grade)
			                            	@if(array_key_exists($grade->name, $value))
			                            		@php
			                            			$total_value += $value[$grade->name];
			                            		@endphp
			                            		<td class="text-center"><div class="text-center alert-info p-10">{{$value[$grade->name]}}</div></td>	
			                            	@else
			                            		<td class="text-center"><div class="text-center alert-warning p-10">NA</div></td>
			                            	@endif

		                            	@endforeach
		                            @endisset
		                            <td class="text-center"><div class="text-center alert-success p-10">{{$total_value}}</div></td>
		                        </tr>
		                        @endif
		                        @if($loop->parent->iteration == 1 && isset($applicants_each_program[1][$key]))
		                        <tr>
		                            <td class="">{{getProgramName($key)}} - Choice 2</td>
		                            @isset($data['all_grades'])
		                            	@php
	                            			$total_value = 0;
	                            		@endphp
		                            	@foreach($data['all_grades'] as $grade)
											@if(array_key_exists($grade->name, $applicants_each_program[1][$key]))
												@php
			                            			$total_value += $applicants_each_program[1][$key][$grade->name];
			                            		@endphp
			                            		<td class="text-center"><div class="text-center {{-- alert --}} alert-info p-10">{{$applicants_each_program[1][$key][$grade->name]}}</div>
			                            		</td>
			                            	@else
			                            		<td class="text-center"><div class="text-center {{-- alert --}} alert-warning p-10">NA</div></td>
			                            	@endif
		                            	@endforeach
		                            	@php
		                            		array_push($skip_programs, $key);
										@endphp	
		                            @endisset	                    	
	                            	
		                            <td class="text-center"><div class="text-center {{-- alert --}} alert-success p-10">{{$total_value}}</div></td>
		                        </tr>
		                        @endif

                        		@endforeach
                        	@endforeach
                        @endisset
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<div class="">
    <div class="card shadow">
        <div class="card-header">Number of Applicants Per Home Zone School</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th class="align-middle">Program Name</th>
                            <th class="align-middle text-center w-90">K</th>
                            <th class="align-middle text-center w-90">1</th>
                            <th class="align-middle text-center w-90">2</th>
                            <th class="align-middle text-center w-90">3</th>
                            <th class="align-middle text-center w-90">4</th>
                            <th class="align-middle text-center w-90">5</th>
                            <th class="align-middle text-center w-90">6</th>
                            <th class="align-middle text-center w-90">7</th>
                            <th class="align-middle text-center w-90">8</th>
                            <th class="align-middle text-center w-120">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="">Allentown Elementary</td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">201</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">221</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">301</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">401</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">501</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">512</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-success p-10">1523</div></td>
                        </tr>
                        <tr>
                            <td class="">Austin Elementary</td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">641</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">545</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">546</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">531</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">541</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">761</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-success p-10">1742</div></td>
                        </tr>
                        <tr>
                            <td class="">Booth Elementary</td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">767</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">731</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">888</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">699</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">681</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">651</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-success p-10">1156</div></td>
                        </tr>
                        <tr>
                            <td class="">Alba Middle</td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">124</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">135</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">164</div></td>
                            <td class="text-center"><div class="text-center alert alert-success p-10">1464</div></td>
                        </tr>
                        <tr>
                            <td class="">Burns Middle</td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">476</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">574</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">564</div></td>
                            <td class="text-center"><div class="text-center alert alert-success p-10">1564</div></td>
                        </tr>
                        <tr>
                            <td class="">Calloway-Smith Middle</td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">321</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">512</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">513</div></td>
                            <td class="text-center"><div class="text-center alert alert-success p-10">1987</div></td>
                        </tr>
                        <tr>
                            <td class="">Causey Middle</td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-warning p-10">NA</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">813</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">555</div></td>
                            <td class="text-center"><div class="text-center alert alert-info p-10">546</div></td>
                            <td class="text-center"><div class="text-center alert alert-success p-10">1645</div></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{asset('resources/assets/admin/js/Chart.min.js')}}"></script> 

<script type="text/javascript">
	$('#switch_dashboard').change(function() {
        if ($(this).val() == "magnet") {
            window.location = "{{url('admin/magnet_dashboard')}}";
        }
    });

	var dynamicColors = function() {
	    var r = Math.floor(Math.random() * 255);
	    var g = Math.floor(Math.random() * 255);
	    var b = Math.floor(Math.random() * 255);
	    return "rgb(" + r + "," + g + "," + b + ")";
	}

	/* chart scripting start */
	/*Chart 1*/    
	var active_submissions = parseInt('{{$data['active_submissions'] or 0}}');
	var pending_submissions = parseInt('{{$data['pending_submissions'] or 0}}');
	var bg_colors_1 = [];
	for (var i=0; i<=1; i++){
		bg_colors_1.push(dynamicColors()); 
	}
	var data1 = {
	    labels: ['Active','Pending'],
	    datasets: [{
	        data: [active_submissions, pending_submissions],
	        backgroundColor: bg_colors_1,
	        borderWidth: 2
	    }]
	};    
	var ctx1 = document.getElementById('myChart1').getContext('2d');
	var myChart1 = new Chart(ctx1, {
	    type: 'doughnut',
	    data: data1,
	    options: {
	        responsive: true,
	        legend : {
	            display: false
	        },
	    }
	});
	//legends
	document.getElementById('js-legend1').innerHTML = myChart1.generateLegend();    
	// center text
	Chart.pluginService.register({
	  beforeDraw: function(chart) {
	    var width = chart.chart.width,
	        height = chart.chart.height,
	        ctx = chart.chart.ctx;
	    ctx1.restore();
	    var fontSize = (height / 114).toFixed(2);
	    ctx1.font = fontSize + "em Open Sans";
	    ctx1.textBaseline = "middle";
	    var text = active_submissions + pending_submissions,
	        textX = Math.round((width - ctx1.measureText(text).width) / 2),
	        textY = height / 2;
	    ctx1.fillText(text, textX, textY);
	    ctx1.save();
	  }
	});

	/*Chart 2*/
	var race = <?php echo json_encode($data['race'] ?? []); ?>;
	var race_keys = Object.keys(race);
	var race_values = Object.values(race);
	var bg_colors_2 = [];
	var total_race_value = 0;
	$.each(race_values, function(key, value){
		total_race_value += parseInt(value);
		bg_colors_2.push(dynamicColors());
	});

	var data2 = {
	    labels: race_keys,
	    datasets: [{
	        label: '',
	        data: race_values,
	        backgroundColor: bg_colors_2,
	        borderWidth: 2
	    }]
	}    
	var ctx2 = document.getElementById('myChart2').getContext('2d');
	var myChart2 = new Chart(ctx2, {
	    type: 'doughnut',
	    data: data2,
	    options: {
	        responsive: true,
	        legend : {
	            display: false
	        }
	    }
	});    
	//legends
	document.getElementById('js-legend2').innerHTML = myChart2.generateLegend();    
	//// center text
	Chart.pluginService.register({
	  beforeDraw: function(chart) {
	    var width = chart.chart.width,
	        height = chart.chart.height,
	        ctx = chart.chart.ctx;
	    ctx2.restore();
	    var fontSize = (height / 114).toFixed(2);
	    ctx2.font = fontSize + "em Open Sans";
	    ctx2.textBaseline = "middle";
	    var text = total_race_value,
	        textX = Math.round((width - ctx2.measureText(text).width) / 2),
	        textY = height / 2;
	    ctx2.fillText(text, textX, textY);
	    ctx2.save();
	  }
	});    
	/* chart scripting end */

</script>
@endsection