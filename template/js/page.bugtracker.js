var Bugtracker = {
    CurPage: 0,
    TotalPulled: 0,
    TotalReports: 0,
    PerPage: 25,
    ListOpened: false,

    Initialize: function() {
        //bind the starting event
        $('#see-all-reports').bind('click', function()
        {
            if (!Bugtracker.ListOpened)
            {
                $('#see-all-reports').html('Hide All');

                //fade in the container
                $('.all-reports-by-me').fadeIn('fast');

                //load the page
                if (Bugtracker.CurPage == 0)
                {
                    Bugtracker.LoadPage(1);
                }

                //define
                Bugtracker.ListOpened = true;
            }
            else
            {
                $('#see-all-reports').html('See All');

                //fade out the container
                $('.all-reports-by-me').fadeOut('fast');
                
                //define
                Bugtracker.ListOpened = false;
            }
            
            return false;
        });
    },

    LoadPage: function(page)
    {
        Bugtracker.CurPage = page;
        
        //pull the data
        $.ajax({
            type: "GET",
            url: $BaseURL + "/bugtracker/get_reports?page="+Bugtracker.CurPage+"&perpage="+Bugtracker.PerPage,
            dataType: 'json',
            cache: false,
            error: function(jqXHR, textStatus, errorThrown)
            {
                $('#report-container').append('<li style="text-align: center;" id="loading"><a class="closed"><p class="title" style="width: auto;">An error occured!</p></a></li>');
                console.log(textStatus);
            },
            success: function(data)
            {
                //get the count
                var count = parseInt(data.count);

                //update the total pulled
                Bugtracker.TotalPulled = Bugtracker.TotalPulled + count;
                
                //loop them issues
                $.each(data.issues, function(key, value)
                {
                    //append the new issue
                    var newIssue = $(
                    '<li class="fadein" style="display: none;">'+
                        '<a class="'+value.approval+'" href="javascript:void(0)">'+
                            '<p class="title"><span class="approval">'+value.approval+'</span>'+value.title+'</p>'+
                            '<p class="main-cat">'+value.maincategory+'</p>'+
                            '<p class="sub-cat">'+value.category+'</p>'+
                            '<p class="prio">'+value.priority+' Priority</p>'+
                            '<p class="status">'+value.status+'</p>'+
                        '</a>'+
                    '</li>');

                    //append
                    $('#report-container').append(newIssue);
                });

                // Move the load more to the bottom
                if ($('#report-container').find('#load-more').length > 0) {
                    $('#report-container').append($('#report-container #load-more'));
                }

                //check if we have to remove the load more button
                if (Bugtracker.TotalPulled >= Bugtracker.TotalReports) {
                    //check if we have the load more button
                    if ($('#report-container').find('#load-more').length > 0) {
                        $('#report-container').find('#load-more').fadeOut('fast', function() {
                            $('#report-container').find('#load-more').detach();
                        });
                    }
                } else {
                    //check if we already have the load more button
                    if ($('#report-container').find('#load-more').length == 0) {
                        //add load more button
                        var loadmore = $('<li style="display: none;" id="load-more"><a href="javascript:void(0)"><p>Load More</p></a></li>');
                        $('#report-container').append(loadmore);

                        //bind the click event
                        $(loadmore).bind('click', function()
                        {
                            Bugtracker.LoadPage(Bugtracker.CurPage + 1);
                            return false;
                        });

                        loadmore.fadeIn('fast');
                    }
                }

                // Fade in the new reports
                $('#report-container .fadein').fadeIn('fast').removeClass('fadein');
            }
        });
    }
};