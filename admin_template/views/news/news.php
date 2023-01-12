<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny(array('deleteNews', 'addNews', 'editNews'));
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="<?=base_url()?>/admin/news">News</a></li>
		<li><a href="<?=base_url()?>/admin/news/post">Post News</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
        <h2>News Management</h2>

        <table class="datatable">
        
            <thead>
              <tr>
                <th>Headline</th>
                <th>Posted</th>
                <th>Posted by</th>
                <th>Actions</th>
              </tr>
            </thead>
            
            <tbody>
            
            <?php
            
            $res = $DB->query("SELECT * FROM `news` ORDER BY id DESC");
            
            if ($res->rowCount() > 0)
            {
                while ($arr = $res->fetch())
                {
                    echo '
                      <tr>
                        <td>', $arr['title'], '</td>
                        <td>', $arr['added'], '</td>
                        <td>', $arr['authorStr'], '</td>
                        <td>
                          <span class="button-group">
                            <a href="', base_url(), '/admin/news/edit?id=', $arr['id'], '" class="button icon edit">Edit</a>
                            <a href="', base_url(), '/admin/news/delete?id=', $arr['id'], '" onclick="return deletecheck(\'Are you sure you want to delete those news?\');" class="button icon remove danger">Remove</a>
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
</section>

<script src="<?=base_url()?>/admin_template/js/jquery.color.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>

<script type="text/javascript">
	var $configURL = '<?php echo base_url(); ?>';

	$(document).ready(function(e)
	{
		//Init datatables
		if ($(".datatable").length > 0)
		{
			var newsTable = $(".datatable").dataTable({
				"aoColumnDefs": [ 
					{ "bSortable": false, "aTargets": [ 3 ] }
				] 
			});
			//sort the table
			newsTable.fnSort( [ [1, 'desc'] ] );
		}
	});
</script>