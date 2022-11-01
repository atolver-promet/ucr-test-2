var galleria_container = "";
var galleria_viewport = "";
var galleria_thumbnails = "";
var galleria_viewport_container = "";
var changedHeight = 50;

jQuery(document).ready(function() {
  if(Galleria) {
    // Setting up the teaser view galleria and its settings.
    jQuery('.ucr-galleria-teaser').each(function(index, element) {
      galleria_container = jQuery(this).find('.ucr-galleria-thumbnails');
      Galleria.run(galleria_container, {
        autoplay: true,
        maxScaleRatio: 1,
        lightbox: true,
        transition: 'fade',
        height: 0.5625,
        theme: 'azur'
      });
    });

    // Setting up of the full page view galleria and its settings.
    jQuery('.ucr-galleria-full').each(function(index, element) {
      galleria_viewport = jQuery(this).find('.ucr-galleria-viewport');
      galleria_thumbnails = jQuery(this).find('.ucr-galleria-thumbnails');
      Galleria.run(galleria_viewport, {
        maxScaleRatio: 1,
        lightbox: true,
        transition: 'fade',
        height: 0.5625,
        dataSource: galleria_thumbnails,
        keepSource: true,
        thumbnails: false,
        theme: 'azur'
      });

      jQuery(galleria_thumbnails).slick({ // Setup the slick slider for the full view of the Galleria Thumbnails.
        slidesToShow: 9,
        slidesToScroll: 9,
        speed: 300,
        dots: true,
        infinite: false,
        variableWidth: true,
        mobileFirst: true,
        respondTo: 'slider',
        responsive: [
          {
            breakpoint: 1070,
            settings: {
              slidesToShow: 9,
              slidesToScroll: 9
            }
          },
          {
            breakpoint: 1023,
            settings: {
              slidesToShow: 8,
              slidesToScroll: 8
            }
          },
          {
            breakpoint: 894,
            settings: {
              slidesToShow: 7,
              slidesToScroll: 7
            }
          },
          {
            breakpoint: 770,
            settings: {
              slidesToShow: 6,
              slidesToScroll: 6
            }
          },
          {
            breakpoint: 616,
            settings: {
              slidesToShow: 5,
              slidesToScroll: 5
            }
          },
          {
            breakpoint: 547,
            settings: {
              slidesToShow: 4,
              slidesToScroll: 4
            }
          },
          {
            breakpoint: 398,
            settings: {
              slidesToShow: 3,
              slidesToScroll: 3
            }
          },
          {
            breakpoint: 0,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1
            }
          }
        ]
      });
    });
  }
});
