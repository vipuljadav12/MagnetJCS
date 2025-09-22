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

   
    <div class="">
            <div class="">
                                <div class="card shadow">
                                    <div class="card-body">
                                        <div class="table-responsive">
                                            @if(!empty($display_data))
                                            <table class="table table-striped mb-0 w-100" id="datatable">
                                                <thead>
                                                    <tr>
                                                        <th class="align-middle text-center">Sub ID</th>
                                                        <th class="align-middle hiderace text-center">Race</th>
                                                        <th class="align-middle text-center">Student Status</th>
                                                        <th class="align-middle text-center">First Name</th>
                                                        <th class="align-middle text-center">Last Name</th>
                                                        <th class="align-middle text-center">Next Grade</th>
                                                        <th class="align-middle text-center">Current School</th>
                                                        <th class="align-middle hidezone text-center">Zoned School</th>
                                                        <th class="align-middle hidezone text-center">Round</th>
                                                        <th class="align-middle text-center">First Choice</th>
                                                        <th class="align-middle text-center">Second Choice</th>
                                                         <th class="align-middle text-center">Third Choice</th>
                                                        <th class="align-middle text-center">Lottery Number</th>
                                                       
                                                        <th class="align-middle text-center committee_score-col">Final Status</th>
                                                        <th class="align-middle text-center committee_score-col">Availability</th>
                                                    </tr>
                                                    
                                                </thead>
                                                <tbody>
                                                    @foreach($display_data as $key=>$value)

                                                   
                                                        <tr>
                                                            <td class="">{{$value['id']}}</td>
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
                                                            <td class="hidezone">{{$value['round']}}</td>
                                                            <td class="">{{ ($value['choice'] == "first" ? $value['first_choice_program'] : "" )}}</td>
                                                            <td class="text-center">{{ ($value['choice'] == "second" ? $value['second_choice_program'] : "" )}}</td>
                                                            <td class="text-center">{{ ($value['choice'] == "third" ? $value['third_choice_program'] : "" )}}</td>
                                                            <td class="">{{$value['lottery_number']}}</td>
                                                            
                                                            <td class="text-center">{!! $value['status'] !!}</td>
                                                            <td class="text-center">{!! $value['msg'] ?? '' !!}</td>
                                                        </tr>
                                                    @endforeach
                                                    <!-- @foreach($final_skipped_arr as $key=>$value)
                                                        <tr>
                                                            <td class="">{{$value['id']}}</td>
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
                                                            <td class="hidezone"></td>
                                                            <td class="">{{ ($value['choice'] == "first" ? $value['first_choice_program'] : "" )}}</td>
                                                            <td class="text-center">{{ ($value['choice'] == "second" ? $value['second_choice_program'] : "" )}}</td>
                                                            <td class="text-center">{{ ($value['choice'] == "third" ? $value['third_choice_program'] : "" )}}</td>
                                                            <td class="">{{$value['lottery_number']}}</td>
                                                            
                                                            <td class="text-center">{{ $value['status']}}</td>
                                                            <td class="text-center">{!! $value['msg'] ?? '' !!}</td>
                                                        </tr>
                                                    @endforeach -->
                                                </tbody>
                                            </table>
                                            @else
                                                <p class="text-center">Process Selection outcome accepted. You can view Selection Report from Process Log section.</p>
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

	</script>

@endsection