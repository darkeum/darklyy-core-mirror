function fnCreateGridster(page, colors, states, titles){
	/* load saved position and sizes */
	if(localdata_position){
		$.each(localdata_position, function(i,value){
				$('#'+value.id).attr({"data-col":value.col, "data-row":value.row, "data-sizex":value.size_x, "data-sizey":value.size_y});
		});
	}
	
	/* load titles */
	if(localdata_titles){
		$.each(localdata_titles, function(i,value){
			if(value){
				if(value.title) $('#'+ value.panel + ' .panel-title-text').html(value.title);
			}
		});
	}


	/* force 1 column on mobile screen sizes */
	if ($( window ).width() <= 480 || $( window ).width() == 640 ){
		var cols=1;
		var offset=40;
	} else {
		var cols=2
		var offset=20;
	}


	/* get the default size for the ratio */
	var base_size=($( window ).width()/cols)-offset;


	/* start gridster */
	var gridster= $(".gridster > ul").gridster({
        extra_cols: 1,
        autogrow_cols: true,
		min_cols:1,
		max_cols:cols,
		widget_margins: [5, 5],
		widget_base_dimensions: [base_size, 50],
		resize: {
			enabled: true,
			stop: function(event, ui, widget) {
				var positions = JSON.stringify(this.serialize());
				localStorage.setItem(page, positions);
				 Highcharts.charts[0].reflow();
			}
         },
		serialize_params: function($w, wgd) 
		{
			return { id: $($w).attr('id'),col: wgd.col, row: wgd.row,size_x: wgd.size_x,size_y: wgd.size_y };
		},
		draggable: 
		{
			handle: '.gridster-drag, .gridster-action',
			
			stop: function(event, ui) {
				var _positions=this.serialize();
				$.each(_positions, function(i,value){
					_state=$('#'+ value.id).attr('data-state');
					if(_state=='min'){
						value.size_y=$('#'+ value.id).attr('data-sizey-old')
						_positions[i]=value;
					}
				
				});
				var positions = JSON.stringify(_positions);
				localStorage.setItem(page, positions);
				 Highcharts.charts[0].reflow();
			}
		}	
    }).data('gridster');




	
	/* load states (after colors) */
	if(localdata_states){
		$.each(localdata_states, function(i,value){
			if(value){
				console.log(value);
				if(value.state == 'closed'){
					$(".gridster > ul").data('gridster').remove_widget($('#'+value.panel));
				}else if(value.state == false) _state_minimize(value.panel);
			}
		});
	}




	/* register the close button */
	$(document).on("click", ".panel-close", function(e) {
		e.preventDefault();    
		var panel = $(this).attr("data-id");
		bootbox.confirm('Are you sure?', function(result) {
			if(result){
				$(".gridster > ul").data('gridster').remove_widget($('#'+panel));
				_state_update(panel, 'closed');
			}
		});
	});




	/* helpers */

	function _state_update(panel, _state){
		var _states = {
			panel: panel,
			state: _state
		  };

		if(localdata_states){
			$.each(localdata_states, function(i,value){
				if(value){
					if(value.panel == panel) localdata_states.splice(i, 1);		
				}
			});
		} else localdata_states=[];

		localdata_states.push(_states);
		localStorage.setItem(states, JSON.stringify(localdata_states));
	}

	function _state_maxamize(panel){
		$('#'+panel +'').attr('data-state', 'max');
		var _oldsize=parseInt($('#'+panel).attr('data-sizey-old'));
		$('#'+panel +'').attr('data-sizey', _oldsize);
		$(".gridster > ul").data('gridster').resize_widget($('#'+panel),$('#'+panel).attr('data-sizex'),_oldsize);
		$('#'+panel +' .panel').css('padding-bottom', '60px');
		$('#'+panel +' .panel-body').slideDown();
		$('#'+panel +' .panel-hide').removeClass('glyphicon-plus').addClass('glyphicon-minus');
		$('#'+panel +' .gs-resize-handle').show();
		$('#'+panel +' .panel-color, #'+panel +' .panel-max, #'+panel +' .panel-close').show();
	}

	function _state_minimize(panel){
		$('#'+panel +'').attr('data-state', 'min');
		$('#'+panel).attr('data-sizey-old', $('#'+panel).attr('data-sizey'));
		$(".gridster > ul").data('gridster').resize_widget($('#'+panel),$('#'+panel).attr('data-sizex'),1);
		$('#'+panel).attr('data-sizey', '1');
		$('#'+panel +' .gs-resize-handle').hide();
		$('#'+panel +' .panel-body').slideUp();
		$('#'+panel +' .panel-hide').removeClass('glyphicon-minus').addClass('glyphicon-plus');
		$('#'+panel +' .panel').css('padding-bottom', '0px');
		$('#'+panel +' .panel-color, #'+panel +' .panel-max, #'+panel +' .panel-close').hide();
	}

	function _resize_gridster(){
		gridster.resize_widget_dimensions({
			widget_base_dimensions: [(((base_size*($('#main-gridster').width()/base_size))/cols)-offset), 50],
			widget_margins: [5, 5],
		});
		setTimeout(function(){
		 Highcharts.charts[0].reflow();
		
	}, 1000);
	}

	/* we're ready for the show */
	$(window).on('resize load',_resize_gridster);
$(".gridster > ul").data('gridster').disable_resize();
	/* give it a bit to fully load then fade in */
	setTimeout(function(){
		$('.gridster').fadeIn('fast');
		
	}, 400);
	
	
	

}