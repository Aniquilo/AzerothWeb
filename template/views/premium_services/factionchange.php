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
                <?php $ERRORS->PrintAny('pStore_faction'); ?>
            </div>
   
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Faction Change</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>    
      
            <!-- FACTION CHANGE -->
            <div class="faction-change">
                
                <div class="page-desc-holder">
                    Faction Change will cost you <b><?=$price?> gold</b> coins.<br/>
                    The faction change cant be reversed, if you want to change to your old faction<br/>
                    you will have to repeat the faction change.
                </div>
            
                <div class="container_3 account-wide">
                    <div style="padding:30px 0 30px 0;">
            	
                        <form action="<?=base_url()?>/factionchange/submit" method="post">
                
                            <!-- Charcaters -->
                            <div style="display:inline-block; vertical-align: top">

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
                                        <div style="display:inline-block; vertical-align: top">
                                            <select id="character-select" class="character-select" name="character" data-stylized="true">
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
                            <!-- Charcaters.End -->
               
                            <input type="submit" value="Change" />
               
                        </form>
               
                    </div>
                </div>
         
      	    </div>
            <!-- VOTE.End -->
   
        </div>
	</div>
 
</div>