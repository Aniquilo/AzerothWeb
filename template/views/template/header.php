<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>
<!doctype html>
<html lang="<?=lang('abbreviation')?>">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
<link rel="shortcut icon" href="img/favicon.html" type="image/x-icon">
<meta name="format-detection" content="telephone=no">
<meta name="robots" content="noindex, nofollow">

<title><?=$HeaderTitle?></title>
<meta name="title" content="<?=$HeaderTitle?> page.">
<meta name="author" content="<?=$config['SiteName']?>">
<meta name="Description" content="<?=$config['MetaDescription']?>">
<meta name="Keywords" content="<?=$config['MetaKeywords']?>">
<meta name="language" content="<?=ucfirst($config['Language'])?>">
<meta name="type" content="website"/>
<meta name="copyright" content="<?=$config['MetaCopyright']?>">
<meta name="resource-type" content="games">
<meta name="Distribution" content="Global">
<meta name="email" content="<?=$config['Email']?>">
<meta name="Charset" content="UTF-8">
<meta name="Rating" content="General">
<meta name="Revisit-after" content="7 Days">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Content-Language" content="<?=lang('abbreviation')?>">

    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Cinzel:500,600,700,800,900&amp;display=swap">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Lato:100i,300,300i,400,400i,700,700i&amp;display=swap">
<link rel="stylesheet" href="<?=base_url()?>/template/style/bootstrap/bootstrap.min.css">
<link rel="icon" type="image/x-icon" href="<?=base_url()?>/template/style/images/logo-mini.png">

    <!-- Google Tag Manager -->
    <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
                new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
            j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
            'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','GTM-WG563T8');</script>
    <!-- End Google Tag Manager -->




<?php
# Common Styles
$CORE->tpl->AddCSS('template/style/fonts.css', false, RESOURCE_LOAD_PRIO_HIGH);
$CORE->tpl->AddCSS('template/style/style.css', false, RESOURCE_LOAD_PRIO_HIGH);
$CORE->tpl->AddCSS('template/style/technical.css?v=1', false, RESOURCE_LOAD_PRIO_HIGH);
$CORE->tpl->AddCSS('template/style/select.css?v=1', false, RESOURCE_LOAD_PRIO_HIGH);

if (defined('is_forums'))
{
	$CORE->tpl->AddCSS('template/forums/style/main.css?v=3', false, RESOURCE_LOAD_PRIO_HIGH);
    $CORE->tpl->AddCSS('template/forums/style/post_topic.css', false, RESOURCE_LOAD_PRIO_HIGH);
    $CORE->tpl->AddCSS('template/style/bbcode-default.css', false, RESOURCE_LOAD_PRIO_HIGH);
}
else
{
	$CORE->tpl->AddCSS('template/style/forms.css?v=3', false, RESOURCE_LOAD_PRIO_HIGH);
    $CORE->tpl->AddCSS('template/style/home.css', false, RESOURCE_LOAD_PRIO_HIGH);
    $CORE->tpl->AddCSS('template/style/sidebar.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$CORE->tpl->AddCSS('template/style/pages-background.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$CORE->tpl->AddCSS('template/style/quick-menu.css', false, RESOURCE_LOAD_PRIO_HIGH);
	$CORE->tpl->AddCSS('template/style/account_panel.css', false, RESOURCE_LOAD_PRIO_HIGH);
}

$CORE->tpl->AddCSS('template/style/shadowbox.css', false, RESOURCE_LOAD_PRIO_HIGH);
$CORE->tpl->AddCSS('template/style/loginbox.css', false, RESOURCE_LOAD_PRIO_HIGH);
$CORE->tpl->AddCSS('template/style/alert-box.css?v=2', false, RESOURCE_LOAD_PRIO_HIGH);
$CORE->tpl->AddCSS('template/style/radio-checkbox.css', false, RESOURCE_LOAD_PRIO_HIGH);
$CORE->tpl->AddCSS('template/style/cookie-consent.css', false, RESOURCE_LOAD_PRIO_HIGH);

//Load css files requested by the page
$CORE->tpl->PrintCSS();

//Global Javascript variables
?>
<script type="text/javascript" language="javascript">
	var $BaseURL = '<?=base_url()?>';
	var $WOWDBURL = '<?=wowdb_url()?>';
	var $TIMEZONE = '<?=$config['TimeZone']?>';
	var $TIMEZONEOFFSET = '<?=$config['TimeZoneOffset']?>';
	var $CURUSER = {
		isOnline: <?=($CORE->user->isOnline() ? 'true' : 'false')?>,
		selectedRealm: <?=$CORE->user->GetRealmId()?>
	};
	var $LoginBox = {
		isLoaded: false
    };
</script>

<?php if (isset($config['GA_TrackingID']) && !empty($config['GA_TrackingID'])) { ?>
<!-- Global site tag (gtag.js) - Google Analytics -->
<script async src="https://www.googletagmanager.com/gtag/js?id=<?=$config['GA_TrackingID']?>"></script>
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());
    gtag('config', '<?=$config['GA_TrackingID']?>');
</script>
<?php } ?>

