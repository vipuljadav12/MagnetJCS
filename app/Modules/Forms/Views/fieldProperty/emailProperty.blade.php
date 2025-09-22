<div class="form-group">
    <label>Label Text</label> 
    <input type="text" class="form-control" id="email_label_setting" property="label" onchange="setValue(this)">
</div>
<div class="form-group">
    <label>Placeholder Text</label> 
    <input type="text" class="form-control" id="email_placeholder_setting" onchange="setValue(this)" property="placeholder">
</div>
<div class="form-group">
    <label>Help Text</label>
    <textarea class="form-control" id="email_help_text_setting" onchange="setValue(this)" property="helptext"></textarea>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Require This Field</label>
    <div><input type="checkbox" class="js-switch" id="email_required_setting" onchange="setValue(this)" property="required"/></div>
</div>
<div class="form-group requiredvalidationmsg" style="display: none;">
    <label>Required Validation Message</label>
    <input type="text" class="form-control" id="email_required_validation_msg_setting" property="requiredvalidationmsg" onchange="setValue(this)">
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Show/Hide</label>
    <div><input type="checkbox" class="js-switch" id="email_showhide_setting" property="showhide" onchange="setValue(this)"/></div>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Read Only</label>
    <div><input type="checkbox" class="js-switch" id="email_readonly_setting" property="readonly" onchange="setValue(this)"/></div>
</div>