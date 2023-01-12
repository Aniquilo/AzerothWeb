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
	if ($topic && $forum)
	{
		echo '
		<a href="', base_url(), '/forums?category=', $forum['category'], '">', WCF::parseTitle($forum['category_name']), '</a>
		<a href="', base_url(), '/forums/forum?id=', $forum['id'], '">', WCF::parseTitle($forum['name']), '</a>
		<a href="', base_url(), '/forums/topic?id=', $topic['id'], '">', WCF::parseTitle($topic['name']), '</a>';
	}
	?>
</div>

<div class="container main-wide forum-bg">
	<div class="create-padding">
	
		<div class="forum_header create_header">
		
			<div class="new_title">
				<p>Edit Reply</p>
				<div></div>
			</div>
			
			<?php
			if ($topic)
			{
				echo '
				<div class="topic_title">
					<h1>', WCF::parseTitle($topic['name']), '</h1>
					<h3>', $topic['added'], '</h3>
				</div>';
			}
			?>
		
		</div>
		
		<?php
		if ($error = $ERRORS->GetErrors('edit_reply', true))
		{
			echo '<div class="alerts-container">', $error, '</div>';
		}	
		unset($error);
		?>
		
		<form method="post" action="<?php echo base_url(); ?>/forums/post/submit_edit" class="post_topic_reply" name="edit_reply">
		
			<label>
				<p>Reply title</p>
				<input name="title" type="text" maxlength="150" value="<?php echo WCF::parseTitle($Post['title']); ?>" />
			</label>
			
			<label>
				<p>Reply text</p>
				<?php
					echo '<textarea name="text" class="bbcode">', stripslashes($Post['text']), '</textarea>';
				?>
			</label>
			
			<input type="hidden" value="<?php echo $Post['id']; ?>" name="post" />
			
			<div>
            	<input type="submit" value="Edit Reply" />
                
				<?php
                //Should we enable staff posting
                if ($CORE->user->hasPermission(PERMISSION_FORUMS_POST_AS_STAFF))
                {
                    //Is staff post
                    $staffPost = $CORE->hasFlag((int)$Post['flags'], WCF_FLAGS_STAFF_POST);
                
                    echo '
					<div class="staff_post_check">
						<label class="label_check" for="staff_post">
							<div></div>
							<input type="checkbox" value="1" ', ($staffPost ? 'checked' : ''), ' id="staff_post" name="staff_post" />
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
    $ERRORS->RestoreForm('edit_reply', 'edit_reply');
?>