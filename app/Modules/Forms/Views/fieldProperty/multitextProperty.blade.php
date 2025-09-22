<div class="form-group">
    <label>Placeholder Text</label> 
    <input type="text" class="form-control">
</div>
<div class="form-group">
    <label>Field Validation</label>
    <select class="form-control custom-select">
        <option value="Alphabetic">Alphabetic</option>
        <option value="Numeric">Numeric</option>
        <option value="Alphanumeric">Alphanumeric</option>
        <option value="Web">Web</option>
    </select>
</div>
<div class="form-group">
    <label>Number of Characters</label>
    <div class="d-flex align-items-center">
        <input type="text" class="form-control w-30" placeholder="Minimum">
        <div class="ml-5 mr-5">to</div>
        <input type="text" class="form-control w-30" placeholder="Maximum">
    </div>
</div>
<div class="form-group">
    <label>Help Text</label>
    <textarea class="form-control"></textarea>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Require This Field</label>
    <div><input type="checkbox" class="js-switch" /></div>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Show/Hide</label>
    <div><input type="checkbox" class="js-switch" id="multitext_showhide_setting" onchange="setValue(this)"></div>
</div>
<div class="form-group d-flex justify-content-between align-items-center">
    <label>Read Only</label>
    <div><input type="checkbox" class="js-switch" id="multitext_readonly_setting" onchange="setValue(this)" name=""></div>
</div>