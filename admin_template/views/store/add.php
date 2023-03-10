<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny('add_storeitem');
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li><a href="<?=base_url()?>/admin/store">Item Store</a></li>
        <li class="current"><a href="<?=base_url()?>/admin/store/add">Add new item</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">

	<div class="tab" id="maintab">
		<h2>Add Store Item</h2>

        <div class="form">
    
            <form method="post" action="<?php echo base_url(); ?>/admin/store/submit_add" name="addItemForm" id="add-item">
            
                <section>
                  	<label for="label">
                    	Item Entry*
                    	<small>The entry from item_template.</small>
                  	</label>
                  	<div>
                    	<input id="label" name="entry" type="text" class="required item-entry" />
                        <div class="for_custom" style="display: none">
                        	<input type="button" class="button primary submit" value="Item Autofill" onclick="return ItemAutofill();" />
                        </div>
                  	</div>
                </section>
                
                <section>
                  	<label for="label4">
                    	Realms*
                    	<small>Hold ctrl to select multiple.</small>
                  	</label>
                  	<div>
                        <select multiple name="realms[]" id="label4" class="realms-select">
                            <?php foreach ($CORE->realms->getRealms() as $realm) { ?>
                                <option value="<?=$realm->getId()?>" selected><?=$realm->getName()?></option>
                            <?php } ?>
                        </select>
                  	</div>
                </section>
                
                <section>
                  	<label for="label5">
                    	Gold Price*
                    	<small>Set to 0 if you wish to disable this currency.</small>
                  	</label>
                  	<div>
                    	<input id="label5" name="gold" type="text" class="required" />
                  	</div>
                </section>
                
                <section>
                  	<label for="label6">
                    	Silver Price*
                    	<small>Set to 0 if you wish to disable this currency.</small>
                  	</label>
                  	<div>
                    	<input id="label6" name="silver" type="text" class="required" />
                  	</div>
                </section>

                <section>
                	<label>
                        Custom Item
                        <small>You can change info when doing custom item.</small>
			     	</label>
                  
                    <div>
                        <div class="column left" style="width: 50px">
                          	<input type="radio" value="0" id="customNo" name="custom" checked />
                          	<label for="customNo" class="prettyCheckbox checkbox list">
                                No
                         	</label>
                        </div>
                        <div class="column left">
                          	<input type="radio" value="1" id="customYes" name="custom" />
                          	<label for="customYes" class="prettyCheckbox checkbox list">
                                Yes
                         	</label>
                        </div>
                        <div class="clear"></div>
                    </div>
                </section>

                <section class="for_custom" style="display: none">
                  	<label for="label2">
                    	Item Name*
                  	</label>
                  	<div>
                    	<input id="label2" name="name" type="text" class="required item-name" />
                  	</div>
                </section>
                
                <section class="for_custom" style="display: none">
                  	<label for="label3">
                    	Item Quality*
                  	</label>
                  	<div>
                    	<select name="quality" id="label3" class="item-quality">
                            <option value="0" selected="selected">Poor</option>
                            <option value="1">Common</option>
                            <option value="2">Uncommon</option>
                            <option value="3">Rare</option>
                            <option value="4">Epic</option>
                            <option value="5">Legendary</option>
                            <option value="6">Artifact</option>
                            <option value="7">Bind to Account</option>
                        </select>
                  	</div>
                </section>
                
                <section class="for_custom" style="display: none">
                  	<label for="label7">
                    	Item Class*
                    	<small>The item class from item_template.</small>
                  	</label>
                  	<div>
                    	<select name="class" id="label7" class="item-class" onchange="return ClassChanges(this);">
                        	<?php
							foreach (Item_Classes() as $i => $data)
							{
								echo '<option value="', $data['id'], '">', $data['name'], ' [', $data['id'], ']</option>';
							}
							unset($i, $data);
							?>
                        </select>
                  	</div>
                </section>
                
                <section class="for_custom" style="display: none">
                  	<label>
                    	Item Subclass*
                    	<small>The item subclass from item_template.</small>
                  	</label>
                  	<div>
                    	<?php
						foreach (Item_Classes() as $i => $data)
						{
							echo '<select ', ($data['id'] == '0' ? 'name="subclass" class="subclass-visible"' : 'style="display: none"'), ' id="subclass-of-class-', $data['id'], '">';
								
								//print them subclasses of the current class
								foreach (Item_Subclasses() as $i2 => $sdata)
								{
									if ($sdata['class'] == $data['id'])
										echo '<option value="', $sdata['subclass'], '">', ($sdata['name2'] != '' ? $sdata['name2'] : $sdata['name']), ' [', $sdata['subclass'], ']</option>';
								}
								unset($i2, $sdata);
								
							echo '</select>';
						}
						unset($i, $data);
						?>
                  	</div>
                    <div class="clear"></div>
                </section>
                
                 <section class="for_custom" style="display: none">
                  	<label for="label8">
                    	Item Level
                    	<small>Used mainly for filtering.</small>
                  	</label>
                  	<div>
                    	<input id="label8" name="itemlevel" type="text" class="required item-itemlevel" />
                  	</div>
                </section>
                
                <section class="for_custom" style="display: none">
                  	<label for="label9">
                    	Required Level
                    	<small>Used mainly for filtering.</small>
                  	</label>
                  	<div>
                    	<input id="label9" name="requiredlevel" type="text" class="required item-requiredlevel" value="0" />
                  	</div>
                </section>
                
            	<br />  
                <p>
                    <input type="button" class="button primary submit" value="Submit" onclick="this.form.submit()" />
                </p>
            
            </form>

       		<div class="clear"></div>
   
		</div>

	</div>

