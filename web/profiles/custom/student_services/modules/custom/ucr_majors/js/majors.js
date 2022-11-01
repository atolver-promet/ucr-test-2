(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.ucrMajors = {
        attach: function(context, settings) {
            $('button#getMajor', context).once().each(function() {
                $(this).click(function() {
                    var goto_page = $('select#majorList option:selected').val();
                    if(goto_page !== "") {
                        window.location = goto_page;
                    }
                });
            });
        }
    };
})(jQuery, Drupal, drupalSettings);