var Character = {
    /**
	  * Whether the tabs are changing or not
	  * @type Boolean
	  */
    tabsAreChanging: false,

    /**
	  * Change tab
	  * @param String selector
	  * @param Object link
	  */
    tab: function(selector, link)
    {
	 	if (!this.tabsAreChanging)
	 	{
	 		this.tabsAreChanging = true;

		 	// Find out the current tab
		 	var currentTabLink = $(".armory_current_tab");
		 	var currentTabId = "#tab_" + currentTabLink.attr("onClick").replace("Character.tab('", "").replace("', this)", "");

		 	// Change link states
		 	currentTabLink.removeClass("armory_current_tab");
		 	$(link).addClass("armory_current_tab");

		 	// Fade the current and show the new
		 	$(currentTabId).fadeOut(300, function()
		 	{
		 		$("#tab_" + selector).fadeIn(300, function()
	 			{
	 				Character.tabsAreChanging = false;
	 			});
		 	});
	 	}
    },

    /**
	  * Slide to an attributes tab
	  * @param Int id
	  */
    attributes: function(id)
    {
        $("#attributes_wrapper").animate({ marginLeft: "-"+((parseInt(id) - 1) * 406)+"px" }, 500);
    }
};

/**************** TALENTS *************************/
$('.talents-spec').click(function()
{
	if ($(this).hasClass('talents-spec-active'))
		return false;
	
	var $tabId = $(this).attr('specId');
	
	if ($tabId.length == 0)
		return;
	
	//disable the currenly selected one
	$('.talents-spec').each(function(index, element)
	{
        if ($(element).hasClass('talents-spec-active'))
		{
			//the active is found
			var activeTabId = $(element).attr('specId');
			//remove the class
			$(element).removeClass('talents-spec-active');
			//hide the talents table and crap
			$('.talents[specId="'+activeTabId+'"]').hide();
		}
    });
	
	//enable the new tab
	$(this).addClass('talents-spec-active')
	$('.talents[specId="'+$tabId+'"]').show();
});

$(document).ready(function() {
    // Remove np from rels, using this to prevent super long preload of tooltips
    setTimeout(function() {
        $('[rel]').each(function(i, e) {
            if (/^np\b/.test($(this).attr("rel"))) {
                $(this).removeAttr('rel');
            }
        });
    }, 500);
})