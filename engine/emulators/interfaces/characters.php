<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

interface emulator_Characters
{
    public function __construct($realmId);
    public function getAccountCharacters($account = false);
    public function FindHightestLevelCharacter($acc);
    public function isMyCharacter($guid = false, $name = false, $account = false);
    public function getCharacterName($guid);
    public function getCharacterData($guid = false, $name = false, $columns = 'all');
    public function getCharacterGuild($guid);
    public function getStats($guid);
    public function getItems($guid);
    public function getTalentSpecsInfo($guid);
    public function getTalents($guid, $specId);
    public function getGlyphs($guid, $specId);
    public function getProfessions($guid);
    public function getRecentAchievements($guid, $limit = 5);
    public function getArenaTeam($guid, $type);
    public function getArenaTeamMembers($teamId);
    public function isCharacterOnline($guid);
    public function characterHasMoney($guid, $cost);
    public function ResolveGuild($guid);
    public function Teleport($guid, $coords);
    public function Unstuck($guid = false, $name = false);
    public function getTable($name);
    public function getColumn($table, $name);
    public function getAllColumns($table);
}