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
				<p>Edit Topic</p>
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
		if ($error = $ERRORS->GetErrors('edit_topic', true))
		{
			echo '<div class="alerts-container">', $error, '</div>';
		}	
		unset($error);
		?>
		
		<form method="post" action="<?php echo base_url(); ?>/forums/topic/submit_edit" class="post_topic_reply" name="edit_topic">
		
			<label>
				<p>Topic title</p>
				<input name="title" type="text" maxlength="150" value="<?php echo WCF::parseTitle($topic['name']); ?>" />
			</label>
			
			<input type="hidden" value="<?php echo $topic['id']; ?>" name="topic" />
			
			<div>
            	<input type="submit" value="Edit Topic" />
			</div>
			
		</form>
	
	</div>
</div>

<?php
    $ERRORS->RestoreForm('edit_topic', 'edit_topic');
?>