<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

	<div class="container_2 account" align="center">
    
        <div style="height:75px;"></div>
        <div class="container_4 account_sub_header">
            <div class="grad">
                <div class="page-title">Private Messages</div>
                <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
            </div>
        </div>
      
        <!-- Private Messages -->
        <div class="private-messages">
            
            <!-- PM Menu -->
            <div class="pm-menu">
                <div class="menu">
                    <a href="#" id="inbox" name="Inbox">Inbox</a>
                    <a href="#" id="sent-items" name="Sent Items">Sent Items</a>
                    <a href="#" id="write-letters" class="active" name="Write Letters"><p>Write Letters</p><div id="ico"></div></a>
                </div>
                <div class="pm-top-info">
                    <h1>55 Letters</h1>
                    <h2><i>(34 Inbox , 21 Sent items)</i></h2>
                </div>
            </div>
            <div class="clear"></div>
            <!-- PM Menu . End-->
            
            <div class="container_3 account-wide pm-container send-msg-cont" align="center">
                <form>
                    <div class="top-label">
                        <label for="reciever">Enter reciever name:</label>
                        <input type="text" name="reciever" />
                    </div>
                    <textarea class="bbcode"></textarea>
                    <div style="text-align:left; margin:15px 0 15px 15px;"><input type="submit" value="Send" /></div>
                </form>         
            </div>
            
            <div style="padding:0 0 70px 0;"></div>
        </div>
        <!-- Private Messages . End -->
     
	</div>
 
</div>

<script>
    $(document).ready(function()
    {
        $("textarea.bbcode").sceditor({
            plugins: 'bbcode',
            style: $BaseURL + '/template/style/bbcode-default-iframe.css'
        });
    });
</script>