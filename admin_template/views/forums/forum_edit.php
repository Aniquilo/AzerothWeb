<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny('editForum');
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
        <li><a href="<?=base_url()?>/admin/forums">Forums</a></li>
        <li><a href="<?=base_url()?>/admin/forums/categories">Categories</a></li>
        <li class="current"><a href="<?=base_url()?>/admin/forums/edit?id=<?php echo $forum['id']; ?>">Edit Forum</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
        <h2>Editing Forum - <?php echo $forum['name']; ?></h2>
        
        <div class="form">
    
        	<form method="post" action="<?php echo base_url(); ?>/admin/forums/submit_edit" id="edit-forum" name="edit-forum">
                <section>
                  <label for="label">
                    Name*
                    <small>250 characters maximum.</small>
                  </label>
                
                  <div>
                    <input id="label" name="name" type="text" value="<?php echo htmlspecialchars(stripslashes($forum['name'])); ?>" />
                  </div>
                </section>
                
                <section>
                  <label for="textarea_s">
                    Description
                    <small>350 characters maximum.</small>
                  </label>
                  
                  <div>
                    <textarea id="textarea_s" name="description" rows="5"><?php echo htmlspecialchars(stripslashes($forum['description'])); ?></textarea>
                  </div>
                </section>
                
                <section>
					<label for="category">Category</label>
					
					<div>
						<select id="category" name="category">
							<option value="0" <?=($forum['category'] == '0' ? 'selected' : '')?>>Select category</option>
							<?php foreach ($categories as $arr)
                            {
                                $isClass = ((int)$arr['flags'] & WCF_FLAGS_CLASSES_LAYOUT);

                                echo '<option value="', $arr['id'], '" data-class="', ($isClass ? '1' : '0'), '" ', ($forum['category'] == $arr['id'] ? 'selected' : ''), '>', $arr['name'], '</option>';
                            } ?>
						</select>
					</div>
                </section>
                
                <section id="class-section" style="display: <?=(!$isCategoryClass ? 'none' : '')?>">
					<label>Class</label>
					
					<div>
						<select name="class">
							<option value="0" <?=($forum['class'] == '0' ? 'selected' : '')?>>Select a class</option>
							<?php
							foreach ($config['wow_classes'] as $id => $class)
							{
								echo '<option value="', $id, '" ', ($forum['class'] == $id ? 'selected' : ''), '>', $class, '</option>';
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
                                $view_roles = explode(',', $forum['view_roles']);
                                
                                foreach ($roles as $i => $role)
								{
									echo '
									<td>
										<input type="checkbox" name="view_roles[', $role['role_id'], ']" data-group="view" data-role="', $role['role_id'], '" id="view-checkbox', $i, '" ', (in_array($role['role_id'], $view_roles) ? 'checked' : ''), '>
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
                                $topic_roles = explode(',', $forum['topic_roles']);
                                
                                foreach ($roles as $i => $role)
								{
									echo '
									<td>
										<input type="checkbox" name="topic_roles[', $role['role_id'], ']" data-group="topic" data-role="', $role['role_id'], '" id="topic-checkbox', $i, '" ', (in_array($role['role_id'], $topic_roles) ? 'checked' : ''), '>
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
                                $post_roles = explode(',', $forum['post_roles']);
                                
                                foreach ($roles as $i => $role)
								{
									echo '
									<td>
										<input type="checkbox" name="post_roles[', $role['role_id'], ']" data-group="post" data-role="', $role['role_id'], '" id="post-checkbox', $i, '" ', (in_array($role['role_id'], $post_roles) ? 'checked' : ''), '>
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
                
                <input type="hidden" name="id" value="<?php echo $forum['id']; ?>" />
         	</form>

            <br />
            <p>
                <input type="submit" class="button primary submit" value="Submit" onclick="$('#edit-forum').submit(); return false" />
            </p>

        </div>
    </div>
</section>

<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/forms.js" type="text/javascript"></script>

<script type="text/javascript">
	var $configURL = '<?php echo base_url(); ?>';
	
	$(document).ready(function(e)
	{
		<?php
		if ($formData = $ERRORS->GetFormData('editForum'))
		{	
			echo '
			var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
			restoreFormData(\'edit-forum\', savedFormData);';
		}
		unset($formData);
		?>
		
		//custom settings for validation
		$("#edit-forum").validate({
			rules: {
				name: {
                    required: true,
                    minlength: 1,
					maxlength: 250
				}
		  	}
        });

        $('#category').on('change', function() {
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