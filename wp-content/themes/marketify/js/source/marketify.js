var Marketify = {};


Marketify.App = ( function($) {
	function menuSearch() {
		$( '.header-search-icon, .header-search-toggle' ).click(function(e) {
			e.preventDefault();

			$( '.search-form-overlay' ).toggleClass( 'active' );
		});
	}

	function menuMobile() {
		var container, button, menu;

		container = $( '#site-navigation' );

		if ( ! container )
			return;

		button = container.find( $( 'h1' ) );

		if ( 'undefined' === typeof button )
			return;

		menu = container.find( $( 'ul:first-of-type' ) );

		// Hide menu toggle button if menu is empty and return early.
		if ( 'undefined' === typeof menu ) {
			button.css( 'display', 'none' );

			return;
		}

		if ( ! menu.hasClass( 'nav-menu' ) )
			menu.addClass( 'nav-menu' );

		button.click(function() {
			container.toggleClass( 'toggled' );
			$( '.site-header' ).toggleClass( 'toggled' );
		});
	}

	function footerHeight() {
		var checks = $( '.site-info, .site-footer .row' );

		checks.each(function() {
			var min      = 0;
			var children = $(this).children();

			children.each(function() {
				if ( $(this).outerHeight() > min )
					min = $(this).outerHeight();
			});

			if ( $(window).width() < 978 )
				children.css( 'height', 'auto' );
			else
				children.css( 'height', min );
		});
	}

	function soliloquySliders() {
		if ( $(window).width() < 500 ) {
			var sliders = $( '.soliloquy' );

			$.each(sliders, function() {
				var image = $(this).find( 'img' ),
				    src   = image.prop( 'src' );

				console.log( src );

				$(this)
					.find( 'li' )
					.css({
						'height'           : $(window).outerHeight(),
						'background-image' : 'url(' + src + ')',
						'background-size'  : 'cover'
					});

				image.hide();
			});
		}
	}

	return {
		init : function() {
			menuSearch();
			menuMobile();
			footerHeight();
			soliloquySliders();

			$(window).resize(function() {
				footerHeight();
				soliloquySliders();
			});

			$(document).on( 'click', '.popup-trigger', function(e) {
				e.preventDefault();

				Marketify.App.popup({
					items : {
						src : $(this).attr( 'href' ),
						fixedContentPos: false,
						fixedBgPos: false,
						overflowY: 'scroll'
					}
				});
			});

			$( '.edd_download.content-grid-download' ).attr( 'style', '' );

			$( '.edd-slg-login-wrapper' ).each(function() {
				var link  = $(this).find( 'a' );
				var title = link.attr( 'title' );

				link.html(title).prepend( '<span></span' );
			});

			$( '.comment_form_rating .edd_reviews_rating_box' ).find('a').on('click', function (e) {
				e.preventDefault();

				$( '.comment_form_rating .edd_reviews_rating_box' ).find('a').removeClass( 'active' );

				$( this ).addClass( 'active' );
			});

			$( '#bbpress-forums #bbp-user-wrapper h2.entry-title, #bbpress-forums fieldset.bbp-form legend, .fes-form h1, .fes-headers' ).wrapInner( '<span></span>' );

			$( '#edd_simple_shipping_fields legend, .edd_form *:not(span) > legend' ).wrap( '<span></span>' );

			$('body').on('click.eddwlOpenModal', '.edd-add-to-wish-list', function (e) {
				$( '#edd-wl-modal-label' ).wrapInner( '<span></span>' );
			});

			$( '.download-sorting input, .download-sorting select' ).change(function(){
				$(this).closest( 'form' ).submit();
			});

			$( '.entry-image' ).bind( 'touchstart', function(e) {
				$(this).toggleClass( 'hover' );
			});

			$( '.individual-testimonial .avatar' ).wrap( '<div class="avatar-wrap"></div>' );
		},

		popup : function( args ) {
			return $.magnificPopup.open( $.extend( args, {
				type         : 'inline',
				overflowY    : 'hidden',
				removalDelay : 250
			} ) );
		},

		downloadStandard : function () {
			$( '.download-image.flexslider' ).flexslider({
				slideshow     : false,
				animation     : 'fade',
				animationLoop : false,
				itemWidth     : 360,
				itemMargin    : 0,
				minItems      : 1,
				maxItems      : 1,
				controlNav    : false,
				smoothHeight  : true,
				prevText      : '<i class="icon-arrow-left5"></i>',
				nextText      : '<i class="icon-arrow-right5"></i>'
			});
		},

		featuredPopular : function() {
			$( '.marketify_widget_featured_popular.popular .flexslider' ).flexslider({
				animation      : "slide",
				slideshow      : false,
				animationLoop  : false,
				itemWidth      : 360,
				itemMargin     : 30,
				minItems       : 1,
				maxItems       : 3,
				directionNav   : false
			});
		},

		downloadGridViewer : function() {
			$( '.download-image-grid-preview .slides li:first-child' ).addClass( 'active' );

			$( '.download-image-grid-preview .slides li a' ).click(function(e) {
				e.preventDefault();

				var landing = $( '.image-preview' );

				$( '.download-image-grid-preview .slides li' ).removeClass( 'active' );
				$(this).parent().addClass( 'active' );

				landing.hide().html( $(this).clone() ).fadeIn();
			});

			var items = [];
			$( '.download-image-grid-preview .slides li' ).each(function() {
				items.push( { src: $( this ).find( 'img' ).attr( 'src' ) } );
			});

			$( '.download-image-grid-preview' ).on( 'click', '.image-preview a', function(e) {
				e.preventDefault();

				$.magnificPopup.open({
					type: 'image',
					items : items,
					gallery:{
						enabled:true
					}
				});
			});
		},

		equalHeights : function( elements ) {
			var tallest = 0;

			$.each( elements, function(key, elements) {
				$.each( elements, function() {
					if ( $(this).outerHeight() > tallest ) {
						tallest = $(this).outerHeight();
					}
				});

				$(elements).css( 'height', tallest );

				if ( $(window).width() < 768 ) {
					$(elements).css( 'height', 'auto' );
				}

				tallest = 0;
			});
		}
	}
} )(jQuery);

