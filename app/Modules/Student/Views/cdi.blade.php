@extends('layouts.admin.app')
@section('styles')
<style type="text/css">
    /*.modal-dialog{
        overflow-y: initial !important
    }*/
    #cdi_details_content{
        max-height: 500px;
        overflow-y: auto !important;
        position: relative;
    }
</style>
@endsection
@section('title')
	Student CDI
@endsection
@section('content')
    <div class="card shadow">
        <div class="card-body d-flex align-items-center justify-content-between flex-wrap">
            <div class="page-title mt-5 mb-5">
                Student CDI (Conduct Disciplinary Info)
            </div>
        </div>
    </div>
    <div class="card shadow">
        <div class="card-body">
            <div class="pt-20 pb-20">
                <div class="table-responsive">
                    <table id="tbl_student_cdi" class="table table-striped mb-0">
                        <thead>
                            <tr> 
                                <th class="align-middle">Student ID</th>
                                <th class="align-middle">Name</th>
                                <th class="align-middle">Current School</th>
                                <th class="align-middle">B_Info</th>
                                <th class="align-middle">C_Info</th>
                                <th class="align-middle">D_Info</th>
                                <th class="align-middle">E_Info</th>
                                <th class="align-middle">Susp.</th>
                                <th class="align-middle">Susp. Days</th>
                                <th class="align-middle">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @isset($cdi)
                            @foreach($cdi as $value)
                            <tr> 
                                <td class="align-middle">{{$value->stateID}}</td>
                                <td class="align-middle">{{($value->first_name ?? '') .' '. ($value->last_name ?? '')}}</td>
                                <td class="align-middle">{{$value->currentSchool ?? ''}}</td>
                                <td class="align-middle">{{$value->b_info}}</td>
                                <td class="align-middle">{{$value->c_info}}</td>
                                <td class="align-middle">{{$value->d_info}}</td>
                                <td class="align-middle">{{$value->e_info}}</td>
                                <td class="align-middle">{{$value->susp}}</td>
                                <td class="align-middle">{{$value->susp_days}}</td>
                                <td class="align-middle text-center">
                                    @if($value->cdi_details == 'Y')
                                    <a href="javascript:void(0)" id="{{$value->stateID}}" class="font-18 ml-5 mr-5 cdi_details_info" title=""><i class="fas fa-info-circle"></i></a>
                                    @endif
                                </td>
                            </tr>
                            @endforeach
                            @endisset
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- CDI Details Modal -->
    <div class="modal fade" id="modal_cdi_details" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">CDI Details</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="cdi_details_content">

          </div>
        </div>
      </div>
    </div>

@endsection
@section('scripts')
<script type="text/javascript">
    $("#tbl_student_cdi").DataTable({
        'columnDefs': [
            {'orderable': false, 'targets': [9]}
        ]
    });

    $(document).on('click', '.cdi_details_info', function() {
        var id = $(this).attr('id');
        $.ajax({
            type: "GET",
            url: "{{url('admin/Student/cdi_details/')}}/" + id,
            data: {
                'id': id
            },
            success: function(res) {
                $('#cdi_details_content').html(res);
                $('#modal_cdi_details').modal({show:true});
            }
        });
    });
</script>
@endsection