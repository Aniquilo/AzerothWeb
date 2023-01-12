<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//print error messages
$ERRORS->PrintAny(array('edit_storeitem', 'add_storeitem'));
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="<?=base_url()?>/admin/store">Item Store</a></li>
        <li><a href="<?=base_url()?>/admin/store/add">Add new item</a></li>
	</ul>
</nav>
 
<!-- The content -->
<section id="content">

	<div class="tab" id="maintab">
        <h2>Item Store Management</h2>
      	
        <br />
        <div>
            <?php
                $realms = $CORE->getRealmsConfig();

				//Realm selection
				if (isset($realms) && count($realms) > 0)
				{
					echo '
					<div style="width: 200px; display: inline-block; vertical-align: middle; margin-right: 10px;">
						<select name="realm" id="realm-select">
							<option value="-1">All Realms</option>';
						
							foreach ($realms as $id => $realmData)
							{
								echo '<option value="', $id, '" ', ($id == $RealmID ? 'selected="selected"' : ''), '>', $realmData['name'], '</option>';
							}
						
						echo '
						</select>
					</div>';
				}
			?>
            
            <script>
				$(function()
				{
					$('#realm-select').on('change', function()
					{
						window.location = '<?=base_url()?>/admin/store&realm=' + $(this).find('option:selected').val();
					});
				});
			</script>
            
        </div>
        <br /><br />
        
        <table class="datatable" id="datatable">
      
            <thead>
                <tr>
                    <th width="6%">Entry</th>
                    <th width="20%">Name</th>
                    <th width="6%">Item Level</th>
                    <th width="6%">Realms</th>
                    <th width="6%">Price Gold</th>
                    <th width="6%">Price Silver</th>
                    <th>Class</th>
                    <th>Subclass</th>
                    <th>Actions</th>
                </tr>
            </thead>
        
            <tbody>

            </tbody>
        
        </table>
    
    </div>
</section>

<script src="<?=base_url()?>/admin_template/js/forms.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery-ui-1.10.0.sortable.min.js" type="text/javascript"></script>

<script type="text/javascript">
var $configURL = '<?php echo base_url(); ?>';
var $editable_onChangeTimer = null;

$(document).ready(function(e)
{
    //Init datatables
	if ($("#datatable").length > 0)
	{
	 	var armorsetsTable = $("#datatable").dataTable(
		{
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": $BaseURL + "/admin/datatable/store_items?realm=<?php echo $RealmID; ?>",
			"aoColumnDefs": [ 
				{ "bSortable": false, "aTargets": [ 8 ] },
    		],
			"fnDrawCallback": function (oSettings)
			{
				Tooltip.refresh();
			}
		});
		//sort the table
		//armorsetsTable.fnSort( [ [1, 'desc'] ] );
	}
});

function DeleteItem(e, id)
{
	var TR = $(e).parent().parent().parent();
	
	var answer = confirm('Are you sure you want to delete this item?');
	
	if (!answer)
		return false;
	
	$.get($BaseURL + '/admin/store/delete',
	{
		id: id
	},
	function(data)
	{
		if (data == 'OK')
		{
			TR.fadeOut('slow');
			
			new Notification('The item was successfully deleted.', 'success');
		}
		else
		{
			new Notification(data, 'error', 'urgent');
		}
	});
	
	return false;
}

function ConstructEdit(id)
{
	//Check if it's already constructed
	if ($('#editor-'+id).length > 0)
	{
		$('#editor-'+id).fadeIn('fast');
		//break
		return false;
	}
	
	var $id = id;
	
	//Pull the data for this report
	$.ajax({
		type: "GET",
		url: $BaseURL + "/admin/store/item_data",
		data: { id: $id },
		dataType: 'json',
		cache: false,
		error: function(jqXHR, textStatus, errorThrown)
		{
			console.log(textStatus);
		},
		success: function(data)
		{
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
					'<h2 style="margin-top:0;">Item Editing</h2>'+
						'<form method="post" action="<?=base_url()?>/admin/store/submit_edit?id='+data.id+'">'+
							'<input type="hidden" value="'+data.id+'" name="id" />'+
							'<section>'+
								'<label>Entry*</label>'+
								'<div><input type="text" placeholder="Required" class="required" name="entry" value="'+data.entry+'" /></div>'+
							'</section>'+
							
							'<section>'+
								'<label>Name*</label>'+
								'<div><input type="text" placeholder="Required" class="required" name="name" value="'+data.name+'" /></div>'+
							'</section>'+
							
							'<section>'+
							  	'<label>'+
									'Realms*'+
									'<small>Which realms should the item be purchasable in.</small>'+
									'<small>Hold ctrl to select more than one realm.</small>'+
							  	'</label>'+
                                '<div>' +
                                    '<select multiple name="realms[]" id="label4" class="realms-select">' +
                                        <?php foreach ($CORE->realms->getRealms() as $realm) { ?>
                                            '<option value="<?=$realm->getId()?>"><?=$realm->getName()?></option>' +
                                        <?php } ?>
                                    '</select>' +
                                '</div>'+
								'<div class="clear"></div>'+
							'</section>'+
							
							'<section>'+
								'<label>'+
									'Gold Price*'+
									'<small>Set to 0 if you wish to disable this currency.</small>'+
								'</label>'+
								'<div><input type="text" placeholder="Required" class="required" name="gold" value="'+data.gold+'" /></div>'+
							'</section>'+
							
							'<section>'+
								'<label>'+
									'Silver Price*'+
									'<small>Set to 0 if you wish to disable this currency.</small>'+
								'</label>'+
								'<div><input type="text" placeholder="Required" class="required" name="silver" value="'+data.silver+'" /></div>'+
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

            var realms = data.realm.split(',');
            
            form.find('.realms-select option').each(function(i, e) {
                if (realms.indexOf($(this).val()) > -1) {
                    $(this).attr('selected', 'selected');
                }
            });

			//Bind the submit
			form.find('#submit_btn').bind('click', function()
			{
				$(this).parent().parent().parent().parent().submit();
				
				return false;
			});
			
			//test
			Overlay.fadeIn('fast', function()
			{
				$(this).find('select').select_skin();
			});
		}
	});
	
	return false;
}

function CloseEditor(id)
{
	$('#editor-'+id).fadeOut('fast');
}
</script>