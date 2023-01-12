<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//load the characters handling class
$CORE->loadLibrary('item.refund.system');

//assume the realm is 1 (for now)
$RealmId = $CORE->user->GetRealmId();
$realm = $CORE->realms->getRealm($RealmId);
?>

<div class="content_holder">

  	<div class="container_2 account">
     	<div class="cont-image">
   			
            <div class="error-holder">
                <?php $ERRORS->PrintAny('refund_item'); ?>
            </div>
            
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Item Refund</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
      		<!-- VOTE -->
      		<div class="vote-page">
      		
                <div class="page-desc-holder">
                    Refunding an item purchased from our store gives you back the full amount you paid.
                    <br/><br/>
                    The system requires your character to be online and the item must be in your character's bags.<br/>
                    You are allowed to use the Refund System 2 times a week.
                </div>
            
				<?php
                //Array storage for character data (less queries)
                $characterData = array();
                
                //Get the refundables
                $res = ItemRefundSystem::GetRefundables();
                ?>
            	
                <div class="container_3 account-wide">
                    <div class="items">
                    
                    <?php
					if ($res)
					{
                        if ($realm->checkCharactersConnection())
                        {
                            while ($arr = $res->fetch())
                            {
                                $GUID = $arr['character'];
                                $itemInfo = $realm->getIteminfo()->getInfoCache($arr['entry']);
                                if ($itemInfo)
                                {
                                    $itemInfo['icon'] = $realm->getIteminfo()->getIcon($arr['entry']);
                                }

                                //Get the character data for this refund
                                if (!isset($characterData[$GUID]))
                                {
                                    $characterData[$GUID] = $realm->getCharacters()->getCharacterData($GUID, false, array('name', 'class', 'level', 'race', 'gender'));
                                }
                                
                                echo '
                                <ul class="item-row ', ($itemInfo ? '' : 'load-info'), '">
                                    <li class="item-icon q', ($itemInfo ? $itemInfo['quality'] : 0), '">
                                        <a href="', item_url($arr['entry'], $RealmId), '" target="_newtab" rel="item=', $arr['entry'], '" data-realm="', $RealmId, '" style="background-image:url(\'http://wow.zamimg.com/images/wow/icons/medium/'.($itemInfo ? $itemInfo['icon'] : 'inv_misc_questionmark').'.jpg\');"></a>
                                    </li>
                                    <li class="item-info">
                                        <a class="name q', ($itemInfo ? $itemInfo['quality'] : 0), '" href="', item_url($arr['entry'], $RealmId), '" target="_newtab" rel="item=', $arr['entry'], '" data-realm="', $RealmId, '">
                                            ', ($itemInfo ? $itemInfo['name'] : 'Loading'), '
                                        </a>
                                        <h5>', $arr['price'], ' ', ($arr['currency'] == CA_COIN_TYPE_SILVER ? 'Silver' : 'Gold'), ' Coins</h5>
                                    </li>
                                    <li class="refund-btn"><a href="#" class="refund-btn" onclick="return RefundItem(', $arr['id'], ');">Refund</a></li>';
                                    
                                    if ($characterData[$GUID])
                                    {
                                        $ClassSimple = str_replace(' ', '', strtolower($CORE->realms->getClassString($characterData[$GUID]['class'])));
                                        
                                        echo '
                                        <li class="character">
                                            <div class="character-holder">
                                                <div class="s-class-icon ', $ClassSimple, '" style="background-image:url(\'http://wow.zamimg.com/images/wow/icons/medium/class_', $ClassSimple, '.jpg\');"></div>
                                                <p>', $characterData[$GUID]['name'], '</p><span>Level ', $characterData[$GUID]['level'], ' ', $CORE->realms->getRaceString($characterData[$GUID]['race']), ' ', ($characterData[$GUID]['gender'] == 0 ? 'Male' : 'Female'), '</span>
                                            </div>
                                        </li>';
                                        
                                        unset($ClassSimple);
                                    }
                                    else
                                    {
                                        //Character not found
                                        echo '
                                        <li class="character">
                                            <div class="character-holder">
                                                <div class="s-class-icon" style="background-image:url(\'http://wow.zamimg.com/images/wow/icons/large/inv_misc_questionmark.jpg\');"></div>
                                                <p>Unknown</p><span>Character not found</span>
                                            </div>
                                        </li>';
                                    }
                                    
                                    echo '
                                    <li class="sent-to"><p>Sent to</p></li>
                                </ul>';
                            }
                            unset($arr, $GUID);
                        }
                        else
                        {
                            echo '<p style="font-size: 16px; text-align:center">Failed to connect to the realm database.</p>';
                        }
					}
					else
					{
						echo '<p style="font-size: 16px; text-align:center">You don\'t have any refundable items for this week.</p>';
					}
					?>
                           
                    </div>
                </div>
            
            	<?php
					unset($characterData, $res);
                ?>
                
      		</div>
      		<!-- VOTE.End -->
    
		</div>
	</div>
 
</div>

<?php
	unset($RealmId);
?>