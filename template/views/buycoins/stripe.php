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
                    <div class="sub-active-page">Stripe</div>
                    <a href="<?=base_url()?>/buycoins"><?=lang('back')?></a>
                </div>
            </div>
            
            <br/><br/>
            
            <!-- Buy Coins -->
            <div class="container_3 account-wide">
            
                <div class="buy-coins clearfix">
            
                    
                
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