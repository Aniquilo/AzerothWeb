<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny(array('add_video', 'delete_video'));
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="<?=base_url()?>/admin/media">Videos</a></li>
        <li><a href="<?=base_url()?>/admin/media/add_video">New Video</a></li>
		<li><a href="<?=base_url()?>/admin/media/screenshots">Screenshots</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
      	<h2>Videos Management</h2>
        
        <div>
            
            <?php
				//Pull them videos
				$res = $DB->query("SELECT `id`, `name`, `image`, `dirname` FROM `videos` ORDER BY `id` DESC;");
				
				if ($res->rowCount() > 0)
				{
					echo '<ul class="imagelist">';
					
					while ($arr = $res->fetch())
					{
						echo '
                        <li>
                            <div style="display: block; width: 255px; height: 143px; background: url(', base_url(), '/uploads/media/videos/', $arr['dirname'], '/thumbnails/medium_', $arr['image'], ') no-repeat center; margin-bottom: 10px;"></div>
							<span>
								<a href="', base_url(), '/media/video?id=', $arr['id'], '" target="_new" class="name ajax cboxElement">', substr(stripslashes($arr['name']), 0, 34), (strlen(stripslashes($arr['name'])) > 34 ? '...' : ''), '</a>
								<!--<a href="#" class="edit ajax cboxElement"></a>-->
								<a href="', base_url(), '/admin/media/delete_video?id='.$arr['id'].'" class="delete" onclick="return deletecheck(\'Are you sure you want to delete this video?\');"></a>
							</span>
						</li>';
					}
					
					echo '</ul>';
				}
				else
				{
					echo '<p>There are no videos.</p>';
				}
				unset($res);
			?>
            
        </div>
        <div class="clear"></div>
        
    </div>
</section>

<script>
	$(document).ready(function() {
		$('.imagelist img').hover(function() {
			$(this).stop().animate({ opacity: '0.75'}, 'fast');
		},
		function() {
			$(this).stop().animate({ opacity: '1'}, 'fast');
		});
	});
</script>