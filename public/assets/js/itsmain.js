(function($) {
	"use strict";
	/* Add Click On Ipad */
	$(window).resize(function(){
		var $width = $(this).width();
		if( $width < 1199 ){
			$( '.primary-menu .nav .dropdown-toggle'  ).each(function(){
				$(this).attr('data-toggle', 'dropdown');
			});
		}
	});
	jQuery(window).load(function() {
		/* Masonry Blog */
		$('body').find('.grid-blog').isotope({ 
			layoutMode : 'masonry'
		});
	});
	
    jQuery('.phone-icon-search').on('click', function(){
		/*alert("The paragraph was clicked.");*/
        jQuery('.top-search').toggle("slide");
    });
	
	 jQuery('.header-style5 .home-search').on('click', function(){
		$(this).toggleClass('close-search');
        jQuery('.sidebar-top-header .top-search').toggle("slide");
    });
	
	jQuery('.search-home3').on('click', function(){
		$(this).toggleClass("close-search");
        jQuery('.sidebar-top-header .flytheme_top .top-form.top-search').toggle("slide");
    });
	
	jQuery('.header-style5 .header-top').on('click', function(){
		$(this).toggleClass("active-menu");
    });
	
	jQuery('.header-style6 .header-top').on('click', function(){
		$(this).toggleClass("active-menu");
    });
	
	jQuery('.header-style6 .top-icon').on('click', function(){
		$('.top-form.top-search ').toggleClass( "active" );
		$('.sidebar-top-header .top-icon ').toggleClass( "close-icon" );
    });
	
	$('ul.orderby.order-dropdown li ul').hide(); 
    $("ul.order-dropdown > li").each( function(){
	   $(this).hover( function() {
		$(this).find( '> ul' ).stop().fadeIn("fast");
	   }, function() {
		 $(this).find( '> ul' ).stop().fadeOut("fast");
	   });
	});

	$(document).ready(function(){
		/* Quickview */
		$('.fancybox').fancybox({
			'width'     : 850,
			'height'   : '500',
			'autoSize' : false,
			afterShow: function() {
				$(' .share ').on('click', function() {
					$(' .social-share ').toggle( "slow" );
				});
				$( '.quickview-container .product-images' ).each(function(){
					var $id = this.id;
					var $rtl = $('body').hasClass( 'rtl' );
					var $img_slider = $( '#' + $id + ' .product-responsive');
					var $thumb_slider = $( '#' + $id + ' .product-responsive-thumbnail' )
					$img_slider.slick({
						slidesToShow: 1,
						slidesToScroll: 1,
						fade: true,
						arrows: false,
						rtl: $rtl,
						asNavFor: $thumb_slider
					});
					$thumb_slider.slick({
						slidesToShow: 3,
						slidesToScroll: 1,
						asNavFor: $img_slider,
						arrows: true,
						focusOnSelect: true,
						rtl: $rtl,
						responsive: [				
							{
							  breakpoint: 360,
							  settings: {
								slidesToShow: 2    
							  }
							}
						  ]
					});

					$(".product-images").fadeIn(1000, function() {
						$(this).removeClass("loading");
					});
				});
			}
		});
		/* Slider Image */
		$( '.product-images' ).each(function(){
			var $id 			= this.id;
			var $rtl 			= $(this).data('rtl');
			var $vertical		= $(this).data('vertical');
			var $img_slider 	= $( '#' + $id + ' .product-responsive');
			var $thumb_slider 	= $( '#' + $id + ' .product-responsive-thumbnail' );
			var $column_sm = 2;
			var $column_md = 5;
			var $column_xs = 2;
			if( $vertical ) {
				$column_sm = 3;
				$column_xs = 1;
			}
			$img_slider.slick({
				slidesToShow: 1,
				slidesToScroll: 1,
				fade: true,
				arrows: false,
				rtl: $rtl,
				asNavFor: $thumb_slider
			});
			$thumb_slider.slick({
				slidesToShow: 4,
				slidesToScroll: 1,
				asNavFor: $img_slider,
				arrows: true,
				focusOnSelect: true,
				rtl: $rtl,
				vertical: $vertical,
				verticalSwiping: $vertical,
				responsive: [				
					{
					  breakpoint: 1190,
					  settings: {
						slidesToShow: $column_sm    
					  }
					},
					{
					  breakpoint: 992,
					  settings: {
						slidesToShow: $column_md    
					  }
					},
					{
					  breakpoint: 767,
					  settings: {
						slidesToShow: 3    
					  }
					},
					{
					  breakpoint: 360,
					  settings: {
						slidesToShow: $column_xs    
					  }
					}
				  ]
			});

			$(".product-images").fadeIn(300, function() {
				$(this).removeClass("loading");
			});
		});
	});

var mobileHover = function () {
    $('*').on('touchstart', function () {
        $(this).trigger('hover');
    }).on('touchend', function () {
        $(this).trigger('hover');
    });
};

mobileHover();


    jQuery('a.phone-icon-menu').on('click', function(){
       var temp = jQuery('.navbar-inner.navbar-inverse').toggle( "slide" );
	   $(this).toggleClass('active');
    });
	$('.flytheme-tooltip').tooltip();
	/* fix accordion heading state */
	$('.accordion-heading').each(function(){
		var $this = $(this), $body = $this.siblings('.accordion-body');
		if (!$body.hasClass('in')){
			$this.find('.accordion-toggle').addClass('collapsed');
		}
	});
	

	/* twice click */
	$(document).on('click.twice', '.open [data-toggle="dropdown"]', function(e){
		var $this = $(this), href = $this.attr('href');
		e.preventDefault();
		window.location.href = href;
		return false;
	});

    $('#cpanel').collapse();

    $('#cpanel-reset').on('click', function(e) {

    	if (document.cookie && document.cookie != '') {
    		var split = document.cookie.split(';');
    		for (var i = 0; i < split.length; i++) {
    			var name_value = split[i].split("=");
    			name_value[0] = name_value[0].replace(/^ /, '');

    			if (name_value[0].indexOf(cpanel_name)===0) {
    				$.cookie(name_value[0], 1, { path: '/', expires: -1 });
    			}
    		}
    	}

    	location.reload();
    });

	$('#cpanel-form').on('submit', function(e){
		var $this = $(this), data = $this.data(), values = $this.serializeArray();

		var checkbox = $this.find('input:checkbox');
		$.each(checkbox, function() {

			if( !$(this).is(':checked') ) {
				name = $(this).attr('name');
				name = name.replace(/([^\[]*)\[(.*)\]/g, '$1_$2');

				$.cookie( name , 0, { path: '/', expires: 7 });
			}

		})

		$.each(values, function(){
			var $nvp = this;
			var name = $nvp.name;
			var value = $nvp.value;

			if ( !(name.indexOf(cpanel_name + '[')===0) ) return ;

			name = name.replace(/([^\[]*)\[(.*)\]/g, '$1_$2');

			$.cookie( name , value, { path: '/', expires: 7 });

		});

		location.reload();

		return false;

	});

	$('a[href="#cpanel-form"]').on( 'click', function(e) {
		var parent = $('#cpanel-form'), right = parent.css('right'), width = parent.width();

		if ( parseFloat(right) < -10 ) {
			parent.animate({
				right: '0px',
			}, "slow");
		} else {
			parent.animate({
				right: '-' + width ,
			}, "slow");
		}

		if ( $(this).hasClass('active') ) {
			$(this).removeClass('active');
		} else $(this).addClass('active');

		e.preventDefault();
	});
/*Product listing select box*/
	jQuery('.catalog-ordering .orderby .current-li a').html(jQuery('.catalog-ordering .orderby ul li.current a').html());
	jQuery('.catalog-ordering .sort-count .current-li a').html(jQuery('.catalog-ordering .sort-count ul li.current a').html());
/*currency Selectbox*/
	$('.currency_switcher li a').on('click', function(){
		var $current = $(this).attr('data-currencycode');
		jQuery('.currency_w > li > a').html($current);
	});
/*language*/
	$("#lang_sel ul > li > a").on({
		mouseover: function () {
			$('#lang_sel ul > li ul').css('display', 'block');
		},
		mouseleave: function () {
			$('#lang_sel ul > li ul').css('display', 'none');
		}
	});
	var $current ='';
	$('#lang_sel ul > li > ul li a').on('click',function(){
	 //console.log($(this).html());
	 $current = $(this).html();
	 $('#lang_sel ul > li > a.lang_sel_sel').html($current);
	  $a = $.cookie('lang_select_atom', $current, { expires: 1, path: '/'}); 
	});
	if( $.cookie('lang_select_atom') && $.cookie('lang_select_atom').length > 0 ) {
	 $('#lang_sel ul > li > a.lang_sel_sel').html($.cookie('lang_select_atom'));
	}
	
	
	 $('#lang_sel ul > li.icl-ge').click(function(){
		$('#lang_sel ul > li.icl-en').removeClass( 'active' );
		$(this).addClass( 'active' );
		$.cookie( 'clickboom_lang_en' , 1, { path: '/', expires: 1 });
	});
	$('#lang_sel ul > li.icl-en').click(function(){
		$('#lang_sel ul > li.icl-ge').removeClass( 'active' );
		$(this).addClass( 'active' );
		$.cookie( 'clickboom_lang_en' , 0, { path: '/', expires: -1 });
	});
	var Jiordano_Lang = $.cookie( 'clickboom_lang_en' );
	if( Jiordano_Lang == null ){
		$('#lang_sel ul > li.icl-en').addClass( 'active' );
		$('#lang_sel ul > li.icl-ge').removeClass( 'active' );
	}else{
		$('#lang_sel ul > li.icl-en').removeClass( 'active' );
		$('#lang_sel ul > li.icl-ge').addClass( 'active' );
	}

/*Quickview*/
	jQuery('.fancybox').fancybox({
		'width'     : 997,
		'height'   : 'auto',
		'autoSize' : false
	});
/*lavalamp*/
	$.fn.lavalamp = function(options){
		var defaults = {
    			elm_class: 'active',
				durations: 400
 		    },
            settings = $.extend(defaults, options);
		this.each( function(){
			var elm = ('> li');
			var current_check = $(elm, this).hasClass( settings.elm_class );
			current = elm + '.' + settings.elm_class;
			if( current_check ){
				var $this=jQuery(this), left0 = $(this).offset().left, dleft0 = $(current, this).offset().left - left0, dwidth0 = $('>ul>li.active', this).width();
				$(this).append('<div class="floatr"></div>');
				var $lava = jQuery('.floatr', $this), move = function(l, w){
					$lava.stop().animate({
						left: l,
						width: w
					}, {
						duration: settings.durations,
						easing: 'linear'
					});
				};
				
				var $li = jQuery('>li', this);		
				
				move(dleft0, dwidth0);
				$lava.show();
				$li.hover(function(e){
					var dleft = $(this).offset().left - left0;
					var dwidth = $(this).width();
					move(dleft, dwidth);
				}, function(e){
					move(dleft0, dwidth0);
				});	
			}
		});
	}

	/*
	** Menu hidden
	*/
	$('.product-categories').each(function(){
		var number	 = $(this).data( 'number' ) - 1;
		var lesstext = $(this).data( 'lesstext' );
		var moretext = $(this).data( 'moretext' );
		if( number > 0 ) {
			$(this).find( 'li:gt('+ number +')' ).hide().end();		
			if( $(this).children('li').length > number ){ 
				$(this).append(
					$('<li><a>'+ moretext +'   +</a></li>')
					.addClass('showMore')
					.on('click',function(){
						if($(this).siblings(':hidden').length > 0){
								$(this).html( '<a>'+ lesstext +'   -</a>' ).siblings(':hidden').show(400);
						}else{
								$(this).html( '<a>'+ moretext +'   +</a>' ).show().siblings( ':gt('+ number +')' ).hide(400);
						}
					})
				);
			}
		}
	});

	jQuery(document).ready(function(){
		var currency_show = jQuery('ul.currency_switcher li a.active').html();
		jQuery('.currency_to_show').html(currency_show);	
	}); 
/*end lavalamp*/
	jQuery(function($){
	// back to top
	$("#flytheme-totop").hide();
	$(function () {
		var wh = $(window).height();
		var whtml = $(document).height();
		$(window).scroll(function () {
			if ($(this).scrollTop() > whtml/10) {
					$('#flytheme-totop').fadeIn();
				} else {
					$('#flytheme-totop').fadeOut();
				}
			});
		$('#flytheme-totop').click(function () {
			$('body,html').animate({
				scrollTop: 0
			}, 800);
			return false;
			});
	});
	/* end back to top */
	}); 
	jQuery(document).ready(function(){
  jQuery('.wpcf7-form-control-wrap').on('hover', function(){
   $(this).find('.wpcf7-not-valid-tip').css('display', 'none');
  });
 });
 
 $(".sw-woo-tab-style2").fadeIn(300, function() {
		$(this).removeClass("loading");
	});
  $(".sw-woo-tab-cat").fadeIn(300, function() {
		$(this).removeClass("loading");
	});
  $(".sw-woo-tab").fadeIn(300, function() {
		$(this).removeClass("loading");
	});
 $(".responsive-slider").fadeIn(300, function() {
		$(this).removeClass("loading");
	});
	$(".product-images").fadeIn(300, function() {
		$(this).removeClass("loading");
	});
 /*fix js */
 $('.wpb_map_wraper').on('click', function () {
    $('.wpb_map_wraper iframe').css("pointer-events", "auto");
});

$( ".wpb_map_wraper" ).on('mouseleave', function() {
  $('.wpb_map_wraper iframe').css("pointer-events", "none"); 
});
/*add class menu style*/
$( "header-style1 .header-bottom .primary-menu" ).find( ".sticky-menu" ).addClass( "menu-ontop" );

//toggle share product detail
$(' .share ').on('click', function() {
  $(' .social-share ').toggle( "slow" );
});


}(jQuery));



