<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//print error messages
$ERRORS->PrintAny(array('pstore_armorsets_addcat', 'pstore_armorsets_delcat'));	
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li><a href="<?=base_url()?>/admin/armorsets">Armor Sets</a></li>
		<li class="current"><a href="<?=base_url()?>/admin/armorsets/categories">Armor Sets Categories</a></li>
	</ul>
</nav>
     
<!-- The content -->
<section id="content">

    <script src="<?=base_url()?>/admin_template/js/forms.js" type="text/javascript"></script>

    <div id="maintab">    
        <div class="column left twothird">
            <h2>Armor Sets Categories</h2>
            
            <table id="datatable" class="datatable datatable_editable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Category Name</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
            
                    <?php
                    $res = $DB->query("SELECT * FROM `armorset_categories` ORDER BY id DESC");
                    
                    if ($res->rowCount() > 0)
                    {
                        while ($arr = $res->fetch())
                        {
                            echo '
                            <tr>
                                <td>', $arr['id'], '</td>
                                <td>
                                    <input type="text" disabled="disabled" value="', $arr['name'], '" id="cat-editable-', $arr['id'], '" isOpen="false">
                                    <div id="cat-editable-', $arr['id'], '-infobox" class="datatable_editable-infobox">Errpr</div>
                                </td>
                                <td>
                                <span class="button-group">
                                    <a href="javascript: void(0);" onclick="return editableDatatable(this, \'cat-editable-', $arr['id'], '\', ', $arr['id'], ');" class="button icon edit">Edit</a>
                                    <a href="', base_url(), '/admin/armorsets/delete_category?id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete this category?\');" class="button icon remove danger">Remove</a>
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
            <h2>Add New Category</h2>
            
            <form action="<?=base_url()?>/admin/armorsets/submit_category" method="post">
                <section>
                <label>Title*</label>
                
                <div>
                    <input type="text" placeholder="Required" class="required" name="name" />
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

    $(document).ready(function(e) {
        if ($("#datatable2").length > 0) {
            var newsTable = $("#datatable").dataTable({
                "bFilter": false,
                "aoColumnDefs": [ 
                    { "bSortable": false, "aTargets": [ 2 ] }
                ]
            });
            //sort the table
            newsTable.fnSort( [ [1, 'desc'] ] );
        }
    });

    function editableDatatable(btn, id, record) {
        var input = $('#' + id);
        
        if (input.attr('isOpen') == 'false') {
            //activate the btn
            $(btn).addClass('active');

            //activate the input
            input.removeAttr('disabled');

            //set the isOpen attr
            input.attr('isOpen', 'true');

            //bind the onchange handler
            input.on('keyup', function() {
                clearTimeout($editable_onChangeTimer);

                //check if the input has any text at all
                if ($(this).val() != '') {
                    $editable_onChangeTimer = setTimeout(function() {
                        save_CategoryData(id, record);
                    }, 1000);
                }
            });
        } else {
            //deactivate the btn
            $(btn).removeClass('active');

            //deactivate the input
            input.attr('disabled', 'disabled');

            //set the isOpen attr
            input.attr('isOpen', 'false');

            //unbind the handler
            input.off('keyup');
        }
        
        return false;
    }

    function save_CategoryData(id, record) {
        var input = $('#' + id);
        var infobox = $('#' + id + '-infobox');
        
        $.post($configURL + "/admin/armorsets/submit_category_edit", { 
            id: record,
            name: input.val() 
        }, 
        function(data) {
            //check for errors
            if (data != 'OK') {
                infobox.html(data);
                infobox.fadeIn('fast');
            } else {
                //check if we need to hide the infobox
                if (infobox.css('display') != 'none') {
                    infobox.fadeOut('fast');
                }
            }
        });
    }
</script>