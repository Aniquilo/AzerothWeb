<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//print error messages
$ERRORS->PrintAny(array('pstore_armorsets_add', 'pstore_armorsets_edit', 'pstore_armorsets_del'));		
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="<?=base_url()?>/admin/armorsets">Armor Sets</a></li>
		<li><a href="<?=base_url()?>/admin/armorsets/categories">Armor Sets Categories</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">

    <script src="<?=base_url()?>/admin_template/js/forms.js" type="text/javascript"></script>

    <div id="maintab">

        <div class="column left twothird">
            <h2>Armor Sets Management</h2>
            
            <table class="datatable" id="datatable_armorsets">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Items</th>
                        <th>Realm</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
            
                    <?php
                    $res = $DB->query("SELECT * FROM `armorsets` ORDER BY id DESC");
                    
                    if ($res->rowCount() > 0)
                    {
                        while ($arr = $res->fetch())
                        {
                            $subInfo = array();

                            //check for set specifications
                            if ($arr['tier'] != '')
                            {
                                $subInfo[] = $arr['tier'];
                            }
                            if ($arr['class'] != '' and $arr['class'] > 0)
                            {
                                $subInfo[] = 'Class: ' . $CORE->realms->getClassString($arr['class']);
                            }
                            if ($arr['type'] != '')
                            {
                                $subInfo[] = 'Type: ' . $arr['type'];
                            }

                            //explode the items
                            $items = explode(',', $arr['items']);

                            //get the category name
                            if (isset($categories[$arr['category']]))
                            {
                                $cat = $categories[$arr['category']];
                            }
                            else
                            {
                                $cat = 'Unknown';
                            }

                            $realmStr = 'All Realms';
                            $realmId = $CORE->realms->getFirstRealm()->getId();

                            //get the realm name
                            if ($arr['realm'] != '-1')
                            {
                                if ($CORE->realms->realmExists((int)$arr['realm']))
                                {
                                    $realmStr = $CORE->realms->getRealm((int)$arr['realm'])->getName();
                                    $realmId = (int)$arr['realm'];
                                }
                                else
                                {
                                    $realmStr = 'Unknown';
                                }
                            }
                            
                            echo '
                            <tr data-id="', $arr['id'], '">
                                <td>', $arr['name'], (isset($subInfo) ? '<p class="subInfo">' . implode(' | ', $subInfo) . '</p>' : ''), '</td>
                                <td class="armorset-items">';
                                    
                                    //loop the items
                                    foreach ($items as $entry)
                                    {
                                        echo '
                                        <a href="', item_url($entry, $realmId), '" data-realm="', $realmId, '" rel="item=', $entry, '" onclick="return false;" id="armorset-', $arr['id'], '-item-', $entry, '"></a>
                                        <script>
                                            $(function()
                                            {
                                                load_ArmorSetItem(', $entry, ', ', $realmId, ', \'#armorset-', $arr['id'], '-item-', $entry, '\');
                                            });
                                        </script>';
                                    }
                                    
                                echo '
                                </td>
                                <td>', $realmStr, '</td>
                                <td>', $cat, '</td>
                                <td>', $arr['price'], '</td>
                                <td>
                                <span class="button-group">
                                    <a href="#" onclick="return ConstructEdit(', $arr['id'], ');" class="button icon edit">Edit</a>
                                    <a href="', base_url(), '/admin/armorsets/delete?id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete this armor set?\');" class="button icon remove danger">Remove</a>
                                </span>
                                </td>
                            </tr>';
                        }
                    }
                    unset($res);
                    ?>

                </tbody>
            </table>
            
        </div>
        <div class="column right third">
            <h2>Add new Armor Set</h2>
                
            <form action="<?=base_url()?>/admin/armorsets/submit" method="post" id="add_armorset_form">
                <section>
                    <label>
                        Title*
                    </label>
                    <div>
                        <input type="text" placeholder="Required" class="required" name="name" />
                    </div>
                </section>
                    
                <section>
                    <label>
                        Realm
                        <small>Which realm should the set be purchasable in.</small>
                    </label>
                    <div>
                        <select name="realm">
                            <option value="-1">All Realms</option>
                            <?php
                            $realms = $CORE->getRealmsConfig();

                            if ($realms)
                            {
                                foreach ($realms as $id => $data)
                                {
                                    echo '<option value="', $id, '">', $data['name'], '</option>';
                                }
                            }
                            unset($realms);
                            ?>
                        </select>
                    </div>
                </section>
                    
                <section>
                    <label>
                        Category*
                    </label>
                    <div>
                        <select name="category">
                            
                            <?php
                            //check if we have any cats at all
                            if (count($categories) > 0)
                            {
                                echo '<option>Select Category</option>';
                                
                                foreach ($categories as $id => $name)
                                {
                                    echo '<option value="', $id, '">', $name, '</option>';
                                }
                            }
                            else
                            {
                                echo '<option>Please add new category</option>';
                            }
                            ?>
                            
                        </select>
                    </div>
                </section>
                                
                <section>
                    <label>
                        Price*
                        <small>The price is in Gold Coins.</small>
                    </label>
                    <div>
                        <input type="text" placeholder="Required" class="required small" name="price" />
                    </div>
                </section>
                    
                <section>
                    <label>
                        Tier/Season
                        <small>Example: "Tier 9", "Season 11" etc.</small>
                    </label>
                    <div>
                        <input type="text" name="tier"/>
                    </div>
                </section>
                    
                <section>
                    <label>
                        Required Class
                    </label>
                    
                    <div>
                        <select name="class">
                        <option value="0">None</option>
                        <option value="1">Warrior</option>
                        <option value="2">Paladin</option>
                        <option value="3">Hunter</option>
                        <option value="4">Rogue</option>
                        <option value="5">Priest</option>
                        <option value="6">Death Knight</option>
                        <option value="7">Shaman</option>
                        <option value="8">Mage</option>
                        <option value="9">Warlock</option>
                        <option value="11">Druid</option>
                        </select>
                    </div>
                </section>
                    
                <section>
                    <label>
                        Type
                        <small>Example: "Elemental", "Arms" etc.</small>
                    </label>
                    <div>
                        <input type="text" name="type" />
                    </div>
                    </section>
                    
                    <section>
                    <label>
                        Items*
                        <small>Click on the text and enter item id.</small>
                    </label>
                    <div>
                        <input type="text" name="items" class="small" id="setItems" />
                    </div>
                    </section>
                    
                    <section>
                    <input type="submit" class="button primary big" value="Submit" />
                </section>
            </form>
            
        </div>
            
        <div class="clear"></div>
    </div>
</section>

<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.inputtags.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery-ui-1.10.0.sortable.min.js" type="text/javascript"></script>

<script type="text/javascript">
    var $configURL = '<?php echo base_url(); ?>';
    var $editable_onChangeTimer = null;

    function ConstructEdit(id) {
        //Check if it's already constructed
        if ($('#editor-'+id).length > 0) {
            $('#editor-'+id).fadeIn('fast');
            //break
            return false;
        }
        
        var $id = id;
        
        //Pull the data for this report
        $.ajax({
            type: "GET",
            url: $configURL + "/admin/armorsets/get_data",
            data: { action: 'armorsetDataAjax', id: $id },
            dataType: 'json',
            cache: false,
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus);
            },
            success: function(data) {
                var $data = data;
            
                //start by constructing overlay
                var Overlay = $('<div class="edit-overlay" id="editor-'+id+'"></div>');
                $('body').append(Overlay);

                //create the form container
                var container = $('<div class="edit-container" style="top: 50%;"></div>');
                Overlay.append(container);
                
                //make it draggable
                container.draggable();
                
                //create the form
                var form = $(
                        '<h2 style="margin-top:0;">Armorset Editing</h2>'+
                            '<form method="post" action="'+$configURL+'/admin/armorsets/submit_edit?id='+data.id+'">'+
                                '<input type="hidden" value="'+data.id+'" name="id" />'+
                                '<section>'+
                                    '<label>Title*</label>'+
                                    '<div><input type="text" placeholder="Required" class="required" name="name" value="'+data.name+'" /></div>'+
                                '</section>'+
                                
                                '<section>'+
                                    '<label>'+
                                        'Realm'+
                                        '<small>Which realm should the set be purchasable in.</small>'+
                                    '</label>'+
                                    '<div>'+
                                        '<select name="realm">'+
                                            '<option value="-1">All Realms</option>'+
                                            <?php
                                            $realmsConfig = $CORE->getRealmsConfig();

                                            if ($realmsConfig)
                                            {
                                                foreach ($realmsConfig as $id => $data)
                                                {
                                                    echo '\'<option value="', $id, '">', $data['name'], '</option>\'+';
                                                }
                                            }
                                            ?>
                                        '</select>'+
                                    '</div>'+
                                '</section>'+
                                
                                '<section>'+
                                    '<label>'+
                                        'Category*'+
                                    '</label>'+
                                    '<div>'+
                                        '<select name="category">'+
                                            
                                            <?php
                                            $res = $DB->query("SELECT id, name FROM `armorset_categories` ORDER BY name ASC;");
                                            //check if we have any cats at all
                                            if ($res->rowCount() > 0)
                                            {
                                                echo '\'<option>Select Category</option>\'+';
                                                
                                                while ($arr = $res->fetch())
                                                {
                                                    echo '\'<option value="', $arr['id'], '">', $arr['name'], '</option>\'+';
                                                }
                                            }
                                            else
                                            {
                                                echo '\'<option>Please add new category</option>\'+';
                                            }
                                            ?>
                                        
                                        '</select>'+
                                    '</div>'+
                                '</section>'+
                                            
                                '<section>'+
                                    '<label>'+
                                        'Price*'+
                                        '<small>The price is in Gold Coins.</small>'+
                                    '</label>'+
                                    '<div>'+
                                        '<input type="text" placeholder="Required" class="required small" name="price" value="'+data.price+'" />'+
                                    '</div>'+
                                '</section>'+
                                
                                '<section>'+
                                    '<label>'+
                                        'Tier/Season'+
                                        '<small>Example: "Tier 9", "Season 11" etc.</small>'+
                                    '</label>'+
                                    '<div>'+
                                        '<input type="text" name="tier" value="'+data.tier+'" />'+
                                    '</div>'+
                                '</section>'+
                                
                                '<section>'+
                                    '<label>'+
                                        'Required Class'+
                                    '</label>'+
                                
                                    '<div>'+
                                        '<select name="class">'+
                                            '<option value="0">None</option>'+
                                            '<option value="1">Warrior</option>'+
                                            '<option value="2">Paladin</option>'+
                                            '<option value="3">Hunter</option>'+
                                            '<option value="4">Rogue</option>'+
                                            '<option value="5">Priest</option>'+
                                            '<option value="6">Death Knight</option>'+
                                            '<option value="7">Shaman</option>'+
                                            '<option value="8">Mage</option>'+
                                            '<option value="9">Warlock</option>'+
                                            '<option value="11">Druid</option>'+
                                        '</select>'+
                                    '</div>'+
                                '</section>'+
                                
                                '<section>'+
                                    '<label>'+
                                        'Type'+
                                        '<small>Example: "Elemental", "Arms" etc.</small>'+
                                    '</label>'+
                                    '<div>'+
                                        '<input type="text" name="type" value="'+data.type+'" />'+
                                    '</div>'+
                                '</section>'+
                                
                                '<section>'+
                                    '<label>'+
                                        'Items*'+
                                        '<small>Click on the text and enter item id.</small>'+
                                    '</label>'+
                                    '<div>'+
                                        '<input type="text" name="items" class="small" id="setItems_'+data.id+'" />'+
                                    '</div>'+
                                '</section>'+
                                
                                '<section>'+
                                    '<div>'+
                                        '<span class="button-group" style="float:left;">'+
                                            '<a href="#" id="submit_btn" class="button icon edit">Submit</a>'+
                                            '<a href="#" onclick="return CloseEditor('+data.id+');" class="button icon remove danger">Cancel</a>'+
                                        '</span>'+
                                        '<div class="clear"></div>'+
                                    '</div>'+
                                '</section>'+
                            '</form>');
                container.append(form);
                setTimeout(function() {
                    container.animate({ marginTop: '-' + (container.outerHeight() / 2) + 'px' }, 'fast');
                }, 100);

                //Bind the submit
                form.find('#submit_btn').bind('click', function() {
                    $(this).parent().parent().parent().parent().submit();
                    
                    return false;
                });
                
                //Manage selects
                form.find('select').each(function(i, e) {
                    var selectName = $(this).attr('name');
                    
                    //manage diferent selects
                    switch (selectName)
                    {
                        case 'realm':
                        {
                            $(this).children('option').each(function()
                            {
                                if ($(this).val() == data.realm)
                                {
                                    $(this).attr('selected', 'selected');
                                }
                            });
                            break;
                        }
                        case 'category':
                        {
                            $(this).children('option').each(function()
                            {
                                if ($(this).val() == data.category)
                                {
                                    $(this).attr('selected', 'selected');
                                }
                            });
                            break;
                        }
                        case 'class':
                        {
                            $(this).children('option').each(function()
                            {
                                if ($(this).val() == data.class)
                                {
                                    $(this).attr('selected', 'selected');
                                }
                            });
                            break;
                        }
                    }
                });
                
                //setup the item list for the Add Armor Set
                $('#setItems_' + data.id).tagsInput({
                    wowheadItems: true,
                    defaultText: 'add a item',
                });
                
                //Add the current items
                var items = data.items.toString().split(",");
                
                $.each(items, function(ind, val) {
                    $('#setItems_' + data.id).addTag(val + "", {
                        wowheadItems: true,
                        focus: true,
                        unique: true
                    });
                });
                
                //test
                Overlay.fadeIn('fast', function() {
                    $(this).find('select').select_skin();
                });
            }
        });
        
        return false;
    }

    function CloseEditor(id) {
        $('#editor-'+id).fadeOut('fast');
    }

    $(document).ready(function(e) {
        //Init datatables
        if ($("#datatable_armorsets").length > 0) {
            var armorsetsTable = $("#datatable_armorsets").dataTable({
                "bAutoWidth": false,
                "aoColumnDefs": [ 
                    { "sWidth": "220px", "aTargets": [ 1 ] },
                    { "sWidth": "80px", "aTargets": [ 2 ] },
                    { "sWidth": "50px", "aTargets": [ 4 ] },
                    { "sWidth": "149px", "aTargets": [ 5 ] },
                    { "bSortable": false, "aTargets": [ 5 ] },
                    { "bSortable": false, "aTargets": [ 1 ] }
                ]
            });
            //sort the table
            //armorsetsTable.fnSort( [ [1, 'desc'] ] );
        }
        
        //setup the item list for the Add Armor Set
        $('#setItems').tagsInput({
            wowheadItems: true,
            getRealmId: 'select[name="realm"] option:selected',
            defaultText: 'add an item',
        });
        
        //custom settings for validation
        $("#add_armorset_form").validate({
            rules: {
                name: {
                    required: true,
                    maxlength: 250
                },
                price: {
                    required: true,
                    number: true
                }
            }
        });
    });

    function load_ArmorSetItem(entry, realmId, elementId) {
        var link = $(elementId);
        
        //check if we have the data of this item
        if (typeof $('body').data('item-'+entry+'-'+realmId) == 'object') {
            var quality = $('body').data('item-'+entry+'-'+realmId).quality;
            var icon = $('body').data('item-'+entry+'-'+realmId).icon;
            
            //append the item
            link.addClass(quality.toLowerCase());
            link.css('background', "url('http://wow.zamimg.com/images/wow/icons/large/" + icon.toLowerCase() + ".jpg');");
        } else {
            //prepare the ajax error handlers
            $.ajaxSetup({
                error:function(x,e) {
                    console.log('Parser Error.');
                },
                dataType: "json",
            });
            //get the icon
            $.get($configURL + "/ajax/getItem", {
                entry: entry,
                realm: realmId
            },
            function(data) {
                var quality = '0';
                var icon = 'inv_misc_questionmark';
                
                if (typeof data.quality_str != 'undefined') {
                    quality = data.quality_str;
                }
                if (typeof data.icon != 'undefined') {
                    icon = data.icon;
                }

                //save the data, and use it the next time we request it
                $('body').data('item-'+entry+'-'+realmId, {quality: quality, icon: icon});
                
                //append the item
                link.addClass(quality.toLowerCase());
                link.attr('style', "background: url('http://wow.zamimg.com/images/wow/icons/large/" + icon.toLowerCase() + ".jpg');");
            });
        }
    }
</script>