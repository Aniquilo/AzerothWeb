<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny(array('grant_permissions', 'user_modify'));
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li><a href="<?=base_url()?>/admin/users">Users</a></li>
        <li class="current"><a href="#maintab">User View</a></li>
	</ul>
</nav>
       
<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
        <h2>User Management</h2>
        
        <div>
    	
			<?php
            
				//Find the user records
				$webRes = $DB->prepare("SELECT * FROM `account_data` WHERE `id` = :acc LIMIT 1;");
				$webRes->bindParam(':acc', $account, PDO::PARAM_INT);
				$webRes->execute();
				
				//Verify the user
				if ($webRes->rowCount() == 0)
				{
					echo 'Error: The user id is invalid!';
				}
				else
				{
					//fetch the webrecord
					$webRecord = $webRes->fetch();
					
                    echo '
                    <div class="row">
                        <div class="column left">
                            <h3>Web Record</h3>
                            <table>';
							
							//Setup the rank
                            $Rank = new UserRank($webRecord['rank']);
                            
							//Setup the avatr
							//prepare the avatar
							if ((int)$webRecord['avatarType'] == AVATAR_TYPE_GALLERY)
							{
								$gallery = new AvatarGallery();
								$Avatar = $gallery->get((int)$webRecord['avatar']);
								unset($gallery);
							}
							else if ((int)$webRecord['avatarType'] == AVATAR_TYPE_UPLOAD)
							{
								$Avatar = new Avatar(0, $webRecord['avatar'], 0, AVATAR_TYPE_UPLOAD);
							}
							
							echo '
							<tr><td>ID</td><td>', $webRecord['id'], '</td></tr>
                            <tr><td>Display Name</td><td>', $webRecord['displayName'];
                            //Is allowed to change display name
                            if ($CORE->user->hasPermission(PERMISSION_MAN_USERS))
                            {
                                echo '<button class="button" id="changeDisplayNameBtn" style="float: right">Change Display Name</button>';
                            }
                            echo '</td></tr>
                            <tr><td>Silver</td><td>', $webRecord['silver'];
                            //Is allowed to manage currencies
                            if ($CORE->user->hasPermission(PERMISSION_MAN_USERS_CURRENCY))
                            {
                                echo '<button class="button" id="changeCurrenciesBtn" style="float: right">Change Currencies</button>';
                            }
                            echo '</td></tr>
							<tr><td>Gold</td><td>', $webRecord['gold'], '</td>
							<tr><td>Birthday</td><td>', $webRecord['birthday'], '</td></tr>
							<tr><td>Gender</td><td>', $webRecord['gender'], '</td></tr>
							<tr><td>Country</td><td>', $webRecord['country'], '</td></tr>
							<tr><td style="vertical-align: top">Avatar</td><td><img src="', ($Avatar->type() == AVATAR_TYPE_GALLERY ? base_url() . '/resources/avatars/'.$Avatar->string() : $Avatar->string()), '" /></td></tr>
							<tr>
								<td style="vertical-align: middle">Rank</td>
								<td>', $Rank->string(), ' [', $Rank->int(), ']';
                                //Is allowed to change users rank
                                if ($CORE->user->hasPermission(PERMISSION_MAN_USERS))
                                {
                                    echo '<button class="button" id="changeRankBtn" style="float: right">Change Rank</button>';
                                }
								echo '
								</td>
                            </tr>
							<tr><td>Latest IP</td><td>', $webRecord['last_ip'], '</td></tr>
							<tr><td>Latest Admin IP</td><td>', $webRecord['admin_last_ip'], '</td></tr>
							<tr><td>Registration IP</td><td>', $webRecord['reg_ip'], '</td></tr>
							<tr><td>Latest Login</td><td>', $webRecord['last_login2'], '</td></tr>
							<tr><td>Latest Admin Login</td><td>', $webRecord['admin_last_login2'], '</td></tr>
                            <tr>
                                <td>Account Status</td>
                                <td>', $webRecord['status'];
                                //Is allowed to change users rank
                                if ($CORE->user->hasPermission(PERMISSION_MAN_USERS))
                                {
                                    echo '<button class="button" id="changeStatusBtn" style="float: right">Change Status</button>';
                                }
                                echo '</td>
                            </tr>';
					
					echo '</table>
                        </div>
                        <div class="column right">
                            <h3>Server Record</h3>
                            <table>';
                            
                            //Find the auth record
                            $authRecord = $CORE->authentication->getACPUserDetails($account);
                            
                            if ($authRecord)
                            {
                                foreach ($authRecord as $i => $field)
                                {
                                    if ($field['name'] == 'Recruiter')
                                    {
                                        //Check the recruiter
                                        if ((int)$field['value'] > 0)
                                        {
                                            $recWebRes = $CORE->db->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
                                            $recWebRes->bindParam(':acc', $field['value'], PDO::PARAM_INT);
                                            $recWebRes->execute();
                                            
                                            if ($recWebRes->rowCount() > 0)
                                            {
                                                $recWebReco = $recWebRes->fetch();
                                                
                                                echo '<tr><td>recruiter</td><td><a href="', base_url(), '/admin/users/view?uid=', $field['value'], '">', $recWebReco['displayName'], '</a></td></tr>';
                                                
                                                unset($recWebReco);
                                            }
                                            unset($recWebRes);
                                        }
                                    }
                                    else
                                    {
                                        echo '<tr><td>', $field['name'], '</td><td>', $field['value'], '</td></tr>';
                                    }
                                }
                            }
										
					echo '
                            </table>
                        </div>
                        <div class="clear"></div>
                    </div>';
				}
                
                echo '<div class="row" style="padding: 30px 0 0 0;">
                    <div class="column left">
                        <h3>Account Activity</h3>
                        <table id="account-activity" class="datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Info</th>
                                    <th>IP</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                            </tbody>
                        </table>
                    </div>
                    <div class="column right">
                        <h3>Coins Activity</h3>
                        <table id="coins-activity" class="datatable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Source</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody id="sortable">
                            </tbody>
                        </table>
                    </div>
                    <div class="clear"></div>
                </div>';

				//Check if we can grant permissions
				if ($CORE->user->hasPermission(PERMISSION_MAN_USER_ROLES))
				{
					echo '
					<div>
						<h3>User Access Roles</h3>';
						
						//Roles per table row
                        $rolesPerRow = 10;

                        //Find the user records
                        $res = $DB->prepare("SELECT * FROM `rbac_roles` ORDER BY `role_id` ASC;");
                        $res->execute();
                        $roles = $res->fetchAll();
                        
                        // Get this user roles
                        $res = $DB->prepare("SELECT * FROM `rbac_user_role` WHERE `user_id` = :user_id ORDER BY `role_id` ASC;");
                        $res->execute(array('user_id' => $webRecord['id']));
                        $userRoles = array();

                        if ($res->rowCount() > 0)
                        {
                            while ($ur = $res->fetch())
                            {
                                $userRoles[$ur['role_id']] = true;
                            }
                        }
                        else
                        {
                            // Give player role if none set
                            $userRoles[2] = true;
                        }

						echo '
						<form method="post" action="', base_url(), '/admin/users/set_roles" id="permissions_form">
							<table>
								<tr>';
                            
								foreach ($roles as $i => $role)
								{
									echo '
									<td>
										<input type="checkbox" name="role[', $role['role_id'], ']" id="checkbox', $i, '" ', (isset($userRoles[$role['role_id']]) ? 'checked' : ''), '>
										<label for="checkbox', $i, '" class="prettyCheckbox checkbox list">
											', $role['role_name'], '
										</label>
									</td>';
									
                                    echo ((($i + 1) % $rolesPerRow) == 0 ? '</tr><tr>' : '');
								}
								unset($i, $role);
						
							echo '
								</tr>
								<tr>
									<td colspan="', $rolesPerRow, '"><input type="submit" value="Apply" class="button primary submit" /></td>
								</tr>
							</table>
							
							<input type="hidden" value="', $webRecord['id'], '" name="uid" />
						</form>';
						
						unset($res, $rolesPerRow);
			
					echo '
					</div>';
				}
				
         		unset($webRes); 
			?> 
            
        </div>
    </div>
