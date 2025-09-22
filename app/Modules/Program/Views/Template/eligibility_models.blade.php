<!-- Modal -->

<div class="modal fade" id="modal_1" tabindex="-1" role="dialog" aria-labelledby="modal_1Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_1Label">Edit Eligibility - Interview Score</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                {{--<div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-header">Available Grade Level (Check all that apply)</div>
                    <div class="card-body">
                        <div class="">
                            <!--<div class="form-group">
                                <label for="" class="">Name of Interview Score : </label>
                                <div class=""><input type="text" class="form-control"></div>
                            </div>-->
                            <div class="form-group">
                                <!--<label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>-->
                                <div class="row flex-wrap">
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="PreK" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table32" checked>
                                            <label for="table32" class="custom-control-label">PreK</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="K" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table33">
                                            <label for="table33" class="custom-control-label">K</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="1" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table34">
                                            <label for="table34" class="custom-control-label">1</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="2" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table35">
                                            <label for="table35" class="custom-control-label">2</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="3" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table36">
                                            <label for="table36" class="custom-control-label">3</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="4" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table37">
                                            <label for="table37" class="custom-control-label">4</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="5" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table38">
                                            <label for="table38" class="custom-control-label">5</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="6" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table39">
                                            <label for="table39" class="custom-control-label">6</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="7" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table40">
                                            <label for="table40" class="custom-control-label">7</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="8" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table41">
                                            <label for="table41" class="custom-control-label">8</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="9" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table42">
                                            <label for="table42" class="custom-control-label">9</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="10" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table43">
                                            <label for="table43" class="custom-control-label">10</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="11" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table44">
                                            <label for="table44" class="custom-control-label">11</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="12" name="eligibility_grade_lavel['interview_score'][]" class="custom-control-input" id="table45">
                                            <label for="table45" class="custom-control-label">12</label></div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="">
                                <label class="control-label"><strong>Select Interview Score Value Method (Select only one) :</strong> </label>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Yes / No Selection : </div>
                                        <input id="chk_6" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_6" data-size="Small" checked>
                                    </div>
                                    <div class="custom-field-list">
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for YES : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for NO : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                        </div>
                                </div>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Numerical Ranking : </div>
                                        <input id="chk_7" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_7" data-size="Small" checked>
                                    </div>
                                    <div class="option-list-outer">
                                        <div class="option-list-custome">
                                            <div class="form-group">
                                                <label class="control-label">Option 1 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 2 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 3 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 4 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                        </div>
                                        <div class=""><a href="javascript:void(0);" class="font-18 add-option-list-custome" title=""><i class="fas fa-plus-square"></i></a></div>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                {{--<button type="button" class="btn btn-success" data-dismiss="modal">Save</button>--}}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_2" tabindex="-1" role="dialog" aria-labelledby="modal_2Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_2Label">Edit Eligibility - Grades Score</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                {{--<div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-header">Available Grade Level (Check all that apply)</div>
                    <div class="card-body">
                        <div class="">
                            <!--<div class="form-group">
                                <label for="" class="">Name of Interview Score : </label>
                                <div class=""><input type="text" class="form-control"></div>
                            </div>-->
                            <div class="form-group">
                                <!--<label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>-->
                                <div class="row flex-wrap">
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="PreK" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table32" checked>
                                            <label for="table32" class="custom-control-label">PreK</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="K" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table33">
                                            <label for="table33" class="custom-control-label">K</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="1" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table34">
                                            <label for="table34" class="custom-control-label">1</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="2" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table35">
                                            <label for="table35" class="custom-control-label">2</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="3" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table36">
                                            <label for="table36" class="custom-control-label">3</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="4" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table37">
                                            <label for="table37" class="custom-control-label">4</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="5" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table38">
                                            <label for="table38" class="custom-control-label">5</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="6" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table39">
                                            <label for="table39" class="custom-control-label">6</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="7" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table40">
                                            <label for="table40" class="custom-control-label">7</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="8" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table41">
                                            <label for="table41" class="custom-control-label">8</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="9" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table42">
                                            <label for="table42" class="custom-control-label">9</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="10" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table43">
                                            <label for="table43" class="custom-control-label">10</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="11" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table44">
                                            <label for="table44" class="custom-control-label">11</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" value="12" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table45">
                                            <label for="table45" class="custom-control-label">12</label></div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="">
                                <label class="control-label"><strong>Select Interview Score Value Method (Select only one) :</strong> </label>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Yes / No Selection : </div>
                                        <input id="chk_6" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_6" data-size="Small" checked>
                                    </div>
                                    <div class="custom-field-list">
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for YES : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for NO : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                        </div>
                                </div>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Numerical Ranking : </div>
                                        <input id="chk_7" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_7" data-size="Small" checked>
                                    </div>
                                    <div class="option-list-outer">
                                        <div class="option-list-custome">
                                            <div class="form-group">
                                                <label class="control-label">Option 1 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 2 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 3 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 4 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                        </div>
                                        <div class=""><a href="javascript:void(0);" class="font-18 add-option-list-custome" title=""><i class="fas fa-plus-square"></i></a></div>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
{{--                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>--}}
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_3" tabindex="-1" role="dialog" aria-labelledby="modal_3Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_3Label">Edit Eligibility - Academic Grade Score</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                {{--<div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-header">Available Grade Level (Check all that apply)</div>
                    <div class="card-body">
                        <div class="">
                            <!--<div class="form-group">
                                <label for="" class="">Name of Academic Grade Score : </label>
                                <div class=""><input type="text" class="form-control"></div>
                            </div>-->
                            <div class="form-group">
                                <!--<label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>-->
                                <div class="row flex-wrap">
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table32" checked>
                                            <label for="table32" class="custom-control-label">PreK</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table33">
                                            <label for="table33" class="custom-control-label">K</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table34">
                                            <label for="table34" class="custom-control-label">1</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table35">
                                            <label for="table35" class="custom-control-label">2</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table36">
                                            <label for="table36" class="custom-control-label">3</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table37">
                                            <label for="table37" class="custom-control-label">4</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table38">
                                            <label for="table38" class="custom-control-label">5</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table39">
                                            <label for="table39" class="custom-control-label">6</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table40">
                                            <label for="table40" class="custom-control-label">7</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table41">
                                            <label for="table41" class="custom-control-label">8</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table42">
                                            <label for="table42" class="custom-control-label">9</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table43">
                                            <label for="table43" class="custom-control-label">10</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table44">
                                            <label for="table44" class="custom-control-label">11</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table45">
                                            <label for="table45" class="custom-control-label">12</label></div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="">
                                <label class="control-label"><strong>Select Academic Grade Score Value Method (Select only one) :</strong> </label>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Yes / No Selection : </div>
                                        <input id="chk_6" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_6" data-size="Small" checked>
                                    </div>
                                    <div class="custom-field-list">
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for YES : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for NO : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                        </div>
                                </div>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Numerical Ranking : </div>
                                        <input id="chk_7" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_7" data-size="Small" checked>
                                    </div>
                                    <div class="option-list-outer">
                                        <div class="option-list-custome">
                                            <div class="form-group">
                                                <label class="control-label">Option 1 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 2 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 3 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 4 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                        </div>
                                        <div class=""><a href="javascript:void(0);" class="font-18 add-option-list-custome" title=""><i class="fas fa-plus-square"></i></a></div>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_4" tabindex="-1" role="dialog" aria-labelledby="modal_4Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_4Label">Edit Eligibility - Recommendation Form 1</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                {{--<div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-body">
                        <div class="">
                            <div class="form-group">
                                <label class="control-label">Select Prior Developed Recommendation Form: : </label>
                                <div class="">
                                    <select class="form-control custom-select">
                                        <option value="">HCS STEM Teacher Recommendation</option>
                                        <option value="">HCS Principal Recommendation</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_5" tabindex="-1" role="dialog" aria-labelledby="modal_5Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_5Label">Edit Eligibility - Writing Prompt Score</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                {{--<div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-header">Available Grade Level (Check all that apply)</div>
                    <div class="card-body">
                        <div class="">
                            <!--<div class="form-group">
                                <label for="" class="">Name of Writing Prompt Score : </label>
                                <div class=""><input type="text" class="form-control"></div>
                            </div>-->
                            <div class="form-group">
                                <!--<label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>-->
                                <div class="row flex-wrap">
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table32" checked>
                                            <label for="table32" class="custom-control-label">PreK</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table33">
                                            <label for="table33" class="custom-control-label">K</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table34">
                                            <label for="table34" class="custom-control-label">1</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table35">
                                            <label for="table35" class="custom-control-label">2</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table36">
                                            <label for="table36" class="custom-control-label">3</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table37">
                                            <label for="table37" class="custom-control-label">4</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table38">
                                            <label for="table38" class="custom-control-label">5</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table39">
                                            <label for="table39" class="custom-control-label">6</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table40">
                                            <label for="table40" class="custom-control-label">7</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table41">
                                            <label for="table41" class="custom-control-label">8</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table42">
                                            <label for="table42" class="custom-control-label">9</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table43">
                                            <label for="table43" class="custom-control-label">10</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table44">
                                            <label for="table44" class="custom-control-label">11</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table45">
                                            <label for="table45" class="custom-control-label">12</label></div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="">
                                <label class="control-label"><strong>Select Writing Prompt Score Value Method (Select only one) :</strong> </label>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Yes / No Selection : </div>
                                        <input id="chk_6" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_6" data-size="Small" checked>
                                    </div>
                                    <div class="custom-field-list">
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for YES : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for NO : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                        </div>
                                </div>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Numerical Ranking : </div>
                                        <input id="chk_7" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_7" data-size="Small" checked>
                                    </div>
                                    <div class="option-list-outer">
                                        <div class="option-list-custome">
                                            <div class="form-group">
                                                <label class="control-label">Option 1 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 2 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 3 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 4 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                        </div>
                                        <div class=""><a href="javascript:void(0);" class="font-18 add-option-list-custome" title=""><i class="fas fa-plus-square"></i></a></div>
                                    </div>
                                </div>
                                <div class="form-group d-flex flex-wrap">
                                    <div class="mr-10">None / Display Only : </div>
                                    <input id="chk_9" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_6" tabindex="-1" role="dialog" aria-labelledby="modal_6Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_6Label">Edit Eligibility - Audition Score</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                {{--<div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-header">Available Grade Level (Check all that apply)</div>
                    <div class="card-body">
                        <div class="">
                            <!--<div class="form-group">
                                <label for="" class="">Name of Audition Score : </label>
                                <div class=""><input type="text" class="form-control"></div>
                            </div>-->
                            <div class="form-group">
                                <!--<label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>-->
                                <div class="row flex-wrap">
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table32" checked>
                                            <label for="table32" class="custom-control-label">PreK</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table33">
                                            <label for="table33" class="custom-control-label">K</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table34">
                                            <label for="table34" class="custom-control-label">1</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table35">
                                            <label for="table35" class="custom-control-label">2</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table36">
                                            <label for="table36" class="custom-control-label">3</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table37">
                                            <label for="table37" class="custom-control-label">4</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table38">
                                            <label for="table38" class="custom-control-label">5</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table39">
                                            <label for="table39" class="custom-control-label">6</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table40">
                                            <label for="table40" class="custom-control-label">7</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table41">
                                            <label for="table41" class="custom-control-label">8</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table42">
                                            <label for="table42" class="custom-control-label">9</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table43">
                                            <label for="table43" class="custom-control-label">10</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table44">
                                            <label for="table44" class="custom-control-label">11</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table45">
                                            <label for="table45" class="custom-control-label">12</label></div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="">
                                <label class="control-label"><strong>Select Audition Score Value Method (Select only one) :</strong> </label>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Yes / No Selection : </div>
                                        <input id="chk_6" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_6" data-size="Small" checked>
                                    </div>
                                    <div class="custom-field-list">
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for YES : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for NO : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                        </div>
                                </div>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Numerical Ranking : </div>
                                        <input id="chk_7" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_7" data-size="Small" checked>
                                    </div>
                                    <div class="option-list-outer">
                                        <div class="option-list-custome">
                                            <div class="form-group">
                                                <label class="control-label">Option 1 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 2 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 3 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 4 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                        </div>
                                        <div class=""><a href="javascript:void(0);" class="font-18 add-option-list-custome" title=""><i class="fas fa-plus-square"></i></a></div>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_7" tabindex="-1" role="dialog" aria-labelledby="modal_7Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_7Label">Edit Eligibility - Committee Score</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                {{--<div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-header">Available Grade Level (Check all that apply)</div>
                    <div class="card-body">
                        <div class="">
                            <!--<div class="form-group">
                                <label for="" class="">Name of Committee Score : </label>
                                <div class=""><input type="text" class="form-control"></div>
                            </div>-->
                            <div class="form-group">
                                <!--<label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>-->
                                <div class="row flex-wrap">
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table32" checked>
                                            <label for="table32" class="custom-control-label">PreK</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table33">
                                            <label for="table33" class="custom-control-label">K</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table34">
                                            <label for="table34" class="custom-control-label">1</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table35">
                                            <label for="table35" class="custom-control-label">2</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table36">
                                            <label for="table36" class="custom-control-label">3</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table37">
                                            <label for="table37" class="custom-control-label">4</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table38">
                                            <label for="table38" class="custom-control-label">5</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table39">
                                            <label for="table39" class="custom-control-label">6</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table40">
                                            <label for="table40" class="custom-control-label">7</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table41">
                                            <label for="table41" class="custom-control-label">8</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table42">
                                            <label for="table42" class="custom-control-label">9</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table43">
                                            <label for="table43" class="custom-control-label">10</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table44">
                                            <label for="table44" class="custom-control-label">11</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table45">
                                            <label for="table45" class="custom-control-label">12</label></div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="">
                                <label class="control-label"><strong>Select Committee Score Value Method (Select only one) :</strong> </label>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Yes / No Selection : </div>
                                        <input id="chk_6" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_6" data-size="Small" checked>
                                    </div>
                                    <div class="custom-field-list">
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for YES : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for NO : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                        </div>
                                </div>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Numerical Ranking : </div>
                                        <input id="chk_7" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_7" data-size="Small" checked>
                                    </div>
                                    <div class="option-list-outer">
                                        <div class="option-list-custome">
                                            <div class="form-group">
                                                <label class="control-label">Option 1 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 2 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 3 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 4 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                        </div>
                                        <div class=""><a href="javascript:void(0);" class="font-18 add-option-list-custome" title=""><i class="fas fa-plus-square"></i></a></div>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_8" tabindex="-1" role="dialog" aria-labelledby="modal_8Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_8Label">Edit Eligibility - Conduct Score</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                {{--<div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-header">Available Grade Level (Check all that apply)</div>
                    <div class="card-body">
                        <div class="">
                            <!--<div class="form-group">
                                <label for="" class="">Name of Conduct Score : </label>
                                <div class=""><input type="text" class="form-control"></div>
                            </div>-->
                            <div class="form-group">
                                <!--<label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>-->
                                <div class="row flex-wrap">
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table32" checked>
                                            <label for="table32" class="custom-control-label">PreK</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table33">
                                            <label for="table33" class="custom-control-label">K</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table34">
                                            <label for="table34" class="custom-control-label">1</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table35">
                                            <label for="table35" class="custom-control-label">2</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table36">
                                            <label for="table36" class="custom-control-label">3</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table37">
                                            <label for="table37" class="custom-control-label">4</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table38">
                                            <label for="table38" class="custom-control-label">5</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table39">
                                            <label for="table39" class="custom-control-label">6</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table40">
                                            <label for="table40" class="custom-control-label">7</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table41">
                                            <label for="table41" class="custom-control-label">8</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table42">
                                            <label for="table42" class="custom-control-label">9</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table43">
                                            <label for="table43" class="custom-control-label">10</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table44">
                                            <label for="table44" class="custom-control-label">11</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table45">
                                            <label for="table45" class="custom-control-label">12</label></div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="">
                                <label class="control-label"><strong>Select Conduct Score Value Method (Select only one) :</strong> </label>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Yes / No Selection : </div>
                                        <input id="chk_6" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_6" data-size="Small" checked>
                                    </div>
                                    <div class="custom-field-list">
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for YES : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for NO : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                        </div>
                                </div>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Numerical Ranking : </div>
                                        <input id="chk_7" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_7" data-size="Small" checked>
                                    </div>
                                    <div class="option-list-outer">
                                        <div class="option-list-custome">
                                            <div class="form-group">
                                                <label class="control-label">Option 1 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 2 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 3 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 4 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                        </div>
                                        <div class=""><a href="javascript:void(0);" class="font-18 add-option-list-custome" title=""><i class="fas fa-plus-square"></i></a></div>
                                    </div>
                                </div>
                                <div class="form-group d-flex flex-wrap">
                                    <div class="mr-10">None / Display Only : </div>
                                    <input id="chk_9" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_9" tabindex="-1" role="dialog" aria-labelledby="modal_9Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_9Label">Edit Eligibility - Special Ed Indicators Score</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
               {{-- <div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-header">Available Grade Level (Check all that apply)</div>
                    <div class="card-body">
                        <div class="">
                            <!--<div class="form-group">
                                <label for="" class="">Name of Special Ed Indicator Score : </label>
                                <div class=""><input type="text" class="form-control"></div>
                            </div>-->
                            <div class="form-group">
                                <!--<label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>-->
                                <div class="row flex-wrap">
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table32" checked>
                                            <label for="table32" class="custom-control-label">PreK</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table33">
                                            <label for="table33" class="custom-control-label">K</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table34">
                                            <label for="table34" class="custom-control-label">1</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table35">
                                            <label for="table35" class="custom-control-label">2</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table36">
                                            <label for="table36" class="custom-control-label">3</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table37">
                                            <label for="table37" class="custom-control-label">4</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table38">
                                            <label for="table38" class="custom-control-label">5</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table39">
                                            <label for="table39" class="custom-control-label">6</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table40">
                                            <label for="table40" class="custom-control-label">7</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table41">
                                            <label for="table41" class="custom-control-label">8</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table42">
                                            <label for="table42" class="custom-control-label">9</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table43">
                                            <label for="table43" class="custom-control-label">10</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table44">
                                            <label for="table44" class="custom-control-label">11</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table45">
                                            <label for="table45" class="custom-control-label">12</label></div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="">
                                <label class="control-label"><strong>Select Special Ed Indicators Score Value Method (Select only one) :</strong> </label>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Yes / No Selection : </div>
                                        <input id="chk_6" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_6" data-size="Small" checked>
                                    </div>
                                    <div class="custom-field-list">
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for YES : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for NO : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                        </div>
                                </div>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Numerical Ranking : </div>
                                        <input id="chk_7" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_7" data-size="Small" checked>
                                    </div>
                                    <div class="option-list-outer">
                                        <div class="option-list-custome">
                                            <div class="form-group">
                                                <label class="control-label">Option 1 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 2 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 3 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 4 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                        </div>
                                        <div class=""><a href="javascript:void(0);" class="font-18 add-option-list-custome" title=""><i class="fas fa-plus-square"></i></a></div>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_10" tabindex="-1" role="dialog" aria-labelledby="modal_10Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_10Label">Edit Eligibility - Standardized Testing Score</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                {{--<div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-header">Available Grade Level (Check all that apply)</div>
                    <div class="card-body">
                        <div class="">
                            <!--<div class="form-group">
                                <label for="" class="">Name of Standardized Testing Score : </label>
                                <div class=""><input type="text" class="form-control"></div>
                            </div>-->
                            <div class="form-group">
                                <!--<label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>-->
                                <div class="row flex-wrap">
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table32" checked>
                                            <label for="table32" class="custom-control-label">PreK</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table33">
                                            <label for="table33" class="custom-control-label">K</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table34">
                                            <label for="table34" class="custom-control-label">1</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table35">
                                            <label for="table35" class="custom-control-label">2</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table36">
                                            <label for="table36" class="custom-control-label">3</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table37">
                                            <label for="table37" class="custom-control-label">4</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table38">
                                            <label for="table38" class="custom-control-label">5</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table39">
                                            <label for="table39" class="custom-control-label">6</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table40">
                                            <label for="table40" class="custom-control-label">7</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table41">
                                            <label for="table41" class="custom-control-label">8</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table42">
                                            <label for="table42" class="custom-control-label">9</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table43">
                                            <label for="table43" class="custom-control-label">10</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table44">
                                            <label for="table44" class="custom-control-label">11</label></div>
                                    </div>
                                    <div class="col-12 col-sm-4 col-lg-2">
                                        <div class="custom-control custom-checkbox"><input type="checkbox" class="custom-control-input" id="table45">
                                            <label for="table45" class="custom-control-label">12</label></div>
                                    </div>
                                </div>
                            </div>
                            <!--<div class="">
                                <label class="control-label"><strong>Select Standardized Testing Score Value Method (Select only one) :</strong> </label>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Yes / No Selection : </div>
                                        <input id="chk_6" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_6" data-size="Small" checked>
                                    </div>
                                    <div class="custom-field-list">
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for YES : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label">Enter Custom Field Label for NO : </label>
                                        <div class=""><input type="text" class="form-control" value=""></div>
                                    </div>
                                    </div>
                                </div>
                                <div class="">
                                    <div class="form-group d-flex flex-wrap">
                                        <div class="mr-10">Numerical Ranking : </div>
                                        <input id="chk_7" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_7" data-size="Small" checked>
                                    </div>
                                    <div class="option-list-outer">
                                        <div class="option-list-custome">
                                            <div class="form-group">
                                                <label class="control-label">Option 1 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 2 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 3 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                            <div class="form-group">
                                                <label class="control-label">Option 4 : </label>
                                                <div class=""><input type="text" class="form-control" value=""></div>
                                            </div>
                                        </div>
                                        <div class=""><a href="javascript:void(0);" class="font-18 add-option-list-custome" title=""><i class="fas fa-plus-square"></i></a></div>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal_11" tabindex="-1" role="dialog" aria-labelledby="modal_11Label" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div class="modal-title" id="modal_11Label">Edit Eligibility - Recommendation Form 2 Score</div>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"> <span aria-hidden="true">&times;</span> </button>
            </div>
            <div class="modal-body">
                {{--<div class="card shadow mb-20 d-none">
                    <div class="card-header">Used in Determination Method</div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap">
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Basic Method Only Active : </div>
                                <input id="chk_3" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                            <div class="d-flex mb-10 mr-30">
                                <div class="mr-10">Combined Scoring Active : </div>
                                <input id="chk_4" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small" checked>
                            </div>
                            <div class="d-flex mb-10">
                                <div class="mr-10">Final Scoring Active : </div>
                                <input id="chk_5" type="checkbox" class="js-switch js-switch-1 js-switch-xs" data-size="Small">
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="card shadow">
                    <div class="card-body">
                        <div class="">
                            <div class="form-group">
                                <label class="control-label">Select Prior Developed Recommendation Form: : </label>
                                <div class="">
                                    <select class="form-control custom-select">
                                        <option value="">HCS STEM Teacher Recommendation</option>
                                        <option value="">HCS Principal Recommendation</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">Save</button>
            </div>
        </div>
    </div>
</div>
<div class="card shadow">
    <div class="card-header">Available Grade Level (Check all that apply)</div>
    <div class="card-body">
        <div class="">
            <!--<div class="form-group">
                <label for="" class="">Name of Grade Score : </label>
                <div class=""><input type="text" class="form-control"></div>
            </div>-->
            <div class="form-group">
                <!--<label class="control-label"><strong>Available Grade Level (Check all that apply) :</strong> </label>-->
                <div class="row flex-wrap">
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="PreK" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table32" checked>
                            <label for="table32" class="custom-control-label">PreK</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="K" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table33">
                            <label for="table33" class="custom-control-label">K</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="1" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table34">
                            <label for="table34" class="custom-control-label">1</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="2" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table35">
                            <label for="table35" class="custom-control-label">2</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="3" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table36">
                            <label for="table36" class="custom-control-label">3</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="4" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table37">
                            <label for="table37" class="custom-control-label">4</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="5" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table38">
                            <label for="table38" class="custom-control-label">5</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="6" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table39">
                            <label for="table39" class="custom-control-label">6</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="7" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table40">
                            <label for="table40" class="custom-control-label">7</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="8" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table41">
                            <label for="table41" class="custom-control-label">8</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="9" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table42">
                            <label for="table42" class="custom-control-label">9</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="10" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table43">
                            <label for="table43" class="custom-control-label">10</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="11" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table44">
                            <label for="table44" class="custom-control-label">11</label></div>
                    </div>
                    <div class="col-12 col-sm-4 col-lg-2">
                        <div class="custom-control custom-checkbox"><input type="checkbox" value="12" name="eligibility_grade_lavel['grade'][]" class="custom-control-input" id="table45">
                            <label for="table45" class="custom-control-label">12</label></div>
                    </div>
                </div>
            </div>
            <!--<div class="">
                <label class="control-label"><strong>Select Grades Score Value Method (Select only one) :</strong> </label>
                <div class="">
                    <div class="form-group d-flex flex-wrap">
                        <div class="mr-10">Yes / No Selection : </div>
                        <input id="chk_6" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_6" data-size="Small" checked>
                    </div>
                    <div class="custom-field-list">
                    <div class="form-group">
                        <label class="control-label">Enter Custom Field Label for YES : </label>
                        <div class=""><input type="text" class="form-control" value=""></div>
                    </div>
                    <div class="form-group">
                        <label class="control-label">Enter Custom Field Label for NO : </label>
                        <div class=""><input type="text" class="form-control" value=""></div>
                    </div>
                        </div>
                </div>
                <div class="">
                    <div class="form-group d-flex flex-wrap">
                        <div class="mr-10">Numerical Ranking : </div>
                        <input id="chk_7" type="checkbox" class="js-switch js-switch-1 js-switch-xs chk_7" data-size="Small" checked>
                    </div>
                    <div class="option-list-outer">
                        <div class="option-list-custome">
                            <div class="form-group">
                                <label class="control-label">Option 1 : </label>
                                <div class=""><input type="text" class="form-control" value=""></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Option 2 : </label>
                                <div class=""><input type="text" class="form-control" value=""></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Option 3 : </label>
                                <div class=""><input type="text" class="form-control" value=""></div>
                            </div>
                            <div class="form-group">
                                <label class="control-label">Option 4 : </label>
                                <div class=""><input type="text" class="form-control" value=""></div>
                            </div>
                        </div>
                        <div class=""><a href="javascript:void(0);" class="font-18 add-option-list-custome" title=""><i class="fas fa-plus-square"></i></a></div>
                    </div>
                </div>
            </div>-->
        </div>
    </div>
</div>