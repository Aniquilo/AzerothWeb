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
	<a href="<?php echo base_url(), '/forums/search'; ?>">Search</a>
</div>

<div class="container main-wide forum-bg">
	<div class="forum-padding search-page">
		
		<!-- Forum Header -->
		<div class="topic_header">
			<div class="topic_title">
                <h1>Search Results</h1>
				<h3>results for "<?=($q ? $q : '')?>"</h3>
			</div>
			<h4><b><?=$totalCount?></b> results</h4>
		</div>
		<!-- Forum Header.End -->

		<?php
		if ($pagination && $countOnPage > 2 && $totalCount > $perPage)
		{
			echo '
			<!-- Actions -->
			<div class="actions_c clearfix">';

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
		
		if ($results)
		{
			//loop the records
			foreach ($results as $i => $arr)
			{
				echo '
				<!-- Topic Post -->
				<div class="topic_post', ($arr['staffPost'] ? ' admin_post' : ''), '" id="post-', $arr['id'], '">';
					
					if ($arr['staffPost'])
					{
						echo '<!-- Admin Warcry WoW post -->
						<div class="admin_post_logo_wc"></div>';
					}
					
					echo '
					<div class="left_side">
					
						<div class="user_avatar">';
							
							//handle avatars
							if ($arr['Avatar']->type() == AVATAR_TYPE_GALLERY)
							{
								echo '<span style="background:url(\'', base_url(), '/resources/avatars/', $arr['Avatar']->string(), '\') no-repeat; background-size: 100%;"></span>';
							}
							else
							{
								echo '<span style="background:url(', $arr['Avatar']->string(), ') no-repeat; background-size: 100%;"></span>';
							}
						
						echo '
						</div>
						
						<div class="user_info">
							<div class="usr_and_pr">
								<a href="', base_url(), '/profile?uid=', $arr['author'], '" class="username">', $arr['author_str'], '</a>
							</div>
							<h3>', $arr['userRank']->string(), '</h3>
						</div>
					
					</div>
                    <div class="right_side">
                        <div class="post_header">
                            <a href="', base_url(), '/forums/topic?id=', $arr['topic'], '&p=', $arr['postPage'], '#post-', $arr['id'], '" title="Go to the topic">', $arr['topic_name'], '</a>
                            ', ($arr['forumInfo'] ? '<p>'.$arr['forumInfo']['name'].'</p>' : ''), '
                        </div>
						<div class="post_container">
						', $arr['text'], '
						</div>
						<ul class="post_controls">
							<li class="post_date">', $arr['added'], '</li>
						</ul>
					</div>
					<div class="clear"></div>
				</div>
				<!-- Topic Post.End -->';
			}
			unset($arr);
		}
		else
		{
			echo '<h2 class="no-results">No matching content was found.</h2>';
        }
        
        echo '<!-- Actions -->
        <div class="actions_c bottom">';
 
            //those should show only if we have more than one page
            if ($pagination && $totalCount > $perPage)
            {
                echo '<ul class="pagination">
                    ', $pagination['previous'], '
                    ', $pagination['pages'], '
                    ', $pagination['next'], '
                </ul>';
            }

        echo '</div><br/><br/><br/>
        <!-- Actions.End -->';
        ?>

	</div>
</div>