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
      
            <!-- Buy Coins -->
        	<div class="page-desc-holder">
            	<?=lang('select_payment_method', 'buycoins')?>
            </div>
            
            <div class="container_3 account-wide">
            
                <div class="buy-coins">
                    
                    <ul class="payment_methods">
                        <?php if ($paypal_enabled) { ?>
                        <li id="paypal"><a href="<?php echo base_url(); ?>/buycoins/paypal"><img src="<?=base_url()?>/template/style/images/misc/paypal.png" /></a></li>
                        <?php } ?>

                        <?php if ($stripe_enabled) { ?>
                        <li id="stripe"><a href="<?php echo base_url(); ?>/buycoins/stripe"><img src="<?=base_url()?>/template/style/images/misc/stripe.png" /></a></li>
                        <?php } ?>

                        <?php if ($paymentwall_enabled) { ?>
                        <li id="paymentwall"><a href="<?php echo base_url(); ?>/buycoins/paymentwall"> <img src="<?=base_url()?>/template/style/images/misc/paymentwall.png" /></a></li>
                        <?php } ?>
                    </ul>
                    
                    <p><?=lang('please_read_info', 'buycoins')?></p>
                            
                </div>
              
            </div>
            <!-- Buy Coins.End -->
    
        </div>
	</div>
 
</div>