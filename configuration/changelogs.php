<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$config['repos'] = array(
    // Web repo
    'web' => array(
        // Get a token from https://github.com/settings/tokens/new
        // Select repo scope
        'token' => '9e21ef90939796ff5b00556b058f5dda92242fd5',

        // Repository
        'owner' => 'ChoMPi0', // The repo owner
        'repo' => 'dbcviewer', // Repository name
        'branch' => 'master',

        // Info for the changelogs page
        'title' => 'Website Changelog',
        'description' => 'Website related changes and updates.'
    ),

    // Trinity repo
    'server' => array(
        // Get a token from https://github.com/settings/tokens/new
        // Select repo scope
        'token' => '9e21ef90939796ff5b00556b058f5dda92242fd5',

        // Repository
        'owner' => 'TrinityCore', // The repo owner
        'repo' => 'TrinityCore', // Repository name
        'branch' => 'master',

        // Info for the changelogs page
        'title' => 'TrinityCore Changelog',
        'description' => 'TrinityCore related changes and updates.'
    )
);