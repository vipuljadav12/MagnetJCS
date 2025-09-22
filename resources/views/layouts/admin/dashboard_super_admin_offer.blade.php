@extends('layouts.admin.app')
@section('title')
	Dashboard
@endsection
@section('styles')
<style type="text/css">
	.modal-body{
		max-height: 550px;
		overflow-y: auto !important;
		/*background-color: */
	}
    .dt-buttons{float: right !important; padding-bottom: 5px;}
</style>
@endsection
@section('content')
         <div class="card shadow">
                <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
                    <div class="page-title mt-5 mb-5">Offer Management Dashboard</div>
                    <div class="">
                        <select class="form-control custom-select w-250" id="switch_dashboard">
                            <option value="magnet" selected>Super Admin - Application</option>
                            <option value="mcpss">District Admin - Application</option>
                            <option value="superoffer" selected>Super Admin - Offer</option>
                            <option value="districtoffer">District Admin - Offer</option>
                        </select>

                        @if(count($versions_lists) > 0)
                            <select class="form-control custom-select w-250" onchange="process_dashboard(this.value)">
                                <option value="magnet" @if($version == 0) selected @endif>Process Selection</option>
                                @foreach($versions_lists as $key=>$value)
                                    <option value="{{$value->version}}" @if($version == $value->version) selected @endif>Process Waitlist v{{$value->version}} - {{getDateTimeFormat($value->created_at)}}</option>
                                @endforeach
                            </select>
                        @endif
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12 mb-20">
                    <div class="card shadow mb-0">
                        <div class="card-header">Offered and Accepted</div>
                        <div class="card-body">
                            <div class="card shadow mb-0">
                                <div class="card-header">
                                    <select class="form-control custom-select" id="change_program1">
                                        @foreach($programs as $key=>$value)
                                            <option value="{{$value->id}}" @if($program_id == $value->id) selected @endif>{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                    <canvas id="myChart1" width="2085" height="833" style="display: block; height: 667px; width: 1668px;" class="chartjs-render-monitor"></canvas>
                                    <div class="custom-legends" id="js-legend1"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-20">
                    <div class="card shadow mb-0">
                        <div class="card-header">Offered and Denied</div>
                        <div class="card-body">
                            <div class="card shadow h-100 mb-0">
                                <div class="card-header">
                                    <select class="form-control custom-select" id="change_program2">
                                        @foreach($programs as $key=>$value)
                                            <option value="{{$value->id}}" @if($program_id == $value->id) selected @endif>{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                    <canvas id="myChart2" width="2085" height="833" style="display: block; height: 667px; width: 1668px;" class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 mb-20">
                    <div class="card shadow mb-0">
                        <div class="card-header">Offered and Waitlisted</div>
                        <div class="card-body">
                            <div class="card shadow h-100 mb-0">
                                <div class="card-header">
                                    <select class="form-control custom-select" id="change_program3">
                                        @foreach($programs as $key=>$value)
                                            <option value="{{$value->id}}" @if($program_id == $value->id) selected @endif>{{$value->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="card-body"><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                                    <canvas id="myChart3" width="2085" height="833" style="display: block; height: 667px; width: 1668px;" class="chartjs-render-monitor"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


<!-- Modals end -->
@endsection
@section('scripts')
<script type="text/javascript" src="{{url('/resources/assets/admin/js/Chart.min.js')}}"></script>     

<script type="text/javascript">
	
	/* switch dashboard start */
	$('#switch_dashboard').change(function() {
		if ($(this).val() == "magnet") {
			window.location = "{{url('admin/magnet_dashboard')}}";
		}
	});
	/* switch dashboard end */

        /*Chart 1*/    
        var data1 = {
            labels: [{{implode(",", array_keys($OfferedAccepted))}}],
            datasets: [{
                data: [{{implode(",", array_values($OfferedAccepted))}}],
                backgroundColor: '#00ca70',
                borderWidth: 2
            }]
        };    
        var ctx1 = document.getElementById('myChart1').getContext('2d');
        var myChart1 = new Chart(ctx1, {
            type: 'bar',
            data: data1,
            options: {
                responsive: true,
                legend : {
                    position: 'top',
                    display: false
                },
                scales: {
                xAxes: [{
                    gridLines: false,
                    scaleLabel: {
                        display: true,
                        labelString: "{{($version > 0 ? date("F") : 'December')}}"
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        //stepSize: 2,
                        //max: 50
                    }
                }]
            },
            }
        });
        /*Chart 2*/
        var data2 = {
            labels: [{{implode(",", array_keys($OfferedDeclined))}}],
            datasets: [{
                data: [{{implode(",", array_values($OfferedDeclined))}}],
                backgroundColor: '#e95b54',
                borderWidth: 2
            }]
        };    
        var ctx2 = document.getElementById('myChart2').getContext('2d');
        var myChart2 = new Chart(ctx2, {
            type: 'bar',
            data: data2,
            options: {
                responsive: true,
                legend : {
                    position: 'top',
                    display: false
                },
                scales: {
                xAxes: [{
                    gridLines: false,
                    scaleLabel: {
                        display: true,
                        labelString: "{{($version > 0 ? date("F") : 'December')}}"
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        //stepSize: 2,
                        //max: 50
                    }
                }]
            },
            }
        });    
        /*Chart 3*/
        var data3 = {
            labels: [{{implode(",", array_keys($OfferedWaitlisted))}}],
            datasets: [{
                data: [{{implode(",", array_values($OfferedWaitlisted))}}],
                backgroundColor: '#ffbf00',
                borderWidth: 2
            }]
        };    
        var ctx3 = document.getElementById('myChart3').getContext('2d');
        var myChart3 = new Chart(ctx3, {
            type: 'bar',
            data: data3,
            options: {
                responsive: true,
                legend : {
                    position: 'top',
                    display: false
                },
                scales: {
                xAxes: [{
                    gridLines: false,
                    scaleLabel: {
                        display: true,
                        labelString: "{{($version > 0 ? date("F") : 'December')}}"
                    }
                }],
                yAxes: [{
                    ticks: {
                        min: 0,
                        //stepSize: 2,
                        //max: 50
                    }
                }]
            },
            }
        }); 

        document.getElementById("change_program1").onchange = function () {
        myChart1.destroy();
        var ctx1 = document.getElementById('myChart1').getContext('2d');
        var cc = $(this).val();
        $.ajax({
            type: 'get',
            dataType: 'JSON',
            url: "{{url('admin/superadmin/offer/chart1/'.$version)}}/"+cc,
            success: function(response) {
                var values = new Array();
                var dates = new Array();
                $.each(response, function(key, value) {
                      dates[dates.length] = key;
                      values[values.length] = value;
                });

                var data1 = {
                    labels: dates,
                    datasets: [{
                        data: values,
                        backgroundColor: '#00ca70',
                        borderWidth: 2
                    }]
                };  

                 myChart1 = new Chart(ctx1, {
                    type: 'bar',
                    data: data1,
                    options: {
                        responsive: true,
                        legend : {
                            position: 'top',
                            display: false
                        },
                        scales: {
                        xAxes: [{
                            gridLines: false,
                            scaleLabel: {
                                display: true,
                                labelString: "{{($version > 0 ? date("F") : 'December')}}"
                            }
                        }],
                        yAxes: [{
                            ticks: {
                                min: 0,
                                //stepSize: 2,
                                //max: 50
                            }
                        }]
                    },
                    }
                });
            }
        });

        }

        document.getElementById("change_program2").onchange = function () {
                myChart2.destroy();
                var ctx2 = document.getElementById('myChart2').getContext('2d');

                var cc = $(this).val();
                $.ajax({
                    type: 'get',
                    dataType: 'JSON',
                    url: "{{url('admin/superadmin/offer/chart2/'.$version)}}/"+cc,
                    success: function(response) {

                        var values = new Array();
                        var dates = new Array();
                        $.each(response, function(key, value) {
                              dates[dates.length] = key;
                              values[values.length] = value;
                        });

                        var data2 = {
                            labels: dates,
                            datasets: [{
                                data: values,
                                backgroundColor: '#00ca70',
                                borderWidth: 2
                            }]
                        };  

                        myChart2 = new Chart(ctx2, {
                            type: 'bar',
                            data: data2,
                            options: {
                                responsive: true,
                                legend : {
                                    position: 'top',
                                    display: false
                                },
                                scales: {
                                xAxes: [{
                                    gridLines: false,
                                    scaleLabel: {
                                        display: true,
                                        labelString: "{{($version > 0 ? date("F") : 'December')}}"
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        min: 0,
                                        //stepSize: 2,
                                        //max: 50
                                    }
                                }]
                            },
                            }
                        });
                    }
                });

        }

        document.getElementById("change_program3").onchange = function () {
                myChart3.destroy();
                var ctx3 = document.getElementById('myChart3').getContext('2d');

                var cc = $(this).val();
                $.ajax({
                    type: 'get',
                    dataType: 'JSON',
                    url: "{{url('admin/superadmin/offer/chart3/'.$version)}}/"+cc,
                    success: function(response) {

                        var values = new Array();
                        var dates = new Array();
                        $.each(response, function(key, value) {
                              dates[dates.length] = key;
                              values[values.length] = value;
                        });

                        var data3 = {
                            labels: dates,
                            datasets: [{
                                data: values,
                                backgroundColor: '#00ca70',
                                borderWidth: 2
                            }]
                        };  
 

                        myChart3 = new Chart(ctx3, {
                            type: 'bar',
                            data: data3,
                            options: {
                                responsive: true,
                                legend : {
                                    position: 'top',
                                    display: false
                                },
                                scales: {
                                xAxes: [{
                                    gridLines: false,
                                    scaleLabel: {
                                        display: true,
                                        labelString: "{{($version > 0 ? date("F") : 'December')}}"
                                    }
                                }],
                                yAxes: [{
                                    ticks: {
                                        min: 0,
                                        //stepSize: 2,
                                        //max: 50
                                    }
                                }]
                            },
                            }
                        });
                    }
                });

        }

        function process_dashboard(val)
        {
            document.location.href = "{{url('/')}}/admin/superadmin/offer/"+val;
        }
    

</script>
@endsection