<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

            <!--header-->
            
            <div class="page-header">
                
            <img id="logo-header" src="<?=base_url()?>/template/style/images/logo-header.png"/>
                
                 <div class="page">
                
                    <div class="page-top"></div>
                    <div class="page-body">
                    

                        <!--HERE IS ABOUT TEXT-->

                        <div class="page-content">
                        
                        <h1>Actualización de los datos</h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>


<div class="content_holder">

 
  	<div class="container_2 account">
     	<div class="cont-image">
            
            <div class="error-holder-acc">
                <?php $ERRORS->PrintAny('setrealm'); ?>
            </div>

            <?php
            if (count($CORE->realms->getRealms()) > 1)
            {
                echo '
                <div class="operating-realm-change clearfix">
                    <form action="', base_url(), '/account/set_realm" method="post">
                        <select name="realm" onchange="this.form.submit()" data-stylized="true">';
                            
                            foreach ($CORE->realms->getRealms() as $id => $realm)
                            {
                                echo '<option value="', $realm->getId(), '" ', ($realm->getId() == $CORE->user->getRealmId() ? 'selected' : ''), '>', $realm->getName(), ' <span>(', $realm->getDescription(), ')</span></option>';
                            }
                            unset($id, $realm);
                            
                        echo '
                        </select>
                        <label>', lang('select_realm', 'account'), '</label>
                    </form>
                </div>';
            }
            ?>

    		<!-- Main Account info -->
        	<div class="account_info_cont" align="left">
         		<div class="account_info" align="left">
         	
                    <?php

                    $CORE->loadLibrary('raf');
                    $raf = new RAF();
                    
                    $realmConfig = $CORE->getRealmConfig($CORE->user->GetRealmId());

					echo '
					<div class="account_avatar">
                        <div id="avatar"><span style="background:url(', ($CORE->user->getAvatar()->type() == AVATAR_TYPE_GALLERY ? base_url().'/resources/avatars/'.$CORE->user->getAvatar()->string() : $CORE->user->getAvatar()->string()), ') no-repeat; background-size: 100%;"></span></div>
                        <div class="account_avatar_frame">
                            <a href="', base_url(), '/account/avatars"><span></span></a>
                        </div>
					</div>
			
					<ul class="account_info_main">
						<li id="displayname"><span>', lang('display_name', 'account'), ':</span><p>', $CORE->user->get('displayName'), '</p></li>
                        <li id="rank"><span>', lang('rank', 'account'), ':</span><p>', $CORE->user->getRank()->string(), '</p></li>';
                        
                        if (!$CORE->configItem('bnet', 'authentication'))
                        {
						    echo '<li><span>', lang('username', 'account'), ':</span><p>', $CORE->user->get('identity'), '</p></li>';
                        }

                    echo'<li><span>', lang('email', 'account'), ':</span><p>', $CORE->user->get('email'), '</p></li>
						<li id="gcoins"><span>', lang('gold_coins'), ':</span><div></div><p>', $CORE->user->get('gold'), '</p></li>
						<li id="scoins"><span>', lang('silver_coins'), ':</span><div></div><p>', $CORE->user->get('silver'), '</p></li>
					</ul>
					
					<ul class="account_info_second">
						<li><span>', lang('ref_members', 'account'), ':</span><p><a href="', base_url(), '/recruit">', $raf->GetReferralsCount($CORE->user->get('id')),'</a></p></li>
						<br/>
						<li><span>', lang('last_login', 'account'), ':</span><p>', get_day_name($CORE->user->get('last_login')), ', ', get_datetime($CORE->user->get('last_login'), 'H:i:s'), '</p></li>
						<li><span>', lang('last_ip', 'account'), ':</span><p>', $CORE->user->get('last_ip'), '</p></li>
						<br/>
						<li><span>', lang('reg_date', 'account'), ':</span><p>', date('d F, Y', strtotime($CORE->user->get('joindate'))), '</p></li>
						<br/>
						<li><span>', lang('op_realm', 'account'), ':</span><p>', ($realmConfig ? $realmConfig['name'] : lang('unknown')), '</p></li>
                    </ul>';
                    
                    unset($raf);
					?>
                
            		<div class="clear"></div>
         		</div>
        	</div>
        	<!-- Main Account info.End -->
        
        	<!-- Main Account menu -->
        	<ul id="accoun_panel_menu">
                <?php if ($CORE->configItem('Enabled', 'boosts')) { ?>
                    <li id="acc_boost">
                        <a href="<?php echo base_url(), '/boosts'; ?>"><p></p></a>
                    </li>
                <?php } ?>

                <li>
                    <a href="<?php echo base_url(), '/store'; ?>">
                        <div id="icon" style="background-image:url('http://wow.zamimg.com/images/wow/icons/large/inv_misc_bag_10_black.jpg');"></div>
                        <p>
                            <?=lang('store', 'account')?>
                            <span><?=lang('store_info', 'account')?></span>
                        </p>
                    </a>
                </li>
            
                <li>
                    <a href="<?php echo base_url(), '/vote'; ?>">
                        <div id="icon" style="background-image:url('http://wow.zamimg.com/images/wow/icons/large/inv_crate_04.jpg');"></div>
                        <p>
                            <?=lang('vote', 'account')?>
                            <span><?=lang('vote_info', 'account')?></span>
                        </p>
                    </a>
                </li>
            
                <li>
                    <a href="<?php echo base_url(), '/buycoins'; ?>">
                        <div id="icon" style="background-image: url('<?=base_url()?>/template/style/images/misc/coins_icon.jpg');"></div>
                        <p>
                            <?=lang('buy_coins', 'account')?>
                            <span><?=lang('buy_coins_info', 'account')?></span>
                        </p>
                    </a>
                </li>
            
                <?php if ($CORE->configItem('Teleporter_Enabled', 'premium_services')) { ?>
                <li>
                    <a href="<?php echo base_url(), '/teleporter'; ?>">
                        <div id="icon" style="background-image:url('http://wow.zamimg.com/images/wow/icons/large/inv_misc_rune_05.jpg');"></div>
                        <p>
                            <?=lang('teleporter', 'account')?>
                            <span><?=lang('teleporter_info', 'account')?></span>
                        </p>
                    </a>
                </li>
                <?php } ?>
                
                <li>
                    <a href="<?php echo base_url(), '/unstuck'; ?>">
                        <div id="icon" style="background-image:url('http://wow.zamimg.com/images/wow/icons/large/inv_misc_rune_01.jpg');"></div>
                        <p>
                            <?=lang('unstuck', 'account')?>
                            <span><?=lang('unstuck_info', 'account')?></span>
                        </p>
                    </a>
                </li>           
                
                <li>
                    <a href="<?php echo base_url(), '/armorsets'; ?>">
                        <div id="icon" style="background-image:url('http://wow.zamimg.com/images/wow/icons/large/inv_chest_robe_raidpriest_k_01.jpg');"></div>
                        <p>
                            <?=lang('armor_sets', 'account')?>
                            <span><?=lang('armor_sets_info', 'account')?></span>
                        </p>
                    </a>
                </li>
            
                <li>
                    <a href="<?php echo base_url(), '/level_up'; ?>">
                        <div id="icon" style="background-image:url('http://wow.zamimg.com/images/wow/icons/large/spell_holy_divineprovidence.jpg');"></div>
                        <p>
                            <?=lang('level_up', 'account')?>
                            <span><?=lang('level_up_info', 'account')?></span>
                        </p>
                    </a>
                </li>
                
                <li>
                    <a href="<?php echo base_url(), '/purchase_gold'; ?>">
                        <div id="icon" style="background-image:url('http://wow.zamimg.com/images/wow/icons/large/inv_misc_coin_02.jpg');"></div>
                        <p>
                            <?=lang('ingame_gold', 'account')?>
                            <span><?=lang('ingame_gold_info', 'account')?></span>
                        </p>
                    </a>
                </li>
                
                <li>
                    <a href="<?php echo base_url(), '/racechange'; ?>">
                        <div id="icon" style="background-image:url('<?=base_url()?>/template/style/images/misc/race_change_icon.jpg');"></div>
                        <p>
                            <?=lang('race_change', 'account')?>
                            <span><?=lang('race_change_info', 'account')?></span>
                        </p>
                    </a>
                </li>

                <li>
                    <a href="<?php echo base_url(), '/factionchange'; ?>">
                        <div id="icon" style="background-image:url('<?=base_url()?>/template/style/images/misc/faction_change_icon.jpg');"></div>
                        <p>
                            <?=lang('faction_change', 'account')?>
                            <span><?=lang('faction_change_info', 'account')?></span>
                        </p>
                    </a>
                </li>
            
                <li>
                    <a href="<?php echo base_url(), '/recustomization'; ?>">
                        <div id="icon" style="background-image:url('http://wow.zamimg.com/images/wow/icons/large/race_human_male.jpg');"></div>
                        <p>
                            <?=lang('recustomization', 'account')?>
                            <span><?=lang('recustomization_info', 'account')?></span>
                        </p>
                    </a>
                </li>
            
            </ul>
            <!-- Main Account menu.End -->
        
        	<!-- Quick account menu -->
	        <ul class="quick_acc_menu">
            	<li class="special"><a href="<?php echo base_url(), '/promo_code';?>"><?=lang('promotion_codes', 'account')?></a></li>
            	<li><a href="<?php echo base_url(), '/recruit';?>"><?=lang('recruit_a_friend', 'account')?></a></li>
                <li><a href="<?php echo base_url(), '/item_refund'; ?>"><?=lang('refund_items', 'account')?></a></li>
                <li><a href="<?php echo base_url(), '/twofactorauth'; ?>"><?=lang('twofactor_auth', 'account')?></a></li>
	        	<li><a href="<?php echo base_url(), '/account/changepass'; ?>"><?=lang('change_password', 'account')?></a></li>
	            <li><a href="<?php echo base_url(), '/account/changemail'; ?>"><?=lang('change_email', 'account')?></a></li>
	            <li><a href="<?php echo base_url(), '/account/changedname'; ?>"><?=lang('change_displayname', 'account')?></a></li>
                <li><a href="<?php echo base_url(), '/account/activity'; ?>"><?=lang('account_activity', 'account')?></a></li>
	            <li><a href="<?php echo base_url(), '/account/store_activity'; ?>"><?=lang('store_activity', 'account')?></a></li>
	            <li><a href="<?php echo base_url(), '/account/coin_activity'; ?>"><?=lang('coin_activity', 'account')?></a></li>
	        </ul>
	    	<!-- Quick account menu.End -->
        
        	<div class="clear"></div>
        
     	</div>
	</div>
 
</div>


                      <!--END OF ABOUT TEXT-->
                </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->