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
                    <div class="page-title inactive"><p><?=lang('get_gold_coins', 'buycoins')?></p></div>
                    <div class="sub-active-page">PayPal</div>
                    <a href="<?=base_url()?>/buycoins"><?=lang('back')?></a>
                </div>
            </div>
            
            <br/><br/>
            
            <!-- Buy Coins -->
            <div class="container_3 account-wide">
            
                <div class="buy-coins clearfix">
            
                    <div class="container-left payment-method">
                        <img src="<?=base_url()?>/template/style/images/misc/paypal.png" width="183" height="57" />
                    </div>
                
                    <!------------------------------------------------------------------->
                    <!-- PAYMENT FORMS -------------------------------------------------->
                
                    <form action="<?=$paypalConfig['url']?>" method="post" target="paypal" id="paypal-form">     
                        <input type="hidden" name="cmd" value="_xclick" />
                        <input type="hidden" name="business" value="<?=$paypalConfig['email']?>" />
                        <input type="hidden" name="item_name" value="<?=$coins_name?>" />
                        <input type="hidden" name="item_number" id="paypal-product-id" value="<?=$CORE->user->get('id')?>WCC10" />
                        <input type='hidden' name='no_shipping' value='1' />
                        <input type="hidden" name="amount" value="1.00" />      
                        <input type="hidden" name="quantity" value="10" />
                        <input type="hidden" name="currency_code" value="<?=$paypalConfig['currecy']?>" />
                        <input type="hidden" name="notify_url" value="<?=base_url()?>/ipn/paypal" />
                        <input type='hidden' name="cancel_return" value="<?=base_url()?>/buycoins" />
	                    <input type='hidden' name="return" value="<?=base_url()?>/buycoins/success" />
                        <input type="hidden" name="custom" value="<?=$CORE->user->get('identity')?>" />
                    </form>
                      
                    <!------------------------------------------------------------------->
                    <!------------------------------------------------------------------->
                
                    <div class="container-right">
                        <!-- If paypal or where can be selected any number -->
                        <div class="coins-number">
                            <div class="coins-bonus-pane" style="display: none">
                                +<span id="bonus_coins_amount">1</span> <?=strtolower(lang('gold_coins'))?> (<span id="bonus_percent_amount">10</span>% bonus)
                            </div>
                            <ul>
                                <li id="onemore-a"><a href="javascript: void(0);" id="payment-increase-coins"></a></li>
                                <li><input id="selected-coins-input" type="text" value="10"/></li>
                                <li id="oneless-a"><a href="javascript: void(0);" id="payment-decrease-coins"></a></li>
                            </ul>
                        </div>
                    
                        <div class="purchase">
                        
                            <div class="coin-money-price">
                                <p><?=lang('you_will_be_charged_via', 'buycoins')?></p> 
                                <div><?=$paypalConfig['currecySymbol']?><span id="payment-infoPane-price">10</span></div>
                            </div>
                            <p><?=lang('you_will_be_charged_in', 'buycoins')?> <?=$paypalConfig['currecy']?>. <?=lang('remember_refunds', 'buycoins')?></p>
                        
                            <input type="submit" value="<?=lang('purchase', 'buycoins')?>" onclick="return submitPaymentForm();" />
                        
                        </div>
                        <!-- If paypal or where can be selected any number -->
                    </div>
              
                </div>
              
            </div>
            <!-- Buy Coins.End -->
    
        </div>
	</div>
 
</div>

<!-- Update the bonus table -->
<script type="text/javascript">
    function Initialize()
    {
        if (typeof $BonusTable != "undefined")
        {
            $BonusTable = <?php echo json_encode($bonuses); ?>;
        }
        else
        {
            setTimeout(Initialize, 100);
        }
    }

    $(document).ready(function()
    {
        Initialize();
    });
</script>