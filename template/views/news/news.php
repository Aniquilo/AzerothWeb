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
						
						
						
						
						
						<div class="row">
                <?php
                if ($count > 0)
                {
                    foreach ($results as $arr)
                    {

                        echo '
							<div class="news-list-item">
								<a href="', base_url(), '/news/view?id=', $arr['id'], '" class="news-list-item-details-title">
									', htmlspecialchars(stripslashes($arr['title'])), '
								</a>
								<span class="news-list-item-details-time">
									', date('d M, Y', strtotime($arr['added'])), '
								</span>
								<div class="news-list-item-cover">
									<a href="', base_url(), '/news/view?id=', $arr['id'], '"><img src="', base_url(), '/template/style/images/news/', $arr['id'], '.jpg"></a>
								</div>
								<div class="news-list-item-details">
									<span class="news-list-item-details-teaser green">
									', htmlspecialchars(stripslashes($arr['shortText'])), '
									</span>
									<a href="', base_url(), '/news/view?id=', $arr['id'], '" class="news-list-item-details-more">
									Leer mas... <i class="fal fa-angle-right"></i>
									</a>
								</div>
							</div>
									
						';						
                    }
                }
                else
                {
                    echo 'No hay noticias pra mostrar';
                }
                ?>						
						

							<!-- <div class="news-list-item">
								<a href="#" class="news-list-item-details-title">
									Wrath of the Lich King Realm Details
								</a>
								<span class="news-list-item-details-time">
									Published 2 days ago
								</span>
								<div class="news-list-item-cover">
									<a href="#"><img src="#/template/style/images/news/0001.jpg"></a>
								</div>
								<div class="news-list-item-details">
									<span class="news-list-item-details-teaser green">
									As the release date is fast approaching, we would like to provide an update regarding our upcoming Wrath of the Lich King realm, Fordring!
									</span>
									<a href="#" class="news-list-item-details-more">
									Read more <i class="fal fa-angle-right"></i>
									</a>
								</div>
							</div>-->
							
							

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
					
						


                      <!--END OF ABOUT TEXT-->
					  </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->