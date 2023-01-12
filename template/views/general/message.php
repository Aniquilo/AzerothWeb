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
                        
                        <h1><?=$headline?></h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>
						
						<p><?=$message?></p>



                      <!--END OF ABOUT TEXT-->
                </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->