Marketify.Widgets = ( function($) {
	var widgetSettings = {};

	return {
		init : function() {
			$.each( marketifySettings.widgets, function(m, value) {
				var cb       = value.cb;
				var settings = value.settings;
				var fn       = Marketify.Widgets[cb];

				widgetSettings[m] = settings;

				if ( typeof fn === 'function' )
					fn( m );
			} );

			$( '.widget_woothemes_features, .widget_woothemes_testimonials' ).find( '.fix' ).remove();
		},

		marketify_widget_featured_popular : function( widget_id ) {
			var settings = widgetSettings[ widget_id ];

			var slider = $( '.marketify_widget_featured_popular .flexslider' ).flexslider({
				animation      : "slide",
				slideshow      : settings.scroll ? settings.scroll : false,
				slideshowSpeed : settings.speed ? settings.speed : false,
				animationLoop  : false,
				itemWidth      : 360,
				itemMargin     : 30,
				minItems       : 1,
				maxItems       : 3,
				directionNav  : false,
				start          : function(slider) {
					slider.css( 'display', 'none' );

					$( '.marketify_widget_featured_popular .flexslider:first-of-type' ).fadeIn( 'slow' );
				}
			});

			$( '.marketify_widget_featured_popular .home-widget-title span' ).click(function() {
				if ( 0 == $(this).index() ) {
					$( '.marketify_widget_featured_popular .flexslider' ).hide();
					$( '.marketify_widget_featured_popular .flexslider:first-of-type' ).fadeIn();
				} else {
					$( '.marketify_widget_featured_popular .flexslider' ).hide();
					$( '.marketify_widget_featured_popular .flexslider:last-of-type' ).fadeIn();
				}

				$( '.marketify_widget_featured_popular .home-widget-title span' ).removeClass( 'active' );
				$(this).addClass( 'active' );

				slider.resize();
			});
		},

		widget_woothemes_testimonials : function( widget_id ) {
			if ( this.alreadyCalled )
				return;

			var quotes = $('.individual-testimonial');

			if ( quotes.length == 2 ) {
				$( '.individual-testimonial' ).fadeIn();

				return;
			}

			var settings = widgetSettings[ widget_id ];

			quotes.find( ':first-child, :nth-child(2n)' ).addClass( 'active' );

			function cycleQuotes () {
				var current = quotes.filter(".active"), next;

				if (current.length == 0 || (next = current.next().next()).length == 0 ) {
					next = quotes.slice(0,2);
				}

				current.removeClass( 'active' ).fadeOut(400).promise().done(function(){
					next.addClass( 'active' ).fadeIn();
				});

				setTimeout(cycleQuotes, settings.speed);
			}

			cycleQuotes();

			this.alreadyCalled = true;
		}
	}

} )(jQuery);

jQuery(document).ready(function() {
	Marketify.App.init();
	Marketify.Widgets.init();
});

jQuery(window).load(function() {
	Marketify.App.downloadStandard();
	Marketify.App.featuredPopular();
	Marketify.App.downloadGridViewer();

	var equalHeighters = [
		jQuery( '.page-template-page-templatesteam-php .entry-author' )
	];

	Marketify.App.equalHeights( equalHeighters );

	jQuery(window).resize(function() {
		Marketify.App.equalHeights( equalHeighters );
	});
});