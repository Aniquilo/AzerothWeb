<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

    <div class="container_2 armory">

        <form id="armory_form" onsubmit="return false;">

            <div class="container_3 account-wide search" id="search_bar">
                <input type="text" id="armory_search" placeholder="Search for characters" name="search" />
                <input type="submit" value="Search" />
            </div>

        </form>

        <div class="container_3 account-wide results" id="armory_results" style="display: none"></div>
    </div>

</div>

<script>
    $(document).ready(function() {
        Armory.Initialize();
    });
</script>