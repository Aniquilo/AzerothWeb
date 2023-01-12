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
        <li><a href="<?=base_url()?>/admin/logs">Paypal Logs</a></li>
        <li><a href="<?=base_url()?>/admin/logs/paymentwall">Paymentwall Logs</a></li>
        <li <?=($type == 'store' ? 'class="current"' : '')?>><a href="<?=base_url()?>/admin/logs/store">Store Purchase Logs</a></li>
		<li <?=($type == 'armorsets' ? 'class="current"' : '')?>><a href="<?=base_url()?>/admin/logs/armorsets">Armor Sets Purchase Logs</a></li>
        <li <?=($type == 'levels' ? 'class="current"' : '')?>><a href="<?=base_url()?>/admin/logs/levels">Level Purchase Logs</a></li>
        <li <?=($type == 'racechange' ? 'class="current"' : '')?>><a href="<?=base_url()?>/admin/logs/racechange">Race Change Logs</a></li>
		<li <?=($type == 'factionchange' ? 'class="current"' : '')?>><a href="<?=base_url()?>/admin/logs/factionchange">Faction Change Logs</a></li>
        <li <?=($type == 'customization' ? 'class="current"' : '')?>><a href="<?=base_url()?>/admin/logs/customization">Re-customization Logs</a></li>
        <li <?=($type == 'gamegold' ? 'class="current"' : '')?>><a href="<?=base_url()?>/admin/logs/gamegold">In-Game Gold Logs</a></li>
        <li <?=($type == 'boosts' ? 'class="current"' : '')?>><a href="<?=base_url()?>/admin/logs/boosts">Boosts Purchase Logs</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
		<h2><?=$title?></h2>

        <table class="datatable">
            <thead>
                <tr>
                <th>ID</th>
                <th>Text</th>
                <th>Account</th>
                <th>Datetime</th>
                <th>IP</th>
                <th>Status</th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>

	</div>
</section>

<script src="<?=base_url()?>/admin_template/js/jquery.datatables.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/expander.js" type="text/javascript"></script>

<script type="text/javascript">
    $(document).ready(function(e)
    {	
        //Init datatables
        if ($(".datatable").length > 0)
        {
            var newsTable = $(".datatable").dataTable(
            {
                "bProcessing": true,
                "bServerSide": true,
                "sAjaxSource": $BaseURL + "/admin/datatable/<?=$type?>_logs",
                "aoColumnDefs": [ 
                    { "sWidth": "40%", "bSortable": false, "aTargets": [ 1 ] }
                ] 
            });
            //sort the table
            newsTable.fnSort( [ [0, 'desc'] ] );
        }
    });
</script>