(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.ucrCounselorSearchFix = {
        attach: function(context, settings) {
            $('input#edit-submit-transfer-college-students', context).once().each(function() {
                $(this).remove();
            });

            $('form#views-exposed-form-transfer-college-students-transfer-counselor-block').once().each(function() {
                $(this).bind('submit', function(e) {
                    e.preventDefault();
                });
            });

            $("input[name='name']").once().each(function() {
                $(this).keyup(function() {
                    var filter = $(this).val().trim();

                    $('#counselorInfo li').each(function() {
                        if(filter !== "") {
                            if($(this).find('a.accordion-title').text().search(new RegExp(filter, 'i')) >= 0) {
                                $(this).fadeIn();
                            }
                            else {
                                $(this).fadeOut();
                            }
                        } else {
                            $(this).fadeOut();
                        }
                    });
                });
            });
        }
    };
})(jQuery, Drupal, drupalSettings);