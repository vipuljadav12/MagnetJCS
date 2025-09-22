@extends('layouts.admin.app')
@section('title')
Permission
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Permission</div>
        <div class=""><a href="{{url('admin/Permission/create')}}" class="btn btn-sm btn-secondary" title="Add Permission">Add Permission</a>
        <a href="{{url('admin/Permission/trashindex')}}" title="View Trash" class="btn btn-danger btn-sm">View Trash</a>
        </div>
    </div>
</div>
@include('layouts.admin.common.alerts')
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped mb-0" id="example">
                <thead>
                    <tr>
                        <th class="align-middle text-center">Sr. No.</th>
                        <th class="align-middle text-center">Slug</th>
                        <th class="align-middle text-center">Display Name</th>
                        <th class="align-middle text-center">Module Name</th>
                        <th class="align-middle text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($permission))
                        @foreach($permission as $key=>$value)
                            <tr>
                                <td class="text-center">{{$key+1}}</td>
                                <td class="">{{$value->slug ?? ''}}</td>
                                <td class="">{{$value->display_name ?? ''}}</td>
                                <td class="">{{(isset($value->modules) && $value->modules != '[]') ? ucfirst($value->modules[0]->name) : ''}}</td>
                                <td class="text-center">
                                    <a href="{{url('admin/Permission/edit/').'/'.$value->id}}" class="font-18 ml-5 mr-5" title="Edit"><i class="far fa-edit"></i>
                                   </a>

                                   <a href="javascript:void(0);" class="font-18 ml-5 mr-5 text-danger deletePermission"  title="Delete" data-value="{{$value->id}}"><i class="far fa-trash-alt"></i>
                                   </a>
                                </td>
                            </tr>
                        @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
@section('scripts')
<script type="text/javascript">
    $(document).ready(function (){
        $('#example').DataTable();
        // alert();
    });
$(document).on('click', '.deletePermission', function(){
    var role_id = $(this).attr('data-value');
    swal({
        // title: "Are you sure?",
        // text: "you want to move this permission to trash.",
        // type: "warning",
        // showCancelButton: true,
        // confirmButtonClass: "btn-danger",
        // confirmButtonText: "Yes",
        // closeOnConfirm: false
         title: "Are you sure you would like to move this Permission to Trash ?",
            text: "",
            // type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
    })
    .then(() => {
        // alert();
            var url = "{{ url('/')}}/admin/Permission/trash/"+role_id;
            window.location.href = url;
        /*$.ajax({                        
            url: "{{ url('/')}}/admin/Permission/trash/"+role_id,
            success:function(response){
                if(response == 'Success')
                    location.reload();
            }
        }); */    
    });

});
</script>
@endsection