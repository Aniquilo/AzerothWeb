<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$RealmId = $CORE->user->GetRealmId();
$realm = $CORE->realms->getRealm($RealmId);

//get the cooldown
$cooldown = $CORE->user->getCooldown('unstuck');
$cooldownTime = '15 minutes';
?>

<div class="content_holder">

  	<div class="container_2 account" align="center">
        <div class="cont-image">
      
            <div class="error-holder">
                <?php $ERRORS->PrintAny('unstuck'); ?>
            </div>
      
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Unstuck</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
            <!-- UNSTUCK -->
            <div class="unstuck">
                
                <div class="page-desc-holder">
                    The unstuck tool will only work partly if your character is online. To revive your character our unstuck function<br/> requires your character to be offline. Teleportation works both ways, online and offline.
                </div>
                
                <form method="post" action="<?=base_url()?>/unstuck/submit">
                    
                    <div class="container_3 account-wide" align="center">
                                    
                        <!-- Charcaters -->
                        <div class="select-charcater-s" align="right">
                            
                            <?php
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
                                        </div>';
                                        
                                        $selectOptions .= '<option value="'. $arr['name'] .'" getHtmlFrom="#character-option-'. $arr['guid'] .'"></option>';
                                        
                                        unset($ClassSimple);
                                    }
                                    
                                    echo '
                                    <div id="select-charcater-selected" style="display:none;">
                                        <p class="select-charcater-selected">Select character</p>
                                    </div>
                                    <select id="character-select" class="character-select" name="character" data-stylized="true">
                                        <option selected="selected" disabled="disabled" getHtmlFrom="#select-charcater-selected"></option>
                                        ', $selectOptions, '
                                    </select>';
                                }
                                else
                                {
                                    echo '<p class="there-are-no-chars">There are no characters.</p>';
                                }
                                unset($selectOptions);
                                unset($characters);
                            }
                            else
                            {
                                echo '<p class="there-are-no-chars">Error: Failed to load your characters.</p>';
                            }
                            ?>
                            
                        </div>
                        <!-- Charcaters.End -->
    
                        <!-- Cooldown Icon -->
                        <div class="cooldown-ico">
                            <div class="ust-cooldown" style="display:block;">
                            
                            <script>
                                function startCooldownTimer(element, percentElement, totalCooldown, leftCooldown)
                                {
                                    var cont = $(element);
                                    var contPercent = $(percentElement);
                                    var leftCooldown = parseInt(leftCooldown);
                                    var totalCooldown = parseInt(totalCooldown);
                                    
                                    var calculatePercent = function(num_amount, num_total)
                                    {
                                        var num_amount = parseInt(num_amount);
                                        var num_total = parseInt(num_total);
                                        
                                        var count1 = num_amount / num_total;
                                        var count2 = count1 * 100;
                                        var count = Math.round(count2);
                                        
                                        return count;
                                    };
                                    
                                    //update each second
                                    var $interval = setInterval(function()
                                    {
                                        //update the cooldown
                                        leftCooldown = parseInt(leftCooldown) - 1;
                                        
                                        var seconds = leftCooldown % 60;
                                        var minutes = Math.floor((leftCooldown / 60) % 60);
                                        var hours = Math.floor((leftCooldown / (60*60)) % 24);
                                        var days = Math.floor((leftCooldown / (24*60*60)) % 30);
                                        
                                        //update the cooldown text
                                        cont.html('(' + minutes + 'm and ' + seconds + 's)');
                                        
                                        //calculate the percentages
                                        var percent = calculatePercent(leftCooldown, totalCooldown);
                                        //update
                                        contPercent.css('height', percent + '%');
                                        
                                        //break the interval
                                        if (minutes == 0 && seconds == 0)
                                        {
                                            clearInterval($interval);
                                            cont.html('');
                                        }
                                        
                                    }, 1000);
                                }
                            </script>
                            
                            <?php
                    
                                if ($cooldown = $CORE->convertCooldown($cooldown))
                                {
                                    $totalCooldown = strtotime($cooldownTime, 0);
                                    $leftCooldown = $cooldown['int'];
                                    $percentCooldown = $CORE->percent($leftCooldown, $totalCooldown);
                                    
                                    echo '<span id="unstuck-timer-percent" style="height:', $percentCooldown, '%"></span>
                                        <p id="unstuck-timer">(', ($cooldown['minutes'] > 0 ? $cooldown['minutes'] . 'm and ' . $cooldown['seconds'] . 's' : $cooldown['seconds'] . 's'), ')</p>
                                        <script>
                                                startCooldownTimer(\'#unstuck-timer\', \'#unstuck-timer-percent\', ', $totalCooldown,', ', $leftCooldown, ');
                                        </script>';
                                }
                                else
                                {
                                    echo '<span style="height:0px"></span>
                                        <p></p>';
                                }
                                
                            ?>
                
                            </div>
                        </div>
                        <!-- Cooldown Icon.End -->
                            
                        <!-- Unstuck submit -->
                        <div class="ust-submit" align="left">
                            <input type="submit" value="unstuck" />
                            <p>
                            Your character will be revived <br/>and teleported to its home.
                            </p>
                        </div>
                        <!-- Unstuck submit.End -->
                
                        <div class="clear"></div>
                
                        <div class="description-small">
                            The <b>Unstuck</b> feature is a free service, but it has 10 minutes cooldown!
                        </div>
                
                    </div>
                
                </form>
            
            </div>
            <!-- UNSTUCK.End -->
    
        </div>
	</div>
 
</div>

<?php
	unset($RealmId);
	unset($cooldownTime);
	unset($cooldown);
?>