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
                <?php $ERRORS->PrintAny('changedname'); ?>
            </div>

            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Change Display Name</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
            <!-- Store Activity -->
            <div class="store-activity">
            
                <div class="page-desc-holder">
                    The display name is used for your publicity, in order to keep your account secure your account login and display name must be significantly different.
                </div>
                
                <div class="container_3 account-wide">
                    
                    <form action="<?php echo base_url(), '/account/submit_changedname'; ?>" method="post" class="page-form">
                        
                        <div class="row">
                            <label for="displayName">Choose your new display name:</label>
                            <input type="text" name="displayName" />
                        </div>
                        
                        <div class="select-currency">
                            <span>Currency:</span>
                            <label class="label_radio"><div></div><input type="radio" name="currency" value="<?php echo CURRENCY_SILVER; ?>"/><p id="sc"><b><?=$price_silver?></b> Silver Coins</p></label>
                            <label class="label_radio"><div></div><input type="radio" name="currency" value="<?php echo CURRENCY_GOLD; ?>" checked="checked"/><p id="gc"><b><?=$price_gold?></b> Gold Coins</p></label>
                        </div>
                        
                        <input type="submit" value="Change" />
                        
                    </form>
                                
                </div>
                            
            </div>
            <!-- Store Activity.End -->
        
        </div>
	</div>
 
</div>