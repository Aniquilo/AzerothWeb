<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$RealmId = $CORE->user->GetRealmId();
?>

<div class="content_holder">

  	<div class="container_2 account">
     	<div class="cont-image">
        	
            <div class="error-holder">
                <?php $ERRORS->PrintAny('pcode'); ?>
            </div>
        
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Promotion Codes</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>    
      
      		<!-- FACTION CHANGE -->
      		<div class="faction-change">
      		
                <div class="page-desc-holder">
                    Each promo code is unique and can be used<br/> just one time per account.
                    You may find promo codes on our social <br/>
                    network pages or in the forums.
                </div>
            
                <div class="container_3 account-wide">
                  	<div class="promotion_codes">
                    	<div class="pcode-top-cont">
                            <form id="promo-code-form" method="post" action="<?php echo base_url(); ?>/promo_code/redeem" onsubmit="return false;">
                                <h1 id="enter" style="display: none;">Press ENTER to redeem your reward!</h1>
                                <h1 id="invalid" style="display: none;">Invalid or expired code !</h1>
                                <input type="text" id="code" name="code" placeholder="Enter the Code here" style="text-align: center;" />
                                <input type="hidden" name="character" id="real-char-select" />
                                <p>Promo codes consist of 12 characters (6 digits and 6 letters).</p>
                            </form>
                        
                            <!-- ITEMS -->
                            <div class="reward_container" style="display:none;">
                                <div class="arrow"></div>
                                <!---->
                                
                                <div class="reward_loading">
                                    <p>Loading...</p>
                                </div>
                                
                                <!-- COINS Reward SILVER -->
                                <div class="coins_reward" id="reward-type-silver" style="display: none;">
                                    <h1>The reward is</h1>
                                    <h2><span id="value" style="font-weight:bold;">25</span> Silver Coins</h2>
                                </div>
                                <!-- COINS Reward . End -->
                                
                                <!-- COINS Reward GOLD -->
                                <div class="coins_reward gold" id="reward-type-gold" style="display: none;">
                                    <h1>The reward is</h1>
                                    <h2><span id="value" style="font-weight:bold;">5</span> Gold Coins</h2>
                                </div>
                                <!-- COINS Reward . End -->
                                
                                <!-- Item Reward -->
                                <div class="item_reward clearfix" id="reward-type-item" style="display: none;">
                                    <h1>The reward is</h1>
                                    <div class="item_">
                                        <a href="javascript: void(0)" class="ico" data-realm="<?=$RealmId?>" style="background-image:url('http://wow.zamimg.com/images/wow/icons/large/inv_misc_questionmark.jpg');"></a>
                                        <div id="item_info">
                                            <h2 id="subclass" style="width:70%;">None</h2>
                                            <h3 id="name"><a href="javascript: void(0)" data-realm="<?=$RealmId?>">None</a></h3>
                                        </div>
                                    </div>
                                </div>
                               <!-- Item Reward . End -->
                           
                            <!-- -->
                            </div>
                            <!-- ITEMS . End -->
                            
                            <div class="clear"></div>
                        </div>
                    	
                        <div class="pcode-chat-select-cont" style="display: none;">
							<?php
                            $realm = $CORE->realms->getRealm($RealmId);

                            //check db connection
                            if ($realm->checkCharactersConnection())
                            {
                                if ($characters = $realm->getCharacters()->getAccountCharacters())
                                {
                                    $selectOptions = '';
                                    
                                    //loop the characters
                                    foreach ($characters as $arr)
                                    {
                                        $ClassSimple = str_replace(' ', '', strtolower($CORE->realms->getClassString($arr['class'])));
                                        
                                        echo '
                                        <!-- Charcater ', $arr['guid'], ' -->
                                        <div id="character-option-', $arr['guid'], '" style="display:none;">
                                            <div class="character-holder">
                                                <div class="s-class-icon ', $ClassSimple, '" style="background-image:url(\'http://wow.zamimg.com/images/wow/icons/medium/class_', $ClassSimple, '.jpg\');"></div>
                                                <p>', $arr['name'], '</p><span>Level ', $arr['level'], ' ', $CORE->realms->getRaceString($arr['race']), ' ', ($arr['gender'] == 0 ? 'Male' : 'Female'), '</span>
                                            </div>
                                        </div>
                                        ';
                                        
                                        $selectOptions .= '<option value="'. $arr['name'] .'" getHtmlFrom="#character-option-'. $arr['guid'] .'"></option>';
                                        
                                        unset($ClassSimple);
                                    }
                                    unset($arr);
                                    
                                    echo '
                                    <div id="select-charcater-selected" style="display:none;">
                                        <p class="select-charcater-selected">Select character</p>
                                    </div>
                                    <div style="display:inline-block; margin: 0 10px 0 4px;">
                                    <select id="character-select" class="character-select" data-stylized="true">
                                        <option selected="selected" disabled="disabled" getHtmlFrom="#select-charcater-selected"></option>
                                        ', $selectOptions, '
                                    </select>
                                    </div>';
                                    unset($selectOptions);
                                }
                                else
                                {
                                    echo '<p class="there-are-no-chars">There are no characters.</p>';
                                }
                                unset($characters);
                            }
                            else
                            {
                                echo '<p class="there-are-no-chars">Error: Failed to load your characters.</p>';
                            }
                            ?>
                        </div>
                        
                  	</div>
                </div>
                
            </div>
            <!-- VOTE.End -->
   
     	</div>
	</div>
 
</div>