<?php
$CORE->tpl->AddHeaderJs('template/js/jquery-1.7.js');
$CORE->tpl->AddHeaderJs('template/js/header.js');
    
//Load diferrent JS Groups
if (defined('is_forums'))
{
	//Add default header javascripts for the Forums
	$CORE->tpl->AddHeaderJs('template/forums/js/base.js?v=2');
}

$CORE->tpl->AddHeaderJs('template/js/alertbox.js?v=1');
$CORE->tpl->AddHeaderJs('template/js/jquery.cycle.all.js');
$CORE->tpl->AddHeaderJs('template/js/jquery.easing.1.3.js');
$CORE->tpl->AddHeaderJs('template/js/cookie-consent.js');

//Load js files requested by the page
$CORE->tpl->PrintHeaderJavascripts();

?>
</head>
<body>
<video class="videoh" autoplay="true" loop="true" muted="muted">
		  <source src="<?=base_url()?>/template/video/header-bg1.mp4" type='video/mp4; codecs="avc1.42E01E, mp4a.40.2"'></video>

          <div><img class="particle" src="<?=base_url()?>/template/style/images/particle.png"/></div>  

                    <!--side nav-->

             <div id="mySidenav" class="sidenav">
  
             <a href="javascript:void(0)" class="closebtn" onclick="closeNav()">&times;</a>
			 <img id="logo-mini" src="<?=base_url()?>/template/style/images/logo-mini.png"/>
			 <a href="<?=base_url()?>/home">Inicio</a>
             <a href="<?=base_url()?>/news">Noticias</a>
             <a href="<?=base_url()?>/support/howto">Como conectar</a>
             <a href="<?=base_url()?>/forums">Foro</a>
             <a href="<?=base_url()?>/downloads">Descargas</a>
             <a href="<?=base_url()?>/register">Registrar</a>
             <a href="<?=base_url()?>/login">Iniciar Sesion</a>  
             </div>

          <!--end side nav-->

         
          <!--social side-->

             <div class="social">
             <a target="_blank" href="https://discord.gg/er2qNuv" class="social-link"><img src="<?=base_url()?>/template/style/images/discord.png"/></a>
             <a target="_blank" href="https://www.facebook.com/azerothproject" class="social-link"><img src="<?=base_url()?>/template/style/images/fb.png"/></a>
             <!--
			 <a target="_blank" href="#" class="social-link"><img src="<?=base_url()?>/template/style/images/vk.png"/></a>
             <a target="_blank" href="#" class="social-link"><img src="<?=base_url()?>/template/style/images/twitter.png"/></a>
			 -->
             </div>

           <!--end social side-->

           <!--navbar top-->

             <div class="nav">  
             
             <div id="left-nav">
             
             <span id="open-nav" onclick="openNav()"><img src="<?=base_url()?>/template/style/images/open-nav.png"/></span>
           
             <a id="item" href="<?=base_url()?>/home">Inicio<a/>
			 <a id="item" href="<?=base_url()?>/news">Noticias<a/>
             <a id="item" href="<?=base_url()?>/support/howto">Como conectar</a>
             <a id="item" href="<?=base_url()?>/forums">Foro<a/>
             <a id="item" href="<?=base_url()?>/downloads">Descargas<a/>
             </div>
			 



            <?php 			
			if (!$CORE->user->isOnline())
			{
			?>

            	<!--Not logged-->
             <div id="right">
             <a id="item2" href="<?=base_url()?>/register">Registrar<a/>
             <span>o</span>
             <a id="item2" href="<?=base_url()?>/login">Iniciar Sesion<a/>
             <a id="play" href="<?=base_url()?>/downloads">Jugar gratis</a>
             </div>
				<!--Not logged.End-->

			<?php
			}
            else
			{
			?>

            	<!-- Logged In -->
             <div id="right">          
			 <a href="<?=base_url()?>/donate" data-toggle="tooltip" title="" class="btn btn-outline-yellow" data-original-title="Tus puntos de donacion"><span class="sf-coin">0</span></a>&nbsp;		 
             <a href="#" class="link-dark text-decoration-none dropdown-toggle" id="dropdownUser2" data-bs-toggle="dropdown" aria-expanded="false">
                 <img src="<?=($CORE->user->getAvatar()->type() == AVATAR_TYPE_GALLERY ? base_url().'/resources/avatars/'.$CORE->user->getAvatar()->string() : $CORE->user->getAvatar()->string())?>" alt="mdo" width="37" height="37" class="rounded-circle">
                </a>
            <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser2">
                <li><a class="dropdown-item" href="#">Configuraciones de la cuenta</a></li>
                <li><a class="dropdown-item" href="#">Historial de pagos</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?=base_url()?>/logout">Cerrar sesión</a></li>
            </ul>
             <a id="play" href="<?=base_url()?>/account"><?=$CORE->user->get('displayName')?></a>
             </div>			
				<!-- Logged In.End -->

            <?php } ?>

 

             </div>
            
            <!--end navbar top-->


