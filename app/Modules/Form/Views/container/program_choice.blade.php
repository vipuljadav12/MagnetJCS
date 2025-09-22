<div class="form-group form-group-input buildBox{{$formContent->id}}" data-build-id="{{$formContent->id}}" id="{{$formContent->id}}">
    <div class="card">
        <div class="card-header ">
            <div class="row">
                <div class="col-11" id="label{{$formContent->id}}">
                    {{getContentValue($formContent->id,"label") ?? " Please select your Magnet Programs choices below"}} 
                   
                </div>
                <div class="col-1">
                    <a class="btn text-danger removeField"><i class="fa fa-times"></i></a>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div class="b-600 font-14 mb-10">First Program Choice</div>
            <div class="border p-20 mb-20">
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Program : </label>
                    <div class="col-12 col-lg-6">
                        <select class="form-control custom-select">
                            <option value="">Choose an option</option>
                            <option value="">Phillips - Magnet Program - Grade 9</option>
                            <option value="">Phillips - Magnet Program - Grade 8</option>
                            <option value="">Phillips - Magnet Program - Grade 7</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Will a Sibling of this applicant attend this school for the upcoming school year?</label>
                    <div class="col-12 col-lg-6">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline1" name="customRadioInline1" class="custom-control-input">
                            <label class="custom-control-label" for="customRadioInline1">Yes</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline2" name="customRadioInline1" class="custom-control-input">
                            <label class="custom-control-label" for="customRadioInline2">No</label>
                        </div>
                    </div>
                </div>
                <div class="form-group row" style="display: none;">
                    <label class="col-12 col-lg-4">Sibling State ID# : </label>
                    <div class="col-12 col-lg-6">
                        <input type="text" class="form-control">
                    </div>
                </div>
            </div>
            <div class="b-600 font-14 mb-10">Second Program Choice</div>
            <div class="border p-20 mb-20">
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Program : </label>
                    <div class="col-12 col-lg-6">
                        <select class="form-control custom-select">
                            <option value="">Choose an option</option>
                            <option value="">Phillips - Magnet Program - Grade 9</option>
                            <option value="">Phillips - Magnet Program - Grade 8</option>
                            <option value="">Phillips - Magnet Program - Grade 7</option>
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label class="col-12 col-lg-4">Will a Sibling of this applicant attend this school for the upcoming school year?</label>
                    <div class="col-12 col-lg-6">
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline3" name="customRadioInline" class="custom-control-input">
                            <label class="custom-control-label" for="customRadioInline3">Yes</label>
                        </div>
                        <div class="custom-control custom-radio custom-control-inline">
                            <input type="radio" id="customRadioInline4" name="customRadioInline" class="custom-control-input">
                            <label class="custom-control-label" for="customRadioInline4">No</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>