<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

	<div class="container_2 armory-character <?=$faction?>">

        <!-- Top part -->
        <section class="armory_top">
            <section class="bars">
                <?php if ($health && $health != "Unknown") { ?>
                    <div class="health">Health: <b><?=$health?></b></div>
                <?php } ?>

                <?php if ($secondBarValue && $secondBarValue != "Unknown") { ?>
                    <div class="<?=$secondBar?>"><?=ucfirst($secondBar)?>: <b><?=$secondBarValue?></b></div>
                <?php } ?>
            </section>

            <section class="name">
                <h1><?=$name?> <?php if ($guild) { ?><span class="guild color-tooltip-<?=$faction?>"><?=$guild['name']?></span><?php } ?></h1>
                <h2 class="color-c<?=$class?>"><b><?=$level?></b> <?=$raceName?> <?=$className?>, <i><?=$realmName?></i></h2>
            </section>

            <div class="clear"></div>
        </section>

        <!-- Main part -->
        <section class="slots">
            <section id="left">
                <div class="item"><?=$items['head']?><del></del></div>
                <div class="item"><?=$items['neck']?><del></del></div>
                <div class="item"><?=$items['shoulders']?><del></del></div>
                <div class="item"><?=$items['back']?><del></del></div>
                <div class="item"><?=$items['chest']?><del></del></div>
                <div class="item"><?=$items['body']?><del></del></div>
                <div class="item"><?=$items['tabard']?><del></del></div>
                <div class="item"><?=$items['wrists']?><del></del></div>
            </section>

            <section class="armory_stats">
                <div id="stats_top">
                    <a href="javascript:void(0)" onClick="Character.tab('stats', this)" class="armory_current_tab">
                        Attributes
                    </a>

                    <?php if ($pvp['kills'] !== false || $pvp['honor'] !== false || $pvp['arena'] !== false) { ?>
                        <a href="javascript:void(0)" onClick="Character.tab('pvp', this)">
                            Player vs Player
                        </a>
                    <?php } ?>
                </div>
                
                <section id="tab_stats" style="display:block;">
                    <div style="width: 1628px; height: 216px;" id="attributes_wrapper">
                        <?php foreach ($stats as $tabIndex => $tabStats) { ?>
                            <div id="tab_armory_<?=($tabIndex + 1)?>" style="float: left;">
                                <div style="padding: 10px 20px;">
                                    <table style="width: 367px" cellspacing="0" cellpadding="0">
                                        <?php foreach ($tabStats as $statKey => $stat) { ?>
                                            <tr>
                                                <td width="50%"><?=$stat['name']?></td>
                                                <td><?=$stat['value']?></td>
                                            </tr>
                                        <?php } ?>
                                    </table>

                                    <div id="armory_stats_next">
                                        <?php if ($tabIndex != 0) { ?>
                                        <a href="javascript:void(0)" class="btn_prev" onClick="Character.attributes(<?=($tabIndex)?>)">&larr; Previous</a>
                                        <?php } ?>
                                        <?php if ($tabIndex != 3) { ?>
                                        <a href="javascript:void(0)" class="btn_next" onClick="Character.attributes(<?=($tabIndex + 2)?>)">Next &rarr;</a>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>

                        <!--
                        <div id="tab_armory_1" style="float: left;">
                            <div style="padding: 10px 20px;">
                                <table style="width: 367px" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="50%">Strength</td>
                                        <td><?php if (strlen($stats['strength'])) { ?><?=$stats['strength']?><?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Stamina</td>
                                        <td><?php if (strlen($stats['stamina'])) { ?><?=$stats['stamina']?><?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Intellect</td>
                                        <td><?php if (strlen($stats['intellect'])) { ?><?=$stats['intellect']?><?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                    <?php if ($stats && array_key_exists("spellPower", $stats)) { ?>
                                        <tr>
                                            <td width="50%">Spell power</td>
                                            <td><?php if (strlen($stats['spellPower'])) { ?><?=$stats['spellPower']?><?php } else { ?>Unknown<?php } ?></td>
                                        </tr>
                                    <?php } ?>
                                    
                                    <?php if ($stats && array_key_exists("attackPower", $stats)) { ?>
                                        <tr>
                                            <td width="50%">Attack power</td>
                                            <td><?php if (strlen($stats['attackPower'])) { ?><?=$stats['attackPower']?><?php } else { ?>Unknown<?php } ?></td>
                                        </tr>
                                    <?php } ?>
                                </table>

                                <div id="armory_stats_next"><a href="javascript:void(0)" onClick="Character.attributes(2)">Next &rarr;</a></div>
                            </div>
                        </div>

                        <div id="tab_armory_2" style="float:left;">
                            <div style="padding: 10px 20px;">
                                <table style="width: 367px" cellspacing="0" cellpadding="0">
                                    <?php if ($stats && array_key_exists("resilience", $stats)) { ?>
                                        <tr>
                                            <td width="50%">Resilience</td>
                                            <td><?php if (strlen($stats['resilience'])) { ?><?=$stats['resilience']?><?php } else { ?>Unknown<?php } ?></td>
                                        </tr>
                                    <?php } ?>

                                    <tr>
                                        <td width="50%">Armor</td>
                                        <td><?php if (strlen($stats['armor'])) { ?><?=$stats['armor']?><?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Block</td>
                                        <td><?php if (strlen($stats['blockPct'])) { ?><?=$stats['blockPct']?>%<?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Dodge</td>
                                        <td><?php if (strlen($stats['dodgePct'])) { ?><?=$stats['dodgePct']?>%<?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Parry</td>
                                        <td><?php if (strlen($stats['parryPct'])) { ?><?=$stats['parryPct']?>%<?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                </table>

                                <div id="armory_stats_next">
                                    <a href="javascript:void(0)" onClick="Character.attributes(1)">&larr; Previous</a>
                                    <a href="javascript:void(0)" onClick="Character.attributes(3)">Next &rarr;</a>
                                </div>
                            </div>
                        </div>

                        <div id="tab_armory_3" style="float:left;">
                            <div style="padding: 10px 20px;">
                                <table style="width: 367px" cellspacing="0" cellpadding="0">
                                    <tr>
                                        <td width="50%">Crit chance</td>
                                        <td><?php if (strlen($stats['critPct'])) { ?><?=$stats['critPct']?>%<?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Ranged crit chance</td>
                                        <td><?php if (strlen($stats['rangedCritPct'])) { ?><?=$stats['rangedCritPct']?>%<?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Spell crit chance</td>
                                        <td><?php if (strlen($stats['spellCritPct'])) { ?><?=$stats['spellCritPct']?>%<?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">Spirit</td>
                                        <td><?php if (strlen($stats['spirit'])) { ?><?=$stats['spirit']?><?php } else { ?>Unknown<?php } ?></td>
                                    </tr>
                                    <tr>
                                        <td width="50%">&nbsp;</td>
                                        <td></td>
                                    </tr>
                                </table>

                                <div id="armory_stats_next"><a href="javascript:void(0)" onClick="Character.attributes(2)">&larr; Previous</a></div>
                            </div>
                        </div>
                        -->
                    </div>
                </section>

                <section id="tab_pvp">
                    <div style="padding: 10px 20px;">
                        <table style="width: 367px" cellspacing="0" cellpadding="0">
                            <?php if ($pvp['kills'] !== false) { ?>
                            <tr>
                                <td width="50%">Total kills</td>
                                <td><?php if (strlen($pvp['kills'])) { ?><?=$pvp['kills']?><?php } else { ?>Unknown<?php } ?></td>
                            </tr>
                            <?php } ?>

                            <?php if ($pvp['honor'] !== false) { ?>
                            <tr>
                                <td width="50%">Honor points</td>
                                <td><?php if (strlen($pvp['honor'])) { ?><?=$pvp['honor']?><?php } else { ?>Unknown<?php } ?></td>
                            </tr>
                            <?php } ?>

                            <?php if ($pvp['arena'] !== false) { ?>
                            <tr>
                                <td width="50%">Arena points</td>
                                <td><?php if (strlen($pvp['arena'])) { ?><?=$pvp['arena']?><?php } else { ?>Unknown<?php } ?></td>
                            </tr>
                            <?php } ?>
                        </table>
                    </div>
                </section>
            </section>

            <section id="right">
                <div class="item"><?=$items['hands']?><del></del></div>
                <div class="item"><?=$items['waist']?><del></del></div>
                <div class="item"><?=$items['legs']?><del></del></div>
                <div class="item"><?=$items['feet']?><del></del></div>
                <div class="item"><?=$items['finger1']?><del></del></div>
                <div class="item"><?=$items['finger2']?><del></del></div>
                <div class="item"><?=$items['trinket1']?><del></del></div>
                <div class="item"><?=$items['trinket2']?><del></del></div>
            </section>

            <section id="bottom">
                <div class="item"><?=$items['mainhand']?><del></del></div>
                <div class="item"><?=$items['offhand']?><del></del></div>
                <div class="item"><?=$items['ranged']?><del></del></div>
            </section>
        </section>

        <section class="mid_info">
            
            <div class="recent-activity">
                <h3 class="headline">Recent Achievements</h3>
                
                <ul class="achievements container">
                <?php if ($recent_achievements) { ?>
                
                    <?php foreach ($recent_achievements as $key => $achiev) { ?>
                        <li class="achievement">
                            <div id="icon">
                                <span class="icon">
                                    <a href="<?=$achiev['url']?>" target="_new" rel="np" class="icon-frame frame-12">
                                        <img src="http://wow.zamimg.com/images/wow/icons/small/<?=$achiev['icon']?>.jpg" alt="" width="16" height="16">
                                    </a>
                                </span>
                            </div>
                            <div id="info">
                                <span id="descr"><?=$achiev['text']?></span> <br/><span id="date"><?=$achiev['date']?></span>
                            </div>
                            <div class="clear"></div>
                        </li>
                    <?php } ?>

                <?php } else { ?>
                    <li id="no-records">No records ware found.</li>
                <?php } ?>
                </ul>
            </div>
            
            <div class="professions">
                <h3 class="headline">Main Professions</h3>
                <ul>
                    <?php foreach ($main_professions as $key => $prof) { ?>
                        <?php if ($prof) { ?>
                            <li class="profession container">
                                <div class="profile-progress border-3 <?php if ($prof['percent'] == 100) { ?>completed<?php } ?>">
                                    <div class="bar border-3 hover" style="width: <?=$prof['percent']?>%"></div>
                                    <div class="bar-contents">
                                        <div class="profession-details">
                                            <span class="icon">
                                                <span class="icon-frame frame-12">
                                                    <img src="http://wow.zamimg.com/images/wow/icons/small/<?=strtolower($prof['icon'])?>.jpg" alt="" width="16" height="16" />
                                                </span>
                                            </span>
                                            <span class="name"><?=$prof['name']?></span>
                                            <span class="value"><?=$prof['value']?>/<?=$prof['max']?></span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php } else { ?>
                            <li class="profession profession-empty container">No Profession</li>
                        <?php } ?>
                    <?php } ?>
                </ul>
                <?php if ($secondary_professions) { ?>
                    <h3 class="headline" style="margin-top: 20px;">Secondary Professions</h3>
                    <ul>
                        <?php foreach ($secondary_professions as $key => $prof) { ?>
                            <li class="profession container">
                                <div class="profile-progress border-3 <?php if ($prof['percent'] == 100) { ?>completed<?php } ?>">
                                    <div class="bar border-3 hover" style="width: <?=$prof['percent']?>%"></div>
                                    <div class="bar-contents">
                                        <div class="profession-details">
                                            <span class="icon">
                                                <span class="icon-frame frame-12">
                                                    <img src="http://wow.zamimg.com/images/wow/icons/small/<?=strtolower($prof['icon'])?>.jpg" alt="" width="16" height="16" />
                                                </span>
                                            </span>
                                            <span class="name"><?=$prof['name']?></span>
                                            <span class="value"><?=$prof['value']?>/<?=$prof['max']?></span>
                                        </div>
                                    </div>
                                </div>
                            </li>
                        <?php } ?>
                    </ul>
                <?php } ?>
            </div>
            
            <div class="clear"></div>
        </section>

        <section class="talents-section talents-expansion-<?=$expansion_str?>">

            <div class="talents-specs">
                <?php if ($talent_specs) { ?>
                    <?php foreach ($talent_specs as $key => $spec) { ?>
                    <a href="javascript: void(0);" class="talents-spec <?php if ($spec['active']) { ?>talents-spec-active<?php } ?>" specId="<?=$key?>">
                        <div>
                            <div class="talents-spec-icon">
                                <?php if ($spec['icon']) { ?>
                                    <img src="http://wow.zamimg.com/images/wow/icons/medium/<?=$spec['icon']?>.jpg" style="vertical-align:middle"> 
                                <?php } else { ?>
                                    <img src="http://wow.zamimg.com/images/wow/icons/medium/inv_misc_questionmark.jpg" style="vertical-align:middle"> <br />
                                <?php } ?>
                            </div>
                            <div class="talents-spec-info">
                                <span id="title"><?=$spec['title']?></span><br />
                                <span id="points"><?=$spec['points']?></span>
                                <div class="clear"></div>
                            </div>
                            <?php if ($spec['active']) { ?>
                                <div class="talents-spec-char-selected"></div>
                            <?php } ?>
                            <div class="clear"></div>
                        </div>
                    </a>
                    <?php } ?>
                    <div class="clear"></div>
                <?php } ?>
            </div>
            
            <?php if ($talent_tables) { ?>
                <?php foreach ($talent_tables as $key => $table) { ?>
                    <div class="talents" specId="<?=$key?>" style="<?php if ($key != $talent_active_spec) { ?>display: none;<?php } ?>"> 
                        
                        <h3>Talents</h3>
                        <div class="talents-body">
                            
                            <?php foreach ($table as $tab_key => $tab) { ?>
                                <div class="talents-tree" id="tree-<?=((int)$tab['order'] + 1)?>" style="float: left;">
                                    
                                    <div class="talents-tree-bg <?=$className_clean?>-<?=((int)$tab['order'] + 1)?>"></div>
                                    
                                    <div class="talents-tree-title">
                                        <img src="http://wow.zamimg.com/images/wow/icons/small/<?=$tab['icon']?>.jpg" height="21px" style="vertical-align:middle"> 
                                        <?=$tab['name']?>
                                    </div>
                                    
                                    <table cellpadding="0" cellspacing="0" border="0">
                                        <?php foreach ($tab['table'] as $row_key => $row) { ?>
                                            <tr>
                                                <?php foreach ($row as $col_key => $talent) { ?>
                                                    <td>
                                                        <?php if ($talent) { ?>
                                                            <div class="iconmedium">
                                                                <ins style="background-image: url(http://wow.zamimg.com/images/wow/icons/medium/<?=$talent['icon']?>.jpg);"></ins>
                                                                <del></del>
                                                                <a href="<?=spell_url($talent['spell'], $realmId)?>" target="_new" rel="np" data-realm="<?=$realmId?>"></a>
                                                                <div class="icon-border" id="<?php if ($talent['points'] == 0) { ?>inactive<?php } else { ?><?php if ($talent['points'] == $talent['max_rank']) { ?>maxed<?php } else { ?>active<?php } ?><?php } ?>"></div>
                                                                <div class="icon-bubble" id="<?php if ($talent['points'] == 0) { ?>inactive<?php } else { ?><?php if ($talent['points'] == $talent['max_rank']) { ?>maxed<?php } else { ?>active<?php } ?><?php } ?>"><?=$talent['points']?></div>
                                                                <?php if ($talent['points'] == 0) { ?>
                                                                    <div class="overlay"></div>
                                                                <?php } ?>
                                                                <?php if (isset($talent['arrows']) && $talent['arrows']) { ?>
                                                                    <!-- Dependency arrows -->
                                                                    <?php foreach ($talent['arrows'] as $arrow_key => $arrow) { ?>
                                                                        <div <?php if ($talent['points'] == $talent['max_rank']) { ?>id="maxed"<?php } ?> class="talent-dependency talent-dependency-<?=$arrow['pointing']?>" <?php if ($arrow['pointing'] == "down") { ?>style="height: <?=(16 + $arrow['rows'] * 50)?>px;"<?php } ?>>
                                                                            <?php if ($arrow['pointing'] == "leftdown" || $arrow['pointing'] == "rightdown") { ?>
                                                                                <div <?php if ($talent['points'] == $talent['max_rank']) { ?>id="maxed"<?php } ?> class="talent-dependency-second" style="height: <?=(27 + $arrow['rows'] * 50)?>px;"></div>
                                                                            <?php } ?>
                                                                        </div>
                                                                    <?php } ?>
                                                                <?php } ?>
                                                            </div>
                                                        <?php } ?>
                                                    </td>
                                                <?php } ?>
                                            </tr>
                                        <?php } ?>
                                    </table>
                                    
                                </div>
                            <?php } ?>
                                
                            <div class="clear"></div>
                        </div>
                        
                        <h3>Glyphs</h3>
                        <div class="talents-glyphs">
                            <?php if ($glyph_tables[$key]["hasPrime"]) { ?>
                                <!-- Prime Glyphs -->
                                <div class="talents-glyphs-column">
                                    <div class="talents-glyphs-list">
                                        <?php $prime = $glyph_tables[$key]['prime']; ?>
                                        <?php for ($glyph = 0; $glyph <= 2; $glyph++) { ?>
                                            <div class="talents-glyphs-glyph">
                                                <div class="iconsmall">
                                                    <ins style="background-image: url(http://wow.zamimg.com/images/wow/icons/small/<?=$prime[$glyph]['icon']?>.jpg);"></ins>
                                                    <del></del>
                                                    <a href="<?=($prime[$glyph]['spellid'] ? spell_url($prime[$glyph]['spellid'], $realmId) : 'javascript:void(0)')?>" target="_new" rel="np" data-realm="<?=$realmId?>"></a>
                                                </div>
                                                <a href="<?=($prime[$glyph]['spellid'] ? spell_url($prime[$glyph]['spellid'], $realmId) : 'javascript:void(0)')?>" target="_new" rel="np" data-realm="<?=$realmId?>" id="glyph-name"><?=$prime[$glyph]['name']?></a>
                                                <div class="clear"></div>
                                            </div>
                                        <?php } ?>
                                        
                                        <div class="clear"></div>
                                    </div> 
                                </div>
                            <?php } ?>
                            
                            <!-- Major Glyphs -->
                            <div class="talents-glyphs-column">
                                <div class="talents-glyphs-list">
                                    <?php $major = $glyph_tables[$key]['major']; ?>
                                    <?php for ($glyph = 0; $glyph <= 2; $glyph++) { ?>
                                        <div class="talents-glyphs-glyph">
                                            <div class="iconsmall">
                                                <ins style="background-image: url(http://wow.zamimg.com/images/wow/icons/small/<?=$major[$glyph]['icon']?>.jpg);"></ins>
                                                <del></del>
                                                <a href="<?=($major[$glyph]['spellid'] ? spell_url($major[$glyph]['spellid'], $realmId) : 'javascript:void(0)')?>" target="_new" rel="np" data-realm="<?=$realmId?>"></a>
                                            </div>
                                            <a href="<?=($major[$glyph]['spellid'] ? spell_url($major[$glyph]['spellid'], $realmId) : 'javascript:void(0)')?>" target="_new" rel="np" data-realm="<?=$realmId?>" id="glyph-name"><?=$major[$glyph]['name']?></a>
                                            <div class="clear"></div>
                                        </div>
                                    <?php } ?>
                                    
                                    <div class="clear"></div>
                                </div> 
                            </div>
                            
                            <!-- Minor Glyphs -->
                            <div class="talents-glyphs-column">
                                <div class="talents-glyphs-list">
                                    <?php $minor = $glyph_tables[$key]['minor']; ?>
                                    <?php for ($glyph = 0; $glyph <= 2; $glyph++) { ?>
                                        <div class="talents-glyphs-glyph">
                                            <div class="iconsmall">
                                                <ins style="background-image: url(http://wow.zamimg.com/images/wow/icons/small/<?=$minor[$glyph]['icon']?>.jpg);"></ins>
                                                <del></del>
                                                <a href="<?=($minor[$glyph]['spellid'] ? spell_url($minor[$glyph]['spellid'], $realmId) : 'javascript:void(0)')?>" target="_new" rel="np" data-realm="<?=$realmId?>"></a>
                                            </div>
                                            <a href="<?=($minor[$glyph]['spellid'] ? spell_url($minor[$glyph]['spellid'], $realmId) : 'javascript:void(0)')?>" target="_new" rel="np" data-realm="<?=$realmId?>" id="glyph-name"><?=$minor[$glyph]['name']?></a>
                                            <div class="clear"></div>
                                        </div>
                                    <?php } ?>
                                    
                                    <div class="clear"></div>
                                </div> 
                            </div>
                            
                            <div class="clear"></div>
                        </div>
                    </div>
                <?php } ?>
            <?php } ?>
            
        </section>

        <section class="arena_title">
            <h3 class="headline">Arena Teams</h3>
        </section>

        <section id="armory_arena_teams">
            
            <?php foreach ($arena_teams_table as $key => $team) { ?>
                <div class="armory_arena_box container" <?php if (!$team) { ?>id="inactive"<?php } ?>>
                    <div class="arena_box_head">
                        <span id="team-type"><?php if ($key == 0) { ?>2v2<?php } ?><?php if ($key == 1) { ?>3v3<?php } ?><?php if ($key == 2) { ?>5v5<?php } ?></span>
                        <?php if ($team) { ?>
                            <p id="player-rating"><a href="javascript: void(0);" data-tip="Personal Rating"><?=$team['player']['rating']?></a></p>
                            <p id="player-games">
                                <span id="player-wins"><?=$team['player']['wins']?></span> - <span id="player-loses"><?=($team['player']['games'] - $team['player']['wins'])?></span> 
                                <?php if ($team['player']['games'] > 0) { ?>
                                    <span id="player-win-ratio">(<?=round(($team['player']['wins'] / $team['player']['games']) * 100)?>%)</span>
                                <?php } ?>
                            </p>
                        <?php } ?>
                    </div>
                    <div class="arena_box_body">
                        <?php if ($team) { ?>
                            <p id="team-name"><?=$team['teamName']?></p>
                            <p id="team-stats">
                                <span id="team-rating"><a href="javascript: void(0);" data-tip="Team Rating"><?=$team['teamRating']?></a></span> <span id="team-rank"><a href="javascript: void(0);" data-tip="Team Rank">#<?=$team['teamRank']?></a></span>
                            </p>
                            <div id="team-members">
                                <?php if ($team['members']) { ?>
                                    <?php foreach ($team['members'] as $key => $member) { ?>
                                        <div class="icon iconmedium">
                                            <ins style="background-image: url('http://wow.zamimg.com/images/wow/icons/medium/class_<?=str_replace(' ', '', strtolower($CORE->realms->getClassString($member['class'])))?>.jpg');"></ins>
                                            <del></del>
                                            <a href="<?=base_url()?>/armory/character?realm=<?=$realmId?>&character=<?=$member['guid']?>" data-tip="<font style='font-size: 18px; line-height: 18px; font-weight: bold;'><?=$member['name']?></font><br /><span class='color-c<?=$member['class']?>'><?=$member['level']?> <?=$member['className']?> <?=$member['raceName']?></span><br/>Games won: <?=$member['wins']?><br />Games lost: <?=($member['games'] - $member['wins'])?><br />Personal Rating: <?=$member['rating']?>"></a>
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                                <div class="clear"></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            <?php } ?>
            
            <div class="clear"></div>
        </section>

    </div>

</div>

<script type="text/javascript" src="<?php echo base_url(); ?>/template/js/page.armory.character.js"></script>