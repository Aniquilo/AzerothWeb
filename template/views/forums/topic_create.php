<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<!--<a href="#" class="important_notice"><p>Please read and accept the rules and regulations before communicating with other members!</p></a>-->

<div class="page-header-navigation">
	<a href="<?php echo base_url(), '/forums'; ?>">Board Index</a>
	<?php
	if ($forum)
	{
		echo '
		<a href="', base_url(), '/forums?category=', $forum['category'], '">', WCF::parseTitle($forum['category_name']), '</a>
		<a href="', base_url(), '/forums/forum?id=', $forum['id'], '">', WCF::parseTitle($forum['name']), '</a>';
	}
	?>
</div>

<div class="container main-wide forum-bg">
	<div class="create-padding">
	
		<div class="forum_header create_header">
		
			<div class="new_title">
				<p>Create New Topic</p>
				<div></div>
			</div>
			
			<?php
			if ($forum)
			{
				echo '
				<div class="forum_title">
					<h1>', WCF::parseTitle($forum['name']), '</h1>
					<h3>', WCF::parseTitle($forum['description']), '</h3>
				</div>
				<h4><b>', WCF::getTopicsCount($forum['id']), '</b> topics</h4>';
			}
			?>
		
		</div>
		
		<?php
		if ($error = $ERRORS->GetErrors('post_topic', true))
		{
			echo '<div class="alerts-container">', $error, '</div>';
		}	
		unset($error);
		?>
		
		<form method="post" action="<?php echo base_url(); ?>/forums/topic/submit_create" class="post_topic_reply" name="post_topic">
		
			<label>
				<p>Topic title</p>
				<input name="title" type="text" maxlength="150" />
			</label>
			
			<label>
				<p>Topic text</p>
				<textarea name="text" class="bbcode"></textarea>
			</label>
			
			<input type="hidden" value="<?php echo $forum['id']; ?>" name="forum" />
			
            <div>
            	<input type="submit" value="Post Topic" />
                
				<?php
                //Should we enable staff posting
                if ($CORE->user->hasPermission(PERMISSION_FORUMS_POST_AS_STAFF))
                {
                    echo '
					<div class="staff_post_check">
						<label class="label_check" for="staff_post">
							<div></div>
							<input type="checkbox" value="1" checked="checked" id="staff_post" name="staff_post" />
							<p>Staff Post</p>
						</label>
					</div>';
                }
                ?>
			</div>
			
		</form>
	
	</div>
</div>

<script>
$(document).ready(function() {
	$("textarea.bbcode").sceditor({
		plugins: 'bbcode',
		style: $BaseURL + '/template/style/bbcode-default-iframe.css'
	});
});
</script>

<?php
    $ERRORS->RestoreForm('post_topic', 'post_topic');
?>