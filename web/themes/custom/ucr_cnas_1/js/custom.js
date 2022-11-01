(function ($, Drupal, drupalSettings) {
	    
    //Check if an object is in view
	$.fn.isInViewport = function() {
	  var elementTop = $(this).offset().top;
	  var elementBottom = elementTop + $(this).outerHeight();
	
	  var viewportTop = $(window).scrollTop();
	  var viewportBottom = viewportTop + $(window).height();
	
	  return elementBottom > viewportTop && elementTop < viewportBottom;
	};
		
    $(document).ready(function() {
	    
	    //Check if page is a 403 or 404
	    if ( $('article').length ) {
		    var articleAbout = $('article').attr('about');
		    
		    if (articleAbout=='/403') {
			    $('body').addClass('page-403');
		    }
		    
		    if (articleAbout=='/404') {
			    $('body').addClass('page-404');
		    }
	    }
	    
	    //Initialize CounterUp plugin
		    //Animate Statistics numbers to countup. Need to apply the class 'animate' through the section config layout builder
			$('.animate .stat-number').counterUp();
	    
	    //Temporary add slide animation to homepage columns
	    setTimeout( function() {
		    $('.column-1').each( function() {
			   $(this).addClass('slide-right');
		    });
		    $('.column-2').each( function() {
			   $(this).addClass('slide-left'); 
		    });
		    
		}, 500);
	
		//Responsive ImageMap Fix by RWD Image Maps
		$('img[usemap]').rwdImageMaps();
	    
	    //Get window width
		var windowWidth = $(window).width();
		
		//Close sticky button on click
		if ( $('.stickybtn').length ) {
			$('#stickybtn-close').on('click', function() {
				$('.stickybtn').addClass('hidden');
			});
		}
	    
	    //Resize hero panel area if there is a video
	    function resizeHeroVideo() {
		    setTimeout( function() {
		    if ( $('.cnas-video').length && $('body.path-frontpage').length ) {
			    vHeight = $('.cnas-video .hero-video video').height();
			    vHeight = Math.floor(vHeight);
			    $('.cnas-video .hia-area').height(vHeight-3);
		    }}, 2000);
    	}
	    
	    		
		
		// Hide Slick Dots if there is only one slide
		if ( $('[id*="slick-block-content"]').length ) {
			$('[id*="slick-block-content"]').each( function() {
				slick_count = 0;
				$('.slick__slide', this).each( function() {
					slick_count++;
				});
			
				//console.log(slick_count);
				if ( slick_count<=1 ) { //only one slide
					$('ul.slick-dots', this).css('display','none');
				}
				/*
				if ( slick_count>=2 && $('.slick-dots', this).length ) { //more than one slide
					$('nav.slick__arrow', this).css('display','none');
				}
				*/
				slick_count = 0;
			});
		}
		
		//Add a class to ucr-custom-block for bubble grids
		$('.bubble-grid').each( function() {
			$(this).parents('.ucr-custom-block').addClass('ucr-bubble-grid--block'); 
		});
		$('.bubble-text-above').each( function() {
			$(this).parents('.bubble-item').addClass('text-above'); 
		});
		$('.bubble-text-below').each( function() {
			$(this).parents('.bubble-item').addClass('text-below'); 
		});
		
		//Add a class to ucr-custom-block for news blocks - Panelizer
	    $('.articles-container').each( function() {
		   $(this).parents('.ucr-custom-block').addClass('ucr-articles--block'); 
	    });
	    
	    //Add a class to ucr-custom-block for article preview blocks - Layout Builder
	    $('.ucr-articles--block--container').each( function() {
		   $(this).parents('.ucr-custom-block').addClass('ucr-articles--block'); 
	    });
	    
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
	    
	    
		//Carousel Slider Image - set class to adjust image size to fill container
		function fillCarouselSlider() {
			setTimeout( function() {
				$('.carousel-slider').each( function() {
	//			$('div:not(.layout-one-col) .carousel-slider').each( function() {
					if ( $(this).parents('.layout-one-col').length ) {
							//do nothing
					} else {
						if ( $('img', this).length ) {
							//Get width of image
							boxW = $(this).width();
							boxH = $(this).height();
							boxRatio = (boxW / boxH);
							
							imgW = $('img', this).width();
							imgH = $('img', this).height();
							imgRatio = (imgW / imgH);
							
							console.log('imgRatio='+imgRatio+', boxRatio='+boxRatio);
							
							if (boxRatio > imgRatio) {
								$('img', this).addClass('fill_width');
								$('img', this).removeClass('fill_height');
							} else {
								$('img', this).addClass('fill_height');
								$('img', this).removeClass('fill_width');
							}
						}
					}
				});
			},
			1000);
		}
		
	    
	    //Add a link to the footer logo
	    if ( $('.footer-logo').length ) {
		    footerLogo = $('.ucr-footer-info', this).html();
		    footerLogoNew = '<a href="https://www.ucr.edu/">' + footerLogo + '</a>';
		    footerLogo = $('.ucr-footer-info', this).html(footerLogoNew);
	    }
	    		
		//Localist Text - Template = Modern
		if ( $('.cnas-localist-text').length ) {
			$('.cnas-localist-text .lw_event_item').each(function() {
				date = $('.lw_event_item_date', this).html();
				time = $('.lw_event_item_time', this).html();
				dateTime = date + ', ' + time;
				dateTime = dateTime.replace(' ,', ',');
				$('.lw_event_item_date', this).html(dateTime);
			});
		}
		
		//Localist Image - Template = horizontal-list
		if ( $('.cnas-localist-image').length ) {
			$('.cnas-localist-image .lw_event_item').each(function() {
				
				eventTitle = $('.lw_event_item_title', this).html();
				eventTitleDiv = '<div class="lw_event_title">'+eventTitle+'</div>';
				$(eventTitleDiv).prependTo(this);
				
				eventDate = $('.lw_event_item_date', this).html();
				eventTime = $('.lw_event_item_time', this).html();
				eventDateDiv = '<div class="lw_event_date">' + eventDate + ', ' + eventTime + '</div>';
				eventDateDiv = eventDateDiv.replace(' ,', ',');
				$(eventDateDiv).prependTo(this);
			});
		}
		
		//Page Title Block needs a class to make sure it has the right styles applied
		if ( $('.ucr-articles--page--title').length ) {
			$('.ucr-articles--page--title').each( function() {
				$(this).parent('.cell').addClass('article-title-block');
			});
		}
		if ( $('h1.page-title').length ) {
			$('h1.page-title').each( function() {
				$(this).parent('.cell').addClass('article-title-block');
			});
		}
        
 		if (windowWidth>=895) { //895 pixels is when UCR Default hides image and shows video
		    $(document).ready(resizeHeroVideo);
		    $(document).ready(equalHeightRows);
		    $(document).ready(fillCarouselSlider);
		    
		    $(window).on('resize', resizeHeroVideo);
		    $(window).on('resize', equalHeightRows);
		    $(window).on('resize', fillCarouselSlider);
		}

    });
    
    
    
})(jQuery, Drupal, drupalSettings);