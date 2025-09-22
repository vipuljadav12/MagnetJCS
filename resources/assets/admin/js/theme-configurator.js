// JavaScript Document
/*Side menu click*/
var special = document.querySelector('#chk_1')
        , specialButton = document.querySelector('.sidemenu-btn');

specialButton.addEventListener('click', function() {
    if (special.checked) {
        special.checked = false;
        onChange(special);
    }
    else {
        special.checked = true;
        onChange(special);
    }
});

function onChange(el) {
    if (typeof Event === 'function' || !document.fireEvent) {
        var event = document.createEvent('HTMLEvents');
        event.initEvent('change', true, true);
        el.dispatchEvent(event);
    } else {
        el.fireEvent('onchange');
    }
}

$(document).ready(function(){
	$('.theme-customizer-btn').on("click",function(){
		$('.theme-customizer').toggleClass('active');
	});	
	$('.color-wrapper li').on("click",function(){
		$(this).addClass('active').siblings('li').removeClass('active');
		var cc = $(this).find('.color-box').attr('datacolor');
		$('body').attr('data-bg',cc);
		$('header').attr('dataheader-bg',cc);		
		return custcolor(cc);
	});
	$('.sidebar-wrapper li').on("click",function(){
		$(this).addClass('active').siblings('li').removeClass('active');
		var dd = $(this).find('.color-box').attr('datacolor');
		$('.side-menu').attr('datasidebar-bg',dd);
	});
})
/*--  --*/	
$('input:radio[name="darkmode"]').on('change', function(){
	if($('#darkmode1').is(':checked')){
		$('body').removeClass('dark-theme');
		$('body').addClass('light-theme');		
		$('.side-menu').attr('datasidebar-bg','theme00');
	}
	else {
		$('body').removeClass('light-theme');
		$('body').addClass('dark-theme');
		$('.side-menu').attr('datasidebar-bg','default');
	}
});
/*-- Header Type --*/	
$('input:radio[name="navbartype"]').on('change', function(){
	if($('#navbartype1').is(':checked')){
		$('main').attr('datanavbar',"hidden");
		$('header').attr('datanavbar',"hidden");
	}
	else if($('#navbartype2').is(':checked')){
		$('main').attr('datanavbar',"static");
		$('header').attr('datanavbar',"static");
	}
	else if($('#navbartype3').is(':checked')){
		$('main').attr('datanavbar',"sticky");
		$('header').attr('datanavbar',"sticky");
	}
	else {
		$('main').attr('datanavbar',"floating");
		$('header').attr('datanavbar',"floating");
	}
});
/*-- Footer Type --*/
$('input:radio[name="footertype"]').on('change', function(){
	if($('#footertype1').is(':checked')){
		$('main').attr('datafooter',"hidden");
		$('footer').attr('datafooter',"hidden");
	}
	else if($('#footertype2').is(':checked')){
		$('main').attr('datafooter',"static");
		$('footer').attr('datafooter',"static");
	}
	else {
		$('main').attr('datafooter',"sticky");
		$('footer').attr('datafooter',"sticky");
	}
});
/*-- Switch Collapse --*/
$('#chk_1').on('change', function(){
	if($('#chk_1').is(':checked')){
		$('body').addClass('enlarged');
		$('.side-menu').addClass('small');		
	}
	else {
		$('body').removeClass('enlarged');
		$('.side-menu').removeClass('small');
	}
})