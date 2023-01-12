/**
 * Tooltip related functions
 */
function Tooltip()
{
	/**
	 * Add event-listeners
	 */
	this.initialize = function()
	{
		// Add the tooltip element
		$("body").prepend('<div id="tooltip" class="tooltip"></div>');

		// Add mouse-over event listeners
		this.addEvents();

		// Add mouse listener
		$(document).mousemove(function(e) {
			Tooltip.move(e.pageX, e.pageY);
		});
	}

	/**
	 * Used to support Ajax content
	 * Reloads the tooltip elements
	 */
	this.refresh = function()
	{
		// Re-add
		this.addEvents();
	}

	this.addEvents = function()
	{
		// Add mouse-over event listeners
		$("[rel]").hover(
			function() {
				if (/item\=(\d+)/.test($(this).attr("rel"))) {
					Tooltip.Item.get(this, function(data) {
						Tooltip.show(data);
					});
                }
                if (/spell\=(\d+)/.test($(this).attr("rel"))) {
					Tooltip.Spell.get(this, function(data) {
						Tooltip.show(data);
					});
				}
            }, 
            function() {
				$("#tooltip").hide();
			}
		);	
	}

	/**
	 * Moves tooltip
	 * @param Int x
	 * @param Int y
	 */
	this.move = function(x, y)
	{
        if (!$("#tooltip").is(':visible'))
            return;
        
		// Get half of the width
		var width = ($("#tooltip").css("width").replace("px", "") / 2);

		// Position it at the mouse, and center
		$("#tooltip").css("left", x - width).css("top", y + 25);
	}

	/**
	 * Displays the tooltip
	 * @param Object element
	 */
	this.show = function(data)
	{
		$("#tooltip").html(data).show();
	}

	/**
	 * Item tooltip object
	 */
	 this.Item = new function()
	 {
	 	/**
	 	 * Loading HTML
	 	 */
	 	this.loading = "Loading...";

	 	/**
	 	 * Runtime cache
	 	 */
	 	this.cache = new Array();

	 	/**
	 	 * The currently displayed item ID
	 	 */
	 	this.currentId = false;

	 	/**
	 	 * Load an item and display it in the tooltip
	 	 * @param Object element
	 	 * @param Function callback
	 	 */
	 	this.get = function(element, callback)
	 	{
	 		var obj = $(element);
            var realm = obj.attr("data-realm");
            var itemRegex = /item\=(\d+)/;
            var id = itemRegex.exec(obj.attr("rel"))[1];
            var enchRegex = /ench=(\d+)/;
            var ench = (enchRegex.test(obj.attr("rel")) ? enchRegex.exec(obj.attr("rel"))[1] : false);

	 		Tooltip.Item.currentId = id;
			
			if (typeof realm == 'undefined')
				realm = 0;
			
	 		if (id in this.cache) {
	 			callback(this.cache[id])
	 		} else {
                callback(this.loading);

                $.get($BaseURL + "/tooltip/item?realm=" + realm + "&entry=" + id + (ench ? '&ench=' + ench : ''), function(data) {
                    if (typeof data.error != 'undefined') {
                        console.log(data.error);
                        // Make sure it's still visible
                        if ($("#tooltip").is(":visible") && Tooltip.Item.currentId == id) {
                            callback(data.error);
                        }
                    } else if (typeof data.html != 'undefined') {
                        Tooltip.Item.cache[id] = data.html;

                        // Make sure it's still visible
                        if ($("#tooltip").is(":visible") && Tooltip.Item.currentId == id) {
                            callback(data.html);
                        }
                    } else {
                        // Make sure it's still visible
                        if ($("#tooltip").is(":visible") && Tooltip.Item.currentId == id) {
                            callback(data);
                        }
                    }
                });
		 	}
	 	}
    },
    
    /**
	 * Spell tooltip object
	 */
	 this.Spell = new function()
	 {
	 	/**
	 	 * Loading HTML
	 	 */
	 	this.loading = "Loading...";

	 	/**
	 	 * Runtime cache
	 	 */
	 	this.cache = new Array();

	 	/**
	 	 * The currently displayed spell ID
	 	 */
	 	this.currentId = false;

	 	/**
	 	 * Load an item and display it in the tooltip
	 	 * @param Object element
	 	 * @param Function callback
	 	 */
	 	this.get = function(element, callback)
	 	{
	 		var obj = $(element);
	 		var realm = obj.attr("data-realm");
            var spellRegex = /spell\=(\d+)/;
            var id = spellRegex.exec(obj.attr("rel"))[1];
             
	 		Tooltip.Spell.currentId = id;
			
			if (typeof realm == 'undefined')
				realm = 0;
			
	 		if (id in this.cache) {
	 			callback(this.cache[id])
	 		} else {
                callback(this.loading);

                $.get($BaseURL + "/tooltip/spell?realm=" + realm + "&entry=" + id, function(data) {
                    if (typeof data.error != 'undefined') {
                        console.log(data.error);
                        // Make sure it's still visible
                        if ($("#tooltip").is(":visible") && Tooltip.Spell.currentId == id) {
                            callback(data.error);
                        }
                    } else if (typeof data.html != 'undefined') {
                        Tooltip.Spell.cache[id] = data.html;

                        // Make sure it's still visible
                        if ($("#tooltip").is(":visible") && Tooltip.Spell.currentId == id) {
                            callback(data.html);
                        }
                    } else {
                        // Make sure it's still visible
                        if ($("#tooltip").is(":visible") && Tooltip.Spell.currentId == id) {
                            callback(data);
                        }
                    }
                });
		 	}
	 	}
	}
}

var Tooltip = new Tooltip();

$(document).ready(function(e)
{
	Tooltip.initialize();
});