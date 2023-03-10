<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//print error messages
$ERRORS->PrintAny(array('forums_addcat', 'forums_delcat'));
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li><a href="<?=base_url()?>/admin/forums">Forums</a></li>
		<li class="current"><a href="<?=base_url()?>/admin/forums/categories">Categories</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">

        <div class="column left twothird">
            <h2>Forum Categories</h2>
            
            <style>
                .sortable-placeholder {
                    height: 45px;
                }
            </style>
            
            <table id="datatable2" class="datatable datatable_editable">
          
                <thead>
                    <th>Position</th>
                    <th>Category Name</th>
                    <th>Actions</th>
                </thead>
                <tbody id="sortable">
        
                <?php
                
                $res = $DB->query("SELECT * FROM `wcf_categories` ORDER BY position ASC;");
                
                if ($res->rowCount() > 0)
                {
                    while ($arr = $res->fetch())
                    {
                        echo '
                          <tr data-id="', $arr['id'], '">
                            <td style="cursor: move;">', $arr['position'], '</td>
                            <td>
                                <div style="width: 500px;">
                                    <input type="text" disabled="disabled" value="', $arr['name'], '" id="cat-editable-', $arr['id'], '" isOpen="false" style="float: left;">
                                    <div style="float: left; height: 23px; margin-left: 12px; margin-top: -4px; display: none;">
                                        <select id="cat-editable-', $arr['id'], '-select">
                                            <option value="0">Default Style</option>
                                            <option value="1" ', (((int)$arr['flags'] & WCF_FLAGS_CLASSES_LAYOUT) ? 'selected="selected"' : ''), '>Classes Style</option>
                                        </select>
                                    </div>
                                    <div class="clear"></div>
                                </div>
                                <div id="cat-editable-', $arr['id'], '-infobox" class="datatable_editable-infobox">Error</div>
                            </td>
                            <td>
                              <span class="button-group">
                                <a href="javascript: void(0);" onclick="return editableDatatable(this, \'cat-editable-', $arr['id'], '\', ', $arr['id'], ');" class="button icon edit">Edit</a>
                                <a href="', base_url(), '/admin/forums/delete_category?id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete this category?\');" class="button icon remove danger">Remove</a>
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
              
              <form action="<?=base_url()?>/admin/forums/submit_create_category" method="post" id="add_forumcat_form">
                <section>
                  <label>Title*</label>
                  
                  <div>
                    <input type="text" placeholder="Required" class="required" name="name" />
                  </div>
                </section>
                
                <section>
                  <label>Design</label>
                  
                  <div>
                    <select name="style">
                        <option value="0" selected="selected">Default Style</option>
                        <option value="1">Classes Style</option>
                    </select>
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

<script src="<?=base_url()?>/admin_template/js/forms.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery-ui-1.10.0.sortable.min.js" type="text/javascript"></script>

<script type="text/javascript">
var $configURL = '<?php echo base_url(); ?>';
var $editable_onChangeTimer = null;
var $click_bug_fix = new Date().getTime();

$(document).ready(function(e)
{
    //Init datatables
	if ($("#datatable2").length > 0) {
	 	var newsTable = $("#datatable2").dataTable({
            "bPaginate": false,
			"bFilter": false,
			"aoColumnDefs": [ 
				{ "bSortable": false, "aTargets": [ 0 ] },
				{ "bSortable": false, "aTargets": [ 1 ] },
	      		{ "bSortable": false, "aTargets": [ 2 ] }
	    	]
		});
		//sort the table
		newsTable.fnSort( [ [0, 'asc'] ] );
	}
	
	//custom settings for validation
	$("#add_forumcat_form").validate({
		rules: {
			name: {
				required: true,
				maxlength: 250
			},
	  	}
    });
    
    $("#sortable").sortable({ 
        placeholder: "sortable-placeholder",
        update: function () {          
            var elements = $('#sortable').find('tr');
            var data = new Array();
            
            for (var i = 0; i < elements.length; i++) {
                data[$(elements[i]).index()] = parseInt($(elements[i]).attr('data-id'));
            }
            
            $.post($BaseURL + "/admin/forums/submit_category_order", {
                'order': data
            });
        }
    });
    $( "#sortable").disableSelection();
});

function editableDatatable(btn, id, record)
{
	var input = $('#' + id);
	
	if (Math.abs($click_bug_fix - new Date().getTime()) < 300) {
		return false;
	}
	
	if (input.attr('isOpen') == 'false') {
		//activate the btn
        $(btn).addClass('active');
        
		//activate the input
        input.removeAttr('disabled');
        
		//activate the select
        input.parent().find('.cmf-skinned-select').parent().css('display', 'block');
        
		//set the isOpen attr
		input.attr('isOpen', 'true');
	} else {
		//deactivate the btn
        $(btn).removeClass('active');
        
		//deactivate the input
        input.attr('disabled', 'disabled');
        
		//deactivate the select
        input.parent().find('.cmf-skinned-select').parent().css('display', 'none');
        
		//set the isOpen attr
        input.attr('isOpen', 'false');
        
		//save for the data
		save_CategoryData(id, record);
	}
	
	$click_bug_fix = new Date().getTime();
	
	return false;
}

function save_CategoryData(id, record)
{
	var input = $('#' + id);
	var select = $('#' + id + '-select');
	var infobox = $('#' + id + '-infobox');
	
	$.post($configURL + "/admin/forums/submit_edit_category", { 
        id: record,
        name: input.val(),
        style: select.find(':selected').val()
    }, 
    function(data) {
        //check for errors
        if (data != 'OK' && data != 'SKIP') {
            infobox.html(data);
            infobox.fadeIn('fast');
        } else if (data != 'SKIP') {
            //check if we need to hide the infobox
            if (infobox.css('display') != 'none') {
                infobox.fadeOut('fast');
            } else {
                infobox.html('Saved!');
                infobox.css('display', 'block');
                infobox.fadeOut('slow');
            }
        }
    });
}
</script>