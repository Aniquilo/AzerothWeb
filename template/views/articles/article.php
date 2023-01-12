<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

    <?php
    if ($config['IMPORTANT_NOTICE_ENABLE'] == true)
    {
        echo '<div class="important_notice">'. $config['IMPORTANT_NOTICE_MESSAGE'] .'</div>';
    }
    ?>

    <!-- Main Side -->
    <div class="main_side">

        <div class="article_top">
            <a id="all_articles" class="article_button" href="<?php echo base_url(); ?>/articles">See all Articles</a>
            <?php
                //Lookup next article id
                if ($NextArticle = ArticlesLib::getNextArticle($row['id']))
                {
                    echo '<a id="next_article" class="article_button" href="', base_url(), '/articles/view?id=', $NextArticle['id'], '">Next Article</a>';
                }
            ?>
        </div>

        <div class="article">
            <h1 id="title"><?php echo $row['title']; ?></h1>
            <h5 id="subinfo"><b><?php echo date('d M, Y', strtotime($row['added'])); ?></b><?php echo $row['views']; ?> Views &nbsp;&nbsp;&nbsp; <?php echo ArticlesLib::getCommentsCount($row['id']); ?> Comments</h5>
            <div id="post" class="tinymce-content"><?=$row['text']?></div>
            
            <div class="comments">
                <h2>Comment</h2>
                
                <?php
                //Check if comments are enabled
                if ($row['comments'] == '1')
                {
                    if (!$CORE->user->isOnline())
                    {
                        echo '
                        <!-- if not logged in -->
                        <div class="not_login">Please log in to comment.</div>';
                    }
                    else
                    {
                        echo '
                        <!-- if logged in -->
                            <div class="post_comment">
                                <form method="post" action="#" id="quick-comment">
                                    <textarea placeholder="Type in your comment..." name="text" id="textarea"></textarea>
                                    <input type="hidden" value="', $row['id'], '" name="article" />
                                    <input type="submit" value="Post comment" />
                                </form>
                            </div>
                        <!-- if logged in.end -->';
                    }
                }
                ?>
                
                <div class="comments-cont">
                    
                    <?php
                    if ($comments)
                    { 
                        //loop the records
                        foreach ($comments as $arr)
                        {
                            echo '
                            <div class="comment_row" data-id="', $arr['id'], '">
                                <div class="headline">
                                    <p><a href="', base_url(), '/profile?uid=', $arr['author'], '">', $arr['author_str'], '</a> said:</p>
                                    <span id="time" data-original="', $arr['added'], '">NaN</span>
                                </div>
                                <p class="content">', ArticlesLib::parseTitle($arr['text']), '</p>';
                                
                                if ($CORE->user->isOnline() && ((int)$CORE->user->get('id') == (int)$arr['author'] || $CORE->user->hasPermission(PERMISSION_ARTICLE_COMMENT_DELETE)))
                                {
                                    echo '<div class="footer">
                                        <div class="links">';

                                        if ($CORE->user->isOnline() && (int)$CORE->user->get('id') == (int)$arr['author'])
                                        {
                                            echo '<a href="javascript:void(0)" onclick="return Article.EditComment(this);">Edit</a> | ';
                                        }

                                        if ($CORE->user->isOnline() && ((int)$CORE->user->get('id') == (int)$arr['author'] || $CORE->user->hasPermission(PERMISSION_ARTICLE_COMMENT_DELETE)))
                                        {
                                            echo '<a href="javascript:void(0)" onclick="return Article.DeleteComment(this);">Delete</a>';
                                        }

                                        echo '</div>
                                        <div class="clear"></div>
                                    </div>';
                                }

                            echo '</div>';
                        }
                    }
                    unset($arr);
                    ?>
                    
                </div>
                
                <?php
                //Pagination
                if ($pages)
                {
                    echo '
                    <!-- Pagination -->
                    <div class="pagination-holder">
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
                ?>
                
            </div>
            
        </div>
            
        <div class="clear"></div>
        
        <script type="text/javascript">
        $(document).ready(function()
        {
            Article.ArticleID = <?php echo $ArticleID; ?>;
            Article.PerPage = <?php echo $perPage; ?>;
            Article.BindHandlers();
            Article.UpdateTimespans();
        });
        </script>
        
    </div>
    <!-- Main side.End-->

    <?php include ROOTPATH . '/template/views/template/sidebar.php'; ?>

    <div class="clear"></div>

</div>