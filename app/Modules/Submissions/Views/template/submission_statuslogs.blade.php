<table class="table table-striped mb-0" id="grade-table">
    <thead>
        <tr> 
            <th class="align-middle">#</th>
            <th class="align-middle">Old Status</th>
            <th class="align-middle">New Status</th>
            <th class="align-middle">Comment</th>
            <th class="align-middle">Updated By</th>
            <th class="align-middle">Updated At</th>
        </tr>
    </thead>
    <tbody>
        @if(isset($data['status_logs']))
            @foreach($data['status_logs'] as $value)
                <tr> 
                    <td class="align-middle">{{ $loop->iteration }}</td>
                    <td class="align-middle">{{$value->old_status}}</td>
                    <td class="align-middle">{{$value->new_status}}</td>
                    <td class="align-middle">{!! $value->comment !!}</td>
                    <td class="align-middle">@if($value->updated_by == 0) {{"By System"}} @else {{getUserName($value->updated_by)}} @endif</td>
                    <td class="align-middle">{{getDateTimeFormat($value->updated_at)}}</td>
                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6">No records found.</td>
            </tr>
        @endif
    </tbody>
</table>