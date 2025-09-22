<div class="">
    <div class="col-lg-12 text-right hidden-xs float-right mb-5"><a href="{{url('/admin/Enrollment/adm_data/import/'.$enrollment->id)}}" class="text-white btn btn-sm btn-success" title="">Import</a></div>
    <div class="card shadow">
        <div class="card-header">Racial Composition for Current Enrollment</div>
        <div class="card-body">
            <div class="table-responsive" style="height: 465px; overflow-y: auto;">
                <table class="table tbl_adm">
                    <thead>
                        <tr>
                            <th class="" style="position: sticky; top: 0; background-color: #fff !important; z-index: 200 !important"></th>
                            <th class="w-200" style="position: sticky; top: 0; background-color: #fff !important; z-index: 200 !important">Black</th>
                            <th class="w-200" style="position: sticky; top: 0; background-color: #fff !important; z-index: 200 !important">Non-Black</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schools as $school)
                            <tr>
                                @php
                                    if (isset($adm_data)) {
                                        $data = $adm_data->where('school_id', $school->id)->first();
                                    }
                                @endphp
                                <input type="hidden" name="adm_data[{{$loop->index}}][school_id]" value="{{$school->id}}">
                                <td class="">{{$school->name}}</td>
                                <td><input class="form-control digit_float" type="text" name="adm_data[{{$loop->index}}][black]" value="{{($data['black'] ?? '')}}" maxlength="5"></td>
                                <td><input class="form-control digit_float" type="text" name="adm_data[{{$loop->index}}][non_black]" value="{{($data['non_black'] ?? '')}}" maxlength="5"></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" align="center">No school(s) available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>