<div class="clearfix" id="search_sections">
    <h1>Search results</h1>
    <div id="search_realms">
        <span>Realms</span>
        <?php foreach ($realms as $realm) { ?>
            <a href="javascript:void(0)" onClick="Armory.toggleRealm(<?=$realm['id']?>, this)" class="search_realm armory_button active">
                <?=$realm['name']?>
            </a>
        <?php } ?>
    </div>
</div>

<div id="search_tab_1" class="search_tab clearfix">
    <?php if (count($characters) > 0) { ?>
        <?php foreach ($characters as $character) { ?>
            <div class="search_result_realm_<?=$character['realm']?> search_result_character">
                <a href="<?=base_url()?>/armory/character?realm=<?=$character['realm']?>&character=<?=$character['name']?>" id="character_avatar">
                    <img src="<?=base_url()?>/resources/armory/avatars/<?=$character['avatar']?>.gif" class="avatar"/>
                    <div id="inset_shadow"></div>
                </a>
                <div id="character_texts">
                    <a class="name" href="<?=base_url()?>/armory/character?realm=<?=$character['realm']?>&character=<?=$character['name']?>"><?=$character['name']?></a>
                    <div class="info">
                        <span class="c<?=$character['class']?>"><?=$character['level']?> <?=$character['raceName']?> <?=$character['className']?></span><br />
                        <?=ucfirst(strtolower($character['realmName']))?>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="noresults">No characters found.</div>
    <?php } ?>
</div>