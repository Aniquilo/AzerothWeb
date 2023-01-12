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
                <?php $ERRORS->PrintAny('pStore_levels'); ?>
            </div>
   
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Level Up</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>    
      
            <!-- LEVEL UP -->
            <div class="faction-change">
      		
       		    <div class="page-desc-holder"></div>
            
                <div class="container_3 account-wide">

  			        <form action="<?=base_url()?>/level_up/submit" method="post">
  
                        <!-- SELECTS (charcater and level options) -->
                        <div style="padding:20px 0 20px 0; text-align: center">
            
                            <!-- Charcaters Select -->
                            <div style="display:inline-block; vertical-align: top; margin:3px 15px 0 0;">
                                
                                <?php
                                $realm = $CORE->realms->getRealm($RealmId);

                                //check realm connection
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
                                        <div style="display:inline-block;">
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
                            <!-- Charcaters Select.End --> 
               
                            <!-- SELECT Levels -->
                            <div style="display:inline-block; vertical-align: top; margin-top: 3px;">
                            
                                <div id="choose-level" style="display:none;"><p class="choose-level">Choose level</p></div>
                                
                                <?php foreach ($levelsConfig as $index => $levelConfig) { ?>
                                    <div id="levels-option-<?=$index?>" style="display:none;">
                                        <div class="level-option">
                                            <b>Level <?=$levelConfig['level']?></b> <i>(<?=$levelConfig['price']?> Gold Coins)</i>
                                            <span><?=$levelConfig['description']?></span>
                                        </div>
                                    </div>
                                <?php } ?>
                                
                                <select id="levels-select" class="levels-select" name="levels" data-stylized="true">
                                    <option selected="selected" disabled="disabled" value="null" getHtmlFrom="#choose-level"></option>
                                    <?php foreach ($levelsConfig as $index => $levelConfig) { ?>
                                        <option value="<?=$index?>" getHtmlFrom="#levels-option-<?=$index?>"></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <!-- SELECT Levels.END -->
               
                            <input style="top:3px; margin:0 0 0 15px;" type="submit" value="DING" />
                        </div>
                        <!-- SELECTS (charcater and level options).END -->           

                    </form>
                                                 
                </div>
            
      	    </div>
            <!-- LEVEL UP.End -->
       
        </div>
	</div>
 
</div>