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
                    <div class="page-title"><?=lang('get_gold_coins', 'buycoins')?></div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
            <div class="container_3 account-wide" style="padding: 35px 0 25px 0;">
            
                <div class="login-success">
                    <h1><?=lang('payment_complete', 'buycoins')?></h1>
                    <p style="padding:0 150px 20px 150px; text-align: center; font-size: 14px"><?=lang('payment_complete_info', 'buycoins')?></p>
                </div>
              
            </div>
            <!-- Buy Coins.End -->
    
        </div>
	</div>
 
</div>