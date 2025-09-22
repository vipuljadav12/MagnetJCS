<div class="table-responsive">
    <table id="tbl_student_cdi" class="table table-striped mb-0">
        <thead>
            <tr> 
                <th scope="col" class="text-center">Date</th>
                <th scope="col" class="text-center">Infraction</th>
                <th scope="col" class="text-center">Disposition</th>
                <th scope="col" class="text-center">Action Name</th>
                <th scope="col" class="text-center">Suspended Days</th>
                <th scope="col" class="text-center">Suspension Start</th>
                <th scope="col" class="text-center">Suspension End</th>
            </tr>
        </thead>
        <tbody>
            @isset($cdi_details)
            @foreach($cdi_details as $cinfo)
                @if($cinfo->datetime >= date("Y-m-d", mktime(0, 0, 0, 9, 1, date("Y")-1)))
                    @php $class = "table-success" @endphp
                @else
                    @php $class = "" @endphp
                @endif
                <tr class="{{$class}}">
                    <td>{{getDateFormat($cinfo->datetime)}}</td>
                    <td>{{$cinfo->infraction_name}}</td>
                    <td>{{$cinfo->disposition}}</td>
                    <td>{{$cinfo->actionname}}</td>
                    <td class="text-center">{{$cinfo->suspend_days}}</td>
                    @if($cinfo->disposition_type == "Suspended/Out of School")
                        <td>{{getDateFormat($cinfo->startdate)}}</td>
                        <td>{{getDateFormat($cinfo->enddate)}}</td>
                    @else
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
            @endisset
        </tbody>
    </table>
</div>
        