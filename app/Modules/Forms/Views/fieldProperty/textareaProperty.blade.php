<div class="form-group">
    <label>Label Text</label> 
    <input type="text" class="form-control" id="textarea_label_setting" property="label" onchange="setValue(this)">
</div>
<div class="form-group">
    <label>Placeholder Text</label>
    <input type="text" class="form-control" id="textarea_placeholder_setting" property="placeholder" onchange="setValue(this)">
</div>
<div class="form-group">
    <label>Rows</label>
    <input type="text" class="form-control" id="textarea_rows_setting" property="rows" onchange="setValue(this)">
</div>
<div class="form-group">
    <label>Number of Characters</label>
    <div class="d-flex align-items-center">
        <input type="text" class="form-control w-30" placeholder="Minimum" id="textarea_minimum_setting" property="minimum" onchange="setValue(this)">
        <div class="ml-5 mr-5">to</div>
        <input type="text" class="form-control w-30" placeholder="Maximum" id="textarea_maximum_setting" property="maximum" onchange="setValue(this)">
    </div>
</div>
<div class="form-group">
    <label>Help Text</label>
    <textarea class="form-control" id="textarea_help_text_setting" property="helptext" onchange="setValue(this)"></textarea>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Require This Field</label>
    <div><input type="checkbox" class="js-switch" id="textarea_required_setting" property="required" onchange="setValue(this)" /></div>
</div>
<div class="form-group requiredvalidationmsg" style="display: none;">
    <label>Required Validation Message</label>
    <input type="text" class="form-control" id="textarea_required_validation_msg_setting" property="requiredvalidationmsg" onchange="setValue(this)">
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Show/Hide</label>
    <div><input type="checkbox" class="js-switch" id="textarea_showhide_setting" property="showhide"  property="showhide"  onchange="setValue(this)"></div>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Read Only</label>
    <div><input type="checkbox" class="js-switch" id="textarea_readonly_setting" property="readonly" property="readonly" onchange="setValue(this)"></div>
</div>