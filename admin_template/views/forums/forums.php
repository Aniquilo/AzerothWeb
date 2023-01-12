<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny(array('addForum', 'editForum', 'deleteForum'));
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="<?=base_url()?>/admin/forums">Forums</a></li>
		<li><a href="<?=base_url()?>/admin/forums/categories">Categories</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
		<div class="column left twothird">
            <h2>Forums Management</h2>
            
            <style>
                .sortable-placeholder {
                    height: 45px;
                }
			</style>

			<table id="datatable2" class="datatable datatable_editable">
          
                <thead>
					<th>ID</th>
					<th>Position</th>
					<th>Category Name</th>
					<th>Forum Name</th>
					<th>Actions</th>
                </thead>
                <tbody id="sortable">
        
                <?php
                
                $res = $DB->query("	SELECT 
										`wcf_forums`.*, 
										`wcf_categories`.`name` AS category_name, 
										`wcf_categories`.`flags` AS category_flags 
									FROM `wcf_forums` 
									LEFT JOIN `wcf_categories` ON `wcf_forums`.`category` = `wcf_categories`.`id` 
									ORDER BY `wcf_forums`.`category`, `wcf_forums`.`position` ASC;");
                
                if ($res->rowCount() > 0)
                {
                    while ($arr = $res->fetch())
                    {
						$isClass = ((int)$arr['category_flags'] & WCF_FLAGS_CLASSES_LAYOUT);

                        echo '
						  <tr data-id="', $arr['id'], '" data-cat="', $arr['category'], '" data-editing="0">
						  	<td>', $arr['id'], '</td>
							<td class="sortable-handle" style="cursor: move;">', $arr['position'], '</td>
							<td>', $arr['category_name'], '</td>
                            <td>
                                <div class="info" style="width: 500px;">
                                    <div>', $arr['name'], '', ((strlen($arr['name']) == 0 && $isClass) ? $CORE->realms->getClassString($arr['class']) : ''), '</div>
									<div style="color: #999;">', $arr['description'], '</div>
                                </div>
                            </td>
                            <td>
                              <span class="button-group">
                                <a href="', base_url(), '/admin/forums/edit?id=', $arr['id'], '" class="button icon edit">Edit</a>
                                <a href="', base_url(), '/admin/forums/delete?id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete this forum?\');" class="button icon remove danger">Remove</a>
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
		
			<h2>Add New Forum</h2>
			<form action="<?=base_url()?>/admin/forums/submit_create" method="post" id="add_forum_form" name="add-forum">
				<section>
					<label>Name*</label>
					
					<div>
						<input type="text" name="name" />
					</div>
				</section>
				
				<section>
					<label>Description</label>
					
					<div>
						<textarea name="description"></textarea>
					</div>
				</section>
				
				<section>
					<label>Category</label>
					
					<div>
						<select name="category" id="category-select">
							<option value="0">Select category</option>
							<?php
							$res = $DB->query("SELECT * FROM `wcf_categories` ORDER BY `id` ASC;");

							if ($res->rowCount() > 0)
							{
								while ($arr = $res->fetch())
								{
									$isClass = ((int)$arr['flags'] & WCF_FLAGS_CLASSES_LAYOUT);

									echo '<option value="', $arr['id'], '" data-class="', ($isClass ? '1' : '0'), '">', $arr['name'], '</option>';
								}
							}
							unset($res);
							?>
						</select>
					</div>
				</section>
				
				<section id="class-section" style="display: none">
					<label>Class</label>
					
					<div>
						<select name="class">
							<option value="0">Select a class</option>
							<?php
							foreach ($config['wow_classes'] as $id => $class)
							{
								echo '<option value="', $id, '">', $class, '</option>';
							}
							?>
						</select>
					</div>
				</section>
				
                <section>
					<label>
                        View Roles
                        <small>Roles allowed to view this forum.</small>
                    </label>
					<div>
                        <table style="width: 96%;">
                            <tr>
                                <?php
                                foreach ($roles as $i => $role)
								{
									echo '
									<td>
										<input type="checkbox" name="view_roles[', $role['role_id'], ']" data-group="view" data-role="', $role['role_id'], '" id="view-checkbox', $i, '" ', ($role['role_id'] == 0 ? 'checked' : ''), '>
										<label for="view-checkbox', $i, '" class="prettyCheckbox checkbox list">
											', $role['role_name'], '
										</label>
									</td>';
									
                                    echo ((($i + 1) % floor(count($roles) / 2)) == 0 ? '</tr><tr>' : '');
								}
								unset($i, $role);
                                ?>
                            </tr>
                        </table>
					</div>
                </section>

                <section>
					<label>
                        Topic Roles
                        <small>Roles allowed to start topics in this forum.</small>
                    </label>
					<div>
                        <table style="width: 96%;">
                            <tr>
                                <?php
                                foreach ($roles as $i => $role)
								{
									echo '
									<td>
										<input type="checkbox" name="topic_roles[', $role['role_id'], ']" data-group="topic" data-role="', $role['role_id'], '" id="topic-checkbox', $i, '" ', ($role['role_id'] == 0 ? 'checked' : ''), '>
										<label for="topic-checkbox', $i, '" class="prettyCheckbox checkbox list">
											', $role['role_name'], '
										</label>
									</td>';
									
                                    echo ((($i + 1) % floor(count($roles) / 2)) == 0 ? '</tr><tr>' : '');
								}
								unset($i, $role);
                                ?>
                            </tr>
                        </table>
					</div>
                </section>

                <section>
					<label>
                        Post Roles
                        <small>Roles allowed to make posts in this forum.</small>
                    </label>
					<div>
                        <table style="width: 96%;">
                            <tr>
                                <?php
                                foreach ($roles as $i => $role)
								{
									echo '
									<td>
										<input type="checkbox" name="post_roles[', $role['role_id'], ']" data-group="post" data-role="', $role['role_id'], '" id="post-checkbox', $i, '" ', ($role['role_id'] == 0 ? 'checked' : ''), '>
										<label for="post-checkbox', $i, '" class="prettyCheckbox checkbox list">
											', $role['role_name'], '
										</label>
									</td>';
									
                                    echo ((($i + 1) % floor(count($roles) / 2)) == 0 ? '</tr><tr>' : '');
								}
								unset($i, $role);
                                ?>
                            </tr>
                        </table>
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
<script src="<?=base_url()?>/admin_template/js/jquery-ui-1.10.0.sortable.min.js" type="text/javascript"></script>

<script type="text/javascript">
    var $configURL = '<?php echo base_url(); ?>';
    var $editable_onChangeTimer = null;
    var $click_bug_fix = new Date().getTime();

    $(document).ready(function(e) {
        <?php
		if ($formData = $ERRORS->GetFormData('addForum'))
		{	
			echo '
			var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
			restoreFormData(\'add-forum\', savedFormData);';
		}
		unset($formData);
        ?>
        
        //Init datatables
        if ($("#datatable2").length > 0) {
            var newsTable = $("#datatable2").dataTable({
                "bPaginate": false,
                "bFilter": false,
                "aoColumnDefs": [ 
                    { "bSortable": false, "aTargets": [ 0 ] },
                    { "bSortable": false, "aTargets": [ 1 ] },
                    { "bSortable": false, "aTargets": [ 2 ] },
                    { "bSortable": false, "aTargets": [ 3 ] },
                    { "bSortable": false, "aTargets": [ 4 ] }
                ]
            });
            //sort the table
            newsTable.fnSort( [ [2, 'asc'], [1, 'asc'] ] );
        }
        
        //custom settings for validation
        $("#add_forum_form").validate({
            rules: {
                name: {
                    required: true,
                    minlength: 1,
                    maxlength: 250
                },
            }
        });

        $("#sortable").sortable({ 
            handle: ".sortable-handle",
            placeholder: "sortable-placeholder",
            update: function(event, ui) {
                var category = ui.item.attr('data-cat');
                var elements = $('#sortable').find('tr[data-cat="' + category + '"]');
                var data = new Array();

                for (var i = 0; i < elements.length; i++) {
                    data[i] = parseInt($(elements[i]).attr('data-id'));
                }
                
                $.post($BaseURL + "/admin/forums/submit_order", {
                    'order': data
                });
            }
        });
        $( "#sortable").disableSelection();

        $('#category-select').on('change', function() {
            var selected = $(this).find('option:selected');
            var isClass = selected.attr('data-class');

            if (isClass == '1') {
                $('#class-section').show();
            } else {
                $('#class-section').hide();
            }
        });

        $('input[type="checkbox"]').on('change', function() {
            const group = $(this).attr('data-group');

            if ($(this).prop('checked') && $(this).attr('data-role') == '0') {
                $('input[data-group="'+group+'"]').each(function(i, e) {
                    if ($(e).attr('data-role') != '0') {
                        $(e).prop('checked', false);
                        $(e).trigger('change');
                    }
                });
            } else if ($(this).prop('checked') && $(this).attr('data-role') != '0') {
                $('input[data-group="'+group+'"]').each(function(i, e) {
                    if ($(e).attr('data-role') == '0') {
                        $(e).prop('checked', false);
                        $(e).trigger('change');
                    }
                });
            }
        });
    });
</script>