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
                <?php $ERRORS->PrintAny('twofactorauth'); ?>
            </div>
  
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Two-factor authentication</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
      	    <div class="store-activity">
        
                <div class="page-desc-holder">
                    Two-factor authentication is an extra layer of security for your account designed to ensure that you're the only person who can access your account, even if someone knows your password.<br />
                </div>
            
                <div class="container_3 account-wide">
            	
                    <form action="<?php echo base_url(), '/twofactorauth/submit'; ?>" method="post" class="page-form">
                        
                        <div class="row">
                            <label for="email">Two-factor via E-mail: </label>
                            <span style="display: inline-block; float:right;">
                                <select id="email" name="email" data-stylized="true">
                                    <option value="0" <?=($CORE->user->get('twofactor_email') == '0' ? 'selected' : '')?>>Disabled</option>
                                    <option value="1" <?=($CORE->user->get('twofactor_email') == '1' ? 'selected' : '')?>>Enabled</option>
                                </select>
                            </span>
                        </div>
                        
                        <input type="submit" value="Save" />

                    </form>
                            
                </div>
                            
            </div>
            
        </div>
    </div>

</div>