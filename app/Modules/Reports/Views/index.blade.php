@extends('layouts.admin.app')
@section('title')
	Selection Report Master
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
            <div class=""><a class=" btn btn-secondary btn-sm" href="{{url('/admin/Reports/')}}" title="Go Back">Go Back</a></div>
        </div>
    </div>
    <div class="">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item"><a class="nav-link active" id="needs1-tab" data-toggle="tab" href="#needs1" role="tab" aria-controls="needs1" aria-selected="true">Selection Report Master</a></li>
            </ul>
            <div class="tab-content bordered" id="myTabContent">
                <div class="tab-pane fade show active" id="needs1" role="tabpanel" aria-labelledby="needs1-tab">
                    <ul class="nav nav-tabs" id="myTab1" role="tablist1">
                        @foreach($gradeTab as $value)
                            @if($value==$existGrade)
                                <li class="nav-item"><a class="nav-link active" id="grade1-tab" data-toggle="tab" href="#grade1" role="tab" aria-controls="grade1" aria-selected="true">Grade {{$value}}</a></li>
                            @else
                                <li class="nav-item"><a class="nav-link" href="{{url('admin/Reports/Selection/'.$application_id.'/'.$value)}}">Grade {{$value}}</a></li>
                            @endif
                        @endforeach
                    </ul>
                    <div class="tab-content bordered" id="myTabContent1">
                        <div class="tab-pane fade show active" id="grade1" role="tabpanel" aria-labelledby="grade1-tab">
                            <div class="">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="text-right mb-10 d-flex justify-content-end align-items-center">
                                            <input type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small"  id="hideRace" @if($settings->race == "Y") checked @endif />&nbsp;Hide Race&nbsp;&nbsp;<input type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small"  id="hideZone" @if($settings->zoned_school == "Y") checked @endif />&nbsp;Hide Zone School&nbsp;&nbsp;<input type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small"  id="hideCommittee" @if($settings->committee_score == "Y") checked @endif/>&nbsp;Hide Committee
                                            @if(count($test_scores_titles) > 0)&nbsp;&nbsp;<input type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small"  id="hideTestScore" @if($settings->test_scores == "Y") checked @endif/>&nbsp;Hide Test Scores
                                            @endif
                                            &nbsp;&nbsp;<input type="checkbox" class="js-switch js-switch-1 js-switch-xs status" data-size="Small"  id="hideGrade" @if($settings->grade == "Y") checked @endif />&nbsp;Hide Grade
                                            <div class="d-none" style="padding-left: 5px;"><a href="{{url('/CDI-All.xls')}}" class="btn btn-secondary">Export</a></div>
                                        </div>
                                        @php $config_subjects = Config::get('variables.subjects') @endphp
                                        <div class="table-responsive">
                                            <table class="table table-striped mb-0 w-100" id="datatable">
                                                <thead>
                                                    @if(count($year) > 0)
                                                        @php $rawspan = ' rowspan=2' @endphp
                                                    @else
                                                        @php $rawspan = "" @endphp
                                                    @endif
                                                    <tr>
                                                        <th class="align-middle text-center"{{$rawspan}}>Sub ID</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>Submission Status</th>
                                                        <th class="align-middle hiderace text-center"{{$rawspan}}>Race</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>Student Status</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>First Name</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>Last Name</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>Next Grade</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>Current School</th>
                                                        <th class="align-middle hidezone text-center"{{$rawspan}}>Zoned School</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>First Choice</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>Second Choice</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>Sibling ID</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>Lottery Number</th>
                                                        <th class="align-middle text-center"{{$rawspan}}>Priority</th>
                                                        @foreach ($year as $tyear => $tvalue)
                                                            <th class="align-middle text-center grade-col" colspan="{{count($subjects)}}">{{$tvalue}}</th>
                                                        @endforeach
                                                        <th class="align-middle text-center committee_score-col"{{$rawspan}}>
                                                        @if($preliminary_score)
                                                            Composite Score
                                                        @else
                                                            Committee Score
                                                        @endif
                                                        </th>
                                                        @foreach($test_scores_titles as $ts=>$tv)
                                                            <th class="align-middle text-center test_scores-col" colspan="2">{{$tv}}</th>
                                                        @endforeach
                                                    </tr>
                                                    @if(count($year) > 0 || count($test_scores_titles) > 0)
                                                        <tr>
                                                            @foreach ($year as $tyear => $tvalue)
                                                                @foreach($subjects as $key=>$value)
                                                                    <th class="align-middle text-center grade-col">{{$config_subjects[$value]}}</th>
                                                                @endforeach
                                                            @endforeach
                                                            @foreach($test_scores_titles as $ts=>$tv)
                                                                <th class="align-middle text-center test_scores-col">Scores</th>
                                                                <th class="align-middle text-center test_scores-col">Rank</th>
                                                            @endforeach
                                                        </tr>
                                                        
                                                    @endif
                                                    
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
                                                            @foreach ($year as $tyear => $tvalue)
                                                                @foreach($subjects as $sjkey=>$sjvalue)
                                                                    @foreach($value['score'] as $skey=>$svalue)
                                                                        @if($svalue['subject'] == $config_subjects[$sjvalue] && $svalue['academic_year'] == $tvalue)
                                                                            <td class="text-center grade-col">{{$svalue['grade']}}</td> 
                                                                        @endif
                                                                    @endforeach
                                                                @endforeach
                                                            @endforeach
                                                            <td class="text-center committee_score-col">
                                                                @if($preliminary_score)
                                                                    @php $class="alert-success" @endphp
                                                                    @php $comValue = $value['composite_score'] @endphp
                                                                @else
                                                                    @if($value['committee_score_status'] == "Pending")
                                                                        @php $class = "alert-warning" @endphp
                                                                        @if($value['committee_score'] != "NA")
                                                                        @php $comValue = '<i class="fas fa-exclamation"></i>' @endphp
                                                                        @else
                                                                        @php $class = "" @endphp
                                                                        @php $comValue = "NA" @endphp
                                                                        @endif
                                                                    @elseif($value['committee_score_status'] == "Fail")
                                                                        @php $class = "alert-danger" @endphp
                                                                        @php $comValue = $value['committee_score'] @endphp
                                                                    @else
                                                                        @php $class = "alert-success" @endphp
                                                                        @php $comValue = $value['committee_score'] @endphp
                                                                    @endif
                                                                @endif
                                                                <div class="alert1 {{$class}}">
                                                                    {!! $comValue !!}
                                                                </div>
                                                            </td>
                                                            @foreach($test_scores_titles as $ts=>$tv)
                                                                @php $tstScores = $value['test_scores'] @endphp
                                                                @if(isset($tstScores[$tv]))
                                                                    <td class="align-middle text-center test_scores-col">{{$tstScores[$tv]['value']}}</td>
                                                                    <td class="align-middle text-center test_scores-col">{{$tstScores[$tv]['score']}}</td>
                                                                @else
                                                                    <td class="align-middle text-center test_scores-col"></td>
                                                                    <td class="align-middle text-center test_scores-col"></td>
                                                                @endif
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

                                                             @foreach ($year as $tyear => $tvalue)
                                                                @foreach($subjects as $sjkey=>$sjvalue)
                                                                    @foreach($value['score'] as $skey=>$svalue)
                                                                        @if($svalue['subject'] == $config_subjects[$sjvalue] && $svalue['academic_year'] == $tvalue)
                                                                            <td class="text-center grade-col">{{$svalue['grade']}}</td> 
                                                                        @endif
                                                                    @endforeach
                                                                @endforeach
                                                            @endforeach

                                                            <td class="text-center committee_score-col">
                                                                @if($preliminary_score)
                                                                    @php $class="alert-success" @endphp
                                                                    @php $comValue = $value['composite_score'] @endphp
                                                                @else
                                                                    @if($value['committee_score_status'] == "Pending")
                                                                        @php $class = "alert-warning" @endphp
                                                                        @if($value['committee_score'] != "NA")
                                                                                @php $comValue = '<i class="fas fa-exclamation"></i>' @endphp
                                                                        @else
                                                                                @php $class = "" @endphp
                                                                                @php $comValue = "NA" @endphp
                                                                        @endif
                                                                    @elseif($value['committee_score_status'] == "Fail")
                                                                        @php $class = "alert-danger" @endphp
                                                                        @php $comValue = $value['committee_score'] @endphp
                                                                    @else
                                                                        @php $class = "alert-success" @endphp
                                                                        @php $comValue = $value['committee_score'] @endphp
                                                                    @endif
                                                                @endif
                                                                <div class="alert1 {{$class}}">
                                                                    {!! $comValue !!}
                                                                </div>
                                                            </td>

                                                            @foreach($test_scores_titles as $ts=>$tv)
                                                                @php $tstScores = $value['test_scores'] @endphp
                                                                @if(isset($tstScores[$tv]))
                                                                    <td class="align-middle text-center test_scores-col">{{$tstScores[$tv]['value']}}</td>
                                                                    <td class="align-middle text-center test_scores-col">{{$tstScores[$tv]['score']}}</td>
                                                                @else
                                                                    <td class="align-middle text-center test_scores-col"></td>
                                                                    <td class="align-middle text-center test_scores-col"></td>
                                                                @endif
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

        $("#hideCommittee").change(function(){
            if($(this).prop("checked") == true)
            {
                $('.committee_score-col').addClass("d-none");
                dtbl_submission_list.$('.committee_score-col').addClass("d-none");
                var update = "Y";
            }
            else
            {
                $('.committee_score-col').removeClass("d-none");
                dtbl_submission_list.$('.committee_score-col').removeClass("d-none");

                var update = "N";

            }
            $.ajax({
                    url : "{{url('/admin/Reports/setting/update/committee_score/')}}/"+update,
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

        $("#hideTestScore").change(function(){
            if($(this).prop("checked") == true)
            {
                $('.test_scores-col').addClass("d-none");
                dtbl_submission_list.$('.test_scores-col').addClass("d-none");
                var update = "Y";            
            }
            else
            {
                $('.test_scores-col').removeClass("d-none");
                dtbl_submission_list.$('.test_scores-col').removeClass("d-none");
                var update = "N";
            }
            $.ajax({
                    url : "{{url('/admin/Reports/setting/update/test_scores/')}}/"+update,
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

            @if($settings->committee_score == "Y") 
                $('.committee_score-col').addClass("d-none");
                dtbl_submission_list.$('.committee_score-col').addClass("d-none");
                 

            @endif

            @if($settings->test_scores == "Y") 
                $('.test_scores-col').addClass("d-none");
                dtbl_submission_list.$('.test_scores-col').addClass("d-none");
                 

            @endif   
        });    
	</script>

@endsection