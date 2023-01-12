<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

            <!--header-->
            
            <div class="page-header">
                
            <a href="<?=base_url()?>"><img id="logo-header" src="<?=base_url()?>/template/style/images/logo-header.png"/></a>
                
                 <div class="page">
                
                    <div class="page-top"></div>
                    <div class="page-body">
                    

                        <!--HERE IS ABOUT TEXT-->

                        <div class="page-content">
                        
                        <h1><?php echo $row['title']; ?></h1>
						<p><?php echo date('d M, Y', strtotime($row['added'])); ?></p>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>
						<p><?=$row['text']?></p>
						</div>



                      <!--END OF ABOUT TEXT-->
					  </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->