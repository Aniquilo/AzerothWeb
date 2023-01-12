var Armory = {
	current: null,
	inactiveRealms: [],
	request: null,
	
	Initialize: function()
	{
		// Apply the url state if any
		Armory.applyCurrentUrlState();
		
		// Hook the event of state change
		$(window).bind("popstate", function(e)
		{
			if (typeof e.originalEvent.state.search != 'undefined' && e.originalEvent.state.search != Search.current)
			{
				$("#search_field").val(e.originalEvent.state.search);
				Armory.activateTabOnLoad = e.originalEvent.state.tab;
				Armory.submit();
			}
        });
        
        $('#armory_form').on('submit', function() {
            Armory.submit();
            return false;
        });
	},
	
	applyCurrentUrlState: function()
	{
		var loc = document.location.href;
		
		// Get the current state
		if (loc.indexOf('#') > -1)
		{
			var stateQuery = loc.substring(loc.indexOf('#') + 1)
			searchText = stateQuery.substring(0, stateQuery.length);
			
			// Get the search string
			if (typeof searchText != 'undefined' && searchText.length > 0)
			{
				var searchValue = decodeURIComponent(searchText);
				
				if (searchValue.length > 0)
					$("#armory_search").val(searchValue);
			}
			
			Armory.submit();
		}
	},
	
	updateQuery: function()
	{
		history.replaceState({ search: Armory.current }, '', '#' + encodeURIComponent(Armory.current));
	},
	
	/**
	 * Get search results
	 */
	submit: function()
	{
		var results = $("#armory_results");
		var value = $("#armory_search").val();

        $("#armory_search").blur();

		if (value.length > 2)
		{
            if (!$('#search_bar').hasClass('animate')) {
                $('#search_bar').addClass('animate');
            }

			if (value != Armory.current)
			{
				if (Armory.request != null)
				{
					Armory.request.abort();
				}
				
				Armory.current = value;
				Armory.updateQuery();
				
				results.hide().html('<div style="text-align: center">Loading...</div>').fadeIn(200, function()
				{
					Armory.request = $.post($BaseURL + "/armory/search", { search: value }, function(response)
					{
                        if (typeof response.error != 'undefined') {
                            //prompt the error
                            $.fn.WarcryAlertBox('open', '<p>'+response.error+'</p>');
                        } else {
                            var dataFormatted = $('<div>' + response.html + '</div>');
						    Armory.showSearch(dataFormatted);
                        }
					});
				});
			}
		}
		else
		{
			//prompt the error
            $.fn.WarcryAlertBox('open', '<p>Please enter search text.</p>');
		}
	},
	
	showSearch: function(data)
	{
		var results = $("#armory_results");

		results.fadeOut(100, function()
		{
			results.html(data).fadeIn(100, function()
			{
				Tooltip.refresh();
			});
		});
	},

	/**
	 * Change to a tab
	 * @param Int tab
	 */
	showTab: function(element)
	{
		var tab = parseInt($(element).attr('data-id'));
		
		Armory.SetActiveTab(parseInt(tab));
		
		$(".search_link").removeClass("active");

		$(element).addClass("active");

		$(".search_tab").hide();

		$("#search_tab_" + tab).fadeIn(500);
	},

	/**
	 * Toggle the visiblity of content of a realm
	 * @param Int realm
	 * @param Element field
	 */
	 toggleRealm: function(realm, btn)
	 {
	 	if ($(btn).hasClass("active"))
	 	{
	 		$(btn).removeClass("active");
	 		$(".search_result_realm_" + realm).hide();
			
             Armory.inactiveRealms.push(realm);
	 	}
	 	else
	 	{
	 		$(btn).addClass("active");
	 		$(".search_result_realm_" + realm).show();
			
			if (Armory.inactiveRealms.indexOf(realm) > -1)
			{
				Armory.inactiveRealms.slice(Armory.inactiveRealms.indexOf(realm), 1);
			}
	 	}
	 }
};