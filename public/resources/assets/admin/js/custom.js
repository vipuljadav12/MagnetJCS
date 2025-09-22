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
	

    function initSlimscrollMenu() {
        $('.slimscroll-menu').slimscroll({
            height: 'auto',
            position: 'right',
            size: "15px",
            color: '#9ea5ab',
            wheelStep: 5,
            alwaysVisible: true,
        });
    }

    function initSlimscroll() {
        $('.slimscroll').slimscroll({
            height: 'auto',
            position: 'right',
            size: "15px",
            color: '#9ea5ab',
            alwaysVisible: true,
        });
    }

    function initMetisMenu() {
        $("#side-menu").metisMenu();
    }

    /*function initLeftMenuCollapse() {
        // Left menu collapse
        $('.sidemenu-btn').on('click', function (event) {
            event.preventDefault();
            $("body").toggleClass("enlarged");
        });
    }*/

    function initEnlarge() {
        if ($(window).width() < 1551 && $(window).width() > 1199 ) {
            $('body').addClass('enlarged');
			$('.side-menu').addClass('small');
        } else {
            $('body').removeClass('enlarged');
			$('.side-menu').removeClass('small');
        }
    }

    function initActiveMenu() {
        // === following js will activate the menu in left side bar based on url ====
        $("#sidebar-menu a").each(function () {
            if (this.href == window.location.href) {
                $(this).addClass("mm-active");
                $(this).parent().addClass("mm-active"); // add active to li of the current link
                $(this).parent().parent().addClass("mm-show");
                $(this).parent().parent().prev().addClass("mm-active"); // add active class to an anchor
                $(this).parent().parent().parent().addClass("mm-active");
                $(this).parent().parent().parent().parent().addClass("mm-show"); // add active to li of the current link
                $(this).parent().parent().parent().parent().parent().addClass("mm-active");
            }
        });
    }

    function init() {
//        initSlimscrollMenu();
        initSlimscroll();
        initMetisMenu();
        //initLeftMenuCollapse();
        //initEnlarge();
        initActiveMenu();
    }

    init();

})(jQuery);

$(window).on('resize', function(){
	//if ($(window).width() < 1551 && $(window).width() > 1199 ) {
	if ($(window).width() < 1251 && $(window).width() > 1199 ) {
		$('body').addClass('enlarged');
		$('.side-menu').addClass('small');
	} else {
		$('body').removeClass('enlarged');
		$('.side-menu').removeClass('small');
	}
});
$(document).ready(function(){
	//if ($(window).width() < 1551 && $(window).width() > 1199 ) {
	if ($(window).width() < 1251 && $(window).width() > 1199 ) {
		$('body').addClass('enlarged');
		$('.side-menu').addClass('small');
	} else {
		$('body').removeClass('enlarged');
		$('.side-menu').removeClass('small');
	}
	$('[data-toggle="tooltip"]').tooltip();
});

//Custom Tabbing Start
function tabfunction(){
    var cc = $('.nav-link.active').parent('.nav-item').width();
    var ee = $('.nav-link.active').parent('.nav-item').height();
    var dd = $('.nav-link.active').parent('.nav-item').position();
    $('.tab-navigator').css({'width': cc , 'left': dd.left, 'top': dd.top + ee - 2});
    $(document).on("click", ".nav-link", function (){
        var cc = $('.nav-link.active').parent('.nav-item').width();
        var ee = $('.nav-link.active').parent('.nav-item').height();
        var dd = $('.nav-link.active').parent('.nav-item').position();
        $('.tab-navigator').css({'width': cc , 'left': dd.left, 'top': dd.top + ee - 2});
    })
}
$(document).on("load", function(){
    tabfunction();
})
//Custom Tabbing End
$(document).on("click","a[data-toggle=tab]", function(){
    var cc = $(this).attr("href");
    $(cc).addClass("show active").siblings(".tab-pane").removeClass("show active");
});
