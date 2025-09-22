@extends('layouts.admin.app')
@section('title')
	Report Master
@endsection
@section('content')
<style type="text/css">
    .alert1 {
    position: relative;
    padding: 0.75rem 1.25rem;
    margin-bottom: 1rem;
    border: 1px solid transparent;
        border-top-color: transparent;
        border-right-color: transparent;
        border-bottom-color: transparent;
        border-left-color: transparent;
    border-radius: 0.25rem;
}
.dt-buttons {position: absolute !important;}
.w-50{width: 50px !important}
.content-wrapper.active {z-index: 9999 !important}
</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Report</div>
        </div>
    </div>
    <div class="">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item"><a class="nav-link active" id="needs1-tab" data-toggle="tab" href="#needs1" role="tab" aria-controls="needs1" aria-selected="true">Late Submission Application Process Report</a></li>
            </ul>
            <div class="tab-content bordered" id="myTabContent">
                <div class="tab-pane fade show active" id="needs1" role="tabpanel" aria-labelledby="needs1-tab">
                    <ul class="nav nav-tabs" id="myTab1" role="tablist1">
                        @foreach($gradeTab as $value)
                            @if($value==$existGrade)
                                <li class="nav-item"><a class="nav-link active" id="grade1-tab" data-toggle="tab" href="#grade1" role="tab" aria-controls="grade1" aria-selected="true">Grade {{$value}}</a></li>
                            @else
                                <li class="nav-item"><a class="nav-link" href="{{url('admin/Reports/late_submission/index/'.$value)}}">Grade {{$value}}</a></li>
                            @endif
                        @endforeach
                    </ul>
                    <div class="tab-content bordered" id="myTabContent1">
                        <div class="tab-pane fade show active" id="grade1" role="tabpanel" aria-labelledby="grade1-tab">
                            <div class="">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="text-right mb-10 d-flex justify-content-end align-items-center">
                                            <input type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small"  id="hideRace" @if($settings->race == "Y") checked @endif />&nbsp;Hide Race&nbsp;&nbsp;<input type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small"  id="hideZone" @if($settings->zoned_school == "Y") checked @endif />&nbsp;Hide Zone School&nbsp;&nbsp;<input type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small"  id="hideCDI" @if($settings->cdi == "Y") checked @endif/>&nbsp;Hide CDI&nbsp;&nbsp;<input type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small"  id="hideGrade" @if($settings->grade == "Y") checked @endif />&nbsp;Hide Grade
                                            <div class="d-none" style="padding-left: 5px;"><a href="{{url('/CDI-All.xls')}}" class="btn btn-secondary">Export</a></div>
                                        </div>
                                        @php $config_subjects = Config::get('variables.subjects') @endphp
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0 w-100" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th class="align-middle text-center">Sub ID</th>
                                                        <th class="align-middle text-center">Submission Status</th>
                                                        <th class="align-middle hiderace text-center">Race</th>
                                                        <th class="align-middle text-center">Student Status</th>
                                                        <th class="align-middle text-center">First Name</th>
                                                        <th class="align-middle text-center">Last Name</th>
                                                        <th class="align-middle text-center">MCPSS Employee</th>
                                                        <th class="align-middle text-center">Next Grade</th>
                                                        <th class="align-middle text-center">Current School</th>
                                                        <th class="align-middle hidezone text-center">Zoned School</th>
                                                        <th class="align-middle text-center">First Choice</th>
                                                        <th class="align-middle text-center">Second Choice</th>
                                                        <th class="align-middle text-center">Sibling ID</th>
                                                        <th class="align-middle text-center">Lottery Number</th>
                                                        <th class="align-middle text-center">Priority</th>
                                                        <th class="align-middle text-center">Late Submission</th>
                                                        @foreach($subjects as $sbjct)
                                                           <!-- foreach($terms as $term) -->
                                                                <th class="align-middle grade-col text-center">{{$config_subjects[$sbjct]}}</th>
                                                           <!--  endforeach -->
                                                        @endforeach
                                                        <th class="align-middle cdi-col text-center">B Info</th>
                                                        <th class="align-middle cdi-col text-center">C Info</th>
                                                        <th class="align-middle cdi-col text-center">D Info</th>
                                                        <th class="align-middle cdi-col text-center">E Info</th>
                                                        <th class="align-middle cdi-col text-center">Susp</th>
                                                        <th class="align-middle cdi-col text-center"># Days Susp</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($firstdata as $key=>$value)
                                                        <tr>
                                                            <td class="">{{$value['id']}}</td>
                                                            <td class="text-center">{{$value['submission_status']}}</td>
                                                            <td class="hiderace">{{$value['race']}}</td>
                                                            <td class="">
                                                                @if($value['student_id'] != '')
                                                                    Current
                                                                @else
                                                                    New
                                                                @endif
                                                            </td>
                                                            <td class="">{{$value['first_name']}}</td>
                                                            <td class="">{{$value['last_name']}}</td>
                                                            <td class="text-center">
                                                                @if($value['magnet_employee'] == "Yes")
                                                                    @if($value['magnet_program_employee'] == "Y")
                                                                        <div class="alert1 alert-success p-10 text-center">Yes</div>
                                                                    @else
                                                                         <div class="alert1 alert-danger p-10 text-center">No</div>
                                                                    @endif
                                                                @else
                                                                        -
                                                                @endif
                                                            </td>
                                                            <td class="text-center">{{$value['next_grade']}}</td>
                                                            <td class="">{{$value['current_school']}}</td>
                                                            <td class="hidezone">{{$value['zoned_school']}}</td>
                                                            <td class="">{{$value['first_program']}}</td>
                                                            <td class="text-center">{{$value['second_program']}}</td>
                                                            <td class="">
                                                                @if($value['first_sibling'] != '')
                                                                    <div class="alert1 alert-success p-10 text-center">{{$value['first_sibling']}}</div>
                                                                @else
                                                                    <div class="alert1 alert-warning p-10 text-center">NO</div>
                                                                @endif
                                                            </td>
                                                            <td class="">{{$value['lottery_number']}}</td>
                                                            <td class="text-center">
                                                                <div class="alert1 alert-success">
                                                                    {{$value['rank']}}
                                                                </div>
                                                            </td>
                                                            <td class="text-center">{{$value['late_submission']}}</td> 
                                                            @foreach($value['score'] as $skey=>$sbjct)
                                                                @php $marks = 0 @endphp
                                                                @php $class = "" @endphp
                                                                @foreach($terms as $term)
                                                                        @if(isset($sbjct[$term]))
                                                                            @if($sbjct[$term] != "")
                                                                                
                                                                                    @if($value['grade_status'] == "Pass")
                                                                                        @php $class = "alert1 alert-success" @endphp
                                                                                    @else
                                                                                        @php $class = "alert1 alert-danger" @endphp
                                                                                    @endif
                                                                                
                                                                                
                                                                                    @php $marks = $sbjct[$term] @endphp
                                                                                
                                                                            @endif
                                                                        @else
                                                                            @if($marks == 0)
                                                                                @php $class = "" @endphp
                                                                                @php $marks = "NA" @endphp
                                                                            @endif
                                                                        @endif
                                                                @endforeach

                                                                 <td class="grade-col text-center">
                                                                    <div class="{{$class}}">{{$marks}}</div>
                                                                </td>
                                                            @endforeach

                                                            @foreach($value['cdi'] as $vkey=>$vcdi)

                                                                <td class="cdi-col text-center">

                                                                        @if($value['cdi'][$vkey] != "" || $value['cdi'][$vkey] == 0)
                                                                            @if(isset($setCDIEligibilityData[$value['first_choice']][$vkey]))
                                                                                @if($value['cdi_status'] == "Pass")
                                                                                     @php $class = "alert1 alert-success" @endphp
                                                                                @else
                                                                                     @php $class = "alert1 alert-danger" @endphp
                                                                                 @endif
                                                                            @else
                                                                                @php $class = "alert1 alert-warning" @endphp
                                                                            @endif
                                                                            <div class="{{$class}}">
                                                                                {{$value['cdi'][$vkey]}}
                                                                            </div>
                                                                        
                                                                        @endif                                                                    
                                                                </td>
                                                            @endforeach
                                                        </tr>
                                                    @endforeach
                                                    @foreach($seconddata as $key=>$value)
                                                        <tr>
                                                            <td class="">{{$value['id']}}</td>
                                                            <td class="text-center">{{$value['submission_status']}}</td>
                                                            <td class="hiderace">{{$value['race']}}</td>
                                                            <td class="">
                                                                @if($value['student_id'] != '')
                                                                    Current
                                                                @else
                                                                    New
                                                                @endif
                                                            </td>
                                                            <td class="">{{$value['first_name']}}</td>
                                                            <td class="">{{$value['last_name']}}</td>
                                                             <td class="text-center">
                                                                @if($value['magnet_employee'] == "Yes")
                                                                    @if($value['magnet_program_employee'] == "Y")
                                                                        <div class="alert1 alert-success p-10 text-center">Yes</div>
                                                                    @else
                                                                         <div class="alert1 alert-danger p-10 text-center">No</div>
                                                                    @endif
                                                                @else
                                                                        -
                                                                @endif
                                                            </td>
                                                            <td class="text-center">{{$value['next_grade']}}</td>
                                                            <td class="">{{$value['current_school']}}</td>
                                                            <td class="hidezone">{{$value['zoned_school']}}</td>
                                                            <td class="">{{$value['first_program']}}</td>
                                                            <td class="text-center">{{$value['second_program']}}</td>
                                                            <td class="">
                                                                @if($value['second_sibling'] != '')
                                                                    <div class="alert1 alert-success p-10 text-center">{{$value['second_sibling']}}</div>
                                                                @else
                                                                    <div class="alert1 alert-warning p-10 text-center">NO</div>
                                                                @endif

                                                            </td>
                                                            <td class="">{{$value['lottery_number']}}</td>
                                                             <td class="text-center">
                                                                <div class="alert1 alert-success">
                                                                    {{$value['rank']}}
                                                                </div>
                                                            </td>
                                                            <td class="text-center">{{$value['late_submission']}}</td>
                                                             @foreach($value['score'] as $skey=>$sbjct)
                                                                @php $marks = 0 @endphp
                                                                @php $class = "" @endphp
                                                                @foreach($terms as $term)
                                                                        @if(isset($sbjct[$term]))
                                                                            @if($sbjct[$term] != "")
                                                                                
                                                                                    @if($value['grade_status'] == "Pass")
                                                                                        @php $class = "alert1 alert-success" @endphp
                                                                                    @else
                                                                                        @php $class = "alert1 alert-danger" @endphp
                                                                                    @endif
                                                                                
                                                                                    @php $marks = $sbjct[$term] @endphp
                                                                                
                                                                            @endif
                                                                        @else
                                                                            @if($marks == 0)
                                                                                @php $class = "" @endphp
                                                                                @php $marks = "NA" @endphp
                                                                            @endif
                                                                        @endif
                                                                @endforeach

                                                                 <td class="grade-col text-center">
                                                                    <div class="{{$class}}">{{$marks}}</div>
                                                                </td>
                                                            @endforeach

                                                            @foreach($value['cdi'] as $vkey=>$vcdi)
                                                                <td class="cdi-col text-center">
                                                                    
                                                                        @if(isset($setCDIEligibilityData[$value['second_choice']][$vkey]))
                                                                            @if($value['grade_status'] == "Pass")
                                                                                 @php $class = "alert1 alert-success" @endphp
                                                                            @else
                                                                                 @php $class = "alert1 alert-danger" @endphp
                                                                             @endif
                                                                        @else
                                                                            @php $class = "alert1 alert-warning" @endphp
                                                                        @endif
                                                                        <div class="{{$class}}">
                                                                            {{$value['cdi'][$vkey]}}
                                                                        </div>
                                                                    
                                                                </td>

                                                            @endforeach

                                                        </tr>
                                                    @endforeach

                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
