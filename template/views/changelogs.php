<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

  	<div class="container_2 changelogs">
    	
        <div class="changelogs-cats clearfix">
            <?php foreach ($repos as $key => $repo) { ?>
                <a href="<?=base_url()?>/changelogs?repo=<?=$key?>" <?=($repoId == $key ? 'class="active"' : '')?> >
                    <?=$repo['title']?>
                    <p><?=$repo['description']?></p>
                </a>
            <?php } ?>
        </div>
    
        <div class="container_3">
            
            <!-- Changelogs -->
            <table class="changes-list" id="changes-list">

                <?php if ($commits) { ?>

                    <?php foreach ($commits as $commit) { ?>
                        <tr>
                            <td class="rev"><?=$commit['rev']?></td>
                            <td class="by"><?=$commit['author']?></td>
                            <td class="date"><?=$commit['time']?></td>
                            <td class="info"><?=$commit['text']?></td>
                        </tr>
                    <?php } ?>

                <?php } else { ?>
                    <tr><td style="padding: 10px;"><strong>There are no recent changes.</strong></td></tr>
                <?php } ?>
                
            </table>

        </div>
        
        <?php if ($loadMore) { ?>
            <div class="load-more"><a href="javascript:void(0)" id="load-more">Load more</a></div>
        <?php } ?>
    </div>
    
</div>

<?php if ($loadMore) { ?>
<script type="text/javascript">
    var CurPage = 1;
    var RepoId = '<?=$repoId?>';
    var PerPage = parseInt(<?=$perPage?>);
    var loading = false;

    $(document).ready(function() {
        $("#load-more").on("click", function() {
            if (loading)
                return false;
            
            //set as loading
            loading = true;

            //update the curpage
            CurPage = CurPage + 1;
            
            //pull the data
            $.ajax({
                type: "GET",
                url: $BaseURL + "/changelogs/get_changesets?page="+CurPage+"&repo="+RepoId,
                dataType: "json",
                success: function(response) {
                    if (typeof response.error != 'undefined') {
                        $.fn.WarcryAlertBox('open', '<p>Error: '+response.error+'</p>');
                    } else {
                        // If we have commits
                        if (response && response.length > 0) {
                            //create our separator
                            var element = $('<tr><td colspan="4" class="separator" style="width: 840px; text-align: center;"><strong>Additional Data</strong></td></tr>');

                            //append our separator
                            $('#changes-list').append(element);

                            // Loop through the commits
                            $.each(response, function(i, commit) {
                                //create our changeset
                                var commitElement = $('<tr style="display:none;"><td class="rev">'+commit.rev+'</td><td class="by">'+commit.author+'</td><td class="date">'+commit.time+'</td><td class="info">'+commit.text+'</td></tr>');

                                //append our changeset
                                $('#changes-list').append(commitElement);

                                // Fade in
                                commitElement.fadeIn('slow');
                            });

                            // If we have reached the last page
                            if (response.length < PerPage) {
                                $('.load-more').fadeOut('fast', function() { $(this).remove(); });
                            }
                        } else {
                            $('.load-more').fadeOut('fast', function() { $(this).remove(); });
                        }
                    }

                    // No longer loading
                    loading = false;
                }
            });
            
            return false;
        });
    });
</script>
<?php } ?>