<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//print error and success messages
$ERRORS->PrintAny('polls');
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="<?=base_url()?>/admin/polls">Polls</a></li>
	</ul>
</nav>
  
<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
    	
        <div class="column left twothird">
        	<h2>Polls Management</h2>
        
            <table id="datatable" class="datatable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Poll</th>
                        <th>X</th>
                    </tr>
                </thead>
                <tbody id="sortable">
                    <?php if ($polls) { ?>
                        <?php foreach ($polls as $poll) { ?>
                            <tr>
                                <td><?=$poll['id']?></td>
                                <td>
                                    <div style="padding: 5px;">
                                        <?=($poll['current'] ? '<span style="color: green">Current:</span> ' : '')?> <b style="font-weight: bold;"><?=$poll['question']?></b> <?=((int)$poll['disabled'] == 1 ? '(Disabled)' : '')?><br /><br />
                                        <?php if ($poll['answers']) { ?>
                                            <ul>
                                                <?php foreach ($poll['answers'] as $answer) { ?>
                                                    <li style="margin-top: 4px;">
                                                        <span style="display: inline-block; width: 100px;"><?=$answer['votes']?> Votes (<?=$answer['pct']?>%)</span> <?=$answer['answer']?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        <?php } ?>
                                    </div>
                                </td>
                                <td style="position: relative">
                                    <div class="button-group" style="position: absolute; top: 10px;">
                                        <a href="<?=base_url()?>/admin/polls/delete?id=<?=$poll['id']?>" onclick="return deletecheck('Are you sure you want to delete this poll?');" class="button icon remove danger">Remove</a>
                                        <?php if ((int)$poll['disabled'] == 0) { ?>
                                            <a href="<?=base_url()?>/admin/polls/disable?id=<?=$poll['id']?>" class="button danger">Disable</a>
                                        <?php } else { ?>
                                            <a href="<?=base_url()?>/admin/polls/enable?id=<?=$poll['id']?>" class="button">Enable</a>
                                        <?php } ?>
                                    </div>
                                </td>
                            </tr>
                        <?php } ?>
                    <?php } ?>
                </tbody>
            </table>
        
        </div>
        
        <div class="column right third">
        	<h2>Add new Poll</h2>
            
            <form action="<?=base_url()?>/admin/polls/submit" method="post" id="add_poll_form">
	            <section>
	              <label>
                  	Question*
                  </label>
	              <div>
	                <input type="text" placeholder="Required" class="required" name="question" />
	              </div>
	            </section>
	            
                <section>
	              <label>
                  	Answers*
                  </label>
	              <div id="answer-fields">
	                <input type="text" placeholder="Answer 1 Required" class="required" name="answers[]" />
                    <input type="text" placeholder="Answer 2 Required" class="required" name="answers[]" />
                    
                    <a href="javascript:void(0)" class="button primary" onclick="AddField(this)">Add field</a>
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
	var table = $('#datatable').dataTable(
	{
		"aoColumnDefs": [ 
            { "sWidth": "7%", "aTargets": [ 0 ] },
            { "sWidth": "80%", "bSortable": false, "aTargets": [ 1 ] },
			{ "bSortable": false, "bSearchable": false, "aTargets": [ 2 ] }
		],
	});
    //sort the table
    table.fnSort( [ [ 0, 'desc' ] ] );
});

function AddField(btn)
{
    var fields = $('#answer-fields').find('input[type="text"]');

    $(btn).before('<input type="text" placeholder="Answer ' + (fields.length + 1) + '" name="answers[]" />');
}
</script>