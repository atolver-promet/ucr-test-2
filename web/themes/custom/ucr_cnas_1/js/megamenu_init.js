(function ($, Drupal) {
	Drupal.behaviors.megamenu = {
	  attach: function (context, settings) {
  	  $('#block-ucr-default-meganav-main .accessible-megamenu-top-nav-item a.is-active').parent('li').addClass('active');
  	  
  	  function calcOffsets() {
	  	  var window_width = $( window ).innerWidth();
	  	  $('div.accessible-megamenu-panel').each(function(){
				  $(this).width(window_width - 18);
				  var offset = $(this).parent().offset();
			  	$(this).css({'left': (offset.left * - 1)  + 'px'});
			  	$(this).css({'right': '0'});
			  });
  	  }

		  if(!$('#block-ucr-default-meganav-main').hasClass('megamenu-init')) {
			  $('#block-ucr-default-meganav-main').children('div').remove();
			  $('#block-ucr-default-meganav-main').accessibleMegaMenu().addClass('megamenu megamenu-init');
			  $('#block-ucr-default-meganav-main a.accessible-megamenu-panel').removeClass('accessible-megamenu-panel');

			  $('.accessible-megamenu .accessible-megamenu-panel .accessible-megamenu-panel-group ul.accessible-megamenu-panel-group > li > a + div').parent().addClass('megamenu-dropdown');

			  $('.accessible-megamenu .accessible-megamenu-panel .accessible-megamenu-panel-group ul.accessible-megamenu-panel-group > li.megamenu-dropdown > a').click(function(e){
				  e.preventDefault();
				  $(this).toggleClass('active');
				  $(this).next('div').slideToggle(250);
			  });

			  $('#block-ucr-default-meganav-main').addClass('megamenu-init-show');
			  
			  setTimeout(calcOffsets, 1000);
			  			  
			  $(window).resize(function() {
				  calcOffsets();
			  });
      }
	  }
	};
})(jQuery, Drupal);