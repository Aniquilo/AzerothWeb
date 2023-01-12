var Tooltip = {
    isOver: false,
    request: null,
    cache: {},

    /**
	 * Add event-listeners
	 */
	initialize: function() {
		// Add the tooltip element
		$("body").prepend('<div id="tooltip" class="tooltip"></div>');

		// Add mouse-over event listeners
		this.addEvents();

		// Add mouse listener
		$(document).mousemove(function(e) {
			Tooltip.move(e.pageX, e.pageY);
		});
    },
    
    /**
	 * Used to support Ajax content
	 * Reloads the tooltip elements
	 */
    refresh: function() {
        
    },

    addEvents: function() {
        // Add mouse-over event listeners
        $('body').on("mouseenter", "[data-tip]", function(e) {
            Tooltip.isOver = true;
			Tooltip.tipHandler(e);
        });
        $('body').on("mouseleave", "[data-tip]", Tooltip.mouseLeaveHandler);

        // Characters
		$('body').on("mouseleave", "[data-character-tip]", function(e)
		{
			Tooltip.isOver = true;
			Tooltip.characterHandler(e);
		});
        $('body').on("mouseleave", "[data-character-tip]", Tooltip.mouseLeaveHandler);
	},

	/**
	 * Moves tooltip
	 * @param Int x
	 * @param Int y
	 */
	move: function(x, y) {
        if (!$("#tooltip").is(':visible'))
            return;
        
		// Get half of the width
		var width = ($("#tooltip").css("width").replace("px", "") / 2);

		// Position it at the mouse, and center
		$("#tooltip").css("left", x - width).css("top", y + 25);
	},

    mouseLeaveHandler: function(e)
	{
		Tooltip.isOver = false;
		$("#tooltip").hide();
    },
    
	/**
	 * Displays the tooltip
	 * @param Object element
	 */
	show: function(data) {
		$("#tooltip").html(data).show();
    },
    
    tipHandler: function(e)
	{
		if (typeof $(e.currentTarget).attr('data-tip') == 'undefined' || $(e.currentTarget).attr('data-tip').length == 0)
			return;
		
		Tooltip.show($(e.currentTarget).attr("data-tip"));
    },
    
    characterHandler: function(e)
	{
		if (typeof $(e.currentTarget).attr('data-character-tip') == 'undefined' || $(e.currentTarget).attr('data-character-tip').length == 0)
			return;
		
		if (typeof $(e.currentTarget).attr('data-realm') == 'undefined' || $(e.currentTarget).attr('data-realm').length == 0)
			return;
		
		var characterName = $(e.currentTarget).attr('data-character-tip');
		var realmId = $(e.currentTarget).attr('data-realm');
		
		// Interrupt previous requests
		if (this.request != null)
		{ 
			this.request.abort();
			this.request = null;
		}
		
		// Check for cache
		if (typeof this.cache[realmId + '-' + characterName] != 'undefined')
		{
			if (Tooltip.isOver)
			{
				Tooltip.show(Tooltip.cache[realmId + '-' + characterName]);
			}
		}
		else
		{
			Tooltip.show('Loading...');
			
			// Make the request
			this.request = $.ajax(
			{
				type: "GET",
				url: $BaseURL + '/armory/character/tooltip?realm=' + realmId + '&character=' + characterName,
			})
			.done(function(msg)
			{
				if (Tooltip.isOver)
				{
					Tooltip.show(msg);
				}
				
				// Cache it
				Tooltip.cache[realmId + '-' + characterName] = msg;
			});
		}
	},
};

$(document).ready(function() {
    // Enable tooltip
    Tooltip.initialize();
});