<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

if (!function_exists('base_url'))
{
    function base_url()
    {
        $CORE =& get_instance();

        return $CORE->configItem('BaseURL');
    }
}

if (!function_exists('current_url'))
{
	function current_url()
	{
		return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	}
}

if (!function_exists('uri_string'))
{
	function uri_string()
	{
		return substr(current_url(), strlen(base_url()));
	}
}

if (!function_exists('item_url'))
{
    function item_url($entry, $realmId = false)
    {
        $CORE =& get_instance();

        if ($realmId === false)
            $realmId = $CORE->user->GetRealmId();

        if (!$CORE->realms->realmExists($realmId))
            $realmId = $CORE->realms->getFirstRealm()->getId();

        $realm = $CORE->realms->getRealm($realmId);
        $emulator = $CORE->realms->getRealm($realmId)->getEmulator();

        switch ($emulator)
        {
            default:
            case 'trinity': return $realm->getConfig('wowdb_url').'/?item='.$entry;
        }
        
        return '#';
    }
}

if (!function_exists('item_icon'))
{
    function item_icon($entry, $realmId = false)
    {
        $CORE =& get_instance();

        if ($realmId === false)
            $realmId = $CORE->user->GetRealmId();

        if (!$CORE->realms->realmExists($realmId))
            $realmId = $CORE->realms->getFirstRealm()->getId();

        $realm = $CORE->realms->getRealm($realmId);
        $icon = $realm->getIteminfo()->getIcon($entry);

        return ($icon ? $icon : 'inv_misc_questionmark');
    }
}

if (!function_exists('item_icon_style'))
{
    function item_icon_style($entry, $realmId = false)
    {
        $CORE =& get_instance();

        if ($realmId === false)
            $realmId = $CORE->user->GetRealmId();

        if (!$CORE->realms->realmExists($realmId))
            $realmId = $CORE->realms->getFirstRealm()->getId();

        $realm = $CORE->realms->getRealm($realmId);
        $icon = $realm->getIteminfo()->getIconCache($entry);

        return 'style="background-image:url(\'http://wow.zamimg.com/images/wow/icons/medium/'.($icon ? $icon : 'inv_misc_questionmark').'.jpg\');" '.($icon ? '' : 'data-update-icon');
    }
}

if (!function_exists('spell_url'))
{
    function spell_url($entry, $realmId = false)
    {
        $CORE =& get_instance();

        if ($realmId === false)
            $realmId = $CORE->user->GetRealmId();

        if (!$CORE->realms->realmExists($realmId))
            $realmId = $CORE->realms->getFirstRealm()->getId();

        $realm = $CORE->realms->getRealm($realmId);
        $emulator = $CORE->realms->getRealm($realmId)->getEmulator();

        switch ($emulator)
        {
            default:
            case 'trinity': return $realm->getConfig('wowdb_url').'/?spell='.$entry;
        }
        
        return '#';
    }
}

if (!function_exists('wowdb_url'))
{
    function wowdb_url($realmId = false)
    {
        $CORE =& get_instance();

        if ($realmId === false)
            $realmId = $CORE->user->GetRealmId();

        if (!$CORE->realms->realmExists($realmId))
            $realmId = $CORE->realms->getFirstRealm()->getId();

        return $CORE->realms->getRealm($realmId)->getConfig('wowdb_url');
    }
}

if (!function_exists('wowdb_lang'))
{
    function wowdb_lang($realmId = false)
    {
        $CORE =& get_instance();

        if ($realmId === false)
            $realmId = $CORE->user->GetRealmId();

        if (!$CORE->realms->realmExists($realmId))
            $realmId = $CORE->realms->getFirstRealm()->getId();

        return $CORE->realms->getRealm($realmId)->getConfig('wowdb_lang');
    }
}

if (!function_exists('prepare_columns'))
{
    function prepare_columns($columns)
    {
        $temp = array();

        foreach ($columns as $key => $name)
        {
            $temp[] = "`".$key."` AS `".$name."`";
        }

        return implode(', ', $temp);
    }
}

if (!function_exists('lang'))
{
    function lang($key, $file = 'general')
    {
        $CORE =& get_instance();

        return $CORE->lang->get($key, $file);
    }
}

if (!function_exists('clientLang'))
{
    function clientLang($key, $file = 'general')
    {
        $CORE =& get_instance();

        return $CORE->lang->setClientData($key, $file);
    }
}

if (!function_exists('get_timestamp'))
{
    function get_timestamp()
    {
        $CORE =& get_instance();
        $time = $CORE->getTime(true);
        return $time->getTimestamp();
    }
}

if (!function_exists('get_datetime'))
{
    function get_datetime($time = false, $format = 'Y-m-d H:i:s')
    {
        $CORE =& get_instance();
        $time = $CORE->getTime(true, $time);
        return $time->format($format);
    }
}

if (!function_exists('time_elapsed'))
{
    function time_elapsed($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);

        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;

        $string = array(
            'y' => 'year',
            'm' => 'month',
            'w' => 'week',
            'd' => 'day',
            'h' => 'hour',
            'i' => 'minute',
            's' => 'second',
        );
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }

        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? implode(', ', $string) . ' '.lang('ago') : lang('just_now');
    }
}

if (!function_exists('get_day_name'))
{
    function get_day_name($timestamp)
    {
        if (!is_numeric($timestamp))
        {
            $timestamp = strtotime($timestamp);
        }

        $date = date('Y-m-d', $timestamp);

        if ($date == date('Y-m-d'))
        {
            $date = lang('today');
        } 
        else if ($date == date('Y-m-d', strtotime('-1 day', $timestamp)))
        {
            $date = lang('yesterday');
        }

        return $date;
    }
}