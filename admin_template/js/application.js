$(document).ready(function()
{
  /**
   * Use search in real-time 
   **/
  
  $("#search").liveSearch({url: '/search?q='}); // Edit this url to match your search action
  // Nice animation on focus
  $("#search").focus(function() { $(this).animate({ width: '175px' }) });
  $("#search").blur(function() {
    if($(this).val() == "") { // Only go back to normal when nothing's filled in
      $(this).animate({ width: '100px' })
    }
  });
    
  /**
   * Placeholders in forms
   */
   
  $('input[type="text"]').placeholderFunction('input-focused');
  
  /**
   * Skin select boxes, checkboxes and radiobuttons
   */
   
  $('select').select_skin();
  $('input[type=checkbox], input[type=radio]').prettyCheckboxes();
  
  /**
   * Validate your forms
   */
   
  $("form").validate();
  
  /**
   * Gallery on hover
   */
   
  $(".gallery img").wrap("<div class=\"image\">");
  $(".gallery .image").append('<div class="overlay"></div><a href="#" class="button icon search">View</a>');
  $(".gallery .image").hover(function() {
    $(this).find("a").stop().animate({ opacity: 1}, 200);
    $(this).find(".overlay").stop().animate({ opacity: .5}, 200);
  }, function() {
    $(this).find("a").stop().animate({ opacity: 0}, 200);
    $(this).find(".overlay").stop().animate({ opacity: 0}, 200);
  });

    /** 
   * Dynamically create charts from tables
   * Just add class="linechart" and replace
   * 'line' with any type of chart.
   */
	   
	// This array contains the colors that will be used in charts
	var colors = ['#005ba8','#1175c9','#92d5ea','#ee8310','#8d10ee','#5a3b16','#26a4ed','#f45a90','#e9e744'];
	  
	if ($('.barchart').length > 0)
	{          
		$('.barchart').visualize({ type: 'bar', height: 300, colors: colors });
	}
	if ($('.linechart').length > 0)
	{          
	  	$('.linechart').visualize({ type: 'line', height: 300, lineWeight: 2, colors: colors });
	}
	if ($('.areachart').length > 0)
	{          
	  	$('.areachart').visualize({ type: 'area', height: 300, lineWeight: 1, colors: colors });
	}
	if ($('.piechart').length > 0)
	{          
		$('.piechart').visualize({ type: 'pie', height: 300, colors: colors });
	}
	$('.barchart, .linechart, .areachart, .piechart').hide();
});

// Custom Edit Modal Box
// Author: ChoMPi
(function($) {
    $.fn.editModal = function(action) {
        let target = $(this);
        if (action === "show") {
            target.find('button.remove').on('click', function() {
                target.fadeOut('fast');
            });
            target.fadeIn('fast');
            setTimeout(function() {
                let container = target.find('.edit-container');
                container.animate({ marginTop: '-' + (container.outerHeight() / 2) + 'px' }, 'fast');
            }, 100);
        }
        if (action === "hide") {
            target.fadeOut('fast');
        }
        return this;
    };
}(jQuery));