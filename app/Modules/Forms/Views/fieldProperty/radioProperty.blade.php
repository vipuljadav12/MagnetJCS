<div class="form-group">
    <label>Label Text</label> 
    <input type="text" class="form-control" id="radio_label_setting" property="label" onchange="setValue(this)">
</div>
<div class="form-group">
    <label>Layout</label>
    <select class="form-control custom-select radio-layout" property="layout" onchange="setValue(this)">
        <option value="One Column">One Column</option>
        <option value="Two Column">Two Column</option>
        <option value="Side by side" selected>Side by side</option>
    </select>
</div>
<div class="form-group">
    <label>Choices</label>
    <div class="radio-choice">
        <div class="mb-5 d-flex align-items-center">
            <input type="checkbox" class="mr-10" property="selected" onchange="setValue(this)">
            <input type="text" class="form-control w-50 mr-10 choice" name="choice[]" value="First choice" property="choices" onchange="setValue(this)">
            <a href="javascript:void(0);" title="" class="mr-10 removechoice"><i class="far fa-trash-alt"></i></a>
            <a href="javascript:void(0);" title="" class="addchoice d-none"><i class="far fa-plus-square"></i></a>
        </div>
        <div class="mb-5 d-flex align-items-center">
            <input type="checkbox" class="mr-10" property="selected" onchange="setValue(this)">
            <input type="text" class="form-control w-50 mr-10 choice" name="choice[]" value="Second choice" property="choices" onchange="setValue(this)">
            <a href="javascript:void(0);" title="" class="mr-10 removechoice"><i class="far fa-trash-alt"></i></a>
            <a href="javascript:void(0);" title="" class="addchoice d-none"><i class="far fa-plus-square"></i></a>
        </div>
        <div class="mb-5 d-flex align-items-center">
            <input type="checkbox" class="mr-10" property="selected" onchange="setValue(this)">
            <input type="text" class="form-control w-50 mr-10 choice" name="choice[]" value="Third choice" property="choices" onchange="setValue(this)">
            <a href="javascript:void(0);" title="" class="mr-10 removechoice"><i class="far fa-trash-alt"></i></a>
            <a href="javascript:void(0);" title="" class="addchoice"><i class="far fa-plus-square"></i></a>
        </div>
    </div>
</div>
<div class="form-group">
    <label>Help Text</label>
    <textarea class="form-control" id="radio_help_text_setting" property="helptext" onchange="setValue(this)"></textarea>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Require This Field</label>
    <div><input type="checkbox" class="js-switch" id="radio_required_setting" property="required" onchange="setValue(this)" /></div>
</div>
<div class="form-group requiredvalidationmsg" >
    <label>Required Validation Message</label>
    <input type="text" class="form-control" id="radio_required_validation_msg_setting" property="requiredvalidationmsg" onchange="setValue(this)">
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Show/Hide</label>
    <div><input type="checkbox" class="js-switch" id="radio_showhide_setting" property="showhide" onchange="setValue(this)"></div>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Ready Only</label>
    <div><input type="checkbox" class="js-switch" id="radio_readonly_setting" property="readonly"  onchange="setValue(this)" name=""></div>
</div>