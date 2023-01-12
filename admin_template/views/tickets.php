<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<!-- Secondary navigation -->
<nav id="secondary">
	<ul>
		<li class="current"><a href="#maintab" onclick="changeCurrentTab('#maintab');">Browse</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
        <h2>GM Tickets</h2>
    	
        <br />
        <div>
            <?php
                $realmsConfig = $CORE->getRealmsConfig();

				//Realm selection
				if (isset($realmsConfig) && count($realmsConfig) > 1)
				{
					echo '
					<div style="width: 200px; display: inline-block; vertical-align: middle; margin-right: 10px;">
						<select name="realm" id="realm-select">';
						
						foreach ($realmsConfig as $id => $realmData)
						{
							echo '<option value="', $id, '" ', ($id == $RealmID ? 'selected="selected"' : ''), '>', $realmData['name'], '</option>';
						}
						
						echo '
						</select>
					</div>';
                }
                unset($realmsConfig);
				
				//Closed inclusion
           		echo ($iclosed == 1) ? 
				'<a href="'.base_url().'/admin/tickets?iclosed=0">Exclude Closed</a>' : 
				'<a href="'.base_url().'/admin/tickets?iclosed=1">Include Closed</a>';
			?>
            
            <script>
				$(function()
				{
					$('#realm-select').on('change', function()
					{
						window.location = '<?=base_url()?>/admin/tickets?realm=' + $(this).find('option:selected').val();
					});
				});
			</script>
            
        </div>
        <br /><br />
        
        <table class="datatable">
		  
		    <thead>
		      <tr>
		        <th>ID</th>
		        <th>Text</th>
                <th>Ticket By</th>
                <th>Status</th>
		        <th>Comment</th>
                <th>Views</th>
		      </tr>
		    </thead>
		  	
		    <tbody>
		
            <?php
			if ($tickets)
			{
				foreach ($tickets as $arr)
				{
					echo '
				    <tr valign="top">
				    	<td style="vertical-align: top !important;">', $arr['ticketId'], '</td>
				        <td style="vertical-align: top !important; padding: 0 !important;">
						  	<div class="datatable-expander" style="height: 16px; position: relative;">
						  		<p style="max-width: 700px;"><strong>', $arr['message'], '</strong></p>
								<span style="position: absolute; top: 1px; right: 0px;">
									<a href="#" onclick="return Toggle(this);">Open</a>
								</span>
						  	</div>
						</td>
						<td style="vertical-align: top !important;">', $arr['name'], '</td>
						<td style="vertical-align: top !important;">', $arr['status'], '</td>
				        <td style="vertical-align: top !important;">', $arr['comment'], '</td>
						<td style="vertical-align: top !important;">', $arr['viewed'], '</td>
				   	</tr>';
				}
			}
			?>
		    
		    </tbody>
		    
        </table>

    </div>
</section>

<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function(e)
    {
        //Init datatables
        if ($(".datatable").length > 0)
        {
            var newsTable = $(".datatable").dataTable({
                "aoColumnDefs": [ 
                    { "bSortable": false, "aTargets": [ 1 ] }
                ],
            });
            //sort the table
            newsTable.fnSort( [ [0, 'desc'] ] );
        }
    });

    function Toggle(btn)
    {
        var parent = $(btn).parent().parent();
            
        if (typeof parent.attr('expanded') == 'undefined' || parent.attr('expanded').length == 0 || parent.attr('expanded') == 'false')
        {
            var height = parent.children('p').height();
            var thisHeight = parent.height();
                                    
            parent.stop(true, true).animate({ height: height }, 'fast');
            parent.parent().parent().addClass('active-expander');
            parent.attr('expanded', 'true');
            parent.attr('oldHeight', thisHeight);
            $(btn).html('Close');
        }
        else
        {
            parent.stop(true, true).animate({ height: parseInt(parent.attr('oldHeight')) }, 'fast', function()
            {
                parent.parent().parent().removeClass('active-expander');
                $(btn).html('Open');
            });
            parent.attr('expanded', 'false');
        }
        
        return false;
    }
</script>