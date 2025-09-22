@extends('layouts.admin.app')
@section('title')
	Processing Log Report
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
.dt-buttons {position: absolute !important; padding-top: 5px !important;}

</style>
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Process Log Report</div></div>
    </div>


  <div class="card shadow" id="response">
        <div class="card-body">

                                     <div class="table-responsive">
                                        <table class="table table-striped mb-0 w-100" id="datatable">
                                            <thead>
                                                <tr>
                                                    <th class="align-middle">Sr. No.</th>
                                                    <th class="align-middle">Date & Time</th>
                                                    <th class="align-middle">Enrollment Period</th>
                                                    <th class="align-middle notexport">Application Name</th>
                                                    <th class="align-middle">Processing Type</th>
                                                    <th class="align-middle">Offer Acceptance Deadline</th>
                                                    <th class="align-middle">Population Changes</th>
                                                    <th class="align-middle">Submission Results</th>
                                                    <th class="align-middle">Seats Status</th>
                                                    @if(Auth::user()->role_id == 1)
                                                        <th class="align-middle">Selection Master Report</th>
                                                    @endif
                                                </tr>
                                            </thead>
                                            <tbody>
                                                    @php $count =1 @endphp
                                                     @foreach($late_submission as $key=>$value)
                                                        <tr>
                                                            <td class="text-center">{{$count}}</td>
                                                            <td class="">
                                                                <div class="">{{getUserName($value->generated_by)}}</div>
                                                                <div class="">{{getDateTimeFormat($value->created_at)}}</div>
                                                            </td>
                                                            <td>{{getEnrollmentYear($value->enrollment_id)}}</td>
                                                            <td>{{get_form_name($value->application_id)}}</td>
                                                            <td>Late Submission</td>
                                                            <td>{{getDateTimeFormat($value->last_date_online_acceptance)}}</td>
                                                            <td class="text-center"><a href="{{url('/admin/LateSubmission/Population/Version/'.$value->application_id.'/'.$value->version)}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="{{url('/admin/LateSubmission/Submission/Result/Version/'.$value->application_id.'/'.$value->version)}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="{{url('/admin/LateSubmission/SeatsStatus/Version/'.$value->id)}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                             @if(Auth::user()->role_id == 1)
                                                                <td class="text-center"><a href="{{url('/admin/Reports/missing/'.$value->enrollment_id.'/selection_report/'.$value->application_id.'/'.$value->version.'/late_submission')}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            @endif
                                                        </tr>
                                                        @php $count++ @endphp
                                                    @endforeach

                                                     @foreach($waitlist as $key=>$value)
                                                        <tr>
                                                            <td class="text-center">{{$count}}</td>
                                                            <td class="">
                                                                <div class="">{{getUserName($value->generated_by)}}</div>
                                                                <div class="">{{getDateTimeFormat($value->created_at)}}</div>
                                                            </td>
                                                            <td>{{getEnrollmentYear($value->enrollment_id)}}</td>
                                                            <td>{{findFormName($value->application_id)}}</td>
                                                            <td>Process Waitlist</td>
                                                            <td>{{getDateTimeFormat($value->last_date_online_acceptance)}}</td>
                                                            <td class="text-center"><a href="{{url('/admin/Waitlist/Population/Version/'.$value->application_id.'/'.$value->version)}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="{{url('/admin/Waitlist/Submission/Result/Version/'.$value->application_id.'/'.$value->version)}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="{{url('/admin/Waitlist/SeatsStatus/Version/'.$value->id)}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            @if(Auth::user()->role_id == 1)
                                                                <td class="text-center"><a href="{{url('/admin/Reports/missing/'.$value->enrollment_id.'/selection_report/'.$value->application_id.'/'.$value->version.'/waitlist')}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            @endif
                                                        </tr>
                                                        @php $count++ @endphp
                                                    @endforeach

                                                    @foreach($regular as $key=>$value)
                                                        <tr>
                                                             <td class="text-center">{{$count}}</td>
                                                            <td class="">
                                                                <div class="">{{$value->updated_by}}</div>
                                                                <div class="">{{getDateTimeFormat($value->created_at)}}</div>
                                                            </td>
                                                            <td>{{getEnrollmentYear($value->enrollment_id)}}</td>
                                                            <td>{{findFormName($value->application_id)}}</td>
                                                            <td>Process Selection</td>
                                                            <td>{{getDateTimeFormat($value->last_date_online_acceptance)}}</td>
                                                            <td class="text-center"><a href="{{url('/admin/Reports/missing/'.$value->enrollment_id.'/populationchange/'.$value->application_id)}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="{{url('/admin/Reports/missing/'.$value->enrollment_id.'/results/'.$value->application_id)}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            <td class="text-center"><a href="{{url('/admin/Reports/missing/'.$value->enrollment_id.'/seatstatus/'.$value->application_id)}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            @if(Auth::user()->role_id == 1)
                                                                <td class="text-center"><a href="{{url('/admin/Reports/missing/'.$value->enrollment_id.'/selection_report/'.$value->application_id)}}" class="font-18 ml-5 mr-5" title="View"><i class="far fa-eye"></i></a></td>
                                                            @endif
                                                        </tr>
                                                        @php $count++ @endphp
                                                    @endforeach


                                                
                                            </tbody>
                                        </table>
                                    </div>

                                </div>
                                </div>
@endsection
@section('scripts')
<div id="wrapperloading" style="display:none;"><div id="loading"><i class='fa fa-spinner fa-spin fa-4x'></i> <br> Process is started.<br>It will take approx 2 minutes to finish. </div></div>
<script src="{{url('/resources/assets/admin')}}/js/bootstrap/dataTables.buttons.min.js"></script>
    <script src="{{url('/resources/assets/admin')}}/js/bootstrap/buttons.html5.min.js"></script>
@endsection