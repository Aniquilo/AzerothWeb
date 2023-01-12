<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>
   
             
            <!--header-->
            
            <div class="header">
                
                <a href="<?=base_url()?>"><img id="logo-header" src="<?=base_url()?>/template/style/images/logo-header.png"/></a>
                <!-- MAIN TITTLE -->   
                <div id="main-text"><p>Azeroth Project</p></div>
                              
                <div><a  class="play2" href="<?=base_url()?>/downloads">Juega gratis ahora</a></div> 
                   
                <div><img class="line" src="<?=base_url()?>/template/style/images/line.png"/></div> 
                   
                    <!--status is online-->
					<div id="server"><span class="green"> Beta Online</span></div>
					<!--end status-->

					<!--status is offline--
					<div id="server"><span class="red"> OFFLINE</span></div>
					<!--end status-->
                 
				<div><a href="#game-info"><img class="scroll" src="<?=base_url()?>/template/style/images/scroll.png"/></a></div>
			</div>	
               
               <!--end header-->

				
			<!--news-->
			<div class="home-last-news">
			<?php
				$res = $DB->prepare("SELECT * FROM `news` ORDER BY `id` DESC LIMIT 3;");
				$res->execute();
				
				while ($arr = $res->fetch())
				{
					echo '
						<a href="', base_url(), '/news/view?id=', $arr['id'], '" class="last-news-item" style="background-image: url(&quot;', base_url(), '/template/style/images/news/', $arr['id'], '.jpg&quot;);"><span class="date">', $CORE->timeAgo($arr['added']), '</span><span class="title">', stripslashes($arr['title']), '</span></a>
					'; 
				}
				unset($arr, $row);
			?>			
			</div>			

			<!--end news-->
		
				
               <!--game info-->
			   

   
			<div id="game-info" class="gameinfo">
			<p>Bienvenido a Azeroth Project Mists of Pandaria</p>
                
                <div><img class="line2" src="<?=base_url()?>/template/style/images/line.png"/></div>
                   
                <div class="text-info">
               
                <div class="info">
   
                <span class="p2">Ven! y únete a nosotros y revive los días de gloria de la expansión Mists of Pandaria.  Vuelve a compartir los campos de batalla junto a tus viejos amigos o haz nuevos. Explora Paramos salvajes cubiertos de niebla, nieve y lluvia. construye tu destino con la sangre de tus enemigos, y aniquila aquellas fuerzas que buscan corromper el equilibrio de este mundo.<br><br> Somos la mejor comunidad latina vibrante y activa, que se enorgullece de llamar hogar a Azeroth Project</br></br></span></div>
                   
                <div class="info">
               
                <span class="p2">Esperamos sinceramente que todo el mundo encuentre algo diferente en nuestro servidor, algo que has estado buscando durante mucho tiempo. <br>No dudes más! Y sigue tu destino en el campo de batalla!!</br><br>El servidor se abrirá el</br> <span class="green">?? ?? 2023 a las 14:00 GMT-4</span></br></br>La apertura del Beta Abierta tendrá lugar el </br> <span class="green">?? de Febrero a las 14:00 GMT-4</span> </span></div>
             
			</div>
               

				
               
                <p>Información General</p>
				
                <p class="p3">Para los reinos de Azeroth Project </p>   
       
                <div class="about">
       
   
                <!--about 1-->            
   
   
                <div class="about-info">
                       
                <div class="about-body1"></div>
                <div class="about-body">
                <img src="<?=base_url()?>/template/style/images/about-1.png">
               
                <p class="p3">Free to Play - Azeroth Project siempre proveerá acceso gratuito a nuestros reinos. Si quieres jugar, o probar nuevas estrategias para Mists of Pandaria, eres más que bienvenido.</p></div>
                   
                </div>    
                   
   
                <!--about 2-->
   
   
                <div class="about-info">
                       
                <div class="about-body1"></div>
                <div class="about-body">
                <img src="<?=base_url()?>/template/style/images/about-2.png">
                       
                <p class="p3">Reinos PvP - Todos nuestros reinos son de tipo PvP. Comenzarás en nivel 1 y podrás disfrutar de los mejores raids y dungeons.</p></div>
                       
                </div> 
                   
      
                <!--about 3-->             
   
   
                <div class="about-info">
                       
                <div class="about-body1"></div>
                <div class="about-body">
                <img src="<?=base_url()?>/template/style/images/about-3.png">
                <p class="p3">Soporte de Game Masters - Los Game Masters, siempre estan listos para resolver todos tus problemas.</p></div>
                   
   
                </div>    
                </div>
	
               
                </div>
               
               <!--end about-->
           
            
               <!--slide-->         
   
                <div>
                <div class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="3500" data-bs-pause="false" id="carousel-1">
                <div class="carousel-inner" style="height: 951px;">
         
                    
                <!--slide 1-->
    
                <div class="carousel-item active" id="slide1">
   
                <p id="h1">Mists of Pandaria<br><span class="p28"> Patch 5.0 - ?? de Agosto del 2022 - 14:00 GMT-4</span></p>

               
                <p class="p4"><strong>Dungeons:</strong> Temple of the Jade Serpent / Stormstout Brewery / Shado-Pan Monastery / Gate of the Setting Sun / Scholomance / Scarlet Halls / Scarlet Monastery / Mogu'shan Palace / Siege of Niuzao Temple. <br><br><strong>Scenarios:</strong> Greenstone Village / Unga Ingoo / Crypt of Forgotten Kings / Arena of Annihilation /  A Brewing Storm / Brewmoon Festival / Theramore's Fall.<br></p>
                </div>
   
       
                <!--slide 2-->
   
                <div class="carousel-item" id="slide2">
   
                <p id="h1">Desembarco<br><span class="p28"> Patch 5.1 - 01 de Septiembre del 2022 - 20:00 GMT-4</span></p>
   
                <p class="p4"> <strong>Scenarios</strong> Dagger in the Dark / A Little Patience / Lion's Landing / Assault on Zan'vess / Domination Point <br><br><strong>New areas</strong> Domination Point / Lion's Landing / Brawler's guild
                <br></p>
                </div>
   
   
                <!--slide 3-->
   
                <div class="carousel-item" id="slide3">
                       
                <p id="h1">El Rey del Trueno<br><span class="p28"> Patch 5.2 - 10 de Diciembre  del 2022 - 20:00 GMT-4</span></p>
                
                <p class="p4">Arena Season 12 close / Raid-Normal: Throne of Thunder / New Area: Isle of Thunder, Isle of Giants / Mantid archaeology<br></p>
                </div>
				
                <!--slide 4-->
   
                <div class="carousel-item" id="slide4">
                       
                <p id="h1">Alzamiento<br><span class="p28"> Patch 5.3 - 03 de Junio  del 2023 - 20:00 GMT-4</span></p>
                
                <p class="p4"><strong>Heroic Scenarios</strong> Battle on the High Seas / Blood in the Snow / Crypt of Forgotten Kings / Dark Heart of Pandaria / Greenstone Village / The Secrets of Ragefire<br> <strong>Normal Scenarios</strong> Battle on the High Seas / Blood in the Snow / Dark Heart of Pandaria / The Secrets of Ragefire<br><br><strong>New mobs on barrens</strong> <br> <strong>New brawler encounters</strong> <br> </p>
                </div>
				
                <!--slide 5-->
   
                <div class="carousel-item" id="slide5">
                       
                <p id="h1">Asedio de Orgrimmar<br><span class="p28"> Patch 5.4 - 02 de Septiembre  del 2023 - 20:00 GMT-4</span></p>
                
                <p class="p4">Arena Season 13 close / Raid-Normal: Siege of Orgimmar / Raid-Flex: Vale of Eternal Sorrows / Celestial Tournament / New area: Timeless Isle<br></p>
                </div>  				
                </div>
               
                
                <!--controls-->
   
                <div>
                <a class="carousel-control-prev" href="#carousel-1" role="button" data-bs-slide="prev" id="prev"><img src="<?=base_url()?>/template/style/images/prev.png"></a>
                <a class="carousel-control-next" href="#carousel-1" role="button" data-bs-slide="next" id="next"><img src="<?=base_url()?>/template/style/images/next.png"></a></div>
                
                </div>
                </div>
   
               <!--end slide-->
   
               <!--trailer-->
   
               <div class="video">
                   <iframe allowfullscreen="" frameborder="0" src="https://www.youtube.com/embed/xjsxnOf0rRg?controls=0" class="trailer"></iframe>            </div>
   
               <!--end trailer-->
      
               <!--forum and rank-->
              
               <div class="forum-rank">
   
               <div class="forum">
                       
                           <div class="body1"></div>
                       <div class="body2">
                       <p class="p5">Forum</p>    
                       <img class="line-title" src="<?=base_url()?>/template/style/images/line-title.png">
                      
                      
					<div class='topic'>
						<a href='http://forum.Azeroth-Project.com/index.php?/topic/6-Just%20asking' class='topic-link'><img id='title-icon' src='<?=base_url()?>/template/style/images/title-icon.png'/>Texto de prueba 1</a>
						<span>2022/04/05</span>           
                    </div>
					<div class='topic'>
						<a href='http://forum.Azeroth-Project.com/index.php?/topic/5-Adena%20bug' class='topic-link'><img id='title-icon' src='<?=base_url()?>/template/style/images/title-icon.png'/>Texto de prueba 2</a>
						<span>2022/01/27</span>           
                    </div>
					<div class='topic'>
						<a href='http://forum.Azeroth-Project.com/index.php?/topic/4-Server%20released%20date' class='topic-link'><img id='title-icon' src='<?=base_url()?>/template/style/images/title-icon.png'/>Texto de prueba 3</a>
						<span>2021/11/13</span>           
                    </div>
					<div class='topic'>
						<a href='http://forum.Azeroth-Project.com/index.php?/topic/4-Server%20released%20date' class='topic-link'><img id='title-icon' src='<?=base_url()?>/template/style/images/title-icon.png'/>Texto de prueba 4</a>
						<span>2021/11/13</span>           
                    </div>
					<div class='topic'>
						<a href='http://forum.Azeroth-Project.com/index.php?/topic/4-Server%20released%20date' class='topic-link'><img id='title-icon' src='<?=base_url()?>/template/style/images/title-icon.png'/>Texto de prueba 5</a>
						<span>2021/11/13</span>           
                    </div>					
           
                       <!--<div class="topic">
                       <a href="#" class="topic-link"><img id="title-icon" src="<?=base_url()?>/template/style/images/title-icon.png"/>A Topic from forum here</a>
                       <span>Admin</span>           
                       </div>-->
           
                                               
                       <div><a  class="forum-link" href="<?=base_url()?>/forums">Ir al foro</a></div>
           
                       </div>
             
                   </div>    
       
                   <div class="forum">
                       
                           <div class="body1"></div>
                       <div class="body2">
                       <p class="p5">Ranking</p>    
                       <img class="line-title" src="<?=base_url()?>/template/style/images/line-title.png">
   
                       <div class="rank-title">
                       <span>#</span>
                       <span>Name</span>
                       <span>Kills</span></div>
       
       
           
                       <div class='player'>
                       <span><img src='<?=base_url()?>/template/style/images/rank1.png'/></span>
                       <span class='p6'>Test1</span>
                       <span class='p6'>31673</span></div>
           
                       <div class='player'>
                       <span><img src='<?=base_url()?>/template/style/images/rank2.png'/></span>
                       <span class='p6'>Test2</span>
                       <span class='p6'>30441</span></div>
           
                       <div class='player'>
                       <span><img src='<?=base_url()?>/template/style/images/rank3.png'/></span>
                       <span class='p6'>Test3</span>
                       <span class='p6'>28876</span></div>
           
                       <div class='player'>
                       <span><img src='<?=base_url()?>/template/style/images/rank4.png'/></span>
                       <span class='p6'>Test4</span>
                       <span class='p6'>24172</span></div>
           
                       <div class='player'>
                       <span><img src='<?=base_url()?>/template/style/images/rank5.png'/></span>
                       <span class='p6'>Test5</span>
                       <span class='p6'>23602</span></div>    
   
                       <!--<div class="player">
                       <span><img src="<?=base_url()?>/template/style/images/rank2.png"/></span>
                       <span class="p6">Player 2</span>
                       <span class="p6">1022 / 10</span></div> -->
                                
                           
                       </div>
                   
                   </div>    
       
                   </div>
   
                 <!--end forum and rank-->
   