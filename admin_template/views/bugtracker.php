<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//print error messages
$ERRORS->PrintAny(array('approve_report', 'edit_report', 'delete_report'));
?>

<!-- Secondary navigation -->
<nav id="secondary">
	<ul>
		<li class="current"><a href="#maintab" onclick="changeCurrentTab('#maintab');">Browse</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
        <h2>Bug Tracker</h2>
        <br />
        <div>
        
            <script type="text/javascript">
                var filter = <?=($filter !== false ? $filter : 'false')?>;
                
                $(document).ready(function()
                {
                    if (filter !== false)
                        $('#filter-select').find('option[value="'+filter+'"]').attr('selected', 'selected');

                    $('#filter-select').on('change', function()
                    {
                        var selected = $(this).find('option:selected');
                        
                        if (selected.length > 0)
                        {
                            document.location.href = "<?=base_url()?>/admin/bugtracker?filter=" + selected.val();
                        }
                    });
                });
            </script>
            
            <select name="filter" style="width: 200px;" id="filter-select">
                <option value="-1">All</option>
                <option value="<?php echo BT_STATUS_NEW; ?>">New</option>
                <option value="<?php echo BT_STATUS_OPEN; ?>">Open</option>
                <option value="<?php echo BT_STATUS_ONHOLD; ?>">On hold</option>
                <option value="<?php echo BT_STATUS_DUPLICATE; ?>">Duplicate</option>
                <option value="<?php echo BT_STATUS_INVALID; ?>">Invalid</option>
                <option value="<?php echo BT_STATUS_WONTFIX; ?>">Wont Fix</option>
                <option value="<?php echo BT_STATUS_RESOLVED; ?>">Resolved</option>
            </select>
        </div>
        <br />
    
        <table class="datatable">
		  
		    <thead>
		      <tr>
		        <th>ID</th>
		        <th>Text</th>
                <th>Category</th>
                <th>Priority</th>
		        <th>Status</th>
                <th>Approval</th>
                <?php echo ($CORE->user->hasPermission(PERMISSION_MAN_BUGTRACKER) ? '<th>Actions</th>' : ''); ?>
		      </tr>
		    </thead>
		  	
		    <tbody>
		
                <?php
                if ($res->rowCount() > 0)
                {
                    //get the categories
                    $CategoryStore = new BTCategories();
                    
                    while ($arr = $res->fetch())
                    {
                        //Translate the status
                        switch ($arr['status'])
                        {
                            case BT_STATUS_NEW:
                                $status = 'New';
                                break;
                            case BT_STATUS_OPEN:
                                $status = 'Open';
                                break;
                            case BT_STATUS_ONHOLD:
                                $status = 'On hold';
                                break;
                            case BT_STATUS_DUPLICATE:
                                $status = 'Duplicate';
                                break;
                            case BT_STATUS_INVALID:
                                $status = 'Invalid';
                                break;
                            case BT_STATUS_WONTFIX:
                                $status = '';
                                break;
                            case BT_STATUS_RESOLVED:
                                $status = 'Resolved';
                                break;
                            default:
                                $status = 'Unknown';
                                break;
                        }
                        
                        //translate the approval
                        switch ($arr['approval'])
                        {
                            case BT_APP_STATUS_APPROVED:
                                $approval = 'approved';
                                break;
                            case BT_APP_STATUS_DECLINED:
                                $approval = 'declined';
                                break;
                            default:
                                $approval = 'pending';
                                break;
                        }
                        
                        //translate the priority
                        switch ($arr['priority'])
                        {
                            case BT_PRIORITY_LOW:
                                $priority = 'Low';
                                break;
                            case BT_PRIORITY_NORMAL:
                                $priority = 'Normal';
                                break;
                            case BT_PRIORITY_HIGH:
                                $priority = 'High';
                                break;
                            default:
                                $priority = 'Abnormal';
                                break;
                        }
                    
                        //get the main category
                        $MainCategory = $CategoryStore->getMainCategory($arr['maincategory']);
        
                        switch ($arr['maincategory'])
                        {
                            case BT_CAT_WEBSITE:
                                $MainCategoryName = 'Website';
                                break;
                            case BT_CAT_WOTLK_CORE:
                                $MainCategoryName = 'Game Server';
                                break;
                            default:
                                $MainCategoryName = 'Unknown';
                                break;
                        }
                        
                        //get the category
                        $Category = $MainCategory->getCategory($arr['category']);
                        
                        if ($Category === false)
                        {
                            $CategoryName = 'Unknown';
                        }
                        else
                        {
                            $CategoryName = $Category->getName();
                        }
                        
                        $SubCategoryName = false;
                        //check for sub category
                        if ($Category->hasSubCategories())
                        {
                            $SubCategoryName = $Category->getSubCategoryName($arr['subcategory']);
                        }
                        
                        //free memory
                        unset($MainCategory, $Category);
                        
                        //put the category string together
                        $category = $CategoryName;
                        if ($SubCategoryName)
                        {
                            $category .= ' - '.$SubCategoryName;
                        }
                        
                        //free memory
                        unset($CategoryName, $SubCategoryName);
                        
                        echo '
                        <tr valign="top" data-reportid="', $arr['id'], '" class="status-', strtolower($status), '">
                            <td style="vertical-align: top !important;">', $arr['id'], '</td>
                            <td style="vertical-align: top !important; padding: 0 !important; position: relative;">
                                <div style="height: 16px; width: 90%; overflow: hidden; margin: 6px 10px 8px 10px;">
                                    <p><strong>', htmlspecialchars(stripslashes($arr['title'])), '</strong><br><br>', $arr['content'], '<br><br><strong>Added:</strong> ', $arr['added'], ' <strong>by</strong> ', $arr['displayName'], ' [', $arr['account'], ']</p>
                                </div>
                                <a href="javascript:void(0)" class="datatable-expander" style="position:absolute; display:block; height:24px; right:0px; top:0px;">
                                    <i class="icon large material-icons">expand_more</i>
                                </a>
                            </td>
                            <td style="vertical-align: top !important;">', $MainCategoryName, ' - ', $category, '</td>
                            <td style="vertical-align: top !important;">', $priority, '</td>
                            <td style="vertical-align: top !important;">', $status, '</td>
                            <td style="vertical-align: top !important;">
                                <p class="approval-status" style="margin: 0px;">', ucfirst($approval), '</p>
                            </td>';
                            
                            //Is Allowed to manage
                            if ($CORE->user->hasPermission(PERMISSION_MAN_BUGTRACKER))
                            {
                                echo '
                                <td style="vertical-align: top !important;">
                                    <span class="button-group">
                                        <a href="javascript: void(0)" data-id="', $arr['id'], '" class="button icon edit report-edit-btn">Edit</a>
                                        <a href="', base_url(), '/admin/bugtracker/delete?id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete this bug report?\');" class="button icon remove danger">Remove</a>
                                        ', ($arr['approval'] == BT_APP_STATUS_PENDING ? '
                                        <a href="javascript: void(0)" onclick="return ApproveReport('.$arr['id'].');" class="button icon approve" id="approval-button">Approve</a>
                                        <a href="javascript: void(0)" onclick="return DisapproveReport('.$arr['id'].');" class="button icon trash" id="disapproval-button">Disapprove</a>
                                        ' : ''), '
                                    </span>
                                </td>';
                            }
                            
                            echo '
                        </tr>';
                        
                        unset($category, $priority, $status, $approval);
                    }
                    unset($CategoryStore, $arr);
                }
			    ?>
		    
		    </tbody>
		    
        </table>

    </div>
