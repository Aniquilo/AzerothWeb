<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>
 
<div class="container_2" >

    <div class="error-holder">
        <?php $ERRORS->PrintAny('login'); ?>
    </div>
    
    <div class="container_4 account_sub_header">
        <div class="grad">
            <div class="page-title">Two-factor authentication</div>
        </div>
    </div>

    <div class="page-desc-holder">
        Please enter the two-factor authentication code we have sent to your e-mail address.<br />
    </div>

    <div class="container_3 account-wide">

        <!-- FORMS -->
        <form action="<?php echo base_url(); ?>/login/submit_code" method="post" name="loginForm" class="page-form">
        
            <div class="row">
                <label for="code">Code</label>
                <input type="text" id="code" name="code" />
            </div>
            
            <input type="submit" value="log in">
        </form>
        <!-- FORMS.End -->

    </div>
   
</div>