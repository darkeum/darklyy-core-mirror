/*
	** Category Ajax Js
	** Version: 1.0.0
*/
(function ($) {
	$(document).ready(function(){
		/* Category slider ajax */
		var el = $( '.active [data-catload=ajax]' );
		el.each( function(){
			var els = $(this);
			sw_tab_click_ajax( els );
		});		
		$('[data-catload=ajax]').on('click', function() {
			sw_tab_click_ajax( $(this) );
		});
		$('[data-catload=ajax_listing]').on('click', function() {
			sw_tab_ajax_listing( $(this) );
		});
		
		$('.sw-wootab-slider .tab-content').addClass( 'loading' );
		$('[data-catload=so_ajax]').on('click', function() {
			sw_tab_click_ajax( $(this) );
		});
		
		
		function sw_tab_click_ajax( element ) {			
			var target 		= $( element.attr( 'href' ) );
			var id 				= element.attr( 'href' );
			var length		= element.data( 'length' );
			var ltype     = element.data( 'type' );
			var layout 		= element.data( 'layout' );
			var orderby 	= element.data( 'orderby' );
			var order 		= element.data( 'order' );
			var item_row  = element.data( 'row' );
			var sorder    = element.data( 'sorder' );
			var catid 		= element.data( 'category' );
			var number 		= element.data( 'number' );
			var columns 	= element.data( 'lg' );
			var columns1 	= element.data( 'md' );
			var columns2 	= element.data( 'sm' );
			var columns3 	= element.data( 'xs' );
			var columns4 	= element.data( 'mobile' );
			var interval 	= element.data( 'interval' );
			var scroll 		= element.data( 'scroll' );
			var speed 		= element.data( 'speed' );
			var autoplay 	= element.data( 'autoplay' );	
			var action = '';
			if( ltype == 'cat_ajax' ) {
				action = 'sw_category_callback';
			} else if( ltype == 'so_ajax' ) {
				action = 'sw_tab_category';
			} else if( ltype == 'tab_ajax' ) {
				action = 'sw_ajax_tab';
			}else if( ltype == 'tab_ajax_listing' ) {
				action = 'sw_ajax_tab_listing';
			}
			var ajaxurl   = element.data( 'ajaxurl' ).replace( '%%endpoint%%', action );
			if( target.html() == '' ){
				target.parent().addClass( 'loading' );
				var data 		= {
					action: action,
					catid: catid,
					number: number,
					target: id,
					title_length: length,
					layout: layout,
					item_row: item_row,
					layout: layout,
					sorder: sorder,
					orderby: orderby,
					order: order,
					columns: columns,
					columns1: columns1,
					columns2: columns2,
					columns3: columns3,
					columns4: columns4,
					interval: interval,
					speed: speed,
					scroll: scroll,
					autoplay: autoplay,
				};
				jQuery.post(ajaxurl, data, function(response) {
					target.html( response );
					sw_slider_ajax( target );
					target.parent().removeClass( 'loading' );
				});
			}
		}
		
		function sw_slider_ajax( target ) {	
			var element 	= $(target).find( '.responsive-slider' );
			var $col_lg 	= element.data('lg');
			var $col_md 	= element.data('md');
			var $col_sm 	= element.data('sm');
			var $col_xs 	= element.data('xs');
			var $col_mobile = element.data('mobile');
			var $speed 		= element.data('speed');
			var $interval 	= element.data('interval');
			var $scroll 	= element.data('scroll');
			var $autoplay 	= element.data('autoplay');
			var $rtl 		= $('body').hasClass( 'rtl' );
			$target = $(target).find( '.responsive' );
			$target.slick({
			  appendArrows: $(target),
			  prevArrow: '<span data-role="none" class="res-button slick-prev" aria-label="previous"></span>',
			  nextArrow: '<span data-role="none" class="res-button slick-next" aria-label="next"></span>',
			  dots: false,
			  infinite: true,
			  speed: $speed,
			  slidesToShow: $col_lg,
			  slidesToScroll: $scroll,
			  autoplay: $autoplay,
			  autoplaySpeed: $interval,
			  rtl: $rtl,			  
			  responsive: [
				{
				  breakpoint: 1199,
				  settings: {
					slidesToShow: $col_md,
					slidesToScroll: $col_md
				  }
				},
				{
				  breakpoint: 991,
				  settings: {
					slidesToShow: $col_sm,
					slidesToScroll: $col_sm
				  }
				},
				{
				  breakpoint: 767,
				  settings: {
					slidesToShow: $col_xs,
					slidesToScroll: $col_xs
				  }
				},
				{
				  breakpoint: 480,
				  settings: {
					slidesToShow: $col_mobile,
					slidesToScroll: $col_mobile					
				  }
				}
			  ]
			});
			setTimeout(function(){
				element.removeClass("loading");
			}, 500);
		}			
	});
	$(window).on('load',function(){
		$( '.sw-woo-tab-listing' ).each(function(){
			var $this 				= $(this);
			var $id 					= this.id;
			var $item_height  = $(this).find( '.item-listing' ).outerHeight();
			var $btn_loadmore = $(this).find('.item-more > a');
			var $categories 	= $btn_loadmore.data('category');
			var $max_page 		= $btn_loadmore.data('max_page');
			var $attributes 	= $btn_loadmore.data('attributes');
			var $number 			= $btn_loadmore.data('number');
			var $selectorder	= $btn_loadmore.data('selectorder');
			var $ajax_url			= $btn_loadmore.data('ajaxurl').replace( '%%endpoint%%', 'sw_resp_listing_ajax' );
			var $container_id = $(this).find('.tab-listing-container');
			var $page = 1;		
			$btn_loadmore.css( 'height', $item_height );
			if( $page >= $max_page ){
				$btn_loadmore.parent().addClass( 'btn-loaded' );
			}
			$btn_loadmore.on( 'click',function(){
				if( $page >= $max_page ){
					return false;
				}
				$(this).parent().addClass('btn-loading');				
				jQuery.ajax({
					type: "POST",
					url: $ajax_url,
					data: ({
						action 	: "sw_resp_listing_ajax",
						catid  	: $categories,
						numb   	: $number,
						selectorder	: $selectorder,
						page 		: $page,
						attributes: $attributes
					}),
					 success: function(data) {		
						var $newItems = $(data);
						if( $newItems.length > 0 ){
							$newItems.imagesLoaded( function(){
								setTimeout(function(){
									$newItems.insertAfter( '#'+ $id + ' .item-listing:nth-last-child(2)' );
								}, 500);
							});
							$btn_loadmore.parent().removeClass('btn-loading');
							$page = $page + 1;
							if( $newItems.length < $number ){
								$btn_loadmore.parent().addClass( 'btn-loaded' );
							}
							if( $page >= $max_page ){
								$btn_loadmore.parent().addClass( 'btn-loaded' );
							}
						}else{
							$btn_loadmore.parent().addClass( 'btn-loaded' );
						}
					}
				});
			});
		});
	});
})(jQuery);