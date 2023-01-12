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

        <div class="container_3 articles">
        
            <div class="header">
                <h1><b><?php echo $count; ?></b> Articles</h1>
            </div>
            
            <div class="articles_cont">
                <?php
                if ($count > 0)
                {
                    foreach ($results as $arr)
                    {
                        echo '
                        <div class="article_short">
                            <a class="title" href="', base_url(), '/articles/view?id=', $arr['id'], '">', ArticlesLib::parseTitle($arr['title']), '</a>
                            <h4>', date('d M, Y', strtotime($arr['added'])), ' | ', $arr['views'], ' Views</h4>
                            <p>', ArticlesLib::parseTitle($arr['short_text']), '</p>
                            <a class="read_more" href="', base_url(), '/articles/view?id=', $arr['id'], '">Read More</a>
                        </div>';
                    }
                }
                else
                {
                    echo 'There are no articles.';
                }
                ?>
            </div>

            <?php
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
    <!-- Main side.End-->

    <?php
    //include the sidebar
    include ROOTPATH . '/template/views/template/sidebar.php';
    ?>

    <div class="clear"></div>
</div>