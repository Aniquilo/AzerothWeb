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
                        
                        <h1>Como conectar</h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>



<div class="content_holder">

  	<div class="container_2" align="center">
    
    	<div class="container_3 container_wide" align="left">

        	<!-- How To -->
            <div class="how-to-top-info">
                <p>¿Tiene problemas para conectarse? ¡Lee la guía a continuación y no mueras en el intento!.</p>
            </div>
            

                        <p>1. Descarga el cliente de World of Warcraft si no lo tienes.<i>(<a href="<?=base_url()?>/downloads"> Descargas</a>)</i></p>
                        <p>2. Crea una cuenta nueva <a href="<?php echo base_url(); ?>/register">(Aqui)</a>.</p>
                        <p>3. Vaya al archivo "<span>../World of Warcraft/WTF/Config.wtf</span>"</p>
                        <p>4. Edita el archivo <span>Config.wtf</span> con notepad u otro programa similar.</p>
                        <p>5. Abra el archivo "config.wtf" usando un editor de texto, como el bloc de notas. Cambie la línea que contiene la lista de reinos a: (<span class="green">set realmlist "<?=$config['realmlist']?>"</span>)<br><br> Si encuentra algún problema para guardarlo, asegúrese de que el archivo realmlist NO esté configurado en Solo lectura (haga clic con el botón derecho en el archivo, seleccione Propiedades y desmarque la casilla de verificación "Solo lectura").</p>
                        <p>6. Inicie el juego usando WoW.exe en su carpeta de World of Warcraft, inicie sesion y a visiar.</p>


	            	

            <!-- How To.End -->

    	</div>
    </div>
</div>


                      <!--END OF ABOUT TEXT-->
                </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->

<script>
	$(document).ready(function()
	{
		$("#accordion").accordion({ header: '.howto-row-title', autoHeight: false, active: false });
		
		<?php
		//do we need to activate one of the guides?
		$activate = isset($_GET['activate']) ? (int)$_GET['activate'] : false;
		
		if ($activate !== false)
		{
			echo '$("#accordion").accordion("activate", ', $activate, ');';
		}
		
		unset($activate);
		?>
	});
</script>