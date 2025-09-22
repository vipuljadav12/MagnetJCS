@php
    if(!empty($cdi_info))
        $class3_incident = $cdi_info->class3_incident;
    else
        $class3_incident = "No";

@endphp
<form class="form" id="insterview_score_form" method="post" action="{{url('admin/Submissions/update/ConductDisciplinaryInfo/'.$submission->id)}}">
    {{csrf_field()}}
    <div class="card shadow">
        <div class="card-header">Discipline Summary</div>
        <div class="card-body">
            <div class="row">
                    <div class="col-12 col-lg-12">
                        <div class="form-group row">
                            <label class="control-label col-12 col-md-12">Does this student have a Class 3 Incident ?</label>
                            <div class="col-12 col-md-12">
                                <select class="form-control" name="class3_incident">
                                    <option value="No" @if($class3_incident == "No") selected @endif>No</option>
                                    <option value="Yes" @if($class3_incident == "Yes") selected @endif>Yes</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                </div>
           
            <div class="tab-pane fade show active" >

                <div class="card">
                        <div class="card-header bg-info text-white">
                           Count of Number of AP (Alternative Placement) for {{$tmpYear}}/08/01-{{$tmpYear+1}}/06/01

                            
                        </div>
                        <div class="card-body">
                                 
                                <ul class="list-group">
                                    @if(count($discpline_data) > 0)
                                         @foreach($discpline_data as $values)
                                             <li class="list-group-item d-flex justify-content-between align-items-center">{{$values->attendance_code}} <span class="">{{$values->total}}</span></li>
                                        @endforeach
                                    @else
                                        <li class="list-group-item d-flex justify-content-between align-items-center">No Discpline Data available.</li>
                                    @endif
                                </ul>
                            </div>
                        
                    </div>
               
               
                        


                </div>

          
        </div>
        <div class="box content-header-floating" id="listFoot">
                <div class="row">
                    <div class="col-lg-12 text-right hidden-xs float-right">
                        <button type="submit" class="btn btn-warning btn-xs" title="Save"><i class="fa fa-save"></i> Save </button>
                        <button type="submit" class="btn btn-success btn-xs" name="save_exit" value="save_exit" title="Save & Exit"><i class="fa fa-save"></i> Save &amp; Exit</button>
                        <a class="btn btn-danger btn-xs" href="{{url('/admin/Submissions')}}" title="Cancel"><i class="fa fa-times"></i> Cancel</a>
                    </div>
                </div>
            </div>
    </div>
</form>