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
        <li><a href="<?=base_url()?>/admin/rbac">Access Role Management</a></li>
		<li class="current"><a href="<?=base_url()?>/admin/rbac/edit?id=<?=$id?>">Edit Role</a></li>
	</ul>
</nav>
  
<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
    	
        <div>
        	<h2>Editing role <?=$row['role_name']?></h2>
            
            <form action="<?=base_url()?>/admin/rbac/edit_submit" method="post" id="add_role_form">
	            <section>
	                <label for="name">Name*</label>
	                <div>
	                    <input type="text" name="name" id="name" placeholder="Required" class="required" value="<?=$row['role_name']?>" />
	                </div>
	            </section>
	            
                <section>
	                <label for="desc">Description</label>
	                <div>
	                    <textarea name="desc" id="desc" placeholder="Optional"><?=$row['role_desc']?></textarea>
	                </div>
	            </section>
                
                <section>
                    <label>Permissions</label>
                    <div>
                        <?php
                        // Get all the permissions
                        $res = $DB->prepare("SELECT * FROM `rbac_permissions` ORDER BY `perm_id` ASC;");
                        $res->execute();
                        $permissions = $res->fetchAll();

                        // Get the role permissions
                        $res = $DB->prepare("SELECT * FROM `rbac_role_perm` WHERE `role_id` = :role_id ORDER BY `perm_id` ASC;");
                        $res->execute(array('role_id' => $id));
                        $rolePermissions = $res->fetchAll();
                        
                        foreach ($permissions as $perm)
                        {
                            $isset = false;

                            foreach ($rolePermissions as $rolePerm)
                            {
                                if ((int)$rolePerm['perm_id'] == (int)$perm['perm_id'])
                                {
                                    $isset = true;
                                    break;
                                }
                            }

                            echo '<div class="column forth left">
                                    <input type="checkbox" value="1" id="perm_', $perm['perm_id'], '" name="permissions[', $perm['perm_id'], ']" ', ($isset ? 'checked' : ''), ' />
                                    <label for="perm_', $perm['perm_id'], '" class="prettyCheckbox checkbox list">
                                        ', $perm['perm_desc'], '
                                    </label>
                            </div>';
                        }
                        ?>
                        <div class="clear"></div>
                    </div>
                </section>

	            <section>
                  <input type="hidden" name="id" value="<?=$id?>" />
	              <input type="submit" class="button primary big" value="Submit" />
	            </section>
			</form>
            
        </div>
        
        <div class="clear"></div>
    </div>
