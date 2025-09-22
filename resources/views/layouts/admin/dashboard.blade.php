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
         @if((checkPermission(Auth::user()->role_id,'dashboard') == 1))
            <div class="">
                <select class="form-control custom-select w-200" id="switch_dashboard">
                    <option value="magnet">Super Admin - Application</option>
                    <option value="mcpss" selected>District Admin - Application</option>
                    <option value="superoffer">Super Admin - Offer</option>
                    <option value="districtoffer">District Admin - Offer</option>
                </select>
            </div>
        @endif
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
        <div class="card-header">Number of Applicants Per Program</div>
        <div class="card-body">
            <div class="table-responsive" style="height: 465px; overflow-y: auto;">
                <table class="table table-striped mb-0" id="tbl_applicants_each_program">
                    <thead>
                        <tr>
                            <th class="align-middle" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Program Name</th>
                            @isset($data['all_grades'])
                                @foreach($data['all_grades'] as $value)
                                    <th class="align-middle text-center w-90" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">{{$value->name or ''}}</th>
                                @endforeach
                            @endisset
                            <th class="align-middle text-center w-120" style="position: sticky; top: 0; background-color: #fff !important; z-index: 9999 !important">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $skip_programs = [];
                            $applicants_each_program = [];
                            foreach (getChoiceAry() as $choicekey => $choicetxt) {
                                if (!empty($data[$choicetxt.'_choice_grade_count'])) {
                                    $applicants_each_program[] = $data[$choicetxt.'_choice_grade_count'];
                                }   
                            }
                        @endphp
                        @isset($applicants_each_program)

                            @foreach($applicants_each_program as $grades_choice)
                                @foreach($grades_choice as $key => $value)
                                @if(in_array($key, $skip_programs) == false)
                                    @php $disp = $loop->parent->iteration @endphp
                                <tr>
                                    <td class="">{{getProgramName($key)}} - Choice {{$disp}}</td>
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
                                @if($loop->parent->iteration == 2 && isset($applicants_each_program[2][$key]))
                                <tr>
                                    <td class="">{{getProgramName($key)}} - Choice 3</td>
                                    @isset($data['all_grades'])
                                        @php
                                            $total_value = 0;
                                        @endphp
                                        @foreach($data['all_grades'] as $grade)
                                            @if(array_key_exists($grade->name, $applicants_each_program[2][$key]))
                                                @php
                                                    $total_value += $applicants_each_program[2][$key][$grade->name];
                                                @endphp
                                                <td class="text-center"><div class="text-center {{-- alert --}} alert-info p-10">{{$applicants_each_program[2][$key][$grade->name]}}</div>
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
                                @else
                                    <tr>
                                        <td class="">{{getProgramName($key)}} - Choice 3</td>
                                        @foreach($data['all_grades'] as $grade)
                                                <td class="text-center"><div class="text-center {{-- alert --}} alert-warning p-10">NA</div></td>
                                        @endforeach
                                        <td class="text-center"><div class="text-center {{-- alert --}} alert-success p-10">0</div></td>
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
        <div class="card-header">Number of Applicants Per Current School</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="tbl_home_zone_school">
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
                        @isset($data['zoned_school'])
                        @foreach($data['zoned_school'] as $school_name => $grades_data)
                            @php
                                $total_value = 0;
                            @endphp
                            <tr>
                                <td class="">{{$school_name}}</td>
                                @isset($data['all_grades'])
                                @foreach($data['all_grades'] as $grade)
                                    @if(array_key_exists($grade->name, $grades_data))
                                        @php
                                            $total_value += $grades_data[$grade->name];
                                        @endphp
                                        <td class="text-center"><div class="text-center alert-info p-10">{{$grades_data[$grade->name]}}</div></td>
                                    @else
                                        <td class="text-center"><div class="text-center alert-warning p-10">NA</div></td>
                                    @endif
                                @endforeach
                                @endisset
                                <td class="text-center"><div class="text-center alert-success p-10">{{$total_value}}</div></td>
                            </tr>
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
        <div class="card-header">Updated Racial Composition</div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped mb-0" id="tbl_updated_race_comp">
                    <thead>
                        <tr>
                            <th class="align-middle">Program Group</th>
                            <th class="text-center">Black</th>
                            <th class="text-center">White</th>
                            <th class="text-center">Other</th>
                            <th class="text-center">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($group_racial_composition as $key => $value)
                            @foreach($value as $rk => $rv)
                                @if(isset($rv['black']))
                                    <tr>
                                        <td>{{$rk}}</td>
                                        <td class="text-center">
                                            @if($rv['black'] > 0)
                                                {{number_format($rv['black']*100/$rv['total'], 2)}} ({{$rv['black']}})
                                            @else
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($rv['white'] > 0)
                                                {{number_format($rv['white']*100/$rv['total'], 2)}} ({{$rv['white']}})
                                            @else
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($rv['other'] > 0)
                                                {{number_format($rv['other']*100/$rv['total'], 2)}} ({{$rv['other']}})
                                            @else
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            {{$rv['total']}}
                                        </td>
                                    </tr>
                                @endif

                            @endforeach
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript" src="{{asset('resources/assets/admin/js/Chart.min.js')}}"></script> 
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>

