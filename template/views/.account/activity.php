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
                    <div class="page-title">Account activity</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
            <!-- Account Activity -->
      	    <div class="account-activity">   
      		
       		    <div class="page-desc-holder"></div>       
            
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
                                    <p id="r-title">', $arr['description'], '</p>
                                    <p id="r-info"></p>
                                    <p id="ar-date">', $arr['time'], '</p>
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
            <!-- Account Activity.End -->
    
        </div>
	</div>
 
</div>