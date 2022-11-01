(function ($, Drupal, drupalSettings) {

/* removed to use the ITS provided sticky nav system
    var $headerSection = $('#header-section');

    var toggleHeaderFloating = function () {
        // Floating Header
        if ($(window).scrollTop() > 150) {
            $headerSection.addClass('floating');
        } else {
            $headerSection.removeClass('floating');
        };
    };

    var rafTimer;
    $(window).on('scroll', function () {
        cancelAnimationFrame(rafTimer);
        rafTimer = requestAnimationFrame(toggleHeaderFloating);
    });
*/

})(jQuery, Drupal, drupalSettings);