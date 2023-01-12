//-----------------------------------------------------------------------//
//-------------------- Loading Bar, jQuery plugin -----------------------//
//---------------------- Script by ChoMPi -------------------------------//
//-----------------------------------------------------------------------//

(function($)
{
	var methods =
	{
		init : function()
		{
			//if we have the element
			if ($(this).length < 1)
			{
				return;
			}
			
			//get the instance of the element
			var $element = $(this);
			
			//create the bar
			$element.append('<div class="loading-bar" align="left"><span id="bar"></span></div>');
			
			//run to state 1
            $element.LoadingBar('state1'); 
		},
		
		state1 : function(callback)
		{
			var $element = $(this);

			$element.find('#bar').stop(true,true).animate({ width: '100px' }, 1000, function()
			{
				$(this).css('width', '100px');
				
				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		state2 : function(callback)
		{
			var $element = $(this);
			
			$element.find('#bar').stop(true,true).animate({ width: '200px' }, 500, function()
			{
				$(this).css('width', '200px');
				
				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		state3 : function(callback)
		{
			var $element = $(this);

			$element.find('#bar').stop(true,true).animate({ width: '300px' }, 500, function()
			{
				$(this).css('width', '300px');

				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		state4 : function(callback)
		{
			var $element = $(this);

			$element.find('#bar').stop(true,true).animate({ width: '400px' }, 500, function()
			{
				$(this).css('width', '400px');
				
				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		restart : function()
		{
			var $element = $(this);
			
			$element.find('#bar').css('width', '0px');
		},
	}
	
  	$.fn.LoadingBar = function(method)
  	{
  		if (methods[method])
		{
     		return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
    	}
		else if (typeof method === 'object' || ! method)
		{
      		return methods.init.apply(this, arguments);
    	}
		else
		{
      		$.error( 'Method ' +  method + ' does not exist on jQuery.LoadingBar');
    	}    
  	};

})(jQuery);