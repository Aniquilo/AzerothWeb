<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

  	<div class="container_2 account" align="center">
     	<div class="cont-image">
  
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Select Avatar</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
            <!-- Store Activity -->
            <div class="store-activity">
            
                <div class="page-desc-holder">
                    <?=$config['SiteName']?> offers a unique selection of avatars based on your rank.<br/>
                    Avatars are progressively unlocked as you go up, at the highest rank you may upload<br/>
                    your own avatar. 
                </div>
                
                <div class="container_3 account-wide" align="center">
                    <div class="avatars_groups">
                    
                    <?php
                    //Let's display our galleries
                    $storage = new AvatarGallery();
    
                    foreach ($storage->getGalleries() as $RequiredRank => $avatars)
                    {
                        $GalleryRank = new UserRank($RequiredRank);
                        
                        //make sure we have any avatars in this gallery
                        if (count($avatars) == 0)
                            continue;
                        
						//Staff avatars, skip for users
						if ($RequiredRank >= RANK_STAFF_MEMBER)
						{
							if ($CORE->user->getRank()->int() < RANK_STAFF_MEMBER)
								continue;
						}
						
                        echo '
                        <div class="avatars_group_holder ', ($CORE->user->getRank()->int() < $RequiredRank ? 'not_avaliable' : ''), '">
                            <h1>', $GalleryRank->string(), ' Avatars</h1>
                            <ul class="avatars_group">';
                            
                                //Loop the avatars in this gallery
                                foreach ($avatars as $id => $string)
                                {
                                    echo '
									<li class="avatar-box" ', (($CORE->user->getAvatar()->type() == AVATAR_TYPE_GALLERY && $CORE->user->getAvatar()->int() == $id) ? 'id="active"' : ''), '>
										<a href="#" data-avatar-id="', $id, '" class="clickable-avatar" style="background-image: url(\'', base_url(), '/resources/avatars/', $string, '\');"></a>
									</li>';
                                }
                                unset($id, $string);
                                
                                echo '
                                <div class="clear"></div>
                            </ul>
                        </div>';
                    }
                    
                    unset($storage, $RequiredRank, $avatars, $GalleryRank);
                    ?>
                    
                    </div>                      
                </div>
                            
            </div>
            <!-- Store Activity.End -->
        
        </div>
    </div>
 
</div>

<script type="text/javascript">
$(function()
{
	$('.clickable-avatar').click(function(e)
	{
		var avatarId = $(this).attr('data-avatar-id');
		
		//prevent clicking on the active one
		if (typeof $(this).parent().attr('id') != 'undefined' && $(this).parent().attr('id') == 'active')
			return false;
		
		//submit the new avatar for change
		$.get(
			$BaseURL + '/account/set_avatar?id=' + avatarId,
			function(data)
			{
				//verify success
				if (data == 'OK')
				{
					//Find the active avatar
					$('.avatar-box#active').attr('id', null);
					//Activate the new
					$('.clickable-avatar[data-avatar-id="' + avatarId + '"]').parent().attr('id', 'active');
				}
				else
				{
					$.fn.WarcryAlertBox('open', '<p>' + data + '</p>');
				}
			}
		);
		
		return false;
	});
});
</script>