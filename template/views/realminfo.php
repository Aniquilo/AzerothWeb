<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$RealmId = isset($_GET['id']) ? (int)$_GET['id'] : $CORE->user->GetRealmId();

##############################################################
########## Realms information ################################

$stats = $CORE->realms->getRealm($RealmId)->getRealmstats();

//get the characters online count
$count = $stats->getOnline();

// Get the config for the realm
$RealmConfig = $CORE->getRealmConfig($RealmId);
$RealmInfo = $RealmConfig['info'];
?>

<div class="content_holder" style="margin-top: -10px;">

  	<div class="container_2 realm-details">
    
    	<!-- REALM TOP INFO -->
        <div class="realm_main_info">
            <h1>
                <?php echo $RealmConfig['name']; ?>
            </h1>
            <h2>
                <?php echo $RealmInfo['expansion']; ?>
                <div>
                    <?php echo $RealmInfo['short_description']; ?>
                </div>
            </h2>
        </div>
        <!-- REALM TOP INFO.End -->
        
        <!-- REALM Status bar -->
        	<div class="realm_staus_info">
            	
                <div class="realm_status">
                	<script type="text/javascript">
						//ajax update status
						$(document).ready(function()
						{
							var $this = $('#realm-status');
							var $realm = <?php echo $RealmId; ?>;
							
							$.get($BaseURL + "/ajax/serverStatus?id=1", { 
                                id: $realm,
                            },
                            function(data) {
                                if (data == '1') {
                                    $this.addClass('online');
                                    $this.find('#status-text').html('Online');
                                } else {
                                    $this.addClass('offline');
                                    $this.find('#status-text').html('Offline');
                                }
                            });	
						});
                    </script>
                    <h1 class="status" id="realm-status">
                        <span id="status-text">Unknown</span>
                    </h1>
                    <h2>
                        <?php echo $stats->getUptime(); ?> Uptime
                    </h2>
                </div>
                
                <div class="realm_online_players">
                	<h1>
                    	<b><?php echo $count['total']; ?></b> Players
                    </h1>
                    <h2>
                        <b><?php echo $count['alliance']; ?></b> Alliance and <b><?php echo $count['horde']; ?></b> Horde
                   	</h2>
                </div>
                
            </div>
        <!-- REALM Status bar.End -->
        
        <!-- REALM Info -->
        	<div class="realm_info">
                <h1>
                    Realm Information
                    <a href="<?=base_url()?>/features">Features</a>
                </h1>
                <h2><?php echo $RealmInfo['description']; ?></h2>
            </div>
        <!-- REALM Info.End -->
        
        <?php
		//Start of IF DETAILS
		if ($details = $stats->GetRealmDetails())
		{
			?>
        
            <!-- REALM STATISTICS -->
                <div class="realm_statistics">
                    
                    <!-- Faction Balance -->
                    <div class="statistic_holder faction-balance">
                        <h1 class="head_info">Faction Balance</h1>
                        <p>A quick overview of the current balance between the Horde and Alliance.</p>
                            
                        <?php
                        //Calculate percentage
                        $AlliancePercent = $CORE->percent((int)$details['alliance'], $details['total']);
                        $HordePercent = $CORE->percent((int)$details['horde'], $details['total']);
                        
                        echo '
                        <div class="alliance_horde_statistics">
                            <div class="faction_bars_case">
                                <div class="alliance_bar faction_bar" style="height:', $AlliancePercent, '%">
                                    <div class="texts">
                                        <h1>', (int)$details['alliance'], '</h1>
                                        <h2>characters</h2>
                                        <h3>', $AlliancePercent, '% Alliance</h3>
                                    </div>
                                    <div class="grad"></div>
                                </div>
                                <div class="horde_bar faction_bar" style="height:', $HordePercent, '%">
                                    <div class="texts">
                                        <h1>', (int)$details['horde'], '</h1>
                                        <h2>characters</h2>
                                        <h3>', $HordePercent, '% Horde</h3>
                                    </div>
                                    <div class="grad"></div>
                                </div>
                            </div>
                            <div class="all_characters">
                                <h1>', $details['total'], ' Characters</h1>
                            </div>
                        </div>';
                            
                        unset($AlliancePercent, $HordePercent);
                        ?>
                        
                    </div>
                    
                    <!-- Seperator --><div class="stats-seperator"></div>
                    
                    <!-- Race Balance -->
                    <div class="statistic_holder race-balance">
                        <h1 class="head_info">Race Balance</h1>
                        <p>A quick overview of the current race balance on a per race basis.</p>
                        
                        <div class="race_class_stats">
                            
                            <?php foreach ($details['races'] as $race => $count) { ?>
                                <?php $icon = strtolower(str_replace(' ', '', $CORE->realms->getRaceString($race))); ?>
                                <?php if ($icon == 'undead') { $icon = 'scourge'; } ?>

                                <div class="bar_row">
									<div class="scale" style="width:<?=($CORE->percent((int)$count, (int)$details['total']))?>%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/race_<?=$icon?>_male.jpg);"></div>
										<span><?=($CORE->percent((int)$count, (int)$details['total']))?>%</span>
									</div>
									<h1><?=(int)$count?> <span><?=$CORE->realms->getRaceString($race)?></span></h1>
                                </div>
                            <?php } ?>
                            
                        </div>
                    </div>
                    
                    <!-- Seperator -->
                    <div class="stats-seperator"></div>
                    
                    <!-- Class Balance -->
                    <div class="statistic_holder">
                        <h1 class="head_info">Class Balance</h1>
                        <p>A quick overview of the current class balance on a per class basis.</p>
                        
                        <div class="race_class_stats">
                            
                            <?php foreach ($details['classes'] as $class => $count) { ?>
                                <?php $icon = strtolower(str_replace(' ', '', $CORE->realms->getClassString($class))); ?>

                                <div class="bar_row">
									<div class="scale <?=$icon?>" style="width:<?=($CORE->percent((int)$count, (int)$details['total']))?>%;">
										<div class="ico" style="background-image:url(http://wow.zamimg.com/images/wow/icons/small/class_<?=$icon?>.jpg);"></div>
										<span><?=($CORE->percent((int)$count, (int)$details['total']))?>%</span>
									</div>
									<h1><?=(int)$count?> <span><?=$CORE->realms->getClassString($class)?></span></h1>
                                </div>
                            <?php } ?>

                        </div>
                    
                    </div>
                    
                    <div class="clear"></div>
                    
                    <!-- Some info -->
                    <div class="statistics_note">
                        <h3>Statistics displayed on this page do not include characters below level 10 and are automatically updated once a day.</h3>
                    </div>
                    <br/><br/>
                    
                    
                </div>
            <!-- REALM STATISTICS -->
        
        <?php
		}
		//End of IF DETAILS
		?>
        
    </div>
    
</div>