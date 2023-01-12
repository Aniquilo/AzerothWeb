<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

	 <div class="container_2" align="center" style="padding:30px 40px; width:916px;">
     
        <!-- Videos -->
        	<div class="media-container flleft half-w" align="left">
            
	            <div class="media-c-header">
	            	<h3>Videos</h3>
	                <a class="view-alll" href="<?=base_url()?>/media/videos">View all</a>
	            </div>
            	
                <?php
					//Latest two videos
					$res = $DB->query("SELECT `id`, `name`, `short_desc`, `youtube`, `image`, `dirname` FROM `videos` ORDER BY `id` DESC LIMIT 2;");
                	
					if ($res->rowCount() > 0)
					{
						while ($arr = $res->fetch())
						{
							echo '
							<div class="media-video-container" align="left">
								<div class="media-video-thumb container_frame">
									<div class="cframe_inner">
										<a href="', base_url(), '/media/video?id=', $arr['id'], '">
										<!--Video THUMB Preview-->
										<div class="image-thumb-preview" style="background-image:url(\'', base_url(), '/uploads/media/videos/', $arr['dirname'], '/thumbnails/small_', $arr['image'], '\');"></div>
										<div class="play-button-small"></div>
										</a>
									</div>
								</div>
								<div class="video-info">
									<h3>', htmlspecialchars(stripslashes($arr['name'])), '</h3>
									<p>', htmlspecialchars(stripslashes($arr['short_desc'])), '</p>
									<a href="', $arr['youtube'], '" class="youtube-link" target="_blank">Watch on YouTube</a>
								</div>
								<div class="clear"></div>
							</div>';
							
							unset($imageName, $imageExt);
						}
						unset($arr);
					}
					else
					{
						echo '<p class="there-is-nothing">There are no videos.</p>';
					}
					unset($res);
				?>
                
            </div>
        <!-- Videos.End -->
        
       <!-- Wallpapers -->
        	<div class="media-container flright half-w" align="left">
            
	            <div class="media-c-header">
	            	<h3>Wallpapers</h3>
	                <a class="view-alll" href="<?=base_url()?>/media/wallpapers">View all</a>
	            </div>
            
				<ul class="screanshots screanshots-media-page">
					<li>
						<a href="<?=base_url()?>/media/wallpapers" class="container_frame" title="Warcry WoW Wallpaper One">
							<span class="cframe_inner" style="background-image:url(<?=base_url()?>/uploads/media/wallpapers/thumbs/thumb-warcry-wall1.jpg);"></span>
                            <!--<div class="media-zoom-ico"></div>-->
						</a>
					</li>
                    <li>
						<a href="<?=base_url()?>/media/wallpapers" class="container_frame" title="Emerald Dragonshrine Wallpaper">
							<span class="cframe_inner" style="background-image:url(<?=base_url()?>/uploads/media/wallpapers/thumbs/1-thumb.jpg);"></span>
                            <!--<div class="media-zoom-ico"></div>-->
						</a>
					</li>
                    
                    <li>
						<a href="<?=base_url()?>/media/wallpapers" class="container_frame" title="Warcry WoW Wallpaper Two">
							<span class="cframe_inner" style="background-image:url(<?=base_url()?>/uploads/media/wallpapers/thumbs/5-thumb.jpg);"></span>
                            <!--<div class="media-zoom-ico"></div>-->
						</a>
					</li>
                    
                     <li>
						<a href="<?=base_url()?>/media/wallpapers" class="container_frame" title="Grizzlemaw Wallpaper">
							<span class="cframe_inner" style="background-image:url(<?=base_url()?>/uploads/media/wallpapers/thumbs/4-thumb.jpg);"></span>
                            <!--<div class="media-zoom-ico"></div>-->
						</a>
					</li>
					
                    <div class="clear"></div>
				</ul>
                
            </div>
        <!-- Wallpapers.End -->
        <div class="clear"></div>
        <BR/>
        
        <!-- Screanshots -->
        	<div class="media-container flright full-w" align="left">
            
	            <div class="media-c-header">
	            	<h3>Screenshots</h3>
	                <a class="view-alll" href="<?=base_url()?>/media/screenshots">View all</a>
	            </div>
                  
				<ul class="screanshots screanshots-media-page-two">
                	
                    <?php
					$type = TYPE_SCREENSHOT;
					$status = SCREENSHOT_STATUS_APPROVED;
					
					$res = $DB->prepare("SELECT * FROM `images` WHERE `type` = :type AND `status` = :status LIMIT 10;");
					$res->bindParam(':type', $type, PDO::PARAM_INT);
					$res->bindParam(':status', $status, PDO::PARAM_INT);
					$res->execute();
					
					if ($res->rowCount() > 0)
					{
						while ($arr = $res->fetch())
						{
							echo '
							<li>
								<a href="', base_url(), '/uploads/media/screenshots/', $arr['image'],'" class="container_frame" rel="shadowbox" title="', $arr['name'], '{|}', $arr['descr'], '">
									<span class="cframe_inner" style="background-image:url(', base_url(), '/uploads/media/screenshots/thumbs/', $arr['image'],'); background-size: 100%; background-repeat: no-repeat;"></span>
		                            <div class="media-zoom-ico"></div>
								</a>
							</li>';
						}
						unset($arr);
					}
					else
					{
						//no images
						echo '<p>There are no screenshots.</p><br><br>';
					}
					unset($res, $type, $status);
					?>
                    
                    <div class="clear"></div>
				</ul>
                <div class="clear"></div>
                
                <?php
				if ($CORE->user->isOnline())
				{
                  	echo '
					<!-- Upload your screanshots -->
	            	<div>
                		<a href="', base_url(), '/media/upload_screanshot" class="container_3 upload-btn">
                    		<span class="error_icons atention"></span>
                        	<p>Submit your screenshots and earn silver coins !</p>
                    	</a>
	            	</div>';
				}
				?>
                
            </div>
            
        <!-- Screanshots.End -->
        <div class="clear"></div>
	        
	 </div>
    
</div>