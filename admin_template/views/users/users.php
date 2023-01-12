<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li class="current"><a href="<?=base_url()?>/admin/users">Users</a></li>
	</ul>
</nav>
   
<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
        <h2>User Management</h2>
        
        <div>
    
            <table id="datatable" class="datatable">
          
                <thead>
                    <tr>
                        <th>Acc ID</th>
                        <th>User [Display|Account]</th>
                        <th>Rank</th>
                        <th>GM Level</th>
                        <th>Email</th>
                        <th>Last IP</th>
                        <th>Register IP</th>
                        <th>Register Date</th>
                    </tr>
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
			"sAjaxSource": $BaseURL + "/admin/datatable/users",
			"aoColumnDefs": [ 
				{ "bSortable": false, "aTargets": [ 4 ] }
			]
		});
	});
</script>