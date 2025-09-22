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

    function initSidebarMenu() {
        var $menu = $('#side-menu');
        // First, hide ALL submenus and reset all active states
        $menu.find('ul.nav-second-level').hide().removeClass('mm-show');
        $menu.find('li').removeClass('mm-active');
        $menu.find('a').removeClass('mm-active');

        // Then, show only submenus that contain active items (based on server-side classes)
        $menu.find('a.active').each(function () {
            var $activeLink = $(this);
            var $parentLi = $activeLink.parent('li');

            // Mark the active link and its parent li as active
            $activeLink.addClass('mm-active');
            $parentLi.addClass('mm-active');

            // If this active link is inside a submenu, show that submenu and all parent submenus
            var $parentSubmenu = $activeLink.closest('ul.nav-second-level');
            while ($parentSubmenu.length) {
                $parentSubmenu.show().addClass('mm-show');
                $parentSubmenu.parent('li').addClass('mm-active');
                $parentSubmenu.prev('a').addClass('mm-active');

                // Check if this submenu is nested inside another submenu
                $parentSubmenu = $parentSubmenu.parent('li').closest('ul.nav-second-level');
            }
        });

        // Handle click events for menu items with submenus
        $menu.find('li > a').off('click.sidebar').on('click.sidebar', function (e) {
            var $this = $(this);
            var $submenu = $this.next('ul.nav-second-level');
            var $parentLi = $this.parent('li');

            if ($submenu.length) {
                e.preventDefault();

                if ($submenu.is(':visible') && $submenu.hasClass('mm-show')) {
                    // Close the submenu and all its children
                    $submenu.find('ul.nav-second-level').slideUp(200, function () {
                        $(this).removeClass('mm-show');
                    });
                    $submenu.slideUp(200, function () {
                        $submenu.removeClass('mm-show');
                    });
                    $parentLi.removeClass('mm-active');
                    $this.removeClass('mm-active');

                    // Remove active state from child items
                    $submenu.find('li').removeClass('mm-active');
                    $submenu.find('a').removeClass('mm-active');
                } else {
                    // Close other submenus at the same level (but not parent or child submenus)
                    var $parentSubmenuContainer = $this.closest('ul.nav-second-level');
                    var $sameLevel;

                    if ($parentSubmenuContainer.length) {
                        // This is a nested submenu, only close siblings at the same level
                        $sameLevel = $parentSubmenuContainer.children('li').children('ul.nav-second-level');
                    } else {
                        // This is a top-level menu, close other top-level submenus
                        $sameLevel = $menu.children('li').children('ul.nav-second-level');
                    }

                    $sameLevel.not($submenu).each(function () {
                        var $otherSubmenu = $(this);
                        // Don't close submenus that contain active items
                        if (!$otherSubmenu.find('a.active').length) {
                            $otherSubmenu.find('ul.nav-second-level').slideUp(200, function () {
                                $(this).removeClass('mm-show');
                            });
                            $otherSubmenu.slideUp(200, function () {
                                $otherSubmenu.removeClass('mm-show');
                            });
                            $otherSubmenu.parent('li').removeClass('mm-active');
                            $otherSubmenu.prev('a').removeClass('mm-active');

                            // Remove active state from child items
                            $otherSubmenu.find('li').removeClass('mm-active');
                            $otherSubmenu.find('a').removeClass('mm-active');
                        }
                    });

                    // Open this submenu
                    $submenu.slideDown(200, function () {
                        $submenu.addClass('mm-show');
                    });
                    $parentLi.addClass('mm-active');
                    $this.addClass('mm-active');
                }
            }
        });
    }

    function initActiveMenu() {
        // Ensure active states are properly maintained
        $("#sidebar-menu a.active").each(function () {
            var $this = $(this);
            $this.addClass('mm-active');
            $this.parent('li').addClass('mm-active');

            // If inside a submenu, ensure all parent submenus are visible
            var $parentSubmenu = $this.closest('ul.nav-second-level');
            while ($parentSubmenu.length) {
                $parentSubmenu.addClass('mm-show').show();
                $parentSubmenu.parent('li').addClass('mm-active');
                $parentSubmenu.prev('a').addClass('mm-active');

                // Move up to the next parent submenu
                $parentSubmenu = $parentSubmenu.parent('li').closest('ul.nav-second-level');
            }
        });
    }

    function initEnlarge() {
        if ($(window).width() < 1251 && $(window).width() > 1199) {
            $('body').addClass('enlarged');
            $('.side-menu').addClass('small');
        } else {
            $('body').removeClass('enlarged');
            $('.side-menu').removeClass('small');
        }
    }

    function init() {
        initSlimscroll();
        initActiveMenu();
        initSidebarMenu();
        initEnlarge();
    }

    init();

})(jQuery);

$(window).on('resize', function () {
    if ($(window).width() < 1251 && $(window).width() > 1199) {
        $('body').addClass('enlarged');
        $('.side-menu').addClass('small');
    } else {
        $('body').removeClass('enlarged');
        $('.side-menu').removeClass('small');
    }
});

$(document).ready(function () {
    if ($(window).width() < 1251 && $(window).width() > 1199) {
        $('body').addClass('enlarged');
        $('.side-menu').addClass('small');
    } else {
        $('body').removeClass('enlarged');
        $('.side-menu').removeClass('small');
    }

    $('[data-toggle="tooltip"]').tooltip();

    // Additional initialization after DOM is ready
    setTimeout(function () {
        // Ensure only active submenus are visible after page load
        $('#sidebar-menu ul.nav-second-level').each(function () {
            var $submenu = $(this);
            if (!$submenu.find('a.active').length && !$submenu.hasClass('mm-show')) {
                $submenu.hide();
            }
        });
    }, 100);
});

// Custom Tabbing Start
function tabfunction() {
    if ($('.nav-link.active').length && $('.nav-link.active').parent('.nav-item').length) {
        var cc = $('.nav-link.active').parent('.nav-item').width();
        var ee = $('.nav-link.active').parent('.nav-item').height();
        var dd = $('.nav-link.active').parent('.nav-item').position();
        $('.tab-navigator').css({ 'width': cc, 'left': dd.left, 'top': dd.top + ee - 2 });
    }

    $(document).on("click", ".nav-link", function () {
        setTimeout(function () {
            if ($('.nav-link.active').length && $('.nav-link.active').parent('.nav-item').length) {
                var cc = $('.nav-link.active').parent('.nav-item').width();
                var ee = $('.nav-link.active').parent('.nav-item').height();
                var dd = $('.nav-link.active').parent('.nav-item').position();
                $('.tab-navigator').css({ 'width': cc, 'left': dd.left, 'top': dd.top + ee - 2 });
            }
        }, 50);
    });
}

$(document).on("load", function () {
    tabfunction();
});
// Custom Tabbing End

$(document).on("click", "a[data-toggle=tab]", function () {
    var cc = $(this).attr("href");
    $(cc).addClass("show active").siblings(".tab-pane").removeClass("show active");
});
