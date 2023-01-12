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
                <?php $ERRORS->PrintAny('changepass'); ?>
            </div>
  
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Change Password</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
            <!-- Store Activity -->
            <div class="store-activity">
        
                <div class="page-desc-holder">
                    Your new password will take place immediately.
                </div>
            
                <div class="container_3 account-wide">
                    
                    <form action="<?php echo base_url(), '/account/submit_changepass'; ?>" method="post" class="page-form">
                        
                        <div class="row">
                            <label for="password">Password: </label>
                            <input type="password" name="password" />
                        </div>
                        
                        <div class="row">
                            <label for="newPassword">New password: </label>
                            <input type="password" name="newPassword" />
                        </div>
                        
                        <div class="row">
                            <label for="newPassword2">Confirm new password: </label>
                            <input type="password" name="newPassword2" />
                        </div>

                        <input type="submit" value="Change" />

                    </form>
                                
                </div>
                        
      	    </div>
            <!-- Store Activity.End -->
        
        </div>
	</div>
 
</div>