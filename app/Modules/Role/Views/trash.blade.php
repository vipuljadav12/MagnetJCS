@extends('layouts.admin.app')
@section('title')
Trash User Type
@endsection
@section('content')
<div class="content-wrapper-in"> <!-- InstanceBeginEditable name="Content-Part" -->
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">Trash User Type</div>
        <div class="">
        <a href="{{url('admin/Role')}}" title="Trash" class="btn btn-success btn-sm">Go Back</a>
        </div>
    </div>
</div>
@include('layouts.admin.common.alerts')
<div class="card shadow">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped mb-0" id="roleList">
                <thead>
                    <tr>
                        <th class="align-middle">User Type</th>
                        <th class="align-middle text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($role) && $role != '[]')
                    @foreach($role as $key => $value)    
                        <tr>
                            <td class="">{{$value->name}}</td>
                            {{-- <td class="text-center">
                                <input type="checkbox" class="js-switch js-switch-1 js-switch-xs statuschnge" id="{{$value->id}}" data-size="Small" @if($value->status == "Y") checked  @endif />
                            </td> --}}
                            <td class="text-center">
                                <a href="{{url('admin/Role/restore/').'/'.$value->id}}" class="font-18 ml-5 mr-5" id="{{$value->id}}" title="Restore"><i class="fa fa-undo text-dark f-20"></i>
                                </a>
                                <a href="javascript:void(0);" class="font-18 ml-5 mr-5 text-danger destoryRole"  title="Delete" data-value="{{$value->id}}"><i class="far fa-trash-alt"></i>
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
</div>
@endsection
@section('scripts')
{{-- @include('layouts.admin.common.datatable') --}}
<script type="text/javascript">
    $('#roleList').DataTable();
     $(document).on('click','.destoryRole',function(){
        var role_id = $(this).attr('data-value');
        swal({
            title: "Are you sure?",
            text: "you want to Delete this Role.",
            type: "warning",
            confirmButtonClass: "btn-danger",
            confirmButtonText: "Yes, delete it!",
            showCancelButton: true,
            closeOnConfirm: false
        },
        function(){
            $.ajax({            
                url: "{{ url('/')}}/admin/Role/delete/"+role_id,
                success:function(response){
                    if(response == 'Success')
                        location.reload();
                }
            });
        });
    });
</script> 
@endsection