<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>
<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />

    <title>Admin Control Panel</title>

    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/reset.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/visualize.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/buttons.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/checkboxes.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/inputtags.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/markitup.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/jquery.Jcrop.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/main.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/datatables.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/fileuploader.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/shadowbox.css" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/edit-box.css?v=1" />
    <link rel="stylesheet" href="<?=base_url()?>/admin_template/css/media.css" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.css" />

    <!--[if lt IE 9]>
    <link rel="stylesheet" href="/admincp/css/ie.css" />
    <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
	<script type="text/javascript">
		var $BaseURL = "<?=base_url()?>";
		var $WOWDBURL = "<?=$CORE->realms->getFirstRealm()->getConfig('wowdb_url')?>";
	</script>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
    <script>
		var $currentTab = null;
		
		$(function()
		{
			//set the current tab variable
			$currentTab = $('#maintab');
		});
	</script>
  </head>

  <body>

        <div id="container">
        
          <header>
          
            <!-- Logo -->
            <h1 id="logo">Admin Control Panel</h1>
          
            <!-- User info -->
            <div id="userinfo">
              <div class="intro">
                <br>Welcome <strong><?php echo $CORE->user->get('displayName'); ?></strong>!&nbsp;&nbsp;&nbsp;&nbsp;<br />
              </div>
            </div>
          
          </header>
        
          <!-- The application "window" -->
          <div id="application">
			
            <?php
            $MENU = array(
                0 => array(
                    'title' => 'Dashboard',
                    'page' => 'home',
                    'icon' => 'dashboard',
                    'permission' => false
                ),
                1 => array(
                    'title' => 'News Management',
                    'page' => 'news',
                    'icon' => 'subject',
                    'permission' => PERMISSION_NEWS
                ),
                2 => array(
                    'title' => 'Articles Management',
                    'page' => 'articles',
                    'icon' => 'view_carousel',	
                    'permission' => PERMISSION_ARTICLES
                ),
                3 => array(
                    'title' => 'Item Store', 
                    'page' => 'store', 
                    'icon' => 'shopping_cart', 
                    'permission' => PERMISSION_STORE
                ),
                4 => array(
                    'title' => 'Armor Sets', 
                    'page' => 'armorsets', 
                    'icon' => 'shopping_cart', 
                    'permission' => PERMISSION_PSTORE
                ),
                5 => array(
                    'title' => 'Media',
                    'page' => 'media,movie-add,screenshots',
                    'icon' => 'perm_media',
                    'permission' => array(PERMISSION_MEDIA_VIDEOS, PERMISSION_MEDIA_SREENSHOTS)
                ),
                6 => array(
                    'title' => 'Forums Management',
                    'page' => 'forums', 
                    'icon' => 'forum', 
                    'permission' => array(PERMISSION_FORUMS, PERMISSION_FORUM_CATS)
                ),
                7 => array(
                    'title' => 'Logs',
                    'page' => 'logs',
                    'icon' => 'library_books',
                    'permission' => PERMISSION_LOGS
                ),
                8 => array(
                    'title' => 'Promo Codes', 
                    'page' => 'pcodes', 
                    'icon' => 'redeem', 
                    'permission' => PERMISSION_PROMO_CODES
                ),
                9 => array(
                    'title' => 'Users', 
                    'page' => 'users', 
                    'icon' => 'perm_identity', 
                    'permission' => PERMISSION_PREV_USERS
                ),
                10 => array(
                    'title' => 'Bug Tracker', 
                    'page' => 'bugtracker', 
                    'icon' => 'view_list', 
                    'permission' => PERMISSION_PREV_BUGTRACKER
                ),
                11 => array(
                    'title' => 'GM Tickets', 
                    'page' => 'tickets', 
                    'icon' => 'visibility', 
                    'permission' => PERMISSION_TICKETS
                ),
                12 => array(
                    'title' => 'Access Role Management',
                    'page' => 'rbac',
                    'icon' => 'lock',
                    'permission' => PERMISSION_MAN_RBAC
                ),
                13 => array(
                    'title' => 'Website Notice',
                    'page' => 'notice',
                    'icon' => 'warning',
                    'permission' => PERMISSION_MAN_NOTICE
                ),
                14 => array(
                    'title' => 'Polls Management',
                    'page' => 'polls',
                    'icon' => 'polls',
                    'permission' => PERMISSION_MAN_POLLS
                )
            );
            ?>
            
            <!-- Primary navigation -->
            <nav id="primary">
              <ul>
				
                <?php
                //Print the menu
                foreach ($MENU as $i => $menuItem)
                {
                    $isAllowed = false;
                    
                    //Determine if we're allowed to use this button, pages...
                    if (!$menuItem['permission'])
                    {
                        //the page does not require permissions
                        $isAllowed = true;
                    }
                    else if (!is_array($menuItem['permission']) && $CORE->user->hasPermission($menuItem['permission']))
                    {
                        //the page is allowed, no multiple pages
                        $isAllowed = true;
                    }
                    else if (is_array($menuItem['permission']))
                    {
                        //we've got multiple permissions
                        foreach ($menuItem['permission'] as $reqPermission)
                        {
                            if ($CORE->user->hasPermission($reqPermission))
                            {
                                //if the user meets one of the required permissions
                                $isAllowed = true;
                                break;
                            }
                        }
                    }
                    
                    //check if we have permissions to use the given page
                    if ($isAllowed)
                    {
                        $isActive = false;
                        
                        //check for multiple pages activation
                        if (strstr($menuItem['page'], ','))
                        {
                            $pages = explode(',', $menuItem['page']);
                            
                            //check if the current page
                            if (in_array($CORE->controller, $pages))
                                $isActive = true;
                        }
                        else
                        {
                            $isActive = ($menuItem['page'] == $CORE->controller) ? true : false;
                        }
                        
                        echo '
                        <li ', ($isActive ? 'class="current"' : ''), '>
                            <a href="', base_url(), '/admin/', (isset($pages) ? $pages[0] : $menuItem['page']), '">
                            <i class="icon large material-icons">', $menuItem['icon'], '</i>
                            ', $menuItem['title'], '
                            </a>
                        </li>';
                        
                        unset($isActive, $pages);
                    }
                    
                    unset($isAllowed);
                }
                unset($MENU, $i, $menuItem);
              	?>
                <div class="clear"></div>            
              </ul>
            </nav>
          