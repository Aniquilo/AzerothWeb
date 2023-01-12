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
			
            <div class="error-holder">
                <?php $ERRORS->PrintAny('purchase_boost'); ?>
            </div>
            
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Boosts</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
          
            <div class="page-desc-holder">
                Boost auras applied to your account and are active on all <br/>of your charcaters.
                Some of the auras does not apply when you are in Battleground, Arena,<br/> Dungeon or Instance.
            </div>
          	
            <!-- Boosts -->  
            <div class="container_3 account-wide">
                <div class="boosts_page">
                
                    <!-- Purchase Aura -->
                    <div class="purchase_boost">
                        
                        <div class="top_info">
                            Please select the boost you need, then select the period of time you want this aura to be active and then select the currency you want to pay with. 
                            You cant purchase boost that is already active on your account.
                        </div>
                        
                        <ul class="select_boost">
                            
                            <?php
							
							//Loop through our boosts
							foreach ($Boosts->data as $BoostId => $BoostData)
							{
								$isActive = false;
								foreach ($ActiveBoosts as $key => $bb)
								{
									if ((int)$bb['boosts'] == $BoostId)
									{
										$isActive = true;
										break;
									}
								}
								
								echo '
								<li ', ($isActive ? 'class="disabled"' : ''), '>
									<a href="#" data-boost-id="', $BoostId, '">
										<div class="icon" style="background-image:url(', $BoostData['icon'], ');"></div>
										<div class="info">
											<h2>', $BoostData['name'], '</h2>
											<h3>', $BoostData['description'], '</h3>
										</div>
										<p>This boost is already active!</p>
									</a>
								</li>';
							}
							?>
                            
                            <div class="clear"></div>
                        </ul>

                        <form method="post" action="<?php echo base_url(); ?>/boosts/purchase" id="boosts-complete-form">
                            <div class="select-currency select-period" id="select-duration">
                                <span>Select boost duration</span>
                                <label class="label_radio"><div></div><input type="radio" name="duration" value="<?php echo BOOST_DURATION_10; ?>" checked="checked" /><p class="dr"><b>10</b> Days</p></label>
                                <label class="label_radio"><div></div><input type="radio" name="duration" value="<?php echo BOOST_DURATION_15; ?>" /><p class="dr"><b>15</b> Days</p></label>
                                <label class="label_radio"><div></div><input type="radio" name="duration" value="<?php echo BOOST_DURATION_30; ?>" /><p class="dr"><b>30</b> Days</p></label>
                            </div>

                            <div class="select-currency select-currency-correct" id="select-currency">
                                <span>Currency</span>
                                <label class="label_radio">
                                	<div></div>
                                    <input type="radio" name="currency" value="<?php echo CURRENCY_SILVER; ?>" data-price-value="<?php echo $pricing[BOOST_DURATION_10][CURRENCY_SILVER]; ?>" />
                                    <p id="sc"><b id="price"><?php echo $pricing[BOOST_DURATION_10][CURRENCY_SILVER]; ?></b> Silver Coins</p>
                                </label>
                                <label class="label_radio">
                                	<div></div>
                                    <input type="radio" name="currency" value="<?php echo CURRENCY_GOLD; ?>" checked="checked" data-price-value="<?php echo $pricing[BOOST_DURATION_10][CURRENCY_GOLD]; ?>" />
                                    <p id="gc"><b id="price"><?php echo $pricing[BOOST_DURATION_10][CURRENCY_GOLD]; ?></b> Gold Coins</p>
                                </label>
                            </div>
                        
                            <input type="hidden" name="boost" value="0" id="selected-boost-id" />
                            <input type="submit" value="Purchase" class="purchase_btn" />
                        </form>

                        <div class="clear"></div>
                        
                    </div>
                    <!-- Purchase Aura.End -->
                        
                    <div class="active_boosts">
                        <h1>Active boosts</h1>
                        <ul class="active_boosts">
                        	<?php
							//Loop through the active boosts
							foreach ($ActiveBoosts as $key => $BoostRecord)
							{
								//Get the boost details
								$BoostDetails = $Boosts->get((int)$BoostRecord['boosts']);
								//Get the time left in single measure
								$timeLeft = $CORE->singleMeasureTimeLeft((int)$BoostRecord['unsetdate']);
								
								echo '
								<li>
									<div class="icon" style="background-image:url(', $BoostDetails['icon'], ');"></div>
									<p>', $timeLeft, ' left</p>
								</li>';
								
								unset($timeLeft, $BoostDetails);
							}
							unset($key, $BoostRecord, $ActiveBoosts);
							?>
                        </ul>
                    </div>
                    <div class="clear"></div>
                                 
                </div>
            </div>
            <!-- Boosts.End -->
    
        </div>
	</div>

</div>