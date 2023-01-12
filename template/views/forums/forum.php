<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

            <!--header-->
            
            <div class="page-header">
                
            <a href="<?=base_url()?>"><img id="logo-header" src="<?=base_url()?>/template/style/images/logo-header.png"/></a>
                
                 <div class="page">
                
                    <div class="page-top"></div>
                    <div class="page-body">
                    

                        <!--HERE IS ABOUT TEXT-->

                        <div class="page-content">
                        
                        <h1><b><?php echo $count; ?></b> Noticias Publicadas </h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>

<!--<a href="#" class="important_notice"><p>Please read and accept the rules and regulations before communicating with other members!</p></a>-->

<div class="page-header-navigation">
	<a href="<?php echo base_url(), '/forums'; ?>">Board Index</a>
	<a href="<?php echo base_url(), '/forums?category=', $forum['category']; ?>"><?php echo WCF::parseTitle($forum['category_name']); ?></a>
	<a href="<?php echo base_url(), '/forums/forum?id=', $forum['id']; ?>"><?php echo WCF::parseTitle($forum['name']); ?></a>
</div>

<div class="container main-wide forum-bg">
	<div class="forum-padding">
		
		<!-- Forum Header -->
		<div class="forum_header">
			<div class="forum_title">
				<h1><?php echo WCF::parseTitle($forum['name']); ?></h1>
				<h3><?php echo WCF::parseTitle($forum['description']); ?></h3>
			</div>
			<h4><b><?php echo $totalCount; ?></b> topics</h4>
		</div>
		<!-- Forum Header.End -->
		
		<?php
		
		if ($canStartTopic || ($countOnPage > 5 && $totalCount > $perPage))
		{
			echo '
			<!-- Actions -->
			<div class="actions_c">';
			
				if ($canStartTopic)
					echo '<a href="', base_url(), '/forums/topic/create" class="forum_btn_large">New Topic</a>';

				if ($countOnPage > 5 && $totalCount > $perPage)
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
		
		echo '
		<!--<ul class="topic_header">
			<li class="topic">Topic</li>
			<li class="lastpost">Last post</li>
		</ul>-->';
		
		if ($totalCount > 0)
		{
			//loop the records
			foreach ($topics as $arr)
			{
				if ($author = WCF::getAuthorById($arr['author']))
				{
					$arr['author_str'] = $author;
				}
				else
				{
					$arr['author_str'] = 'Unknown';
				}
				unset($author);
				
				//format the time
				$arr['added'] = date('D M j, Y, h:i a', strtotime($arr['added']));
				
				//Get the last post
				$lastPost = WCF::getTopicLastPost($arr['id']);
				
				echo '
				<ul class="topic_row', ((int)$arr['locked'] == 1 ? ' locked' : ''), ((int)$arr['sticky'] == 1 ? ' sticky' : ''), '">
					<li class="icon">
						<img src="', base_url(), '/template/forums/style/icons/topic_unread', ((int)$arr['locked'] == 1 ? '_locked' : ''), '.png" width="55px" height="39px"/>
					</li>
					<li class="topic_title_by_date">
						<h1><a href="', base_url(), '/forums/topic?id=', $arr['id'], '">', WCF::parseTitle($arr['name']), '</a></h1>
						<p>Created by <a href="#">', $arr['author_str'], '</a>, ', $arr['added'], '</p>
					</li>
					<li class="lastpost">';
					
						if ($lastPost)
						{
							echo '
							<h4>by <a href="', base_url(), '/profile?uid=', $lastPost['author'], '">', $lastPost['author_str'], '</a></h4>
							<h5>', $lastPost['added'], '</h5>
							<a href="', base_url(), '/forums/topic?id=', $arr['id'], '&p=', $lastPost['page_number'], '#post-', $lastPost['id'], '" class="go_to_lastpost" title="Go to last post"><p>Go to last post</p></a>';
						}
						
					echo '
					</li>
				</ul>';
			}
			unset($topics_res, $arr, $lastPost);
		}
		else
		{
			echo '<h2>There are no topics.</h2>';
		}
		
		if (($canStartTopic && $countOnPage > 5) || $totalCount > $perPage)
		{
			echo '
			<!-- Actions -->
			<div class="actions_c bottom">';
			
				//this button should show only when we have more than 5 posts on this apge
				if ($canStartTopic && $countOnPage > 5)
					echo '<a href="', base_url(), '/forums/topic/create" class="forum_btn_large">New Topic</a>';
				
				//those should show only if we have more than one page
				if ($totalCount > $perPage)
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
		?>

	</div>
</div>

                      <!--END OF ABOUT TEXT-->
					  </div>
                </div>
            
                    
                </div>   
            
            <!--end header--> 