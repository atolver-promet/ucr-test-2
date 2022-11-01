(function ($, Drupal, drupalSettings) {
    $( document ).ready(function() {
        $(document).foundation(); // Init Foundation

        if ($(".colorbox-youtube").length) {
            $(".colorbox-youtube").each(function( index ) {
                var linkUrl = $(this).attr("href");
                $(this).attr("href",linkUrl.alterVidLinks());
            });
        }
        $( ".colorbox-youtube" ).on( "click", function() {
            var viewPortDim = getViewportSize();
            var viewPortWidth = viewPortDim[0];
            var cboxWidth = 854;
            if (viewPortWidth < 1050) { cboxWidth = viewPortWidth * 0.8; }
            if (viewPortWidth < 767) { cboxWidth = viewPortWidth * 0.9; }
            var cboxHeight = cboxWidth * (9/16);
            console.log('Colorbox width: ' + cboxWidth + ', Colorbox height: ' + cboxHeight);
            if (viewPortWidth >= 767) {
                $(this).colorbox({
                    iframe: true,
                    innerWidth: cboxWidth,
                    innerHeight: cboxHeight
                });
            }
        });

        $(".hero-video-player").each(function(index) {
            $(this).css('opacity', 1);
        });

        $('.mobile-menu ul li a.drop-down-arrow').unbind('click').click(function(e) {
            $(this).toggleClass('active');
            $(this).next('div').slideToggle();
            e.preventDefault();
        });

        $('.mobile-menu ul li span.find-info-link').unbind('click').click(function(e) {
            $(this).toggleClass('active');
            $(this).next('div').slideToggle();
            e.preventDefault();
        });

        var general_menu_block = $('#block-audiencemenuleftside-2').get();
        $('#block-audiencemenuleftside-2').remove();
        $('#main-menu-container').prepend(general_menu_block);

        var search_block = $('#block-ucriversideaudiencesearchblock-4').get();
        $('#block-ucriversideaudiencesearchblock-4').remove();
        $(search_block).insertBefore('#main-menu-container #block-audiencemenuleftside-2');

        var quicklinks_block = $('#block-quicklinks-2').get();
        $('#block-quicklinks-2').remove();
        $(quicklinks_block).insertAfter('#main-menu-container #block-mainnavigation-4');

        $('.mobile-menu-expander').click(function() {
            $(this).toggleClass('active');
            //$('.primary-content-area').toggleClass('overlay');
        });

        $('.block--quicklinks-sidebar-menu h2').unbind('click').click(function() {
            $(this).toggleClass('active');
            $('.block--quicklinks-sidebar-menu .paragraph--type--quicklinks-sidebar').slideToggle();
        });


        $(".audience-container .google-search input").focusout(function() {
            $(".audience-container .google-search input").val("");
        });

        $("footer .footer-google-search .google-search input").focusout(function() {
            $("footer .footer-google-search .google-search input").val("");
        });

        $("#block-ucriversideaudiencesearchblock-4 .google-search #audience-search").focusout(function() {
            $("#block-ucriversideaudiencesearchblock-4 .google-search #audience-search").val("");
        });
    });

    // Drupal Behavior for the Quicklinks Slider
    Drupal.behaviors.pushmenu = {
        attach: function (context, settings) {
            $( document ).ready(function() {
                var timer;

                $('#block-quicklinks').on('click',function() {
                    $('#block-quicklinks h2').addClass('active');
                    clearTimeout(timer);
                    openSubmenu();
                })
                    .on('mouseleave', function() {
                        timer = setTimeout(closeSubmenu, 0);
                    });

                function openSubmenu() {
                    $('#block-quicklinks ul.menu').slideDown();
                }

                function closeSubmenu() {
                    $('#block-quicklinks ul.menu').slideUp();
                    $('#block-quicklinks h2').removeClass('active');
                }

                $('#block-quicklinks-2 h2').unbind('click').click(function() {
                    $(this).toggleClass('active');
                    $('#block-quicklinks-2 ul.menu').slideToggle();
                });
            });
        }
    };

    $('.flexslider').bind('before', function(e, slider) {
        if (!$('body').hasClass("processed-video-script") && $(window).width() > 767) {
            play_slideshow_video();
        }
    });

    if ($(window).width() < 767) {
        $(".field-name-field-mp4-video").remove();
    }

    // Fix colorbox
    if ($(window).width() > 1024) {
        var width = $(window).width() * 0.7;
        var height = width * 0.75;
    }

    var play_slideshow_video = function() {
        if ($(window).width() < 767) {
            return;
        }

        // @TODO add some caching / memoization to this function to make it a bit smarter.
        $("#hero-video").closest(".video-wrapper").removeClass("video-loaded");

        $(".video-slideshow .video-wrapper").each(function () {
            var $this = $(this);

            var mp4 = $this.data('mp4');
            var webm = $this.data('webm');

            var video = $.parseHTML("<video height='auto' id='hero-video' muted='muted' loop='loop' autoplay='autoplay' ></video>");
            if (mp4 !== undefined) {
                $(video).append("<source src='" + mp4 + "' type='video/mp4' />");
                $("body").addClass("processed-video-script");
            }

            if (webm !== undefined) {
                $(video).append("<source src='" + webm + "' type='video/webm' />");
                $("body").addClass("processed-video-script");
            }

            $(video).bind('', function(e) {
                if (mp4 !== undefined || webm !== undefined) {
                    $this.addClass("video-loaded");
                }
            });
            $this.append(video);
            $("body").addClass("processed-video");
        });
    };

    $(window).on("load", function() {
        if (!$('body').hasClass("processed-video") && $(window).width() > 767) {
            play_slideshow_video();
        }
    });

    $(".flex-hero-slideshow-direction-nav, .flex-hero-slideshow-control-nav, .flex-direction-nav, .flex-control-nav  ").click(play_slideshow_video);

    if (navigator.userAgent.indexOf("Safari") != -1 && navigator.userAgent.indexOf('Chrome') == -1) {
        document.body.classList.add("safari-no-video");
    }

    // Function to Get Viewport Size
    function getViewportSize() {
        var viewPortWidth;
        var viewPortHeight;
        // the more standards compliant browsers (mozilla/netscape/opera/IE7) use window.innerWidth and window.innerHeight
        if (typeof window.innerWidth != 'undefined') {
            viewPortWidth = window.innerWidth,
                viewPortHeight = window.innerHeight
        }
        // IE6 in standards compliant mode (i.e. with a valid doctype as the first line in the document)
        else if (typeof document.documentElement != 'undefined'
            && typeof document.documentElement.clientWidth !=
            'undefined' && document.documentElement.clientWidth != 0) {
            viewPortWidth = document.documentElement.clientWidth,
                viewPortHeight = document.documentElement.clientHeight
        }
        // older versions of IE
        else {
            viewPortWidth = document.getElementsByTagName('body')[0].clientWidth,
                viewPortHeight = document.getElementsByTagName('body')[0].clientHeight
        }
        return [viewPortWidth, viewPortHeight];
    }

    // Alter YouTube/Vimeo links with colorbox-load CSS class
    if(!String.alterVidLinks) {
        String.prototype.alterVidLinks = function() {
            var urlPattern = /www.youtube\.com\/watch\?v=([a-zA-Z0-9_\-]+)/gim;
            var urlPattern2 = /youtu\.be\/([a-zA-Z0-9_\-]+)/gim;
            var urlPattern3 = /\/\/vimeo\.com\/([a-zA-Z0-9_\-]+)/gim;
            return this
                .replace(urlPattern, 'www.youtube.com/embed/$1?autoplay=1&rel=0&modestbranding=0')
                .replace(urlPattern2, 'www.youtube.com/embed/$1?autoplay=1&rel=0&modestbranding=0')
                .replace(urlPattern3, 'player.vimeo.com/video/$1?iframe=true&autoplay=1');
        };
    }

    setTimeout(function(){
        $('.main-nav-bar #main-menu-container').addClass('show');
        $('#block-ucriversideaudiencesearchblock-4').addClass('show');
    }, 2000);

    // Height Matching Initialization
    Drupal.behaviors.match_heights = {
        attach: function (context, settings) {
            if ($('.slick--view--articles--block-1').length && !$('.person-grid-view').hasClass('match-heights')) {
                $('.slick--view--articles--block-1 .article_content').matchHeight({
                    byRow: false
                });
                $('.slick--view--articles--block-1').addClass('match-heights');
            }
        }
    };

    // Mega Menu Initialization
    Drupal.behaviors.megamenu = {
        attach: function (context, settings) {
            $('#block-ucr-umbrella-main-menu .accessible-megamenu-top-nav-item a.is-active').parent('li').addClass('active');

            function calcOffsets() {
                var window_width = $( window ).innerWidth();
                $('div.accessible-megamenu-panel').each(function(){
                    $(this).width(window_width - 17);
                    var offset = $(this).parent().offset();
                    $(this).css({'left': (offset.left * -1) + 'px'});
                });
            }

            if (!$('#block-ucr-umbrella-main-menu').hasClass('megamenu-init')) {
                $('#block-ucr-umbrella-main-menu').children('div').remove();
                $('#block-ucr-umbrella-main-menu').accessibleMegaMenu().addClass('megamenu megamenu-init');
                $('#block-ucr-umbrella-main-menu a.accessible-megamenu-panel').removeClass('accessible-megamenu-panel');

                $('.accessible-megamenu .accessible-megamenu-panel .accessible-megamenu-panel-group ul.accessible-megamenu-panel-group > li > a + div').parent().addClass('megamenu-dropdown');

                $('.accessible-megamenu .accessible-megamenu-panel .accessible-megamenu-panel-group ul.accessible-megamenu-panel-group > li.megamenu-dropdown > a').click(function(e){
                    e.preventDefault();
                    $(this).toggleClass('active');
                    $(this).next('div').slideToggle(250);
                });

                $('#block-ucr-umbrella-main-menu').addClass('megamenu-init-show');

                setTimeout(calcOffsets, 1000);

                $(window).resize(function(){
                    calcOffsets();
                });
            }
        }
    };

    // Add Responsive Tables to the Site.
    $('table').each(function() {
        if($(this).hasClass('non-responsive') === false) {
            $(this).wrap('<div class="table-scroll"></div>');
        }
    });

})(jQuery, Drupal, drupalSettings);

(function () {
    var sz = document.createElement('script');
    sz.type = 'text/javascript';
    sz.async = true;
    sz.src = '//siteimproveanalytics.com/js/siteanalyze_8343.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(sz, s);
})();