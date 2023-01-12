<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

  	<div class="container_2">

        <div class="error-holder">
            <?php $ERRORS->PrintAny('password_recovery'); ?>
        </div>

        <div class="page-desc-holder">
            This is the final step of the password recovery process,
            all you have to do is to enter your new password.
        </div>
            
        <div class="container_3 account-wide">
            
            <form action="<?=base_url()?>/recovery/password_finish" method="post" class="page-form">

                <div class="row">
                    <label for="password">New password:</label>
                    <input type="password" id="password" name="password" />
                </div>
                
                <div class="row">
                    <label for="password2">Confirm password:</label>
                    <input type="password" id="password2" name="password2" />
                </div>
                
                <input type="submit" value="Continue" />

            </form>
        
        </div>

    </div>

</div>