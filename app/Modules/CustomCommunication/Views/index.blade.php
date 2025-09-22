@extends('layouts.admin.app')
@section('title')
	Custom Communication
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">Custom Communication</div>
            @if((checkPermission(Auth::user()->role_id,'CustomCommunication/create') == 1))
                <div class="w-auto"><a href="{{url('admin/CustomCommunication/create')}}" title="Add Custom Communication" class="btn btn-secondary ml-5">Add Custom Communication</a> </div>
            @endif
        </div>
    </div>

    <div class="card shadow">
            <div class="card-body">
                <div class="pt-20 pb-20">
                    <div class="table-responsive">
                        <table class="table table-striped mb-0" id="datatable">
                            <thead>
                                <tr>
                                    <th class="align-middle">Template Name</th>                                                
                                    @if((checkPermission(Auth::user()->role_id,'CustomCommunication/edit') == 1))
                                    <th class="align-middle text-center w-120">Action</th>
                                    @endif
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data as $key=>$value)
                                    <tr>
                                        <td class="">{{$value->template_name}}</td>
                                        @if((checkPermission(Auth::user()->role_id,'CustomCommunication/edit') == 1))

                                        <td class="text-center"><a href="{{url('/admin/CustomCommunication/edit/'.$value->id)}}" class="font-18 ml-5 mr-5" title=""><i class="far fa-edit"></i></a></td>
                                        @endif
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
               
            </div>
        </div>

  
       
@endsection
@section('scripts')
	<script type="text/javascript">
		
       $(document).ready(function() {
            $('#datatable').DataTable();
            });

        $('.status').change(function() {
            var click=$(this).prop('checked')==true ? 'Y' : 'N' ;
            $.ajax({
                type: "get",
                url: '{{url('admin/CustomCommunication/status')}}',
                data: {
                    id:$(this).attr('id'),
                    status:click
                },
                complete: function(data) {
                    console.log('success');
                }
            });
        });
	</script>

@endsection