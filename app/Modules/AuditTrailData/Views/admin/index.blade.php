@extends('layouts.admin.app')
@section('title')
		 Audit Trails | {{config('APP_NAME',env("APP_NAME"))}} 
@endsection
@section('styles')
    <!-- DataTables -->
    <link href="{{asset('resources/assets/admin/plugins/datatables/dataTables.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="{{asset('resources/assets/admin/plugins/datatables/buttons.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />
    <!-- Responsive datatable examples -->
    <link href="{{asset('resources/assets/admin/plugins/datatables/responsive.bootstrap4.min.css')}}" rel="stylesheet" type="text/css" />

    <style type="text/css">
    	.text-strong
    	{
    		font-weight: bold;
    	}
        .custom-select{
    margin: 5px !important;
}
    </style>
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Audit Trail</div>
            <div class="">
                
            </div>
        </div>
    </div>
    <ul class="nav nav-tabs" id="AuditTrailTab" role="tablist">
        <li class="nav-item"><a class="nav-link active" id="submission-tab" data-toggle="tab" href="#submission" role="tab" aria-controls="submission" aria-selected="true">Submission</a></li>
        <li class="nav-item d-none"><a class="nav-link" id="general-tab" data-toggle="tab" href="#general" role="tab" aria-controls="general" aria-selected="true">General</a></li>
    </ul>
     <div class="tab-content bordered" id="AuditTrailTabContent">
            <div class="tab-pane fade show active" id="submission" role="tabpanel" aria-labelledby="submission-tab">
                <div>
                	<div class="card shadow">
				        <div class="card-body">

				        	@include("AuditTrailData::admin.submission")				            
				        </div>
				    </div>
                </div>
            </div>
            <div class="tab-pane fade d-none" id="general" role="tabpanel" aria-labelledby="general-tab">
                <div class="">
                   <div class="card shadow">
				        <div class="card-body">
				        	@include("AuditTrailData::admin.general")				            

				            
				        </div>
				    </div>
                </div>
            </div>
        </div>
			    
@endsection
@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
	        $(".alert").delay(2000).fadeOut(1000);
           

	        var dtbl_submission_list =  $('#datatable').DataTable();
            $("#datatable thead th").each( function ( i ) {
            // Disable dropdown filter for disalble_dropdown_ary (index=0)
            var disalble_dropdown_ary = [0, 3, 4, 5, 6];//13
            if ($.inArray(i, disalble_dropdown_ary) == -1) {
                var column_title = $(this).text();
                
                var select = $('<select class="form-control col-md-3 custom-select custom-select2"><option value="">Select '+column_title+'</option></select>')
                    .appendTo( $('#submission_filters') )
                    .on( 'change', function () {
                        dtbl_submission_list.column( i )
                            .search($(this).val())
                            .draw();
                    } );
         
                dtbl_submission_list.column( i ).data().unique().sort().each( function ( d, j ) {
                    str = d.replace('<div class="alert1 alert-success p-10 text-center d-block">', "");
                    str = str.replace('<div class="alert1 alert-warning p-10 text-center d-block">', "");
                    str = str.replace('</div>', "");
                    if(str != "")
                        select.append( '<option value="'+str+'">'+str+'</option>' );
                } );
            }
        } );
        // Hide Columns
        //dtbl_submission_list.columns([1, 2]).visible(true);

	        $('#datatable-general').DataTable();
	       /* $('#datatable').DataTable({
	            'columnDefs': [ {
	                'targets': [3,4], // column index (start from 0)
	                'orderable': false, // set orderable false for selected columns
	            }]
	        });*/
	        //Buttons examples
	        var table = $('#datatable-buttons').DataTable({
	            lengthChange: false,
	            buttons: ['copy', 'excel', 'pdf', 'colvis'],
	        });
	        table.buttons().container()
	            .appendTo('#datatable-buttons_wrapper .col-md-6:eq(0)');
	    });
    
    </script>
@endsection