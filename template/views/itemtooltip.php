<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

echo '<div style="max-width:350px;">
		<span class="q', $tooltipData['quality'], '" style="font-size: 16px">', $tooltipData['name'], '</span><br />';

if ($tooltipData['level']){ echo '<span style="color: #ffd100">Item Level ', $tooltipData['level'], '</span><br />'; }

if ($tooltipData['isHeroic']){ echo '<div class="q2">Heroic</div>'; }

if ($tooltipData['bind']){ echo $tooltipData['bind'], '<br />'; }
if ($tooltipData['unique']){ echo $tooltipData['unique'], '<br />'; }

// Dont print slot string if it's a bag
if (isset($tooltipData['bag']))
{
	echo $tooltipData['bag'], '<br />';
}
else if ($tooltipData['slot'])
{
	echo '<div style="float:left;">', $tooltipData['slot'], '</div>';
}

echo '<div style="float:right;">', $tooltipData['type'], '</div>
<div style="clear:both;"></div>';

if ($tooltipData['armor']){ echo $tooltipData['armor'], ' Armor<br />'; }

if ($tooltipData['damage_min'])
{
	echo '
	<div style="float:left;">', $tooltipData['damage_min'], ' - ', $tooltipData['damage_max'], ' ', $tooltipData['damage_type'], ' Damage</div>
	<div style="float:right;margin-left:15px;">Speed ', $tooltipData['speed'], '</div><br />
	(', $tooltipData['dps'], ' damage per second)<br />';
}

if (count($tooltipData['attributes']['regular']) > 0)
{
	foreach ($tooltipData['attributes']['regular'] as $attribute)
	{
		echo $attribute['text'];
	}
}

if ($tooltipData['holy_res']){ echo $tooltipData['holy_res'], ' Holy Resistance<br />'; }
if ($tooltipData['nature_res']){ echo $tooltipData['nature_res'], ' Nature Resistance<br />'; }
if ($tooltipData['fire_res']){ echo $tooltipData['fire_res'], ' Fire Resistance<br />'; }
if ($tooltipData['frost_res']){ echo $tooltipData['frost_res'], ' Frost Resistance<br />'; }
if ($tooltipData['shadow_res']){ echo $tooltipData['shadow_res'], ' Shadow Resistance<br />'; }
if ($tooltipData['arcane_res']){ echo $tooltipData['arcane_res'], ' Arcane Resistance<br />'; }

echo '<div class="q2" id="tooltip-item-enchantments"></div>';

echo '<div id="tooltip-item-sockets">
	', ($tooltipData['sockets'] ? $tooltipData['sockets'] : ''), '
</div>';

if ($tooltipData['socketBonus']){ echo '<div class="q0" id="tooltip-item-sock-bonus">Socket Bonus: ', $tooltipData['socketBonus'], '</div>'; }

if ($tooltipData['durability']){ echo 'Durability ', $tooltipData['durability'], ' / ', $tooltipData['durability'], '<br />'; }
if ($tooltipData['required']){ echo 'Requires Level ', $tooltipData['required'], '<br />'; }
if ($tooltipData['required_skill']){ echo 'Requires ', $tooltipData['required_skill'], '<br />'; }

if (count($tooltipData['attributes']['spells']) > 0)
{
	foreach ($tooltipData['attributes']['spells'] as $attribute)
	{
		echo $attribute['text'];
	}
}

if (count($tooltipData['spells']) > 0)
{
	foreach ($tooltipData['spells'] as $spell)
	{
		echo '
		<a class="q2" href="https://wowhead.com/?spell=', $spell['id']. '" target="_blank">
			', $spell['trigger'], '
			', ((strlen($spell['text']) == 0) ? 'Unknown effect' : $spell['text']), '
		</a>
		<br />';
	}
}

if (isset($tooltipData['description']))
{
	echo $tooltipData['description'], '<br />';
}

if ($tooltipData['itemSet'])
{
	echo '
    <div id="tooltip-item-set" style="padding-top: 10px;">
        <div class="q" id="tooltip-item-set-name">', $tooltipData['itemSet']['name'], ' (<span id="tooltip-item-set-count">0</span>/', count($tooltipData['itemSet']['items']), ')</div>
    	<div id="tooltip-item-set-pieces" style="padding: 0;">
        	<ul style="list-style: none; margin: 0px; padding: 0 0 0 10px;" class="q0">';
			
            	foreach ($tooltipData['itemSet']['items'] as $setItem)
				{
            		echo '<li class="item-set-piece" data-itemset-item-entry="', $setItem['entry'], '" data-possible-entries="', $setItem['possibleItemEntries'], '">', $setItem['name'], '</li>';
                }
	
	echo '
            </ul>
        </div>
        <div id="tooltip-item-set-bonuses" style="padding: 10px 0 0 0;">
        	<ul style="list-style: none; margin: 0px; padding: 0;" class="q0">';
			
            	foreach ($tooltipData['itemSet']['setBonuses'] as $setBonus)
				{
            		echo '<li class="item-set-bonus" data-bonus-required-items="', $setBonus['requiredItems'], '">(', $setBonus['requiredItems']. ') Set: <span id="set-bonus-text">', $setBonus['spell'], '</span></li>';
                }
				
	echo '
            </ul>
        </div>
    </div>';
}

echo '</div>';