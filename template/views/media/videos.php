<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$p = (isset($_GET['p']) ? (int)$_GET['p'] : 1);

//load the pagination lib
$CORE->loadLibrary('paginationType2');

//Let's setup our pagination
$pagies = new Pagination();
//$pagies->addToLink('?page='.$pageName);

$perPage = 8;
$where = "";

//count the total records
$res = $DB->prepare("SELECT COUNT(*) FROM `videos` " . $where . ";");
$res->execute();

$count_row = $res->fetch(PDO::FETCH_NUM);
$count = $count_row[0];
			
unset($count_row);
unset($res);

?>

<div class="content_holder">

	 <div class="container_2" align="center" style="padding:30px 40px; width:916px;">
     
        <div class="media-header">
            <h2>VIDEOS</h2>
            <h3 class="items-number">(<?php echo $count; ?>)</h3>
            <div class="clear"></div>
            <div class="bline"></div>
        </div>
     	
        <!-- All Videos -->

        <?php
            
        if ($count > 0)
        {
            //calculate the pages
            $pages = $pagies->calculate_pages($count, $perPage, $p);
            
            $res = $DB->query("SELECT `id`, `name`, `short_desc`, `youtube`, `image`, `dirname` FROM `videos` ORDER BY id DESC LIMIT ".$pages['limit'].";");

            //loop the records
            while ($arr = $res->fetch())
            {
                echo '
                <div class="media-video-container media-all-vids-page" align="left">
                    <div class="media-video-thumb container_frame">
                        <div class="cframe_inner">
                            <a href="', base_url(), '/media/video?id=', $arr['id'], '">
                            <!--Video THUMB Preview-->
                            <div class="image-thumb-preview" style="background-image:url(\'', base_url(), '/uploads/media/videos/', $arr['dirname'], '/thumbnails/medium_', $arr['image'], '\');"></div>
                            <div class="play-button-small"></div>
                            </a>
                        </div>
                    </div>
                    <div class="video-info">
                        <h3><a href="', base_url(), '/media/video?id=', $arr['id'], '">', htmlspecialchars(stripslashes($arr['name'])), '</a></h3>
                        <p style="height: auto;">', htmlspecialchars(stripslashes($arr['short_desc'])), '</p>
                        <a href="', $arr['youtube'], '" class="youtube-link" target="_blank">Watch on YouTube</a>
                    </div>
                    <div class="clear"></div>
                </div>';
                
                unset($imageName, $imageExt);
            }
            unset($arr, $res);
        }
        else
        {
            echo '<p class="there-is-nothing">There are no videos.</p>';
        }
        
        ?>
        
        <div class="clear"></div>
        <!-- All Videos.End -->
         
        <?php
			
        if ($count > 0 and $count > $perPage)
        {
            echo '
            <!-- Pagination -->
            <div class="pagination-holder pagination-media">
                <ul class="pagination">
                    ', $pages['first'], '
                    ', $pages['previous'], '
                    ', $pages['info'], '
                    ', $pages['next'], '
                    ', $pages['last'], '
                </ul>
                <div class="clear"></div>
            </div>';
        }
        unset($pagies, $pages);
			
		?>
	        
    </div>
    
</div>

<?php

unset($count, $where, $perPage, $p);

?>