</section>

<div class="edit-overlay edit-modal" id="editorDisplayName">
    <div class="edit-container">
        <h2 style="margin-top:0;">Change Display Name</h2>
        <form method="post" action="<?=base_url()?>/admin/users/change_displayname">
            <input type="hidden" value="<?=$account?>" name="id" />
            <section>
                <label>Display Name*</label>
                <div><input type="text" placeholder="Required" class="required" name="displayName" value="" /></div>
            </section>
            <section>
                <div>
                    <span class="button-group" style="float:left;">
                        <button type="submit" class="button icon edit">Submit</button>
                        <button type="button" class="button icon remove danger">Cancel</button>
                    </span>
                    <div class="clear"></div>
                </div>
            </section>
        </form>
    </div>
</div>

<div class="edit-overlay edit-modal" id="editorCurrency">
    <div class="edit-container">
        <h2 style="margin-top:0;">Change Currencies</h2>
        <form method="post" action="<?=base_url()?>/admin/users/change_currency">
            <input type="hidden" value="<?=$account?>" name="id" />
            <section>
                <label>Silver*</label>
                <div><input type="text" placeholder="Required" class="required" name="silver" value="" /></div>
            </section>
            <section>
                <label>Gold*</label>
                <div><input type="text" placeholder="Required" class="required" name="gold" value="" /></div>
            </section>
            <section>
                <div>
                    <span class="button-group" style="float:left;">
                        <button type="submit" class="button icon edit">Submit</button>
                        <button type="button" class="button icon remove danger">Cancel</button>
                    </span>
                    <div class="clear"></div>
                </div>
            </section>
        </form>
    </div>