<script src="<?=base_url()?>/admin_template/js/forms.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>

<script type="text/javascript">
	var $configURL = '<?php echo base_url(); ?>';
	
	$(document).ready(function(e)
	{
		<?php
		if ($formData = $ERRORS->GetFormData('add_storeitem'))
		{	
			echo '
			var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
			restoreFormData(\'addItemForm\', savedFormData);';
		}
		unset($formData);
        ?>
        
        $('[name="custom"]').on('change', function() {
            var val = $('[name="custom"]:checked').val();

            if (val == 1) {
                $('.for_custom').fadeIn('fast');
            } else {
                $('.for_custom').fadeOut('fast');
            }
        });
	});
	
	function ItemAutofill()
	{
		var entry = $('.item-entry').val();
		
		if (entry.length == 0)
			return false;
		
		//prepare the ajax error handlers
		$.ajaxSetup({
			error:function(x,e)
			{
				console.log('Ajax Error.');
				console.log(x);
			},
			dataType: "json",
		});
					
		$.get("<?=base_url()?>/ajax/getItem",
		{
			entry: entry
		},
		function(data)
		{
			if (typeof data.error != 'undefined')
			{
				new Notification('The item entry seems to be invalid.', 'error', 'urgent');
				return;
            }
            
			var name = data.name;
			var quality = data.quality;
            var icon = data.icon;
            var itemlevel = data.itemlevel || 0;
			var requiredLevel = data.reqlevel || 0;
			
			//fill in the data
			$('.item-name').val(name);
			$('.item-quality').find('option:selected').attr('selected', null);
			$('.item-quality').find('option[value="'+quality+'"]').attr('selected', 'selected');
			$('.item-quality').trigger('change');
			
			$('.item-class').find('option:selected').attr('selected', null);
			$('.item-class').find('option[value="'+data.class+'"]').attr('selected', 'selected');
			$('.item-class').trigger('change');
			
			$('#subclass-of-class-' + data.class).find('option:selected').attr('selected', null);
			$('#subclass-of-class-' + data.class).find('option[value="'+data.subclass+'"]').attr('selected', 'selected');
			$('#subclass-of-class-' + data.class).trigger('change');
            
            $('.item-itemlevel').val(itemlevel);
			$('.item-requiredlevel').val(requiredLevel);
			
			if (typeof data.level != 'undefined')
			{
				$('.item-itemlevel').val(data.level);
			}
		});
	}
	
	function ClassChanges(e)
	{
		var selected = $(e).find('option:selected');
		var theclass = selected.val();
		
		//hide the currenly visible subclass select
		$('.subclass-visible').attr('name', null);
		$('.subclass-visible').parent().fadeOut(500);
		$('.subclass-visible').removeClass('subclass-visible');
		
		//Show the new one
		$('#subclass-of-class-' + theclass).attr('name', 'subclass');
		$('#subclass-of-class-' + theclass).css('display', 'block');
		$('#subclass-of-class-' + theclass).addClass('subclass-visible');
		$('#subclass-of-class-' + theclass).parent().css('width', 'auto');
		$('#subclass-of-class-' + theclass).parent().find('.cmf-skinned-text').css('width', 'auto');
		$('#subclass-of-class-' + theclass).parent().delay(500).fadeIn('fast');
	}
</script>