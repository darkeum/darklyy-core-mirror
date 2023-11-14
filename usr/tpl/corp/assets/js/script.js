function modal_o(id, sub_id = null) {
	var time = 0;
	if(sub_id != null)
	{
		$(sub_id).magnificPopup('close');
		time = 777;
	}
	setTimeout(function() 
	{ 
		// Inline Admin-Form example
        $.magnificPopup.open({
            removalDelay: 500, //delay removal by X to allow out-animation,
            items: {
              src: id
            },
            // overflowY: 'hidden', //
            callbacks: {
              beforeOpen: function (e) {
                var Animation = 'mfp-zoomIn';
                this.st.mainClass = Animation;
              }
            },
            midClick: true // allow opening popup on middle mouse click. Always set it to true if you don't provide alternative source.
          });
	}, time);
		
         
        };
	
   var Stacks = {
          stack_top_right: {
            "dir1": "down",
            "dir2": "left",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_top_left: {
            "dir1": "down",
            "dir2": "right",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_bottom_left: {
            "dir1": "right",
            "dir2": "up",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_bottom_right: {
            "dir1": "left",
            "dir2": "up",
            "push": "top",
            "spacing1": 10,
            "spacing2": 10
          },
          stack_bar_top: {
            "dir1": "down",
            "dir2": "right",
            "push": "top",
            "spacing1": 0,
            "spacing2": 0
          },
          stack_bar_bottom: {
            "dir1": "up",
            "dir2": "right",
            "spacing1": 0,
            "spacing2": 0
          },
          stack_context: {
            "dir1": "down",
            "dir2": "left",
            "context": $("#stack-context")
          },
        };

   // PNotify Plugin Event Init
function notif(style, title_in, text_in) {
          var noteStyle = style;
          var noteShadow = true;
          var noteOpacity = '1';
          var noteStack = 'stack_bar_bottom';
          var width = "290px";
          // If notification stack or opacity is not defined set a default
          var noteStack = noteStack ? noteStack : "stack_top_right";
          var noteOpacity = noteOpacity ? noteOpacity : "1";
          // We modify the width option if the selected stack is a fullwidth style
          function findWidth() {
            if (noteStack == "stack_bar_top") {
              return "100%";
            }
            if (noteStack == "stack_bar_bottom") {
              return "70%";
            } else {
              return "290px";
            }
          }
   
      
	   new PNotify({
            title: title_in,
            text: text_in,
            shadow: noteShadow,
            opacity: noteOpacity,
            addclass: noteStack,
            type: noteStyle,
            stack: Stacks[noteStack],
            width: findWidth(),
            delay: 1400
          });
};

function scrollInit() {
$.scroller("reset");
}

let flagCost = false; 

 
function runFormElements2() {

    // Init Bootstrap tooltips, if present
    var Tooltips = $("[data-toggle=tooltip]");
    if (Tooltips.length) {
      if (Tooltips.parents('#sidebar_left')) {
        Tooltips.tooltip({
          container: $('body'),
          template: '<div class="tooltip" role="tooltip"><div class="tooltip-arrow"></div><div class="tooltip-inner"></div></div>'
        });
      } else {
        Tooltips.tooltip();
      }
    }

    // Init Bootstrap Popovers, if present
    var Popovers = $("[data-toggle=popover]");
    if (Popovers.length) {
      Popovers.popover();
    }

    // Init Bootstrap persistent tooltips. This prevents a
    // popup from closing if a checkbox it contains is clicked
    $('.dropdown-menu.dropdown-persist').on('click', function(e) {
      e.stopPropagation();
    });

    // Prevents a dropdown menu from closing when
    // a nav-tabs menu it contains is clicked
    $('.dropdown-menu .nav-tabs li a').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).tab('show')
    });

    // Prevents a dropdown menu from closing when
    // a btn-group nav menu it contains is clicked
    $('.dropdown-menu .btn-group-nav a').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      // Remove active class from btn-group > btns and toggle tab content
      $(this).siblings('a').removeClass('active').end().addClass('active').tab('show');
    });

    // if btn has ".btn-states" class we monitor it for user clicks. On Click we remove
    // the active class from its siblings and give it to the button clicked.
    // This gives the button set a menu like feel or state
    if ($('.btn-states').length) {
      $('.btn-states').on('click', function() {
        $(this).addClass('active').siblings().removeClass('active');
      });
    }

    // If a panel element has the ".panel-scroller" class we init
    // custom fixed height content scroller. An optional delay data attr
    // may be set. This is useful when you expect the panels height to
    // change due to a plugin or other dynamic modification.
    var panelScroller = $('.panel-scroller');
    if (panelScroller.length) {
      panelScroller.each(function(i, e) {
        var This = $(e);
        var Delay = This.data('scroller-delay');
        var Margin = 5;

        // Check if scroller bar margin is required
        if (This.hasClass('scroller-thick')) { Margin = 0; }

        // Check if scroller bar is in a dropdown, if so
        // we initilize scroller after dropdown is visible
        var DropMenuParent = This.parents('.dropdown-menu');
        if (DropMenuParent.length) {
          DropMenuParent.prev('.dropdown-toggle').on('click', function() {
            setTimeout(function() {
              This.scroller();
              $('.navbar').scrollLock('on', 'div');
            },50);
          });
          return;
        }

        if (Delay) {
          var Timer = setTimeout(function() {
            This.scroller({ trackMargin: Margin, });
            $('#content').scrollLock('on', 'div');
          }, Delay);
        }
        else {
          This.scroller({ trackMargin: Margin, });
          $('#content').scrollLock('on', 'div');
        }

      });
    }

    // Init smoothscroll on elements with set data attr
    // data value determines smoothscroll offset
    var SmoothScroll = $('[data-smoothscroll]');
    if (SmoothScroll.length) {
      SmoothScroll.each(function(i,e) {
        var This = $(e);
        var Offset = This.data('smoothscroll');
        var Links = This.find('a');

        // Init Smoothscroll with data stored offset
        Links.smoothScroll({
          offset: Offset
        });

      });
    }

  }


function accessModalAlert(mytext = null)
{
	const content_elements = document.createElement("div");
	content_elements.innerHTML = mytext;
	swal({
		title: "Отказано в доступе!",
		icon: "error",
		content: content_elements,
	});
}