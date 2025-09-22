(function() {
    "use strict"

jQuery('.mydatepicker, #datepicker').datepicker({format: 'yyyy-mm-dd',autoclose: true,});
jQuery('.bdatepicker').datepicker({format: 'M dd, yyyy',autoclose: true,});

	jQuery('.mydatepicker01, #datepicker-autoclose').datepicker({
		autoclose: true,
		todayHighlight: true,
		format: 'yyyy-mm-dd'
	});
	jQuery('#date-range').datepicker({
		toggleActive: true
	});
	jQuery('#datepicker-inline').datepicker({
		todayHighlight: true
	});
})(jQuery);