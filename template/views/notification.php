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
                        
                        <h1>Notificacion de registro</h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>

<?php
echo '
<div class="container_2" align="center">
    <div class="vertical_center" align="center">
    
        <div class="container_3" align="center">
            
            <div class="login-success">
                <h1>', $data['headline'], '</h1>
                <p style="padding:0 50px 20px 50px; text-align: ', $data['textAlign'], ';">', $data['text'], '</p>
                ', (!$data['autoContinue'] ? '<a href="'.$data['return'].'" style="padding-bottom: 20px; font-size: 12px;">Continue</a>' : ''), '
            </div>
        
        </div>

    </div>
</div>';

//check for auto continue
if ($data['autoContinue'])
{
    echo '<meta http-equiv="refresh" content="', $data['delay'], ';URL=\'', $data['return'], '\'">';
}
?>

                      <!--END OF ABOUT TEXT-->
                </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->


