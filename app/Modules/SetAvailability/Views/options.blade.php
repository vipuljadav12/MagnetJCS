<div class="card shadow">
    <div class="card-header">{{$program->name}}- Rising Composition</div>
    <input type="hidden" name="year" value="{{$enrollment->school_year ?? (date("Y")-1)."-".date("Y")}}">
    @php
        if (isset($program->zoned_schools) && ($program->zoned_schools!='')) {
            $zoned_schools = explode(',', $program->zoned_schools);
        } else {
            $zoned_schools = [];
        }
        $rising_composition = isset($availabilities->rising_composition) ? json_decode($availabilities->rising_composition, true) : [];
    @endphp
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th class="align-middle w-10">Zoned School</th>
                        <th class="align-middle w-20">Black</th>
                        <th class="align-middle w-20">Non-Black</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($zoned_schools as $value)
                        <tr>
                            <td class="w-10">
                                {{$value}}
                                <label class="error text-danger d-none">Rising enrollment can not exceed total capacity.</label>
                            </td>
                            <td class="w-20">
                                <input type="text" class="form-control digit_float" name="rising_composition[{{$value}}][black]" value="{{$rising_composition[$value]['black'] ?? ''}}" maxlength="5">
                            </td>
                            <td class="w-20">
                                <input type="text" class="form-control digit_float" name="rising_composition[{{$value}}][non_black]" value="{{$rising_composition[$value]['non_black'] ?? ''}}" maxlength="5">
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="text-center">No Data Available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="card shadow">
    <div class="card-header">{{$program->name}}- Available</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <tbody>
                        <tr>
                            <td class="w-20">Available Seats</td>
                            <td>
                                <input type="text" class="form-control digit" name="available_seats" value="{{$availabilities->available_seats ?? ''}}" maxlength="10">
                            </td>
                        </tr>
                    
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- <div class="card shadow">
    <div class="card-header">{{$program->name}}- Capacity</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <tbody>
                        <tr>
                            <td class="w-20">Capacity</td>
                            <td>
                                <input type="text" class="form-control">
                            </td>
                        </tr>
                    
                </tbody>
            </table>
        </div>
    </div>
</div> --}}

<div class="text-right"> 
    <div class="box content-header-floating" id="listFoot">
        <div class="row">
            <div class="col-lg-12 text-right hidden-xs float-right">
                <button type="submit" class="btn btn-warning btn-xs" title="Save" id="optionSubmit"><i class="fa fa-save"></i> Save </button>
            </div>
        </div>
    </div>
</div>