(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.ucrArticlesInitSlickSlider = {
        attach: function (context, settings) {
            $('.ucr-articles--block--slideshow', context).once().slick();
        }
    };
})(jQuery, Drupal, drupalSettings);
