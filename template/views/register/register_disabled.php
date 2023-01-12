<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//get raf hash
$rafHash = isset($_GET['raf']) ? $_GET['raf'] : false;
?>

            <!--header-->
            
            <div class="page-header">
                
            <a href="<?=base_url()?>"><img id="logo-header" src="<?=base_url()?>/template/style/images/logo-header.png"/></a>
                
                 <div class="page">
                
                    <div class="page-top"></div>
                    <div class="page-body">
                    
                        <div class="page-content">
                        <h1>Registrar</h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>
                     





                        <div class="container_2">
 
                          <div class="error-holder">
                              <span class="red"><?php $ERRORS->PrintAny('register'); ?></span> 
                          </div>

                          <div class="container_3 account-wide">

							<p>El registro esta deshabilitado hasta terminar votación. </p>
							<p>Si aun no votaste puedes hacerlo <a href="https://www.facebook.com/azerothproject/posts/pfbid0wYuLRBm4axGAd67uXU8j4y3ysEthwT1r9k6WVPVS5FcCTsrzLnY1LW4YFVgrFf9zl">AQUÍ</a></p>
                          </div>


                          </div>











                        </div>
                </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->


<?php
    $ERRORS->RestoreForm('register', 'registrationForm');
?>