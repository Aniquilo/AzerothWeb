<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

require_once ROOTPATH . '/engine/forums_controller.php';

class Search extends Forums_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $this->loadLibrary('forums.parser');
        $this->loadLibrary('pagination.forum');

        $string = isset($_GET['q']) ? $_GET['q'] : false;
        if ($string)
            $string = filter_var($string, FILTER_SANITIZE_STRING);
        $page = isset($_GET['p']) ? (int)$_GET['p'] : 1;

        $perPage = (int)$this->config['FORUM']['Posts_Limit'];
        $totalCount = 0;
        $countOnPage = 0;
        $pagination = false;
        $results = false;

        // If we have search keywords
        if ($string)
        {
            // Get excluded topics
            $excludeTopics = WCF::getExcludeTopicsBasedOnRoles($this->user->getRoles());

            // Get the total results count
            $totalCount = WCF::getSearchPostsCount($string, $excludeTopics);

            //Let's setup our pagination
            $pagies = new Pagination();
            $pagies->addToLink('?q='.$string);

            //calculate the pages
            $pagination = $pagies->calculate_pages($totalCount, $perPage, $page);
            $results = WCF::getSearchPosts($string, $pagination['limit'], $excludeTopics);

            // If we have found results
            if ($results)
            {
                //loop the records
                foreach ($results as $i => $arr)
                {
                    // Get forum info
                    $results[$i]['forumInfo'] = WCF::getForumInfo($arr['forum_id']);

                    // Parse bbcodes
                    $results[$i]['text'] = WCF::parsePostText($arr['id'], $arr['text']);
                    
                    // Highlight keywords
                    $results[$i]['text'] = str_ireplace($string, '<span class="highlighted">'.$string.'</span>', $results[$i]['text']);

                    // Get author info
                    if ($userInfo = WCF::getAuthorInfo($arr['author']))
                    {
                        $results[$i]['userRank'] = new UserRank($userInfo['rank']);
                        $results[$i]['author_str'] = $userInfo['displayName'];
                        
                        //prepare the avatar
                        if ((int)$userInfo['avatarType'] == AVATAR_TYPE_GALLERY)
                        {
                            $gallery = new AvatarGallery();
                            $results[$i]['Avatar'] = $gallery->get((int)$userInfo['avatar']);
                            unset($gallery);
                        }
                        else if ((int)$userInfo['avatarType'] == AVATAR_TYPE_UPLOAD)
                        {
                            $results[$i]['Avatar'] = new Avatar(0, $userInfo['avatar'], 0, AVATAR_TYPE_UPLOAD);
                        }
                    }
                    else
                    {
                        $results[$i]['userRank'] = new UserRank(0);
                        $results[$i]['author_str'] = 'Unknown';
                        $results[$i]['author_rank'] = 'Unknown';
                        $gallery = new AvatarGallery();
                        $results[$i]['Avatar'] = $gallery->get(0);
                        unset($gallery);
                    }
                    
                    //format the time
                    $results[$i]['added'] = date('D M j, Y, h:i A', strtotime($arr['added']));
                    
                    //Is staff post
                    $results[$i]['staffPost'] = $this->hasFlag((int)$arr['flags'], WCF_FLAGS_STAFF_POST);

                    //Calculate the post page
                    $results[$i]['postPage'] = WCF::calculatePostPage($arr['id']);
                }
                
                $countOnPage = count($results);
            }
        }

        //Set the title
        $this->tpl->SetTitle('Forums');
        $this->tpl->SetParameter('topbar', true);
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('forums/search', array(
            'q' => $string,
            'perPage' => $perPage,
            'totalCount' => $totalCount,
            'countOnPage' => $countOnPage,
            'pagination' => $pagination,
            'results' => $results,
        ));

        $this->tpl->LoadFooter();
    }
}