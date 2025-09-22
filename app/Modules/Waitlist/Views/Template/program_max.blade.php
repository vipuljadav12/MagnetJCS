
    <div class="card shadow">
            <div class="card-header">Program Max Percent Swing</div>
            <div class="card-body">
                 <form action="{{url('/admin/Waitlist/Program/Swing/'.$application_id.'/store')}}" method="post" id="process_selection">
             {{csrf_field()}}
             <input type="hidden" name="type" id="type" value="update">
             <input type="hidden" name="process_event" id="process_event" value="">
                <p class="text-right text-danger">Leave empty to use the Default Max Percent Swing</p>
                <div class="table-responsive" style="height: auto; overflow-y: auto;">
                    <table class="table tbl_adm">
                        <thead>
                            <tr>
                                <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 200 !important">Program Group Name</th>
                                <th style="position: sticky; top: 0; background-color: #fff !important; z-index: 200 !important">% Swing</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($display_outcome > 0)
                                @php $disabled = "disabled" @endphp
                            @else
                                @php $disabled = "" @endphp
                            @endif

                            @foreach($prgGroupArr as $value)
                                @if($value != "")
                                <tr>
                                    <input type="hidden" name="swing_data[]" value="{{$value}}" {{$disabled}}>
                                    <td class="">{{$value}}</td>
                                    <td class=""><input type="text" name="swing_value[]" class="form-control adm_value" value="{{ $swingData[$value] ?? '' }}"  {{$disabled}}></td>
                                </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="text-right pt-20"><input type="submit" class="btn btn-success" title="Save Swing Data" value="Save Swing Data"></div>
            </form>
                 
            </div>
        </div>