</section>

<div class="edit-overlay edit-modal" id="editorReport">
    <div class="edit-container">
        <h2 style="margin-top:0;">Report Editing</h2>
            <form method="post" action="<?=base_url()?>/admin/bugtracker/submit_edit">
                <input type="hidden" name="id" value="" />
                <section>
                    <label>Title*</label>
                    <div><input type="text" placeholder="Required" class="required" name="title" value="" /></div>
                </section>
                <section>
                    <label>Content*</label>
                    <div><textarea name="content" style="height: 80px;"></textarea></div>
                </section>
                <section>
                    <label>Priority*</label>
                    <div><select name="priority">
                        <option value="1">Low</option>
                        <option value="2">Normal</option>
                        <option value="3">High</option>
                    </select></div>
                </section>
                <section>
                    <label>Status*</label>
                    <div><select name="status">
                        <option value="0">New</option>
                        <option value="1">Open</option>
                        <option value="2">On Hold</option>
                        <option value="3">Duplicate</option>
                        <option value="5">Invalid</option>
                        <option value="6">Wontfix</option>
                        <option value="7">Resolved</option>
                    </select></div>
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
            </form>'
    </div>
</div>

<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery-ui-1.10.0.sortable.min.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function(e)
    {
        //Init datatables
        if ($(".datatable").length > 0)
        {
            var newsTable = $(".datatable").dataTable({
                "aoColumnDefs": [ 
                    { "bSortable": false, "aTargets": [ 1 ] },
                    <?php echo ($CORE->user->hasPermission(PERMISSION_MAN_BUGTRACKER) ? '{ "bSortable": false, "aTargets": [ 6 ] },' : ''); ?>
                ],
                "fnDrawCallback": function()
                {
                    //remove the click bind
                    $('.datatable-expander').off('click');

                    //Bind the click event
                    $('.datatable-expander').on('click', function(event)
                    {
                        if (typeof $(this).attr('expanded') == 'undefined' || $(this).attr('expanded').length == 0 || $(this).attr('expanded') == 'false')
                        {
                            var height = $(this).parent().children('div').children('p').height();
                            var thisHeight = $(this).parent().children('div').height();
                                                    
                            $(this).parent().children('div').stop(true, true).animate({ height: height }, 'fast');
                            $(this).parent().parent().addClass('active-expander');
                            $(this).attr('expanded', 'true');
                            $(this).attr('oldHeight', thisHeight);
                            $(this).children('i').html('expand_less');
                        }
                        else
                        {
                            $(this).parent().children('div').stop(true, true).animate({ height: parseInt($(this).attr('oldHeight')) }, 'fast', function()
                            {
                                $(this).parent().parent().removeClass('active-expander');
                            });
                            $(this).attr('expanded', 'false');
                            $(this).children('i').html('expand_more');
                        }
                    });
                }
            });
            //sort the table
            newsTable.fnSort( [ [0, 'desc'] ] );
        }

        $('.report-edit-btn').on('click', function() {
            let id = $(this).attr('data-id');

            $.get($BaseURL + "/admin/bugtracker/get_report?id=" + id, function(resp) {
                if (resp.error == undefined) {
                    $('#editorReport').find('[name="id"]').val(resp.id);
                    $('#editorReport').find('[name="title"]').val(resp.title);
                    $('#editorReport').find('[name="content"]').val(resp.content);
                    $('#editorReport').find('[name="priority"]').val(resp.priority);
                    $('#editorReport').find('[name="priority"]').trigger('change');
                    $('#editorReport').find('[name="status"]').val(resp.status);
                    $('#editorReport').find('[name="status"]').trigger('change');
                    $('#editorReport').editModal('show');
                }
            });
        });
    });

    function ApproveReport(id)
    {
        var $id = id;
        
        //Run the ajax
        $.ajax({
            type: "GET",
            url: $BaseURL + "/admin/bugtracker/approve",
            data: { id: $id },
            cache: false,
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log(textStatus);
            },
            success: function(data)
            {
            var $data = data;
            
            //check if it was successful
            if ($data == 'OK')
            {
                //update the text
                var tr = $('.datatable').find("[data-reportid='" + id + "']");
                tr.find('.approval-status').html('Approved');
                //hide the approval buttons
                tr.find('#approval-button').hide();
                tr.find('#disapproval-button').hide();
                //alert success
                new Notification('The report has been successfully update.', 'success');
            }
            else
            {
                new Notification($data, 'error', 'urgent');
            }
            }
        });
        
        return false;
    }

    function DisapproveReport(id)
    {
        var $id = id;
        
        //Run the ajax
        $.ajax({
            type: "GET",
            url: $BaseURL + "/admin/bugtracker/disapprove",
            data: { id: $id },
            cache: false,
            error: function(jqXHR, textStatus, errorThrown)
            {
                console.log(textStatus);
            },
            success: function(data)
            {
            var $data = data;
            
            //check if it was successful
            if ($data == 'OK')
            {
                //update the text
                var tr = $('.datatable').find("[data-reportid='" + id + "']");
                tr.find('.approval-status').html('Declined');
                //hide the approval buttons
                tr.find('#approval-button').hide();
                tr.find('#disapproval-button').hide();
                //alert success
                new Notification('The report has been successfully update.', 'success');
            }
            else
            {
                new Notification($data, 'error', 'urgent');
            }
            }
        });
        
        return false;
    }
</script>














