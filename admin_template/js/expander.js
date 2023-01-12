
function ToggleExpand(btn)
{
    var parent = $(btn).parent().parent();
    var content = parent.find('#content');

    if (typeof parent.attr('expanded') == 'undefined' || parent.attr('expanded').length == 0 || parent.attr('expanded') == 'false')
    {
        content.css('height', 'auto');
        var height = content.height();
        
        content.css('height', '0px');
        content.show();
        content.stop(true, true).animate({ height: height }, 'fast');
        
        parent.parent().parent().addClass('active-expander');
        parent.attr('expanded', 'true');
        $(btn).html('Close');
    }
    else
    {
        content.stop(true, true).animate({ height: 0 }, 'fast', function()
        {
            content.hide();
            parent.parent().parent().removeClass('active-expander');
            $(btn).html('Open');
        });
        parent.attr('expanded', 'false');
    }
    
    return false;
}