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
                        
                        <h1>Iniciar sesión</h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>
      



	  
							<p>El registro esta deshabilitado hasta terminar votacion. </p>
							<p>Si aun no votaste puedes hacerlo <a href="https://www.facebook.com/azerothproject/posts/pfbid0wYuLRBm4axGAd67uXU8j4y3ysEthwT1r9k6WVPVS5FcCTsrzLnY1LW4YFVgrFf9zl">AQUI</a></p>

							</div>
						   
						 </div>			



 

                      <!--END OF ABOUT TEXT-->
                </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->











<?php
    $ERRORS->RestoreForm('login', 'loginForm');
?>