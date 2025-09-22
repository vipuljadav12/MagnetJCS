@extends('layouts.admin.app')
@section('title')
User Role
@endsection
@section('content')
<div class="content-wrapper-in"> <!-- InstanceBeginEditable name="Content-Part" -->
<div class="card shadow">
    <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
        <div class="page-title mt-5 mb-5">User Role</div>
        <div class=""><a href="{{url('admin/Role/create')}}" class="btn btn-sm btn-secondary" title="Add UserType">Add User Role</a>
         {{-- <a href="{{url('admin/Role/trashindex')}}" title="View Trash" class="btn btn-danger btn-sm" >View Trash</a> --}}
         @if(Auth::user()->role_id == 1)   
            <a href="{{url('admin/Permission')}}" class="btn btn-sm btn-info" title="Permission">Permission</a>
         @endif
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
                        <th class="align-middle">User Role</th>
                        {{-- <th class="align-middle text-center">Status</th> --}}
                        <th class="align-middle text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                @if(isset($roles) && $roles != '[]')    
                    @foreach($roles as $key => $value)    
                        <tr>
                            <td class="">{{$value->name}}</td>
                            {{-- <td class="text-center">
                                <input type="checkbox" class="js-switch js-switch-1 js-switch-xs statuschnge" id="{{$value->id}}" data-size="Small" @if($value->status == "Y") checked  @endif />
                            </td> --}}
                            <td class="text-center">
                                <a href="{{url('admin/Role/edit/').'/'.$value->id}}" class="font-18 ml-5 mr-5" title="Edit"><i class="far fa-edit"></i>
                                </a>

                                {{-- <a href="javascript:void(0);" class="font-18 ml-5 mr-5 text-danger deleteRole"  title="Delete" data-value="{{$value->id}}"><i class="far fa-trash-alt"></i> --}}
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
<!-- InstanceEndEditable --> </div>
@endsection
@section('scripts')
{{-- @include('layouts.admin.common.datatable') --}}
<script type="text/javascript">
$('#roleList').DataTable({
    columnDefs: [
            { width: 200, targets: [1,2] }
        ],
});
$(document).on('click', '.deleteRole', function(){
    var role_id = $(this).attr('data-value');
    swal({
        title: "Are you sure?",
        text: "you want to move this record to trash.",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn-danger",
        confirmButtonText: "Yes",
        closeOnConfirm: false
    },
    function(){
        $.ajax({                        
            url: "{{ url('/')}}/admin/Role/trash/"+role_id,
            success:function(response){
                if(response == 'Success')
                    location.reload();
            }
        });     
    });

});
    $(document).on('change','.statuschnge',function(){
        var role_id = $(this).attr('id');
        if(this.checked == true){
            var status = 'Y';           
        }else{
            var status = 'N';
        }
        $.ajax({            
            data: {status},         
            url: "{{ url('/')}}/admin/Role/status_change/"+role_id
        });
    })
</script>
@endsection