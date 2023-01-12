<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//print error and success messages
$ERRORS->PrintAny('rbac');
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="<?=base_url()?>/admin/rbac">Access Role Management</a></li>
	</ul>
</nav>
  
<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
    	
        <div class="column left twothird">
        	<h2>Access Role Management</h2>
        
            <table id="datatable" class="datatable">
          
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Info</th>
                        <th>X</th>
                    </tr>
                </thead>
                <tbody id="sortable">
                    <?php
                    //Find the user records
                    $res = $DB->prepare("SELECT * FROM `rbac_roles` ORDER BY `role_id` ASC;");
                    $res->execute();
                    
                    while ($row = $res->fetch())
                    {
                        echo '<tr>';
                        echo '<td>', $row['role_id'], '</td>';
                        echo '<td>', $row['role_name'], '</td>';
                        echo '<td>', $row['role_desc'], '</td>';
                        echo '<td>';
                        echo '<span class="button-group">';
                        echo '<a href="', base_url(), '/admin/rbac/edit?id=', $row['role_id'], '" class="button icon edit">Edit</a>';

                        // Dont allow deletion of Guest, Player and Owner
                        if ((int)$row['role_id'] != 7 && (int)$row['role_id'] != 1 && (int)$row['role_id'] != 2) {
                            echo '<a href="', base_url(), '/admin/rbac/delete?id=', $row['role_id'], '" onclick="return deletecheck(\'Are you sure you want to delete this role?\');" class="button icon remove danger">Remove</a>';
                        }

                        echo '</span>';
                        echo '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            
            </table>
        
        </div>
        
        <div class="column right third">
        	<h2>Add new Role</h2>
            
            <form action="<?=base_url()?>/admin/rbac/submit" method="post" id="add_role_form">
	            <section>
	                <label for="name">Name*</label>
	                <div>
	                    <input type="text" name="name" id="name" placeholder="Required" class="required" />
	                </div>
	            </section>
	            
                <section>
	                <label for="desc">Description</label>
	                <div>
	                    <textarea name="desc" id="desc" placeholder="Optional"></textarea>
	                </div>
	            </section>
                
                <section>
                    <label>Permissions</label>
                    <div>
                        <?php
                        $res = $DB->prepare("SELECT * FROM `rbac_permissions` ORDER BY `perm_id` ASC;");
                        $res->execute();
                        
                        while ($row = $res->fetch())
                        {
                            echo '<div class="column left">
                                    <input type="checkbox" value="1" id="perm_', $row['perm_id'], '" name="permissions[', $row['perm_id'], ']" />
                                    <label for="perm_', $row['perm_id'], '" class="prettyCheckbox checkbox list">
                                        ', $row['perm_desc'], '
                                    </label>
                            </div>';
                        }
                        ?>
                        <div class="clear"></div>
                    </div>
                </section>

	            <section>
	              <input type="submit" class="button primary big" value="Submit" />
	            </section>
			</form>
            
        </div>
        
        <div class="clear"></div>
    </div>

<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>

<script type="text/javascript">
$(document).ready(function(e)
{
	$('#datatable').dataTable(
	{
		"aoColumnDefs": [
            { "bSortable": false, "aTargets": [ 2 ] },
			{ "bSortable": false, "aTargets": [ 3 ] }
		],
	});
});
</script>