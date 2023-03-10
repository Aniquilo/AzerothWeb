<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//print error and success messages
$ERRORS->PrintAny(array('pcode_delete', 'pcode_add'));
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="<?=base_url()?>/admin/pcodes">Promotion Codes</a></li>
	</ul>
</nav>
  
<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
    	
        <div class="column left twothird">
        	<h2>Promotion Codes Management</h2>
        
            <table id="datatable" class="datatable">
          
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Code</th>
                        <th>Usage</th>
                        <th>Reward</th>
                        <th>Added</th>
                        <th>X</th>
                    </tr>
                </thead>
                <tbody id="sortable">
            
                </tbody>
            
            </table>
        
        </div>
        
        <div class="column right third">
        	<h2>Add new Promo Code</h2>
            
            <form action="<?=base_url()?>/admin/pcodes/submit" method="post" id="add_pcode_form">
	            <section>
	              <label>
                  	Format*
                  </label>
	              <div>
	                <input type="text" placeholder="Required" class="required" name="format" value="XXXX-XXXX-XXXX" />
	              </div>
	            </section>
	            
                <section>
	              <label>
                  	Usage
	              	<small>The usage determines how the code expires.</small>
                  </label>
	              <div>
	                <select name="usage">
                    	<option value="0">Unique</option>
                        <option value="1">Per Account</option>
                    </select>
	              </div>
                  <div class="clear"></div>
	            </section>
                
  				<section>
	              <label>
                  	Reward Type*
                  </label>
	              <div>
	                <select name="reward_type">
                   		<option value="0">Please select</option>
                        <option value="1">Silver Coins</option>
                        <option value="2">Gold Coins</option>
                        <option value="3">Item</option>
                    </select>
	              </div>
	            </section>
                              
                <section>
	              <label>
                  	Reward Value*
	              	<small>Use item entry in case of Item type reward.</small>
                  </label>
	              <div>
	                <input type="text" placeholder="Required" class="required small" name="reward_value" />
	              </div>
	            </section>
                
	            <section>
	              <input type="submit" class="button primary big" value="Submit" />

	            </section>
			</form>
            
        </div>
        
        <div class="clear"></div>
    </div>

<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function(e)
{
	$('#datatable').dataTable(
	{
		"bProcessing": true,
		"bServerSide": true,
		"sAjaxSource": $BaseURL + "/admin/datatable/promo_codes",
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 5 ] }
		],
		"fnRowCallback": function(nRow, aData, iDisplayIndex, iDisplayIndexFull)
		{
            var rewardCont = $(nRow).find('td:nth-child(4)');
            
			//Check for item reward type
			var reward = rewardCont.html().toString();
			var entry = false;
			
			if (reward.indexOf('Item:') > -1)
			{
                entry = parseInt(reward.substr(6));
                
				//set the item class
				rewardCont.addClass('pretty-items');
			}
			
			//If we have an item get some data
			if (entry)
			{
				$.get('<?=base_url()?>/ajax/getItem',
				{
					entry: entry
				},
				function(data)
				{
                    var quality = '0';
                    var icon = 'inv_misc_questionmark';
                    
                    if (typeof data.quality_str != 'undefined') {
                        quality = data.quality_str;
                    }
                    if (typeof data.icon != 'undefined') {
                        icon = data.icon;
                    }

					rewardCont.html(
						'<a href="'+$WOWDBURL+'/?item='+entry+'" data-realm="<?=$CORE->realms->getFirstRealm()->getId()?>"' + 
						' rel="item='+entry+'" onclick="return false;"' + 
                        ' class="'+quality.toLowerCase()+'" style="background: url(\'http://wow.zamimg.com/images/wow/icons/large/'+icon.toLowerCase()+'.jpg\');"></a>');
                        
                    Tooltip.refresh();
				});
			}
			
			//This has to be returned
			return nRow;
		}
	});
});
</script>