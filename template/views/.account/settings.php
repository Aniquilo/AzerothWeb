<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

  	<div class="container_2 account" align="center">
        <div class="cont-image">
    
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Account Settings</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
      	    <div class="vote-page">
      		
                <div class="container_3 account-wide" align="center">
             
            		<ul class="account-settings">
                        <li>
                        	<a href="<?php echo base_url(), '/twofactorauth'; ?>">
                        	Two-factor authentication
                            <p>Keep your account secure.</p>
                            </a>
                        </li>
                    	<li>
                        	<a href="<?php echo base_url(), '/changepass'; ?>">
                        	Change password
                            <p>Change your account password.</p>
                            </a>
                        </li>
                        <li>
                        	<a href="<?php echo base_url(), '/changemail'; ?>">
                            Change e-mail address
                            <p>Change your account e-mail address.</p>
                            </a>
                        </li>
                        <li>
                        	<a href="<?php echo base_url(), '/changedname'; ?>">
                            Change display name
                            <p>Change your account name. This service costs coins.</p>
                           	</a>
                        </li>
                        <li>
                        	<a href="#">
                            Support ticket
                            <p>If you have a problem submit a ticket.</p>
                            </a>
                        </li>
                    </ul>
             
                </div>
            
      	    </div>
    
        </div>
	</div>
 
</div>