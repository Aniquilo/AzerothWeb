// JavaScript Document

$(document).ready(function()
{
    //Bind Post Delete Buttons
	$('.post-delete-button').on('click', function()
	{
		$(this).WarcryAlertBox('open', '<p>Are you sure you want to delete this post?</p>',
		{
			0: { 
				text: 'Yes', onclick: function(event)
				{
					var PostId = parseInt($.fn.WarcryAlertBox('getCaller').attr('data-post-id'));
					
					$.get($BaseURL + '/forums/post/delete', 
					{ 
						id: PostId,
					},
					function(data)
					{
						if (data == 'OK')
						{
							$('#post-' + PostId).fadeOut('slow', function(){ $(this).detach(); });
						}
						else
						{
							$.fn.WarcryAlertBox('open', '<p>Error: '+data+'</p>');
						}
					});	
					
					//Close the box
					$.fn.WarcryAlertBox('close');
					
					return false;
				}
			},
			1: { text: 'No', onclick: 'close' }
		});
		
		return false;
	});
	
	//Bind Post Quote Buttons
	$('.post-quote-button').on('click', function()
	{
		var PostId = $(this).attr('data-post-id');
		
		//Pull info about the post
		$.get($BaseURL + '/forums/post/quoteInfo', 
		{ 
			id: PostId,
		},
		function(data)
		{
			//Check for error
			if (typeof data.error == 'undefined')
			{
				var PostText = data.text;
				var PostAuthor = data.author;
				var QuoteText = '[quote='+PostAuthor+']'+PostText+'[/quote]' + "\n";
				
				//Focus the text area
				$('#quick_reply_textarea').focus();
				//Append the text
				$('#quick_reply_textarea').html(QuoteText);
				//Update the advanced button href
				$('#go-advanced-post').attr('href', $('#go-advanced-post').attr('href') + '&quote=' + PostId);
			}
			else
			{
				$.fn.WarcryAlertBox('open', '<p>Error: '+data.error+'</p>');
			}
		});	

		return false;
	});
});

var TopicActions = {

    onSelectChange: function(element) {
        const topicId = $(element).attr('data-topic-id');
        const action = $(element).find('option:selected').val();

        if (typeof topicId == 'undefined' || topicId.length == 0 || typeof action == 'undefined' || action.length == 0) {
            return false;
        }

        if (action == 'edit')
        {
            window.location = $BaseURL + '/forums/topic/edit?id=' + topicId;
        }
        else if (action == 'lock')
        {
            $.get($BaseURL + '/forums/topic/lock', { 
                id: topicId,
            },
            function(resp) {
                if (resp.error == undefined) {
                    $.fn.WarcryAlertBox('open', '<p>The topic was successfully locked.</p>');
                    setTimeout(function() { window.location.reload(); }, 2000);
                } else {
                    $.fn.WarcryAlertBox('open', '<p>Error: '+resp.error+'</p>');
                }
                $(element).SelectTransform('quickSelect', { option: $(element).find('option[disabled]'), index: 0 });
            });
        }
        else if (action == 'unlock')
        {
            $.get($BaseURL + '/forums/topic/unlock', { 
                id: topicId,
            },
            function(resp) {
                if (resp.error == undefined) {
                    $.fn.WarcryAlertBox('open', '<p>The topic was successfully unlocked.</p>');
                    setTimeout(function() { window.location.reload(); }, 2000);
                } else {
                    $.fn.WarcryAlertBox('open', '<p>Error: '+resp.error+'</p>');
                }
                $(element).SelectTransform('quickSelect', { option: $(element).find('option[disabled]'), index: 0 });
            });
        }
        else if (action == 'delete')
        {
            $(this).WarcryAlertBox('open', '<p>Are you sure you want to delete this topic?</p>', {
                0: { 
                    text: 'Yes', onclick: function(event) {
                        $.get($BaseURL + '/forums/topic/delete', { 
                            id: topicId,
                        },
                        function(resp) {
                            if (resp.error == undefined) {
                                window.location = $BaseURL + '/forums';
                            } else {
                                $.fn.WarcryAlertBox('open', '<p>Error: '+resp.error+'</p>');
                            }
                            $(element).SelectTransform('quickSelect', { option: $(element).find('option[disabled]'), index: 0 });
                        });	
                        
                        //Close the box
                        $.fn.WarcryAlertBox('close');
                        return false;
                    }
                },
                1: { text: 'No', onclick: 'close' }
            });
        }
        else if (action == 'sticky')
        {
            $.get($BaseURL + '/forums/topic/set_sticky', { 
                id: topicId,
                state: 1
            },
            function(resp) {
                if (resp.error == undefined) {
                    $.fn.WarcryAlertBox('open', '<p>The topic was successfully marked as sticky.</p>');
                    setTimeout(function() { window.location.reload(); }, 2000);
                } else {
                    $.fn.WarcryAlertBox('open', '<p>Error: '+resp.error+'</p>');
                }
                $(element).SelectTransform('quickSelect', { option: $(element).find('option[disabled]'), index: 0 });
            });
        }
        else if (action == 'unsticky')
        {
            $.get($BaseURL + '/forums/topic/set_sticky', { 
                id: topicId,
                state: 0
            },
            function(resp) {
                if (resp.error == undefined) {
                    $.fn.WarcryAlertBox('open', '<p>The topic was successfully unmarked as sticky.</p>');
                    setTimeout(function() { window.location.reload(); }, 2000);
                } else {
                    $.fn.WarcryAlertBox('open', '<p>Error: '+resp.error+'</p>');
                }
                $(element).SelectTransform('quickSelect', { option: $(element).find('option[disabled]'), index: 0 });
            });
        }
        else if (action == 'move')
        {
            $.get($BaseURL + '/ajax/forum_moveinfo', function(resp) {
                if (resp.error == undefined) {
                    var catSelect = '<select name="moveTo" id="topic-move-select" class="topic-move-select">';
                    catSelect += '<option disabled selected>Select Destionation</option>';
                    for (var i = 0; i < resp.length; i++) {
                        if (resp[i].forums) {
                            for (var i2 = 0; i2 < resp[i].forums.length; i2++) {
                                catSelect += '<option value="'+resp[i].forums[i2].id+'">'+resp[i].name+' - '+resp[i].forums[i2].name+'</option>';
                            }
                        }
                    }
                    catSelect += '</select>';
                    $.fn.WarcryAlertBox('open', catSelect, { 
                        0: { 
                            text: 'Move', onclick: function(event) {
                                const moveTo = $('#topic-move-select').val();
                                $.post($BaseURL + '/forums/topic/move', { 
                                    id: topicId,
                                    moveTo: moveTo
                                },
                                function(resp) {
                                    if (resp.error == undefined) {
                                        $.fn.WarcryAlertBox('open', '<p>The topic was successfully moved.</p>');
                                        setTimeout(function() { window.location.reload(); }, 2000);
                                    } else {
                                        $.fn.WarcryAlertBox('open', '<p>Error: '+resp.error+'</p>');
                                    }
                                    $(element).SelectTransform('quickSelect', { option: $(element).find('option[disabled]'), index: 0 });
                                });
                                //Close the box
                                $.fn.WarcryAlertBox('close');
                                return false;
                            }
                        },
                        1: { text: 'Cancel', onclick: 'close' }
                    });
                    $('#topic-move-select').SelectTransform();
                } else {
                    $.fn.WarcryAlertBox('open', '<p>Error: '+resp.error+'</p>');
                }
            });
        }

        return false;
    }
};