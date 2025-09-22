@extends('layouts.admin.app')
@section('title')
Trash Permission 
@endsection
@section('content')
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Trash Permission</div>
        <div class="">
            <a href="{{url('admin/Permission')}}" class="btn btn-success btn-sm" title="Back">Go Back</a>
        </div>
    </div>
</div>
<div class="card shadow">
    <div class="card-body">
        @include('layouts.admin.common.alerts')
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
                                <td class="">{{ucfirst($value->module_name) ?? ''}}</td>
                                <td class="text-center">
                                    <a href="{{url('admin/Permission/restore/').'/'.$value->id}}" class="font-18 ml-5 mr-5" id="{{$value->id}}" title="Restore"><i class="fa fa-undo text-dark f-20"></i>
                                    </a>
                                    <a href="javascript:void(0);" class="font-18 ml-5 mr-5 text-danger destoryPermission"  title="Delete" data-value="{{$value->id}}"><i class="far fa-trash-alt"></i>
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
    });
     $(document).on('click','.destoryPermission',function(){
        var user_id = $(this).attr('data-value');
        swal({
            // title: "Are you sure?",
            // text: "you want to Delete this permission.",
            // type: "warning",
            // confirmButtonClass: "btn-danger",
            // confirmButtonText: "Yes, delete it!",
            // showCancelButton: true,
            // closeOnConfirm: false
            title: "Are you sure you would like to permanently delete this permission ?",
            text: "",
            // type: "warning",
            showCancelButton: true,
            confirmButtonColor: "#DD6B55",
            confirmButtonText: "Yes",
            closeOnConfirm: false
        })
        .then(() => {
            var url = "{{ url('/')}}/admin/Permission/delete/"+user_id;
            window.location.href = url;
        });
        /* OLD CODE REPLACED BY 44*/
        /*swal({
            title: "Are you sure?",
            text: "you want to Delete this permission.",
            type: "warning",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, delete it!",
            showCancelButton: true,
            closeOnConfirm: false
        },
        function(){
            $.ajax({            
                url: "{{ url('/')}}/admin/Permission/delete/"+user_id,
                success:function(response){
                    if(response == 'Success')
                        location.reload();
                }
            });
        });*/
    });
</script> 
@endsection