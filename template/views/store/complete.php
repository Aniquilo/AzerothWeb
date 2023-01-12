<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

  	<div class="container_2 account" align="center">
        <div class="cont-image">
    
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Store</div>
                    <a href="<?php echo base_url(), '/store'; ?>">Back to store</a>
                </div>
            </div>
      
            <!-- Store Complete purchase -->
            <div class="store-complete">
            
                <div class="container_3 account-wide" align="center">
            
                    <div class="top-info">
                        <p>Purchase Complete</p>
                        <span>The items below ware sent to <b><?php echo $_SESSION['StoreItemReturnChar']; ?></b>.</span>
                    </div>
                
                    <!-- Item List -->
	                <ul class="items-list">

                        <?php			
						foreach ($_SESSION['StoreItemReturn'] as $id => $data)
						{
							if (isset($items[(int)$data['id']]))
							{
								$row = $items[(int)$data['id']];
								
								if ($data['error'] == '')
								{
									//successfully sent item
									echo '
	                        		<li>
		                    			<p title="Item successfully sent." class="status success-i"><em></em></p>
		                        		<p class="item-info">
	                            			<a href="', item_url($row['entry'], $RealmId), '" data-realm="', $RealmId, '" target="_newtab" rel="item=', $row['entry'], '" class="item-ico q', $row['Quality'], '" ', item_icon_style($row['entry'], $RealmId), '></a>
	                                		<b class="q', $row['Quality'], '">', $row['name'], '</b>
	                            		</p>
		                    		</li>';
								}
								else
								{
									//failed item
									echo '
	                        		<li>
		                    			<p title="', $data['error'],'" class="status fail-i"><em></em></p>
		                        		<p class="item-info">
	                            			<a href="', item_url($row['entry'], $RealmId), '" data-realm="', $RealmId, '" target="_newtab" rel="item=', $row['entry'], '" class="item-ico q', $row['Quality'], '" ', item_icon_style($row['entry'], $RealmId), '></a>
	                                		<b class="q', $row['Quality'], '">', $row['name'], '</b>
	                            		</p>
		                    		</li>';
								}
								unset($row);
							}
							else
							{
								echo '
                        		<li>
	                    			<p title="The item does not exist in the store." class="status fail-i"><em></em></p>
	                        		<p class="item-info">
                            			<a href="#" class="item-ico poor" style="background-image:url(\'http://wow.zamimg.com/images/wow/icons/large/inv_misc_questionmark.jpg\');"></a>
                                		<b>Unknown</b>
                            		</p>
	                    		</li>';
							}
						}
						
						//unset the sessions
						unset($_SESSION['StoreItemReturn']);
						unset($_SESSION['StoreItemReturnChar']);
						?>
                                                
	                </ul>
                    <!-- Item List -->
                
                	<div class="description">
                    Items marked with red cross have not been sent. <br/>
                    To understand why move your mouse over the red cross, <br/>the reason should be displayed as tooltip.
                    </div>
             
	            </div>
            
      	    </div>
        
        </div>
	</div>
 
</div>