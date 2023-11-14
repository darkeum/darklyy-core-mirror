$(window).load(function() {

	"use strict";


	
});


$(document).ready(function() {

	"use strict";

	/* Open search box */
	$('.btn-search').on( 'click', function() {
		$('.search-box').addClass('search-open');
		$('.overlay').addClass('overlay-open');
	});

	/* Open rightmenu box */
	$('.btn-rightbar').on( 'click', function() {
		$('.rightbar-box').addClass('rightbar-open');
		$('.overlay').addClass('overlay-open');
	});

	/* Open menu mobile */
	$('.menu-mobile').on( 'click', function() {
		$('.main-menu').addClass('mobile-open');
		$('.overlay').addClass('overlay-open');
	});

	/* Close box on overlay click */
	$('.overlay').on( 'click', function() {
		$('.search-box').removeClass('search-open');
		$('.rightbar-box').removeClass('rightbar-open');
		$('.main-menu').removeClass('mobile-open');
		$('.overlay').removeClass('overlay-open');
	});

	/* Dropdown menu mobile */
	$('.sub').on( 'click', function() {
		$('.sub').find('ul').removeClass('ul-open');
		$(this).find('ul').addClass('ul-open');
	});

	/* Rotate 3d effect */
	$(".bg-head").mousemove(function( event ) {
	  var cursorX = $( ".bg-head" ).width() / 2;
	  var cursorY = $( ".bg-head" ).height() / 2;
	  var posX = event.pageX - cursorX;
	  var posY = event.pageY - cursorY;
	  var move = "rotateY(" + posX * 0.03 + "deg) rotateX(" + (1 * - posY * 0.05) + "deg)";
	  var rotate = {'transform' : move};
	  $(".box-test").css(rotate);
	});


	/* Isotope Portfolio */
	var $grid = $('.grid').isotope({
	  itemSelector: '.grid-item',
	  layoutMode: 'fitRows'
	});

	$grid.imagesLoaded().progress( function() {
	  $grid.isotope('layout');
	});

/* Owl carousel clients */
  $(".owl-clients").owlCarousel({
  	items: 6,
  	loop: true,
  	autoplay: true,
  	autoplayTimeout: 2000,
  	dots: false,
  	responsiveClass: true,
    responsiveRefreshRate: true,
    responsive : {
      0 : {
        items: 1
      },
      480 : {
        items: 2
      },
      768 : {
      	items: 3
      },
      1080 : {
      	items: 6
      }
  	}
  });

	/* Owl our workmates */
  $(".owl-workmates").owlCarousel({
  	items: 7,
  	loop: true,
  	autoplay: true,
  	autoplayTimeout: 4000,
  	dots: true,
  	responsiveClass: true,
    responsiveRefreshRate: true,
    responsive : {
      0 : {
        items: 1
      },
      480 : {
        items: 3
      },
      980 : {
      	items: 4
      },
	  1080 : {
      	items: 7
      }
  	}
  });


	$('.filter-item').on( 'click', 'li', function() {
	  var filterValue = $(this).attr('data-filter');
	  $grid.isotope({ filter: filterValue });
	});

	/* Active class on isotope filter */
	$('.filter-item li').on('click', function(){
		$('.filter-item li').removeClass('active');
		$(this).addClass('active');
	});


  /* Magnific popup */
	$('.popup').magnificPopup({
		type: 'image',

		gallery:{
			enabled:true
    }
	});


 	/* Accordion */
	$('#accordion-1 ul li').on("click", function(){
		$('#accordion-1 ul .active').removeClass('active');
		$(this).addClass('active');
	});

});


/* Ajax contact form */
$("#contact-form").on("submit", function(e){
	e.preventDefault();
	var $form = $(this);

	$form.find('.errors').removeClass('errors');

  	$.ajax({
		url: $form.attr('action'),
		type: $form.attr('method'),
		data: $form.serialize(),
		datatype: 'json',
		success: function(data){
			console.log(data);
		    if(data.success === true){
		     	$('.form-success').addClass('success-open');
		    }
		    else{
		     	for(field in data.errors){
		     		var $input = $form.find('input[name='+ field+'], textarea[name='+ field+']');
		     		$input.addClass('errors');
		     	}
		    }
		},
		error:function(xhr){
			console.log(xhr.responseText);
		}   
	}); 
});