</div>

<div class="edit-overlay edit-modal" id="editorRank">
    <div class="edit-container">
        <h2 style="margin-top:0;">Change Rank</h2>
        <form method="post" action="<?=base_url()?>/admin/users/change_rank">
            <input type="hidden" value="<?=$account?>" name="id" />
            <section>
                <label>Rank*</label>
                <div>
                    <select name="rank">
                        <?php 
                        $RanksData = new RankStringData();
                        foreach ($RanksData->data as $trank => $name)
                        {
                            echo '<option value="', $trank, '" data-rank="', $trank, '" ', ($trank == $Rank->int() ? 'selected="selected"' : ''), '>', $name, '</option>';
                        }
                        ?>
                    </select>
                </div>
            </section>
            <section>
                <div>
                    <span class="button-group" style="float:left;">
                        <button type="submit" class="button icon edit">Submit</button>
                        <button type="button" class="button icon remove danger">Cancel</button>
                    </span>
                    <div class="clear"></div>
                </div>
            </section>
        </form>
    </div>
</div>

<div class="edit-overlay edit-modal" id="editorStatus">
    <div class="edit-container">
        <h2 style="margin-top:0;">Change Status</h2>
        <form method="post" action="<?=base_url()?>/admin/users/change_status">
            <input type="hidden" value="<?=$account?>" name="id" />
            <section>
                <label>Status*</label>
                <div>
                    <select name="status">
                        <option value="pending">Pending</option>
                        <option value="active">Active</option>
                        <option value="disabled">Disabled</option>
                    </select>
                </div>
            </section>
            <section>
                <div>
                    <span class="button-group" style="float:left;">
                        <button type="submit" class="button icon edit">Submit</button>
                        <button type="button" class="button icon remove danger">Cancel</button>
                    </span>
                    <div class="clear"></div>
                </div>
            </section>
        </form>
    </div>
</div>

<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>

<script>
	$(document).ready(function()
	{
        var table1 = $('#account-activity').dataTable(
		{
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": $BaseURL + "/admin/datatable/account_activity?uid=<?=$account?>",
		});
        //sort the table
        table1.fnSort( [ [0, 'desc'] ] );

        var table2 = $('#coins-activity').dataTable(
		{
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": $BaseURL + "/admin/datatable/coins_activity?uid=<?=$account?>",
		});
        //sort the table
        table2.fnSort( [ [0, 'desc'] ] );

        $('#changeDisplayNameBtn').on('click', function() {
            $.get($BaseURL + "/admin/users/info?uid=<?=$account?>", function(resp) {
                if (resp.error == undefined) {
                    $('#editorDisplayName').find('[name="displayName"]').val(resp.displayName);
                    $('#editorDisplayName').editModal('show');
                }
            });
        });

        $('#changeCurrenciesBtn').on('click', function() {
            $.get($BaseURL + "/admin/users/info?uid=<?=$account?>", function(resp) {
                if (resp.error == undefined) {
                    $('#editorCurrency').find('[name="silver"]').val(resp.silver);
                    $('#editorCurrency').find('[name="gold"]').val(resp.gold);
                    $('#editorCurrency').editModal('show');
                }
            });
        });

        $('#changeRankBtn').on('click', function() {
            $.get($BaseURL + "/admin/users/info?uid=<?=$account?>", function(resp) {
                if (resp.error == undefined) {
                    const select = $('#editorRank').find('[name="rank"]');
                    const options = select.find('option');
                    options.each(function(i, e) {
                        $(e).attr('selected', null);
                    });
                    select.find('option[value="'+resp.rank+'"]').prop('selected', true);
                    select.trigger('change');
                    $('#editorRank').editModal('show');
                }
            });
        });

        $('#changeStatusBtn').on('click', function() {
            $.get($BaseURL + "/admin/users/info?uid=<?=$account?>", function(resp) {
                if (resp.error == undefined) {
                    const select = $('#editorStatus').find('[name="status"]');
                    const options = select.find('option');
                    options.each(function(i, e) {
                        $(e).attr('selected', null);
                    });
                    select.find('option[value="'+resp.status+'"]').prop('selected', true);
                    select.trigger('change');
                    $('#editorStatus').editModal('show');
                }
            });
        });
    });
</script>