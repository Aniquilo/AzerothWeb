<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">
 
  	<div class="container_2 account">
        <div class="cont-image">

            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title inactive"><p>Get Gold Coins</p></div>
                    <div class="sub-active-page">Paymentwall</div>
                    <a href="<?php echo base_url(), '/buycoins'; ?>">Back</a>
                </div>
            </div>
      
            <!-- Purchase Gold Coins -->
            <div class="faction-change">
                
                <div class="page-desc-holder"></div>
                
                <div class="container_3 account-wide" style="min-height:377px;">
                    <iframe style="min-height:377px;" src="http://wallapi.com/api/ps/?key=<?=$paymentwallConfig['secret_key']?>&uid=<?=$CORE->user->get('id')?>&widget=p1_2" width="843" frameborder="0"></iframe>
                </div>
                
            </div>
            <!-- Purchase Gold Coins.End -->
       
        </div>
	</div>
 
</div>