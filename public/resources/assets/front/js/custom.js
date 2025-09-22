(function ($) {
    'use strict';
	
	//circular switch
	var elem1 = Array.prototype.slice.call($('.js-switch-1'));
	elem1.forEach(html => {
		new Switchery(html, {
			color: '#72a84b', 
			secondaryColor: '#c82333'
		});
	});

	//square switch
	var elem2 = Array.prototype.slice.call($('.js-switch-2'));
	elem2.forEach(html => {
		new Switchery(html, {
			color: '#72a84b', 
			secondaryColor: '#c82333'
		});
	});
})(jQuery);

$(document).ready(function(){
	$('[data-toggle="tooltip"]').tooltip();
});