var cookieconsent_options =
{
	message: "This website uses cookies to ensure you get the best experience on our website",
	dismiss: "Got it!",
	learnMore: "More info",
	link: $BaseURL + "/support/cookie_policy",
	delay: 5000,
};

function setCookie(cname, cvalue, exdays)
{
    var d = new Date();
    d.setTime(d.getTime() + (exdays*24*60*60*1000));
    var expires = "expires="+d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires;
}

function getCookie(cname)
{
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for(var i=0; i<ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1);
        if (c.indexOf(name) == 0) return c.substring(name.length, c.length);
    }
    return "";
}

$(document).ready(function(e)
{
	setTimeout(function()
	{
		var cc_agreed = getCookie("cc_agreed");
		
		if (cc_agreed != '1')
		{
			var html = $('<div class="cookie-consent" id="cookie-consent">' +
							'<div class="container cc_container clearfix">' +
								'<button class="simple_button cc_btn" id="cc_dismiss">' + cookieconsent_options.dismiss + '</button>' + 
								'<p class="cc_message">' + cookieconsent_options.message + ' <a class="cc_more_info" href="' + cookieconsent_options.link + '">' + cookieconsent_options.learnMore + '</a></p>' + 
							'</div>' +
						'</div>');
			
			$('#cc_dismiss', html).click(function(e)
			{
				$('#cookie-consent').remove();
				setCookie("cc_agreed", '1', 365);
			});
			
			$('body').append(html);
		}
	}, cookieconsent_options.delay);
});