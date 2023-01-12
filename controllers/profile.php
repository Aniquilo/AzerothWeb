<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Profile extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }
    
    public function index()
    {
        $uid = isset($_GET['uid']) ? (int)$_GET['uid'] : false;

        if (!$uid)
        {
            $this->tpl->Message('User Profile', 'Oops...', 'The user profile was not found!');
        }

        $userInfo = false;
        $userRank = false;
        $Avatar = false;

        $res = $this->db->prepare("SELECT `id`, `displayName`, `rank`, `avatar`, `avatarType`, `gender`, `country` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
		$res->bindParam(':acc', $uid, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			$userInfo = $res->fetch();
        }
        else
        {
            $this->tpl->Message('User Profile', 'Oops...', 'The user profile was not found!');
        }
        
        $userRank = new UserRank($userInfo['rank']);
        
        //prepare the avatar
        if ((int)$userInfo['avatarType'] == AVATAR_TYPE_GALLERY)
        {
            $gallery = new AvatarGallery();
            $Avatar = $gallery->get((int)$userInfo['avatar']);
            unset($gallery);
        }
        else if ((int)$userInfo['avatarType'] == AVATAR_TYPE_UPLOAD)
        {
            $Avatar = new Avatar(0, $userInfo['avatar'], 0, AVATAR_TYPE_UPLOAD);
        }

        $authInfo = $this->authentication->getAccountById($uid);

        if ($authInfo)
        {
            $userInfo['joindate'] = date('M j, Y, H:i', strtotime($authInfo['joindate']));
        }

        $this->loadLibrary('forums.base');

        $latestPosts = WCF::getUserLatestPosts($uid, 5);
        
        // Collect realms info
        $realms = array();
        
        //Get characters, guilds, items for each realm
        foreach ($this->realms->getRealms() as $realm)
        {
            $characters = false;

            if ($realm->checkCharactersConnection())
            {
                $characters = $realm->getCharacters()->getAccountCharacters($uid);

                if ($characters)
                {
                    foreach ($characters as $k => $character)
                    {
                        $characters[$k] = array(
                            'guid' => $character['guid'],
                            'name' => $character['name'],
                            'race' => $character['race'],
                            'gender' => $character['gender'],
                            'class' => $character['class'],
                            'level' => $character['level'],
                            'className' => $this->realms->getClassString($character['class']),
                            'raceName' => $this->realms->getRaceString($character['race']),
                            'avatar' => $this->realms->getCharacterAvatar($character),
                            'guild' => $realm->getCharacters()->getCharacterGuild($character['guid'])
                        );
                    }
                }
            }

            $realms[] = array(
                'id' => $realm->getId(),
                'name' => $realm->getName(),
                'characters' => $characters
            );
        }

        $this->tpl->SetTitle('User Profile');
        $this->tpl->SetSubtitle('User Profile');
        $this->tpl->LoadHeader();

        //Print the page view
        $this->tpl->LoadView('profile', array(
            'userInfo' => $userInfo,
            'userRank' => $userRank,
            'avatar' => $Avatar,
            'realms' => $realms,
            'latestPosts' => $latestPosts
        ));

        $this->tpl->LoadFooter();
    }
}