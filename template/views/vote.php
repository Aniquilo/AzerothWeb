<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

  	<div class="container_2 account">
        <div class="cont-image">
    
            <div class="error-holder">
                <?php $ERRORS->PrintAny('vote'); ?>
            </div>
   
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Vote</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
            <!-- VOTE -->
            <div class="vote-page">
                
                <div class="page-desc-holder">
                    With every vote you will recieve <b>2 silver</b> coins. <br/>
                    You can spend your coins for amazing stuff on our website.
                </div>
                
                <div class="container_3 account-wide" align="center">
                    
                    <ul class="vote-sites-cont">
                        
                        <?php
                        $VoteSites = new VoteSitesData();
                        
                        foreach ($VoteSites->data as $id => $data)
                        {								
                            $cooldown = $CORE->user->getCooldown('votingsite'.$id);
                            
                            //if the site is availible for voting
                            if (time() > $cooldown)
                            {
                                echo '
                                <li>
                                    <a href="', base_url(), '/vote/process?site=', $id, '" onclick="window.open(\'', $data['url'], '\', \'_newtab\'); return true;">
                                    <div class="vote-site-image" style="background-image:url(\'', $data['img'], '\')"></div>
                                    <p>Vote Now!</p>
                                    </a>
                                </li>';
                            }
                            else
                            {
                                //convert the cooldown to minutes and stuff
                                $cooldownArr = $CORE->convertCooldown($cooldown);
                                
                                echo '
                                <li class="not-active">
                                    <a href="', $data['url'], '">
                                        <div class="vote-site-image" style="background-image:url(\'', $data['img'], '\')"></div>
                                        <p>';
                                        
                                        if ($cooldownArr['hours'] > 0)
                                        {
                                            echo $cooldownArr['hours'], ' hours until vote!';
                                        }
                                        else if ($cooldownArr['minutes'] > 0)
                                        {
                                            echo $cooldownArr['minutes'], ' minutes until vote!';
                                        }
                                        else if ($cooldownArr['seconds'] > 0)
                                        {
                                            echo $cooldownArr['seconds'], ' seconds until vote!';
                                        }
                                        
                                        echo ' 
                                        </p>
                                    </a>
                                </li>';
                                
                                unset($cooldownArr);
                            }
                            unset($cooldown);
                        }
                        
                        unset($VoteSites, $data, $id);
                        ?>
                                                    
                    </ul>
                    
                </div>
                
            </div>
            <!-- VOTE.End -->
    
        </div>
	</div>
 
</div>