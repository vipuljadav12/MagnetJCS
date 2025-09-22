@extends('layouts.admin.app')
@section('title') Files @endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Front Page Links</div>
        <div class="">
            <a href="{{url('admin/Files/create')}}" class="btn btn-sm btn-secondary" title="Add">Add New</a>
        </div>
    </div>
</div>
@include("layouts.admin.common.alerts")
<div class="card shadow">
   <div class="card-body">
        <div class="table-responsive">
            <table id="tbl_index" class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th class="align-middle text-center">#</th>
                    	<th class="align-middle">Front Page Link Name</th>
                        <th class="align-middle">Description</th>
                        <th class="align-middle text-center">Status</th>
                        <th class="align-middle text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="">
                    @foreach($files as $key=>$value)
                    <tr class="" id="{{$value->link_id}}">
                        <td class="text-center">{{$value->link_id}}</td>
                        <td>{{$value->link_title}}</td>
                        <td class="card-group">
                            @if($value->link_filename != "")
                                @php
                                    $file_path = url('/resources/filebrowser/'.$district->district_slug.'/documents/'.$value->link_filename);
                                    $file_name = explode('.',$value->link_filename);
                                    $file_type = "";
                                    if (isset($file_name[1])) {
                                        if ($file_name[1] == 'pdf') {
                                            $file_type = 'pdf';
                                        }
                                    }
                                @endphp
                                @if($file_type == 'pdf')
                                    <!--<a href="{{$file_path}}" target="_blank" href="">-->{{$value->link_filename}}<!--<i class="far fa-file-pdf fa-lg ml-5"></i></a>-->
                                @else
                                    {{$value->link_filename}}
                                    <!--<div class="ml-5" style="height: 30px; width: 30px;"><img src="{{$file_path}}" style="height: 100%; width: 100%"></div>-->
                                @endif
                            @elseif($value->popup_text != "")
                                {!!$value->popup_text!!}
                            @elseif($value->link_url != "")
                                <div class="text-info">{{$value->link_url}}</div>
                            @endif
                        </td>
                        <td class="text-center">
                            <input id="{{$value->link_id}}" type="checkbox"  class="js-switch js-switch-1 js-switch-xs file_status" data-size="Small" {{$value->status=='Y'?'checked':''}} />
                        </td>
                        <td class="text-center">
                            <a href="{{url('/admin/Files/edit/'.$value->link_id)}}"  class="font-18 ml-5 mr-5" title="Edit"><i class="far fa-edit"></i></a>
                            <a href="javascript:void(0)" onclick="deletefunction({{$value->link_id}})" class="font-18 ml-5 mr-5 text-danger" title="Trash"><i class="far fa-trash-alt"></i></a>
                        </td>
                    </tr>
                    @endforeach
            	</tbody>
            </table>
        </div>
    </div>
</div>

@endsection
@section('scripts')
<script type="text/javascript"> 
	var table = $('#tbl_index').DataTable({
        order: [],
        'columnDefs': [
            {
                'orderable': false,
                'targets': [2, 3]
            }
        ],
        'rowReorder':true,
        'rowReorder': {
            update: true
        }
    });

    //table.rowReordering();
    var dataArr = new Array();
    table.on( 'row-reordered', function ( e, diff, edit ) {
        table.rows().eq(0).each( function ( index ) {
            var row = table.row( index );
            dataArr[dataArr.length] = row.data()[0];
            // ... do something with data(), or row.node(), etc
        } );
         $.ajax({
            url: "{{url('admin/Files/sort_update')}}",
            method: "post",
            data: {
                "_token": "{{csrf_token()}}",
                "data": dataArr
            },
             success: function(){
                document.location.href = "{{url('/admin/Files')}}"
            }
        });


    } );

    // File status
    $(document).on('change', '.file_status', function() {
        var status = $(this).prop('checked') == true ? 'Y' : 'N';
        var id  = $(this).attr('id');
        $.ajax({
            url: "{{url('admin/Files/status_update')}}",
            data: {
                id: id,
                status: status
            }

        });
    });

    //delete confermation
    var deletefunction = function(id){
        swal({
            title: "Are you sure you would like to delete this file?",
            text: "",
            // type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        }).then(function() {
            window.location.href = '{{url('/')}}/admin/Files/delete/'+id;
        });
    };

</script> 
@endsection