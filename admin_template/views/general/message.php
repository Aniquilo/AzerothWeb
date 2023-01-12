<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}          
?>
      
<!-- The content -->
<section id="content">
    <h2><?=$headline?></h2>
    <h2>
        <pre><?=$message?></pre>
    </h2>
</section>