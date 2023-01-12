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
                    <div class="page-title">Store activity</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
            <!-- Store Activity -->
      	    <div class="store-activity">
        
                <div class="page-desc-holder">
                    All the items you have bought from our Store will be shown at this page.
                </div>
            
                <div class="container_3 account-wide" align="center">

            	    <ul class="activity-list">
                  
                        <?php
                        if ($results)
                        {
                            //loop the records
                            foreach ($results as $arr)
                            {
                                echo '
                                <li>
                                    <p id="r-item"><a class="q', $arr['item']['Quality'], '" href="', item_url($arr['item']['entry'], $arr['realmId']), '" target="_newtab" data-realm="', $arr['realmId'], '" rel="item=', $arr['item']['entry'], '">[', $arr['item']['name'], ']</a></p>
                                    <p id="r-date">', $arr['time'], '</p>
                                    <p id="r-info">', $arr['money'], '</p>
                                </li>';
                            }
                            unset($arr);
                        }
                        else
                        {
                            echo '<p class="there-is-nothing">There are no items.</p>';
                        }
				        ?>  
				                 
                    </ul>
                          
                </div>
            
                <?php
                if ($pages)
                {				
                    echo '
                    <!-- Pagination -->
                    <div class="pagination-holder">
                        <ul class="pagination">
                            ', $pages['first'], '
                            ', $pages['previous'], '
                            ', $pages['info'], '
                            ', $pages['next'], '
                            ', $pages['last'], '   
                        </ul>
                        <div class="clear"></div>
                    </div>';
                }
                ?>
            
      	    </div>
            <!-- Store Activity.End -->
    
        </div>
	</div>
 
</div>