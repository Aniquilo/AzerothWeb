<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="container_2">
    
    <div class="container_3">
        
        <div class="login-success">
            <h1><?=$headline?></h1>
            <p style="padding:0 50px 20px 50px; text-align: center;"><?=$message?></p>
            <a href="<?=base_url()?>/home" style="padding-bottom: 20px; font-size: 12px;">Continue</a>
        </div>
    
    </div>

</div>