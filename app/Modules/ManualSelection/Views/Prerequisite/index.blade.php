@extends('layouts.admin.app')
@section('title')Selection Prerequisite  | {{config('APP_NAME',env("APP_NAME"))}}  @endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Program Prerequisite Settings</div>
            <div class="">
                <a href="{{url('admin/ManualSelection/pre_req/create')}}" class="btn btn-sm btn-secondary" title="Add">Add Selection Prerequisite</a>
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            @include("layouts.admin.common.alerts")
            <div class="table-responsive">
                <table id="datatable" class="table table-striped mb-0">
                    <thead>
                    <tr>
                        <th class="align-middle">Program Name</th>
                        <th class="align-middle text-center">Grade</th>
                        <th class="align-middle text-left">Course Name</th>
                        <th class="align-middle text-center w-90">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($data as $key=>$value)
                        <tr>
                            <td class="">{{$value->program_name}}</td>
                            <td class="text-center">{{ (($value->grade != 0) ? $value->grade : 'All') }}</td>
                            <td class="text-left">{!! str_replace( '|', '<br />', $value->course_name ) !!}</td>
                            <td class="text-center">
                                <a href="{{url('admin/ManualSelection/pre_req/edit',$value->id)}}" class="font-18 ml-5 mr-5" title="Edit">
                                    <i class="far fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $(".alert").delay(2000).fadeOut(1000);
            $('#datatable').DataTable({
                'columnDefs': [ {
                    'targets': [2], // column index (start from 0)
                    'orderable': false, // set orderable false for selected columns
                }]
            });
        });
    </script>
@endsection