@endsection
@section('scripts')
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<!--<script src="{{url('/resources/assets/admin')}}/js/bootstrap/jszip.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/pdfmake.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/vfs_fonts.js"></script>-->
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>

	<script type="text/javascript">
		//$("#datatable").DataTable({"aaSorting": []});
        var dtbl_submission_list = $("#datatable").DataTable({"aaSorting": [],
            "bSort" : false,
             "dom": 'Bfrtip',
             "autoWidth": true,
            // "scrollX": true,
             buttons: [
                    {
                        extend: 'excelHtml5',
                        title: 'Reports',
                        text:'Export to Excel',
                        //Columns to export
                        exportOptions: {
                                columns: "thead th:not(.d-none)"
                        }
                    }
                ]
            });

        $("#hideGrade").change(function(){
            if($(this).prop("checked") == true)
            {
                $('.grade-col').addClass("d-none");
                dtbl_submission_list.$('.grade-col').addClass("d-none");
                var update = "Y";
            }
            else
            {
                $('.grade-col').removeClass("d-none");
                dtbl_submission_list.$('.grade-col').removeClass("d-none");
                var update = "N";
            }
            $.ajax({
                    url : "{{url('/admin/Reports/setting/update/grade/')}}/"+update,
                    type: "GET"
            });
        })

        $("#hideCDI").change(function(){
            if($(this).prop("checked") == true)
            {
                $('.cdi-col').addClass("d-none");
                dtbl_submission_list.$('.cdi-col').addClass("d-none");
                var update = "Y";
            }
            else
            {
                $('.cdi-col').removeClass("d-none");
                dtbl_submission_list.$('.cdi-col').removeClass("d-none");

                var update = "N";

            }
            $.ajax({
                    url : "{{url('/admin/Reports/setting/update/cdi/')}}/"+update,
                    type: "GET"
            });

        })        

        $("#hideRace").change(function(){
            if($(this).prop("checked") == true)
            {
                $('.hiderace').addClass("d-none");
                dtbl_submission_list.$('.hiderace').addClass("d-none");

                var update = "Y";        
            }
            else
            {
                $('.hiderace').removeClass("d-none");
                dtbl_submission_list.$('.hiderace').removeClass("d-none");
                var update = "N";
            }
            $.ajax({
                    url : "{{url('/admin/Reports/setting/update/race/')}}/"+update,
                    type: "GET"
            });
        })        

        $("#hideZone").change(function(){
            if($(this).prop("checked") == true)
            {
                $('.hidezone').addClass("d-none");
                dtbl_submission_list.$('.hidezone').addClass("d-none");
                var update = "Y";            
            }
            else
            {
                $('.hidezone').removeClass("d-none");
                dtbl_submission_list.$('.hidezone').removeClass("d-none");
                var update = "N";
            }
            $.ajax({
                    url : "{{url('/admin/Reports/setting/update/zoned_school/')}}/"+update,
                    type: "GET"
            });
        })

        $(document).ready(function(){
            var hideArr = new Array();
            @if($settings->race == "Y") 
                $('.hiderace').addClass("d-none");
               dtbl_submission_list.$('.hiderace').addClass("d-none");

            @endif       

            @if($settings->zoned_school == "Y")         
                $('.hidezone').addClass("d-none");
               dtbl_submission_list.$('.hidezone').addClass("d-none");

            @endif       

            @if($settings->grade == "Y") 
                $('.grade-col').addClass("d-none");
                dtbl_submission_list.$('.grade-col').addClass("d-none");

            @endif       

            @if($settings->cdi == "Y") 
                $('.cdi-col').addClass("d-none");
                dtbl_submission_list.$('.cdi-col').addClass("d-none");
                 

            @endif   
        });    
	</script>

@endsection