<script type="text/javascript">

    
    $('#tbl_updated_race_comp').DataTable({
        paging: false,
        info: false,
        ordering: false,
        searching: false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Updated-Racial',
                text:'Export to Excel',
           }
        ]

    });

     $('#tbl_applicants_each_program').DataTable({
        paging: false,
        info: false,
        ordering: false,
        searching: false,
        dom: 'Bfrtip',
        buttons: [
            {
                extend: 'excelHtml5',
                title: 'Submissions-Count',
                text:'Export to Excel',
           }
        ]

    });

    $('#tbl_home_zone_school').DataTable({
        paging: false,
        info: false,
        ordering: false,
        sDom: 'lrtip',
        initComplete: function () {
            this.api().columns(0).every( function () {
                var column = this;
                var select = $('<select class="form-control col-3" style="float:right; margin-bottom: 10px;" id="filter_zoneschool"><option>Select Current School</option></select>')
                    .prependTo( $("#tbl_home_zone_school").parent())
                    .on( 'change', function () {
                       var term = $(this).val();
 
                        column
                            .search( term ? '^'+term+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    var selected = '';
                    if (j==0) {
                        //selected = 'selected';
                    }
                    var tmptxt = d.replace('<div class="alert alert-info">','');
                     tmptxt = tmptxt.replace('</div>','');
                    select.append( '<option '+selected+' value="'+tmptxt+'">'+tmptxt+'</option>' )
                } );
            } );
            $('#filter_zoneschool').trigger('change');
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
    var astatus = <?php echo json_encode($data['submission_status'] ?? []); ?>;
    var status_keys = Object.keys(astatus);
    var status_values = Object.values(astatus);

    var bg_colors_1 = [];

    var default_bg_colors_2 = ['#117899','#0d3c55','#c02e1d','#d94e1f','#f16c20','#ef8b2c','#ecaa38'];
    var total_rstatus_value = 0;
    $.each(status_values, function(key, value){
        total_rstatus_value += parseInt(value);
        if(default_bg_colors_2.length > 0) {
            bg_colors_1.push(default_bg_colors_2.shift());
        }else{
            bg_colors_1.push(dynamicColors());
        }
    });

    // for (var i=0; i<=1; i++){
    //  bg_colors_1.push(dynamicColors()); 
    // }
   var data1 = {
        labels: status_keys,
        datasets: [{
            label: '',
            data: status_values,
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
    Chart.pluginService.register({
      beforeDraw: function(chart) {
        var width = chart.chart.width,
            height = chart.chart.height,
            ctx = chart.chart.ctx;
        ctx1.restore();
        var fontSize = (height / 114).toFixed(2);
        ctx1.font = fontSize + "em Open Sans";
        ctx1.textBaseline = "middle";
        var text = total_rstatus_value,
            textX = Math.round((width - ctx1.measureText(text).width) / 2),
            textY = height / 2;
        ctx1.fillText(text, textX, textY);
        ctx1.save();
      }
    });      
    // center text
    

    /*Chart 2*/
    var race = <?php echo json_encode($data['race'] ?? []); ?>;
    var race_keys = Object.keys(race);
    var race_values = Object.values(race);
    var bg_colors_2 = [];
    var default_bg_colors_2 = ['#117899','#0d3c55','#c02e1d','#d94e1f','#f16c20','#ef8b2c','#ecaa38'];
    // var default_bg_colors_2 = ['#1395ba','#117899','#0f5b78','#0d3c55','#c02e1d','#d94e1f','#f16c20','#ef8b2c','#ecaa38'];
    var total_race_value = 0;
    $.each(race_values, function(key, value){
        total_race_value += parseInt(value);
        if(default_bg_colors_2.length > 0) {
            bg_colors_2.push(default_bg_colors_2.shift());
        }else{
            bg_colors_2.push(dynamicColors());
        }
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