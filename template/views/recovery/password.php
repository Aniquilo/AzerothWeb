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
                        
                        <h1>Iniciar sesión</h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>


<div class="content_holder">

  	<div class="container_2">

        <div class="error-holder">
            <?php $ERRORS->PrintAny('password_recovery'); ?>
        </div>

        <div class="page-desc-holder">
			<p>Para recuperar la contraseña de su cuenta, debe ingresar la dirección de correo electrónico asociada con su cuenta, y luego siga las instrucciones que le enviaremos a la dirección de correo electrónico proporcionada.</p>
        </div>
                
        <div class="container_3 account-wide">
        
            <form action="<?=base_url()?>/recovery/submit_password" method="post" class="page-form">

                <div class="row">
                    <label for="email"><p>Introduzca su dirección de correo electrónico:</p></label>
                    <input type="text" id="email" name="email" />
                </div>
                
                <?php // If recaptcha is enabled
                    if ($CORE->configItem('enabled', 'recaptcha'))
                    {
                        $CORE->loadLibrary('recaptcha');

                        $recaptcha = new Recaptcha();

                        echo '<div class="row">', $recaptcha->render(), '</div>';

                        unset($recaptcha);
                    }
                ?>

                <input type="submit" value="Continue" />
                
            </form>
        
        </div>
        
	</div>
 
</div>


                      <!--END OF ABOUT TEXT-->
                </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->