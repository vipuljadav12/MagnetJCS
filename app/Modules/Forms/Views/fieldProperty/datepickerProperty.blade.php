<div class="form-group"><label>Label Text</label>
  <input type="text" class="form-control" id="date_label_setting" property="label" onchange="setValue(this)">
</div>
<div class="form-group"><label>Placeholder Text</label> 
  <input type="text" class="form-control" id="date_placeholder_setting" property="placeholder" onchange="setValue(this)">
</div>

<div class="form-group"><label>Help Text</label>
  <textarea class="form-control" id="date_help_text_setting" property="helptext" onchange="setValue(this)"></textarea>
</div>
<div class="form-group d-flex justify-content-between align-items-center"><label>Require This Field</label>
  <div><input type="checkbox" class="js-switch" id="date_required_setting" property="required" onchange="setValue(this)" /></div>
</div>
<div class="form-group requiredvalidationmsg" >
    <label>Required Validation Message</label>
    <input type="text" class="form-control" id="date_required_validation_msg_setting" property="requiredvalidationmsg" onchange="setValue(this)">
</div>
<div class="form-group d-flex justify-content-between align-items-center">
  <label>Show/Hide</label>
  <input type="checkbox" class="js-switch" data-switchery="true" id="date_showhide_setting" property="showhide" onchange="setValue(this)">
</div>
<div class="form-group d-flex justify-content-between align-items-center">
  <label>Read Only</label>
  <input type="checkbox" class="js-switch" data-switchery="true" id="date_readonly_setting" property="readonly" onchange="setValue(this)">
</div>