// JavaScript Document

(function($)
{
  	var $AlertBox = {};
	
	var methods =
	{
		getCaller : function()
		{
			return (typeof $(this) != 'undefined') ? $AlertBox.CalledBy : null;
		},
		
		open : function(html, buttons)
		{
			if (typeof $(this) != 'undefined')
			{
				$AlertBox.CalledBy = $(this);
			}
			else
			{
				$AlertBox.CalledBy = null;
			}
            
            //check if a box is being closed
            if ($('#Alert-box_container').length > 0) {
                $('#Alert-box_container').stop(true, true).hide();
                $('#Alert-box_container').remove();
            }

			//append new element
			$('body').append('<div id="Alert-box_container" align="center"><div class="alert-box-holder">'+ html +'</div></div>');
			
			//Check if we have buttons
			if (typeof buttons == 'object')
			{
				$('.alert-box-holder').append('<div class="alert-box-buttons"></div>');
				
				//loop the buttons
				for (var key in buttons)
				{
					var button = buttons[key];
					
					//create the button
					var newButton = $('<a class="button" href="javascript:void(0)">' + button.text + '</a>');
					
					//bund click function
					if (typeof button.onclick == 'string')
					{
						if (button.onclick == 'close')
							$(newButton).click(function(){ $.fn.WarcryAlertBox('close'); return false; });
					}
					else if (typeof button.onclick == 'function')
					{
						$(newButton).click(button.onclick);
					}
					
					//append the button
					$('.alert-box-buttons').append(newButton);
				}
			}

            $('#Alert-box_container').stop().animate({ opacity: 1 }, 'fast');
            
            setTimeout(function() {
                let container = $('#Alert-box_container').find('.alert-box-holder');
                container.animate({ marginTop: '-' + (container.outerHeight() / 2) + 'px' }, 'fast');
            }, 100);

			$AlertBox.closeEvent = true;
														
			//bind some close events
			$('#Alert-box_container > .alert-box-holder').on('mouseenter', function()
			{
				$AlertBox.closeEvent = false;
			});
			$('#Alert-box_container > .alert-box-holder').on('mouseleave', function()
			{
				$AlertBox.closeEvent = true;
			});
			$('#Alert-box_container').on('click', function()
			{
				if ($AlertBox.closeEvent)
				{
					$.fn.WarcryAlertBox('close');
				}
			});
			//close the box on escape
			$(document).keyup(function(e)
			{
				//if escape key
				if (e.keyCode == 27) 
				{
					if ($('#Alert-box_container').length > 0)
					{
						//if the container is visible only
						if ($('#Alert-box_container').is(':visible'))
						{
							$.fn.WarcryAlertBox('close');
						}
					}
				}
			});
		},
		
		close: function()
		{
			$('#Alert-box_container').fadeOut('fast', function()
			{
				$('#Alert-box_container').remove();
			});
		},
	}
	
  	$.fn.WarcryAlertBox = function(method)
  	{
  		if (methods[method])
		{
     		return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
    	}
		else
		{
      		$.error( 'Method ' +  method + ' does not exist on jQuery.WarcryAlertBox');
    	}    
  	};

})(jQuery);