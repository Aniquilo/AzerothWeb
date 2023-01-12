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
	<a href="<?php echo base_url(), '/forums?category=', $forum['category']; ?>"><?php echo WCF::parseTitle($forum['category_name']); ?></a>
	<a href="<?php echo base_url(), '/forums/forum?id=', $forum['id']; ?>"><?php echo WCF::parseTitle($forum['name']); ?></a>
	<a href="<?php echo base_url(), '/forums/topic?id=', $topic['id']; ?>"><?php echo WCF::parseTitle($topic['name']); ?></a>
</div>

<div class="container main-wide forum-bg">
	<div class="forum-padding">
		
		<!-- Forum Header -->
		<div class="topic_header">
			<div class="topic_title">
                <h1><?php echo WCF::parseTitle($topic['name']); ?><?php if ((int)$topic['locked'] == 1) { ?> <span>(Locked)</span><?php } ?></h1>
				<h3><?php echo $topic['added']; ?></h3>
			</div>
			<h4><b><?php echo $totalCount; ?></b> posts</h4>
		</div>
		<!-- Forum Header.End -->

		<?php
		if (($countOnPage > 2 && $totalCount > $perPage) || ($userCanPost && (int)$topic['locked'] == 0))
		{
			echo '
			<!-- Actions -->
			<div class="actions_c clearfix">';

				if ($userCanPost && (int)$topic['locked'] == 0)
				{
					echo '<a href="', base_url(), '/forums/post/write?topic=', $topic['id'], '" class="forum_btn_large">Post Reply</a>';
				}
				
				if ($countOnPage > 2 && $totalCount > $perPage)
				{
					echo '
					<ul class="pagination">
						', $pagination['previous'], '
						', $pagination['pages'], '
						', $pagination['next'], '
					</ul>';
				}
			
			echo '
			</div>
			<!-- Actions.End -->';
		}
		
		if ($countOnPage > 0)
		{
			//loop the records
			while ($arr = $posts_res->fetch())
			{
				$text = WCF::parsePostText($arr['id'], $arr['text']);
				
				if ($userInfo = WCF::getAuthorInfo($arr['author']))
				{
					$userRank = new UserRank($userInfo['rank']);
					$arr['author_str'] = $userInfo['displayName'];
					
					//prepare the avatar
					if ((int)$userInfo['avatarType'] == AVATAR_TYPE_GALLERY)
					{
						$gallery = new AvatarGallery();
						$Avatar = $gallery->get((int)$userInfo['avatar']);
						unset($gallery);
					}
					else if ((int)$userInfo['avatarType'] == AVATAR_TYPE_UPLOAD)
					{
						$Avatar = new Avatar(0, $userInfo['avatar'], 0, AVATAR_TYPE_UPLOAD);
					}
				}
				else
				{
					$userRank = new UserRank(0);
					$arr['author_str'] = 'Unknown';
					$arr['author_rank'] = 'Unknown';
					$gallery = new AvatarGallery();
					$Avatar = $gallery->get(0);
					unset($gallery);
				}
				
				//format the time
				$arr['added'] = date('D M j, Y, h:i A', strtotime($arr['added']));
				
				//Is staff post
				$staffPost = $CORE->hasFlag((int)$arr['flags'], WCF_FLAGS_STAFF_POST);
				//Is deleted
				$deletedPost = ((int)$arr['deleted_by'] > 0 ? true : false);
				
				//Resolve the deletion author
				if ($deletedPost)
				{
					$userInfo = WCF::getAuthorInfo($arr['deleted_by']);
					$arr['deleted_by_str'] = $userInfo['displayName'];
					unset($userInfo);
					$arr['deleted_time'] = date('D M j, Y, h:i A', strtotime($arr['deleted_time']));
				}
				
				echo '
				<!-- Topic Post -->
				<div class="topic_post', ($staffPost ? ' admin_post' : ''), ($deletedPost ? ' deleted_post' : ''), '" id="post-', $arr['id'], '">';
					
					if ($staffPost)
					{
						echo '<!-- Admin Warcry WoW post -->
						<div class="admin_post_logo_wc"></div>';
					}
					
					echo '
					<div class="left_side">
					
						<div class="user_avatar">';
							
							//handle avatars
							if ($Avatar->type() == AVATAR_TYPE_GALLERY)
							{
								echo '<span style="background:url(\'', base_url(), '/resources/avatars/', $Avatar->string(), '\') no-repeat; background-size: 100%;"></span>';
							}
							else
							{
								echo '<span style="background:url(', $Avatar->string(), ') no-repeat; background-size: 100%;"></span>';
							}
						
						echo '
						</div>
						
						<div class="user_info">
							<div class="usr_and_pr">
								<a href="', base_url(), '/profile?uid=', $arr['author'], '" class="username">', $arr['author_str'], '</a>
								
								<div class="drop_down_profile">
									<span class="profile">Profile</span>
									<a href="" class="arrow"></a>
									<div class="drop_down_container">
										<h1>', $arr['author_str'], '</h1>
										<h3>', $userRank->string(), '</h3>
										<ul class="user_menu">
											<li><a href="', base_url(), '/profile?uid=', $arr['author'], '">Profile</a></li>
											<li><a href="#">View Posts</a></li>
											<li><a href="#">Ignore</a></li>
										</ul>
									</div>
								</div>
								
							</div>
							
							<h3>', $userRank->string(), '</h3>
						</div>
					
					</div>
					<div class="right_side">
						<div class="post_container">
						', ($deletedPost ? '<p class="deleted-note">This post has been deleted by '.$arr['deleted_by_str'].' on '.$arr['deleted_time'].'.</p><br>' : ''), '
						', $text, '
						</div>
						<ul class="post_controls">
							<li class="post_date">', $arr['added'], '</li>';
							
							//Check if we can edit the post
							if ($CORE->user->isOnline() && !$deletedPost && ((int)$CORE->user->get('id') == (int)$arr['author'] || ($CORE->user->hasPermission(PERMISSION_FORUMS_EDIT_POSTS) && $CORE->user->getRank()->int() > $userRank->int())))
								echo '<li><a class="edit" href="', base_url(), '/forums/post/edit?id=', $arr['id'], '" title="Edit">Edit</a></li>';
							
							//Check if we can delete the post
							if ($CORE->user->isOnline() && !$deletedPost && ((int)$CORE->user->get('id') == (int)$arr['author'] || ($CORE->user->hasPermission(PERMISSION_FORUMS_DELETE_POSTS) && $CORE->user->getRank()->int() > $userRank->int())))
								echo '<li><a class="delete post-delete-button" data-post-id="', $arr['id'], '" href="javascript:void(0)" title="Delete">Delete</a></li>';
							
							//Staff is not reportable
							if (!$staffPost)
								echo '<!--<li><a class="report" href="', base_url(), '" title="Report">Report</a></li>-->';
								
							echo '<!--<li><a class="warn" href="', base_url(), '" title="Warn">Warn</a></li>-->';
							
							//Can quote only if the user can post here and the post is not deleted
							if ($userCanPost && !$deletedPost)
								echo '<li><a class="quote post-quote-button" data-post-id="', $arr['id'], '" href="javascript:void(0)" title="Quote">Quote</a></li>';
						
						echo '
						</ul>
					</div>
					<div class="clear"></div>
				</div>
				<!-- Topic Post.End -->';
			}
			unset($arr);
		}
		
        echo '<!-- Actions -->
        <div class="actions_c bottom">';

            if ($CORE->user->isOnline() && (
                $CORE->user->hasPermission(PERMISSION_FORUMS_EDIT_TOPICS) || 
                $CORE->user->hasPermission(PERMISSION_FORUMS_LOCK_TOPICS) || 
                $CORE->user->hasPermission(PERMISSION_FORUMS_UNLOCK_TOPICS) || 
                $CORE->user->hasPermission(PERMISSION_FORUMS_DELETE_TOPICS) ||
                $CORE->user->hasPermission(PERMISSION_FORUMS_MOVE_TOPICS) ||
                $CORE->user->hasPermission(PERMISSION_FORUMS_MAN_STICKY)
            )) {
                echo '<div class="quick_actions">
                    <select name="action" data-stylized="true" onchange="return TopicActions.onSelectChange(this);" data-topic-id="', $topic['id'], '">
                        <option selected disabled>Topic Actions</option>';

                        if ((int)$topic['sticky'] == 0 && $CORE->user->hasPermission(PERMISSION_FORUMS_MAN_STICKY))
                            echo '<option value="sticky">Mark Topic Sticky</option>';

                        if ((int)$topic['sticky'] == 1 && $CORE->user->hasPermission(PERMISSION_FORUMS_MAN_STICKY))
                            echo '<option value="unsticky">Unmark Topic Sticky</option>';

                        if (((int)$topic['author'] == (int)$CORE->user->get('id') || $CORE->user->hasPermission(PERMISSION_FORUMS_EDIT_TOPICS)))
                            echo '<option value="edit">Edit this Topic</option>';

                        if ((int)$topic['locked'] == 0 && $CORE->user->hasPermission(PERMISSION_FORUMS_LOCK_TOPICS))
                            echo '<option value="lock">Lock this Topic</option>';

                        if ((int)$topic['locked'] == 1 && $CORE->user->hasPermission(PERMISSION_FORUMS_UNLOCK_TOPICS))
                            echo '<option value="unlock">Unlock this Topic</option>';

                        if ($CORE->user->hasPermission(PERMISSION_FORUMS_MOVE_TOPICS))
                            echo '<option value="move">Move this Topic</option>';

                        if ($CORE->user->hasPermission(PERMISSION_FORUMS_DELETE_TOPICS))
                            echo '<option value="delete">Delete this Topic</option>';
                        
                    echo '</select>
                </div>';
            }

            //those should show only if we have more than one page
            if ($totalCount > $perPage)
            {
                echo '<ul class="pagination">
                    ', $pagination['previous'], '
                    ', $pagination['pages'], '
                    ', $pagination['next'], '
                </ul>';
            }

        echo '</div><br/><br/><br/>
        <!-- Actions.End -->';

		// Quick Reply
		if ($userCanPost && (int)$topic['locked'] == 0)
		{
			echo '
			<div class="quick_reply topic_post">
				<form method="post" action="', base_url(), '/forums/post/submit">
					<h2>Quick Reply</h2>
					<textarea id="quick_reply_textarea" name="text" placeholder="Enter your message here..."></textarea>
					<input type="hidden" name="topic" value="', $topic['id'], '" />
					', (($CORE->user->hasPermission(PERMISSION_FORUMS_POST_AS_STAFF)) ? '<input type="hidden" value="1" name="staff_post" />' : ''), '
					<input type="submit" class="forum_btn_small" value="Post">
					<a href="', base_url(), '/forums/post/write?topic=', $topic['id'], '" class="forum_btn_large dark advanced_post" id="go-advanced-post">Advanced post</a>
				</form>
			</div>';
		}
		?>

	</div>
</div>