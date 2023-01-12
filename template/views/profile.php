<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

  	<div class="container_2 centered">
    
        <!-- Main Account info -->
        <div class="account_info_cont user_profile" style="margin-top: 10px;">
            <div class="account_info">
        
                <?php
                $CORE->loadLibrary('raf');
                $raf = new RAF();
                
                echo '
                <div class="account_avatar">
                    <div id="avatar">';
                
                    //handle avatars
                    if ($avatar->type() == AVATAR_TYPE_GALLERY)
                    {
                        echo '<span style="background:url(\'', base_url(), '/resources/avatars/',  $avatar->string(), '\') no-repeat; background-size: 100%;"></span>';
                    }
                    else
                    {
                        echo '<span style="background:url(\'', $avatar->string(), '\') no-repeat; background-size: 100%;"></span>';
                    }
                    
                echo '
                    </div>
                    <div class="account_avatar_frame"></div>
                </div>';
        
                echo '
                <ul class="account_info_main">
                    <li id="displayname"><span>Display name:</span><p>', $userInfo['displayName'], '</p></li>
                    <li id="rank"><span>Rank:</span><p>', $userRank->string(), '</p></li>
                    <li><span>Country:</span><p>', CountriesData::get($userInfo['country']), '</p></li>
                    ', (isset($userInfo['joindate']) ? '<br/><li><span>Member since:</span><p>'.$userInfo['joindate'].'</p></li>' : ''), '
                </ul>
                
                <ul class="account_info_second">
                    <li><span>Referred members:</span><p>', $raf->GetReferralsCount($userInfo['id']),'</p></li>
                    <br/>
                    <li><span>Forum Topics:</span><p>', WCF::getUserTopicsCount($userInfo['id']), '</p></li>
                    <li><span>Forum Posts:</span><p>', WCF::getUserPostsCount($userInfo['id']), '</p></li>
                </ul>';
                
                unset($raf);
                ?>
            
                <div class="clear"></div>
            </div>
        </div>
        <!-- Main Account info.End -->
        
        <!-- Characters -->
        <?php if (count($realms) > 0) { ?>
            <div class="user_profile_realms centered">
                <h2>Characters</h2>

                <div class="realms">
                    <?php foreach ($realms as $realm) { ?>
                        <?php if ($realm['characters'] && count($realm['characters']) > 0) { ?>
                            <div class="realm">
                                <h3><?=$realm['name']?></h3>
                                <div class="characters">
                                    <?php foreach ($realm['characters'] as $character) { ?>
                                        <div class="character">
                                            <a href="<?=base_url()?>/armory/character?realm=<?=$realm['id']?>&character=<?=$character['name']?>" id="character_avatar">
                                                <img src="<?=base_url()?>/resources/armory/avatars/<?=$character['avatar']?>.gif" class="avatar"/>
                                                <div id="inset_shadow"></div>
                                            </a>
                                            <div id="character_texts">
                                                <a class="name" href="<?=base_url()?>/armory/character?realm=<?=$realm['id']?>&character=<?=$character['name']?>"><?=$character['name']?></a>
                                                <div class="info">
                                                    <span class="c<?=$character['class']?>"><?=$character['level']?> <?=$character['raceName']?> <?=$character['className']?></span><br />
                                                    <?php if ($character['guild']) { ?><?=$character['guild']['name']?><?php } ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php } ?>
                    <div class="correct-margins"></div>
                </div>
            </div>
        <?php } ?>

        <?php if ($latestPosts) { ?>
            <div class="user_posts centered">
                <h2>Latest Posts</h2>

                <?php
                //loop the records
                foreach ($latestPosts as $arr)
                {
                    $text = WCF::parsePostText($arr['id'], $arr['text']);
                    
                    //Post page number
                    $pageNumber = WCF::calculatePostPage($arr['id']);
                
                    //format the time
                    $arr['added'] = date('D M j, Y, h:i A', strtotime($arr['added']));
                    
                    echo '
                    <div class="post" id="post-', $arr['id'], '">
                        <h3 class="title">', (WCF::parseTitle($arr['title'])), '</h3>
                        <h4>', $arr['added'], '</h4>
                        <div class="post_container">', $text, '</div>
                        <a class="jump" href="', base_url(), '/forums/topic?id=', $arr['topic'], '&p=', $pageNumber, '#post-', $arr['id'], '">Go to Topic</a>
                    </div>';
                }
                ?>
            </div>
        <?php } ?>
            
    </div>
    
</div>