<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class Tooltip extends Core_Controller
{
    public function __construct()
	{
        parent::__construct();
    }

    public function item()
    {
        $entry = ((isset($_GET['entry'])) ? (int)$_GET['entry'] : false);
        $realmId = ((isset($_GET['realm'])) ? (int)$_GET['realm'] : $this->user->GetRealmId());
        $ench = ((isset($_GET['ench'])) ? (int)$_GET['ench'] : false);

        if (!$entry)
        {
            $this->JsonError('Invalid spell entry!');
        }

        if (!$this->realms->realmExists($realmId))
        {
            $this->JsonError('Invalid realm!');
        }
        
        $realm = $this->realms->getRealm($realmId);

        $cacheKey = 'world/items/tooltip_'.($realm->getConfig('wowdb_lang') != 'en' ? $realm->getConfig('wowdb_lang').'_' : '').$realmId.'_'.$entry;

        if ($ench)
        {
            $cacheKey .= '_ench_'.$ench;
        }

        $cached = $this->cache->get($cacheKey);
		
		if ($cached !== false)
		{
			$this->Json(array('html' => $cached));
		}
        else
        {
            $url = $realm->getConfig('wowdb_url').'/?item='.$entry.'&domain='.$realm->getConfig('wowdb_lang').($ench ? '&ench='.$ench : '').'&power';
            $content = $this->getRemotePage($url);
            
            if ($content !== false)
            {
                $tooltip = false;
                
                if (preg_match("/tooltip\_[a-z]{0,4}\:[\s?]\'(.*)\'[\n]+/i", $content, $match))
                {
                    $tooltip = stripslashes($match[1]);
                }
                
                // Cache it for an year
                $this->cache->store($cacheKey, $tooltip, false);

                $this->Json(array('html' => $tooltip));
            }
        }

        $this->JsonError('Unable to find item!');
    }
    
    public function spell()
    {
        $entry = ((isset($_GET['entry'])) ? (int)$_GET['entry'] : false);
        $realmId = ((isset($_GET['realm'])) ? (int)$_GET['realm'] : $this->user->GetRealmId());

        if (!$entry)
        {
            $this->JsonError('Invalid spell entry!');
        }

        if (!$this->realms->realmExists($realmId))
        {
            $this->JsonError('Invalid realm!');
        }
        
        $realm = $this->realms->getRealm($realmId);

        $cacheKey = 'world/spells/tooltip_'.($realm->getConfig('wowdb_lang') != 'en' ? $realm->getConfig('wowdb_lang').'_' : '').$realmId.'_'.$entry;
        $cached = $this->cache->get($cacheKey);
		
		if ($cached !== false)
		{
			$this->Json(array('html' => $cached));
		}
        else
        {
            $url = $realm->getConfig('wowdb_url').'/?spell='.$entry.'&domain='.$realm->getConfig('wowdb_lang').'&power';
            $content = $this->getRemotePage($url);
            
            if ($content !== false)
            {
                $tooltip = false;
                
                if (preg_match("/tooltip\_[a-z]{0,4}\:[\s?]\'(.*)\'\,/i", $content, $match))
                {
                    $tooltip = stripslashes($match[1]);
                }
                
                // Cache it for an year
                $this->cache->store($cacheKey, $tooltip, false);

                $this->Json(array('html' => $tooltip));
            }
        }

        $this->JsonError('Unable to find spell!');
    }
}