<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny(array('addArticle', 'editArticle'));
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="<?=base_url()?>/admin/articles">Articles</a></li>
        <li><a href="<?=base_url()?>/admin/articles/post">New Article</a></li>
	</ul>
</nav>
  
<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
        <h2>Articles Management</h2>
        
        <div>
    
            <table id="datatable" class="datatable">
          
                <thead>
					<th>ID</th>
					<th>Title</th>
					<th>Short Text</th>
					<th>Views</th>
					<th>Added</th>
					<th>Author</th>
					<th>Actions</th>
                </thead>
                <tbody id="sortable">
            
                </tbody>
            
            </table>
        
        </div>
    </div>
</section>

<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function(e)
	{
		$('#datatable').dataTable(
		{
			"bProcessing": true,
			"bServerSide": true,
			"sAjaxSource": $BaseURL + "/admin/datatable/articles",
			"aoColumnDefs": [ 
				{ "bSortable": false, "aTargets": [ 2 ] },
				{ "bSortable": false, "aTargets": [ 6 ] }
			]
		});
	});
</script>