(function ($, Drupal, drupalSettings) {
    Drupal.behaviors.foundationInit = {
        attach: function (context, settings) {
            $(document, context).once('foundationInit').each(function () {
                $(document).foundation();
                setTimeout(function () {
                    Foundation.reInit($('[data-equalizer]'));
                }, 5);
            });
        }
    };

    Drupal.behaviors.ucrMegaNav = {
        attach: function (context, settings) {
            $(document, context).once('ucrMegaNav').each(function () {
                function calcMegaNavPaneDimensions(pane) {
                    if (typeof pane === "undefined") {
                        console.log("Variable 'pane' is not defined / null.");
                        return;
                    }

                    var window_width = $(window).innerWidth();
                    var offset = pane.parent().offset();

                    if (window_width > 769) {
                        pane.width(window_width);
                        pane.css({'left': (offset.left * -1) + 'px'});
                        pane.css({'right': '0'});
                    } else {
                        pane.width('');
                        pane.css({'left': ''});
                        pane.css({'right': ''});
                    }
                }

                $('nav#primary-site-menu', context).once().each(function () {
                    $('ul.meganav-menu', $(this)).once().each(function () {
                        $('ul.meganav-submenu').once().each(function () {
                            calcMegaNavPaneDimensions($(this));
                            $(this).parent().on('mouseenter', {aMenu: $(this)}, function (event) {
                                calcMegaNavPaneDimensions(event.data.aMenu);
                                event.stopPropagation();
                            });
                        });
                    });
                });
            });
        }
    };

    Drupal.behaviors.ucrThemeInitalize = {
        attach: function (context, settings) {
            // Add the functionality to align all of the Desktop Audience Links to the right by opening to the left.
            function updateAudienceDesktopSubOpens() {
                $('div#audience-links-desktop-view nav.audience-links li').each(function() {
                    if($(this).hasClass('is-dropdown-submenu-parent') || $(this).hasClass('is-accordion-submenu-parent')) {
                        $(this).removeClass('opens-right').addClass('opens-left');
                    }
                });
            }

            // Append changes when the content loading is the window itself.
            $(window, context).once('ucrThemeInitalize').each(function () {
                if ($(this).width() < 769) {
                    $(".block--quicklinks-sidebar-menu h2").parent().find('ul.vertical.menu').css("display", "none");
                } else {
                    updateAudienceDesktopSubOpens();
                }

                $(this).resize(function () {
                    if ($(window).width() > 769) {
                        $(".block--quicklinks-sidebar-menu h2").parent().find('ul.vertical.menu').css("display", "block");
                        updateAudienceDesktopSubOpens();
                    } else {
                        $(".block--quicklinks-sidebar-menu h2").parent().find('ul.vertical.menu').css("display", "none");
                    }
                });
            });

            // Main Menu & Mobile Menu updates.
            $('#mobile-menu-expander', context).once().click(function () {
                $(this).toggleClass('mobile-menu-expander', 'addOrRemove');
                $(this).toggleClass('mobile-menu-expander-clicked');
            });

            // Add the Slide Toggle for the verticle menu.
            $('.block--quicklinks-sidebar-menu h2').once().click(function () {
                if ($(window).width() < 769) {
                    $(this).parent().find('ul.vertical.menu').slideToggle();
                }
            });

            // Find all table tags and do the formatting as needed.
            $('table', 'main').once().each(function () {
                if ($(this).hasClass('non-responsive') === false) {
                    $(this).wrap('<div class="table-scroll"></div>');
                }

                if ($(this).hasClass('datatables') === true) {
                    $(this).DataTable();
                }
            });

            $('div.dataTables_wrapper', context).once().each(function () {
                $(this).find('div.grid-x').css('clear', 'both');
            });

            // Add the functionality to do the new Google Search
            $("form[name='gsc-search-form']", context).once().find(':button').each(function () {
                if ($(this).attr('name') === "search-by") {
                    $(this).click(function () {
                        var form = $("form[name='gsc-search-form']");
                        var send_to = form.attr('action');
                        var by_val = $(this).val();
                        var q_val = form.find(":input[name='q']").val();

                        if (by_val !== 'all') {
                            q_val = 'site:' + by_val + ' ' + q_val;
                        }

                        window.location = send_to + '?q=' + encodeURIComponent(q_val) + '&search-by=' + encodeURIComponent(by_val);
                        return false;
                    });
                }
            });

            // Set the focus on the input of the modal when it opens.
            $("#gscSearchFormModal").once().each(function() {
                $(this).on('open.zf.reveal', function() {
                    setTimeout(function() { $("#gscSearchFormModal").find("#audience-search").first().focus(); }, 600);
                });
            });

            // Make the Hero Video Player show up.
            $(".hero-video-player", context).once().each(function () {
                $(this).css('opacity', 1);
            });

        }
    };

    Drupal.behaviors.ucrThemeSticky = {
        attach: function (context, settings) {
            $('[data-ucr-sticky]', context).once('ucrThemeSticky').each(function () {
                var stickyOptions = {
                    marginTop: 0,
                    stickyOn: 'small',
                    topAnchor: 1,
                    btmAnchor: 'content-container:bottom'
                };
                var stickyHeader = new Foundation.Sticky($(this), stickyOptions);

                $(this).on('sticky.zf.stuckto:top', function () {
                    var desktopOption = $(this).data('ucr-sticky');
                    var mobileOption = $(this).data('ucr-mobile-sticky');
                    if(Foundation.MediaQuery.atLeast('medium')) {
                        if(desktopOption !== 1) {
                            $(this).removeClass('sticky').attr('style', '');
                        } else {
                            $(this).addClass('sticky-shrink');
                        }
                    } else {
                        if(mobileOption !== 1) {
                            $(this).addClass('sticky');
                        } else {
                            $(this).removeClass('sticky').attr('style', '');
                        }
                    }
                }).on('sticky.zf.unstuckfrom:top', function () {
                    var desktopOption = $(this).data('ucr-sticky');
                    var mobileOption = $(this).data('ucr-mobile-sticky');
                    if(Foundation.MediaQuery.atLeast('medium')) {
                        if (desktopOption === 1) {
                            $(this).removeClass('sticky-shrink');
                        }
                    } else {
                        if(mobileOption === 1) {
                            $(this).removeClass('sticky').attr('style', '');
                        }
                    }
                });
            });
        }
    };

    //EqualHeights
  function equalHeightRows() {
    //Set matching height of panelizer columns
    $('.ucr-custom-block').each( function() {
      if ( $('.slick', this).length ) { //first check if there is a carousel slider in the custom block
        if ( $(this).parents('[class*="medium-"]').length ) {
          customBlockHeight = $(this).height();
          containerDivHeight = $(this).closest('.cell').height();

          if ( ($(this).parents('[class*="column-"]').length) ) {
            $(this).height(containerDivHeight);
            $('.grid-x', this).height(containerDivHeight);
          }

        }
      }
    });

    //Layout builder equal heights
    setTimeout( function() {
        $('.layout.grid-container').each( function() {
          if ( $(this).hasClass('.layout-one-col') ) {
            //do nothing
          } else {
            var thisHeight = 0;
            $('.equalheights .layout__region', this).each( function() {
              thisRegionHeight = $(this).height();
              if (thisRegionHeight > thisHeight) {
                thisHeight = thisRegionHeight;
              }
            });
            $('.equalheights .layout__region', this).css('height', thisHeight);
            $('.equalheights .layout__region .ucr-custom-block', this).css('height', '100%');
          }
        });
      },
      500);
  }


})(jQuery, Drupal, drupalSettings);

(function () {
    var sz = document.createElement('script');
    sz.type = 'text/javascript';
    sz.async = true;
    sz.src = '//siteimproveanalytics.com/js/siteanalyze_8343.js';
    var s = document.getElementsByTagName('script')[0];
    s.parentNode.insertBefore(sz, s);
})();
