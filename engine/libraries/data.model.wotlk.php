<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

class data_model_wotlk
{
    private $core;
    private $db;
	private $runtimeCache = array();
	
	public function __construct()
	{
        $this->core =& get_instance();
        $this->db = $this->core->data_db;
	}

    public function getItemIcon($displayId)
	{
        $res = $this->db->prepare("SELECT `icon` FROM `data_wotlk_itemdisplayinfo` WHERE `id` = :displayId ORDER BY `id` ASC;");
        $res->bindParam(':displayId', $displayId, PDO::PARAM_INT);
        $res->execute();

		if ($res->rowCount() > 0)
		{
            $row = $res->fetch(PDO::FETCH_ASSOC);
			return $row['icon'];
		}
		
        return false;
    }
    
	public function getTalentTabs($class)
	{
		$res = $this->db->prepare("	SELECT `data_wotlk_talenttab`.`id`, `data_wotlk_talenttab`.`name`, `data_wotlk_talenttab`.`classes`, `data_wotlk_talenttab`.`order`, `data_wotlk_spellicons`.`icon` 
									FROM `data_wotlk_talenttab` 
									INNER JOIN `data_wotlk_spellicons` ON `data_wotlk_talenttab`.`spellicon` = `data_wotlk_spellicons`.`id`
									WHERE `data_wotlk_talenttab`.`classes` = :class 
									ORDER BY `order` ASC;");
        $res->bindParam(':class', $class, PDO::PARAM_INT);
        $res->execute();

		if ($res->rowCount() > 0)
		{
			return $res->fetchAll(PDO::FETCH_ASSOC);
		}
		
		return false;
	}
	
	public function getTalentsForTab($tab)
	{
        $res = $this->db->prepare("SELECT * FROM `data_wotlk_talent` WHERE `tab` = :tab ORDER BY `id` ASC;");
        $res->bindParam(':tab', $tab, PDO::PARAM_INT);
        $res->execute();

		if ($res->rowCount() > 0)
		{
			return $res->fetchAll(PDO::FETCH_ASSOC);
		}
		
        return false;
	}
	
	public function getSpellIcon($spell)
	{
        $res = $this->db->prepare("SELECT `icon` FROM `data_wotlk_spellicons` WHERE `id` = (SELECT `data_wotlk_spell`.`spellicon` FROM `data_wotlk_spell` WHERE `data_wotlk_spell`.`spellID` = :spell) LIMIT 1;");
		$res->bindParam(':spell', $spell, PDO::PARAM_INT);
        $res->execute();

		if ($res->rowCount() > 0)
		{
			return $res->fetch(PDO::FETCH_ASSOC);
		}
		
        return false;
	}
	
	public function getGlyphInfo($id)
	{
		$res = $this->db->prepare("	SELECT `data_wotlk_glyphproperties`.`id`, `data_wotlk_glyphproperties`.`spellid`, `data_wotlk_glyphproperties`.`typeflags`, `data_wotlk_spell`.`spellname` AS name
									FROM `data_wotlk_glyphproperties` 
									INNER JOIN `data_wotlk_spell` on `data_wotlk_glyphproperties`.`spellid` = `data_wotlk_spell`.`spellID`  
									WHERE `data_wotlk_glyphproperties`.`id` = :id 
									LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

		if ($res->rowCount() > 0)
		{
			return $res->fetch(PDO::FETCH_ASSOC);
		}
		
        return false;
	}
	
	public function getEnchantmentInfo($id)
	{
		if (isset($this->runtimeCache[$id]))
		{
			return $this->runtimeCache[$id];
		}
		
		$res = $this->db->prepare("	SELECT 
										`data_wotlk_spellitemenchantment`.`id`, 
										`data_wotlk_spellitemenchantment`.`description`, 
										`data_wotlk_spellitemenchantment`.`GemID`, 
										`data_wotlk_spellitemenchantment`.`EnchantmentCondition`, 
										`data_wotlk_gemproperties`.`color` 
									FROM `data_wotlk_spellitemenchantment` 
									LEFT JOIN `data_wotlk_gemproperties` ON `data_wotlk_gemproperties`.`SpellItemEnchantement` = `data_wotlk_spellitemenchantment`.`id` 
									WHERE `data_wotlk_spellitemenchantment`.`id` = :id 
									LIMIT 1;");
        $res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

		if ($res->rowCount() > 0)
		{
			$result = $res->fetch(PDO::FETCH_ASSOC);
			
			//save to cache
			$this->runtimeCache[$id] = $result;
			
			return $result;
		}
		
		return false;
	}
	
	public function getEnchantmentConditions($ConditionEntry)
	{
		$res = $this->db->prepare("	SELECT *
									FROM `data_wotlk_spellitemenchantmentcondition` 
									WHERE `id` = :entry 
									LIMIT 1;");
		$res->bindParam(':entry', $ConditionEntry, PDO::PARAM_INT);
        $res->execute();

		if ($res->rowCount() > 0)
		{
			return $res->fetch(PDO::FETCH_ASSOC);
		}
		
        return false;
	}
	
	public function getAchievementInfo($id)
	{
		$res = $this->db->prepare("	SELECT 
										`data_wotlk_achievement`.`id`, 
										`data_wotlk_achievement`.`name`, 
										`data_wotlk_achievement`.`description`, 
										`data_wotlk_achievement`.`points`,  
										`data_wotlk_spellicons`.`icon` 
									FROM `data_wotlk_achievement` 
									LEFT JOIN `data_wotlk_spellicons` ON `data_wotlk_spellicons`.`id` = `data_wotlk_achievement`.`icon` 
									WHERE `data_wotlk_achievement`.`id` = :id 
									LIMIT 1;");
		$res->bindParam(':id', $id, PDO::PARAM_INT);
        $res->execute();

		if ($res->rowCount() > 0)
		{
			return $res->fetch(PDO::FETCH_ASSOC);
		}
		
        return false;
    }
}