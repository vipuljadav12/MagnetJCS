
        <div class="card shadow">
        <div class="card-header">Racial Composition</div>
        <div class="card-body">
            <div class="row">
                <div class="col-12 col-sm-3">
                    <div class="form-group">
                        <label for="">Black</label>
                        <div class="">
                            <input name="racial[black]" value="{{$enrollment_racial->black ?? ''}}" type="text" maxlength="10" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <div class="form-group">
                        <label for="">White</label>
                        <div class="">
                            <input name="racial[white]" value="{{$enrollment_racial->white ?? ''}}" type="text" maxlength="10" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <div class="form-group">
                        <label for="">Other</label>
                        <div class="">
                            <input name="racial[other]" value="{{$enrollment_racial->other ?? ''}}" type="text" maxlength="10" class="form-control">
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <div class="form-group">
                        <label for="">Swing (%)</label>
                        <div class="">
                            <input name="racial[swing]" value="{{$enrollment_racial->swing ?? ''}}" type="text" maxlength="10" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

