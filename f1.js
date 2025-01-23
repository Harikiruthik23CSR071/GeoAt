/* Quote Loop */
function fade($ele) {
    $ele.fadeIn(1000).delay(3000).fadeOut(1000, function() {
        var $next = $(this).next('.quote');
        fade($next.length > 0 ? $next : $(this).parent().children().first());
    });
}
fade($('.quoteLoop > .quote').first());

/* Navigation */
$(window).scroll(function() {
    if ($(window).scrollTop() > 300) {
        $('.main_nav').addClass('sticky');
    } else {
        $('.main_nav').removeClass('sticky');
    }
});

// Mobile Navigation
$('.mobile-toggle').click(function() {
    $('.main_nav').toggleClass('open-nav');
});

$('.main_nav li a').click(function() {
    $('.main_nav').removeClass('open-nav');
});

/* Smooth Scrolling */
jQuery(document).ready(function($) {
    $('.smoothscroll').on('click', function(e) {
        e.preventDefault();
        var target = this.hash,
            $target = $(target);
        $('html, body').stop().animate({
            scrollTop: $target.offset().top
        }, 800, 'swing');
    });
});
