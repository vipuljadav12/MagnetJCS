@extends('layouts.admin.app')
@section('title') Student Master @endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Student Master</div>
        <div class="">
            <a href="{{url('admin/Student/create')}}" class="btn btn-sm btn-secondary" title="">Add Student</a>
            <a href="{{url('admin/Student/trash')}}" class="btn btn-sm btn-danger" title="">Trash</a>

        </div>
    </div>
</div>
@include("layouts.admin.common.alerts")
<div class="card shadow">
   <div class="card-body">
    <div class="table-responsive">
        <table id="datatable" class="table table-striped mb-0">
            <thead>
                <tr>
                	<th class="align-middle">ID</th>
                    <th class="align-middle">State ID</th>
                    <th class="align-middle">First name</th>
                    <th class="align-middle">Last name</th>
                    <th class="align-middle">Race</th>
                    <th class="align-middle">Gender</th>
                    <th class="align-middle">Birthday</th>
                    <th class="align-middle">Address</th>
                    <th class="align-middle">Current School</th>
                    <th class="align-middle">Current Grade</th>
                    <th class="align-middle">State</th>
                    <th class="align-middle">Email</th>
                </tr>
            </thead>
            <tbody>
        	</tbody>
    </table>
</div>
</div>
</div>

@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $(".alert").delay(2000).fadeOut(1000);
       
		var student = $('#datatable').DataTable({
            "deferRender":    true,
            "columnDefs": [
                {"className": "dt-center", "targets": "_all"}
            ],
            "columnDefs": [{
                "defaultContent": '-',
                "targets": '_all'
            }],
        });

		/* load pending records with ajax start */
        loadPending()
        function loadPending() {
            $.ajax({
                url: "{{url('admin/Student/loadData/')}}",
                success: function(response) {
                    $('#datatable').DataTable().rows.add(JSON.parse(response)).draw(false);
                }
            });
        }
        /* load pending records with ajax end */
    });
    </script> 
@endsection