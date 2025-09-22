<div class="form-group">
    <label>Label Text</label> 
    <input type="text" class="form-control" id="uploadfile_label_setting" property="label" onchange="setValue(this)">
</div>
<div class="form-group">
    <label>Field Validation</label>
    <select class="form-control custom-select" id="uploadfile_format_validation_setting" property="formatvalidation" onchange="setValue(this)">
        <option selected="selected" value="All">All</option>
        <option value="Doc">Doc</option>
        <option value="Image">Image</option>
    </select>
</div>
<div class="form-group">
    <label>Help Text</label>
    <textarea class="form-control" id="uploadfile_help_text_setting" onchange="setValue(this)" property="helptext"></textarea>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Require This Field</label>
    <div><input type="checkbox" class="js-switch" id="uploadfile_required_setting" onchange="setValue(this)" property="required"/></div>
</div>
<div class="form-group requiredvalidationmsg" >
    <label>Required Validation Message</label>
    <input type="text" class="form-control" id="uploadfile_required_validation_msg_setting" property="requiredvalidationmsg" onchange="setValue(this)">
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Show/Hide</label>
    <div><input type="checkbox" class="js-switch" id="uploadfile_showhide_setting" property="showhide" onchange="setValue(this)"/></div>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Read Only</label>
    <div><input type="checkbox" class="js-switch" id="uploadfile_readonly_setting" property="readonly" onchange="setValue(this)"/></div>
</div>