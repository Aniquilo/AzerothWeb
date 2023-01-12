<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

	<div class="container_2" align="center" style="padding:30px 75px; width:846px;">
     
        <div class="media-header">
            <h2>VIDEOS</h2>
            <div class="clear"></div>
            <div class="bline"></div>
        </div>
   
		<?php          
            $embed = preg_replace("/width=\"(\d+)\"/", "width=\"846\"", $row['embed_code']);
            $embed = preg_replace("/height=\"(\d+)\"/", "height=\"476\"", $embed);
            echo $embed;

            echo '
            <!-- VEDEO Info -->
            <div class="open-video-info">
                <h3>', htmlspecialchars(stripslashes($row['name'])), '</h3>
                <div class="tinymce-content">', $row['descr'], '</div>
            </div>';
			
			//Check if we have other videos [Exclude that one]
			$res = $DB->prepare("SELECT `id`, `name`, `short_desc`, `youtube`, `image`, `dirname` FROM `videos` WHERE `id` != :id ORDER BY `id` DESC LIMIT 2;");
			//exlude this video ID if valid
			$res->bindValue(':id', $videoId, PDO::PARAM_INT);
			$res->execute();
			
			if ($res->rowCount() > 0)
			{
                echo '<div class="more_videos clearfix">';

				while ($arr = $res->fetch())
				{
					echo '
                    <!-- Media item -->
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
                            <h3><a href="', base_url(), '/media/video?id=', $arr['id'], '">', htmlspecialchars(stripslashes($arr['name'])), '</a></h3>
                                <p>', htmlspecialchars(stripslashes($arr['short_desc'])), '</p>
                            <a href="', $arr['youtube'], '" class="youtube-link" target="_blank">Watch on YouTube</a>
                        </div>
                        <div class="clear"></div>
                    </div>';
                }
                
                echo '</div>';
			}
		?>
         
	</div>
</div>