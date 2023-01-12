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
                    <div class="page-title">Coins activity</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
        
            <!-- Coins Activity -->
            <div class="coins-activity">
            
                <div class="page-desc-holder"></div>
                
                <div class="container_3 account-wide" align="center">
                
                    <ul class="activity-list">

                        <?php
                        if ($results)
                        {
                            
                            foreach ($results as $arr)
                            {
                                echo '
                                <li>
                                    <p id="r-title2">', $arr['sourceType'], $arr['exchangeType'], '<b>', $arr['amount'], ' ', $arr['coinType'], '</b></p>
                                    <p id="r-date2">', $arr['time'], '</p>
                                    <p id="r-info2">', $arr['source'], '</p>
                                </li>';
                            }
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
            <!-- Coins Activity.End -->
    
        </div>
	</div>
 
</div>