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
            <div class="page-title mt-5 mb-5">Process Selection</div>
        </div>
    </div>

    {{-- Group Updated Racial Composition  
    --}}
    @if(count($group_racial_composition) > 0)
    <style>
        .collapsible-link::before {
            content: '';
            width: 14px;
            height: 2px;
            background: #333;
            position: absolute;
            top: calc(50% - 1px);
            right: 1rem;
            display: block;
            transition: all 0.3s;
        }
    
        /* Vertical line */
        .collapsible-link::after {
            content: '';
            width: 2px;
            height: 14px;
            background: #333;
            position: absolute;
            top: calc(50% - 7px);
            right: calc(1rem + 6px);
            display: block;
            transition: all 0.3s;
        }
    
        .collapsible-link[aria-expanded='true']::after {
            transform: rotate(90deg) translateX(-1px);
        }
    
        .collapsible-link[aria-expanded='true']::before {
            transform: rotate(180deg);
        }
        
    </style>
        <div id="accordionExample" class="">
                                <!-- Accordion item 2 -->
                                <div class="card" style="width: 100%">
                                        <div id="headingTwo" class="card-header bg-white shadow-sm border-0" style="background: #f6f6f6 !important">
                                            <h6 class="mb-0 font-weight-bold"><a href="#" data-toggle="collapse" data-target="#PreK" aria-expanded="false" aria-controls="PreK" class="d-block position-relative collapsed text-dark text-uppercase collapsible-link py-2">Updated Racial Composition</a></h6>
                                        </div>
                                        <div id="PreK" aria-labelledby="headingPreK" data-parent="#accordionExample" class="collapse">
                                            <div class="card-body p-5 mt-20">
                                                <div class="table-responsive">
                                                 <table class="table table-striped mb-0 w-100">
                                                    <tr>
                                                        <th>Program Group</th>
                                                        <th class="text-center">Black</th>
                                                        <th class="text-center">White</th>
                                                        <th class="text-center">Other</th>
                                                    </tr>
                                                    @foreach($group_racial_composition as $key=>$value)
                                                        @if(isset($value['black']))
                                                            <tr>
                                                                <td>{{$value['program_group']}}</td>
                                                                <td class="text-center">
                                                                @if($value['black'] > 0)
                                                                {{number_format($value['black']*100/$value['total'], 2)}} ({{$value['black']}})
                                                                @else
                                                                @endif
                                                                </td>
                                                                <td class="text-center">
                                                                @if($value['white'] > 0)
                                                                {{number_format($value['white']*100/$value['total'], 2)}} ({{$value['white']}})
                                                                @else
                                                                @endif
                                                                </td>
                                                                <td class="text-center">
                                                                @if($value['other'] > 0)
                                                                {{number_format($value['other']*100/$value['total'], 2)}} ({{$value['other']}})
                                                                @else
                                                                @endif
                                                                </td>
                                                            </tr>
                                                        @endif
                                                    @endforeach
                                                </table>
                                            </div>
                                                    </div>
                                                    </div>
                                                </div>
                                            </div>
    @endif
    <div class="">
            <div class="">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="table-responsive">

                                        @if(!empty($data))
                                            <table class="table table-striped mb-0 w-100" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th class="align-middle text-center">Sub ID</th>
                                                        <th class="align-middle text-center">Submission Status</th>
                                                        <th class="align-middle hiderace text-center">Race</th>
                                                        <th class="align-middle text-center">Student Status</th>
                                                        <th class="align-middle text-center">First Name</th>
                                                        <th class="align-middle text-center">Last Name</th>
                                                        <th class="align-middle text-center">Next Grade</th>
                                                        <th class="align-middle text-center">Current School</th>
                                                        <th class="align-middle hidezone text-center">Zoned School</th>
                                                        <th class="align-middle text-center">First Choice</th>
                                                        <th class="align-middle text-center">Second Choice</th>
                                                        <th class="align-middle text-center">Sibling ID</th>
                                                        <th class="align-middle text-center">Lottery Number</th>
                                                        <th class="align-middle text-center">Priority</th>
                                                        @if($preliminary_score)
                                                            <th class="align-middle text-center committee_score-col">Composite Score</th>
                                                        @else
                                                            <th class="align-middle text-center committee_score-col">Committee Score</th>
                                                        @endif
                                                        <th class="align-middle text-center committee_score-col">Final Status</th>
                                                        <th class="align-middle text-center committee_score-col">Race Composition<br>Update</th>
                                                        <th class="align-middle text-center committee_score-col">Availability</th>
                                                    </tr>
                                                    
                                                </thead>
                                                <tbody>
                                                    @foreach($data as $key=>$value)
                                                        @php $submission_data = \App\Modules\Submissions\Models\Submissions::where("id",$value['submission_id'])->first(); @endphp
                                                        <tr>
                                                            <td class="">{{$submission_data->id}}</td>
                                                            <td class="text-center">{{$submission_data->submission_status}}</td>
                                                            <td class="hiderace">{{$submission_data->calculated_race}}</td>
                                                            <td class="">
                                                                @if($submission_data->student_id != '')
                                                                    Current
                                                                @else
                                                                    New
                                                                @endif
                                                            </td>
                                                            <td class="">{{$submission_data->first_name}}</td>
                                                            <td class="">{{$submission_data->last_name}}</td>
                                                            
                                                            <td class="text-center">{{$submission_data->next_grade}}</td>
                                                            <td class="">{{$submission_data->current_school}}</td>
                                                            <td class="hidezone">{{$submission_data->zoned_school}}</td>
                                                            <td class="">{{$value['first_choice']}}</td>
                                                            <td class="text-center">{{$value['second_choice']}}</td>
                                                            <td class="">
                                                                @if($value['first_choice'] != '')
                                                                    @php $sibling_id = $submission_data->first_sibling @endphp
                                                                @else
                                                                    @php $sibling_id = $submission_data->second_sibling @endphp
                                                                @endif
                                                                @if($sibling_id  != '')
                                                                    <div class="alert1 alert-success p-10 text-center">{{$sibling_id}}</div>
                                                                @else
                                                                    <div class="alert1 alert-warning p-10 text-center">NO</div>
                                                                @endif
                                                            </td>
                                                            <td class="">{{$submission_data->lottery_number}}</td>
                                                            <td class="text-center">
                                                                <div class="alert1 alert-success">
                                                                    {{$value['priority']}}
                                                                </div>
                                                            </td>
                                                            <td class="text-center committee_score-col">
                                                                @if($preliminary_score)
                                                                    <div class="alert1 alert-success">
                                                                        {!! $value['composite_score'] !!}
                                                                    </div>
                                                                @else
                                                                    <div class="alert1 alert-success">
                                                                        {!! $value['committee_score'] !!}
                                                                    </div>
                                                                @endif
                                                            </td>
                                                            <td class="text-center">{{ $value['final_status']}}</td>
                                                            <td class="text-center">{!! $value['race_composition_update'] ?? '' !!}</td>
                                                            <td class="text-center">{{ $value['available_seats'] ?? '-'}}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <p class="text-center">No process selection done for this application.</p>
                                        @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
        </div>
@endsection
@section('scripts')
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>

	<script type="text/javascript">
        @if(!empty($data))
		//$("#datatable").DataTable({"aaSorting": []});
        var dtbl_submission_list = $("#datatable").DataTable({"aaSorting": [],
            "bSort" : false,
             "dom": 'Bfrtip',
             "autoWidth": true,
             "iDisplayLength": 50,
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
        @endif

	</script>

@endsection