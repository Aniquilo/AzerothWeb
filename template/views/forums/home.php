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
	
<a href="<?php echo base_url(); ?>/support/rules" class="important_notice"><p>Please read and accept the rules and regulations before communicating with other members!</p></a>

<div class="page-header-navigation">
    <a href="<?php echo base_url(), '/forums'; ?>">Board Index</a>
</div>

<?php

if ($categories)
{
    foreach ($categories as $category)
    {
        echo '
            <div class="container main-wide">
                <div class="wide-padding">
                    <h1 class="category-title"><a href="javascript:void(0)" title="', $category['name'], '">', WCF::parseTitle($category['name']), '</a></h1>';
                    
                    $res2 = $DB->prepare("SELECT * FROM `wcf_forums` WHERE `category` = :id ORDER BY `position` ASC;");
                    $res2->bindParam(':id', $category['id'], PDO::PARAM_INT);
                    $res2->execute();

                    //Check if we have any forums in this category
                    if ($category['forums'])
                    {
                        //WoW Classes Layout
                        if ($CORE->hasFlag((int)$category['flags'], WCF_FLAGS_CLASSES_LAYOUT))
                        {
                            echo '<div class="classes">';
                                
                                foreach ($category['forums'] as $forum)
                                {
                                    $classSimple = strtolower(str_replace(' ', '', $CORE->realms->getClassString($forum['class'])));
                                    
                                    //OMG It's a class row
                                    echo '
                                    <ul class="class_row ', $classSimple, '">
                                        <li class="icon"><div class="image_icon"></div></li>
                                        <li class="info">
                                            <a href="', base_url(), '/forums/forum?id=', $forum['id'], '">
                                                <h1>', $CORE->realms->getClassString($forum['class']), '</h1>
                                                <h2>', WCF::getTopicsCount($forum['id']), ' Topics</h2>
                                            </a>
                                        </li>
                                    </ul>';
                                    
                                    unset($classSimple);
                                }

                                echo '<div class="clear"></div>';
                            echo '</div>';
                        }
                        //Default Layout
                        else
                        {
                            foreach ($category['forums'] as $forum)
                            {
                                $lastTopic = ((int)$forum['lasttopic_id'] > 0) ? WCF::getTopicInfo($forum['lasttopic_id']) : false;
                                
                                echo '
                                <ul class="forum_row">
                                    <li class="icon">
                                        <img src="', base_url(), '/template/forums/style/icons/forum_read.png" width="56" height="53" title="No unread posts" />
                                    </li>
                                    <li class="forum_title_desc">
                                        <a href="', base_url(), '/forums/forum?id=', $forum['id'], '">
                                            <h1>', WCF::parseTitle($forum['name']), '</h1>
                                            <h2>', WCF::parseTitle($forum['description']), '</h2>
                                        </a>
                                    </li>
                                    <li class="post">
                                        <p>', WCF::getForumPostsCount($forum['id']), '</p>
                                    </li>
                                    <li class="topics">
                                        <p>', WCF::getTopicsCount($forum['id']), '</p>
                                    </li>
                                    <li class="lastpost">';
                                        
                                        if ($lastTopic)
                                        {
                                            echo '
                                            <p class="topic_title"><a href="', base_url(), '/forums/topic?id=', $lastTopic['id'], '">', WCF::parseTitle($lastTopic['name']), '</a></p>
                                            <p class="by"><a href="', base_url(), '/profile?uid=', $lastTopic['author'], '">', $lastTopic['author_str'], '</a></p>
                                            <p class="postdate">', $lastTopic['added'], '</p>';
                                        }
                                        
                                    echo '
                                    </li>
                                </ul>';
                            }
                        }
                    }
                    else
                    {
                        echo '<div><h2>This category is empty.</h2></div>';
                    }
        
        echo '	</div>
            </div>';
    }
}
else
{
    echo '<div class="container main-wide">
            <div class="wide-padding" style="padding: 40px;">
                <h2>There are no forum categories.</h2>
            </div>
        </div>';
}
?>


                      <!--END OF ABOUT TEXT-->
					  </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->