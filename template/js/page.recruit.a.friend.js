
function UpdatePedingRAFLinks()
{
	$.get($BaseURL + "/recruit/update_links",
	function(data)
	{
		if (typeof data.error == 'undefined')
		{
			// refresh the page
			document.location.reload(true);
		}
		else
		{
			//prompt the error
			$.fn.WarcryAlertBox('open', '<p>' + data.error + '</p>');
		}
	}, 'json');
			
	return false;
}