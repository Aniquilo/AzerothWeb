<?php $CORE->loadConfig('premium_services'); ?>

<li><a href="<?php echo base_url(); ?>/account">Account</a></li>
<li><a href="<?php echo base_url(); ?>/buycoins">Buy Coins</a></li>
<li><a href="<?php echo base_url(); ?>/vote">Vote</a></li>
<li><a href="<?php echo base_url(); ?>/store">Store</a></li>
<?php if ($CORE->configItem('Teleporter_Enabled', 'premium_services')) { ?>
<li><a href="<?php echo base_url(); ?>/teleporter">Teleporter</a></li>
<?php } ?>
<li><a href="<?php echo base_url(); ?>/unstuck">Unstuck</a></li>
<li><a href="<?php echo base_url(); ?>/account/settings">Settings & Options</a></li>
<!--<li id="messages-ddm">
    <a href="<?php echo base_url(); ?>/pm">
        <b>55</b> <i>Private Messages</i>
    </a>
</li>-->