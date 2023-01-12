<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class skyfire_Iteminfo implements emulator_Iteminfo
{
    private $core;
    private $realmId;
    private $dbUrl;
    private $dbLang;
    private $db;

	public function __construct($realmId)
	{
        $this->core =& get_instance();
        $this->realmId = $realmId;

        $this->dbUrl = $this->core->realms->getRealm($this->realmId)->getConfig('wowdb_tooltip');
        $this->dbLang = $this->core->realms->getRealm($this->realmId)->getConfig('wowdb_lang');

        $this->db = $this->core->realms->getRealm($this->realmId)->getWorldConnection();
    }

    private function getXmlInfo($entry)
    {
        $cacheKey = 'world/items/xml_'.($this->dbLang != 'en' ? $this->dbLang.'_' : '').$this->realmId.'_'.$entry;
        $cached = $this->core->cache->get($cacheKey);
		
		if ($cached !== false)
		{
			return $cached;
		}
        else
        {
            
            $url = $this->dbUrl.'/?item='.$entry.'&domain='.$this->dbLang.'&xml';
            $content = $this->core->getRemotePage($url);
            
            if ($content !== false)
            {
                $aowow = new SimpleXMLElement($content);

                $xmlinfo = array(
                    'name' => (string)$aowow->item->name,
                    'itemlevel' => (int)$aowow->item->level,
                    'quality' => (int)$aowow->item->quality['id'],
                    'quality_str' => (string)$aowow->item->quality,
                    'class' => (int)$aowow->item->class['id'],
                    'subclass' => (int)$aowow->item->subclass['id'],
                    'displayId' => (int)$aowow->item->icon['displayId'],
                    'icon' => (string)$aowow->item->icon,
                    'inventorySlot' => (int)$aowow->item->inventorySlot['id'],
                    'inventorySlot_str' => (string)$aowow->item->inventorySlot,
                    'tooltip' => (string)$aowow->item->htmlTooltip,
                    'json' => (string)$aowow->item->json,
                    'jsonEquip' => (string)$aowow->item->jsonEquip
                );

                // Cache it for an year
                $this->core->cache->store($cacheKey, $xmlinfo, false);

                return $xmlinfo;
            }
        }

        return false;
    }

    /**
	 * Gets item info by entry
	 * @param Int $entry
	 * @return Array
	 */
    public function getInfo($entry)
    {
        $cached = $this->core->cache->get('world/items/info/'.$this->realmId.'_'.$entry);
		
		if ($cached !== false)
		{
			return $cached;
        }

        $res = $this->db->prepare("SELECT   `entry`, 
                                            `name`, 
                                            `Quality` AS `quality`, 
                                            `bonding`, 
                                            `InventoryType` AS `inventorySlot`, 
                                            `RequiredLevel` AS `reqlevel`, 
                                            `ItemLevel` AS `itemlevel`, 
                                            `class`, 
                                            `subclass`, 
                                            `displayid` AS `displayId`, 
                                            `stackable`,
                                            `itemset`, 
											`socketColor_1`,
											`socketColor_2`,
											`socketColor_3`,
											`socketBonus`
                                        FROM `item_template` WHERE `entry` = :entry LIMIT 1;");
        $res->bindParam(':entry', $entry, PDO::PARAM_INT);
        $res->execute();

        if ($res->rowCount() == 0)
		{
	  		return false;
        }

        $row = $res->fetch(PDO::FETCH_ASSOC);

        $row['entry'] = (int)$row['entry'];
        $row['quality'] = (int)$row['quality'];
        $row['bonding'] = (int)$row['bonding'];
        $row['inventorySlot'] = (int)$row['inventorySlot'];
        $row['reqlevel'] = (int)$row['reqlevel'];
        $row['itemlevel'] = (int)$row['itemlevel'];
        $row['class'] = (int)$row['class'];
        $row['subclass'] = (int)$row['subclass'];
        $row['displayId'] = (int)$row['displayId'];
        $row['stackable'] = (int)$row['stackable'];
        $row['itemset'] = (int)$row['itemset'];
		$row['socketColor_1'] = (int)$row['socketColor_1'];
        $row['socketColor_2'] = (int)$row['socketColor_2'];
        $row['socketColor_3'] = (int)$row['socketColor_3'];
        $row['socketBonus'] = (int)$row['socketBonus'];

        $this->core->cache->store('world/items/info/'.$this->realmId.'_'.$entry, $row, false);

        return $row;
    }

    /**
	 * Gets an item info by entry from cache
	 * @param Int $entry
	 * @return Array
	 */
    public function getInfoCache($entry)
    {
        $cached = $this->core->cache->get('world/items/info/'.$this->realmId.'_'.$entry);
		
		if ($cached !== false)
		{
			return $cached;
        }
        
        return false;
    }


    /**
	 * Gets an item icon by entry
	 * @param Int $entry
	 * @return Icon string
	 */
    public function getIcon($entry)
    {
        $cached = $this->core->cache->get('world/items/icons/'.$this->realmId.'_'.$entry);
		
		if ($cached !== false)
		{
			return $cached;
        }
        
        $iteminfo = $this->getInfo($entry);

        if ($iteminfo)
        {
            $res = $this->core->data_db->prepare("SELECT `icon` FROM `data_wotlk_itemdisplayinfo` WHERE `id` = :displayId ORDER BY `id` ASC;");
            $res->bindParam(':displayId', $iteminfo['displayId'], PDO::PARAM_INT);
            $res->execute();
			
			if ($res->rowCount() == 0)
            {
				$url = $this->dbUrl.'/?item='.$entry.'&domain='.$this->dbLang.'&xml';
				$content = $this->core->getRemotePage($url);
            
				if ($content !== false)
				{
                $aowow = new SimpleXMLElement($content);

                $xmlinfo = array(
                    'name' => (string)$aowow->item->name,
                    'itemlevel' => (int)$aowow->item->level,
                    'quality' => (int)$aowow->item->quality['id'],
                    'quality_str' => (string)$aowow->item->quality,
                    'class' => (int)$aowow->item->class['id'],
                    'subclass' => (int)$aowow->item->subclass['id'],
                    'displayId' => (int)$aowow->item->icon['displayId'],
                    'icon' => (string)$aowow->item->icon,
                    'inventorySlot' => (int)$aowow->item->inventorySlot['id'],
                    'inventorySlot_str' => (string)$aowow->item->inventorySlot,
                    'tooltip' => (string)$aowow->item->htmlTooltip,
                    'json' => (string)$aowow->item->json,
                    'jsonEquip' => (string)$aowow->item->jsonEquip
                );
				
                $row = $xmlinfo;

                $this->core->cache->store('world/items/icons/'.$this->realmId.'_'.$entry, $row['icon'], false);

                return $row['icon'];
				}
			}

            if ($res->rowCount() > 0)
            {
                $row = $res->fetch(PDO::FETCH_ASSOC);

                $this->core->cache->store('world/items/icons/'.$this->realmId.'_'.$entry, $row['icon'], false);

                return $row['icon'];
            }
        }

        return 'inv_misc_questionmark';
    }

    /**
	 * Gets an item icon by entry from cache
	 * @param Int $entry
	 * @return Icon string or bool false
	 */
    public function getIconCache($entry)
    {
        $cached = $this->core->cache->get('world/items/icons/'.$this->realmId.'_'.$entry);
		
		if ($cached !== false)
		{
			return $cached;
		}
        
        return $this->getIcon($entry);
    }
}