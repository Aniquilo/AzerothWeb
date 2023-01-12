<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

        </div>
        <footer id="copyright">
            <div style="line-height: 16px; padding: 20px 0;">
                ACP design by Bram Jetten<br />
                Warcry CMS by ChoMPi & EvilSystem
            </div>
        </footer>
    </div>

    <!-- JavaScript -->
    <script type="text/javascript">
        $(function()
        {
            //hide all tabs
            $(".tab").hide();
        });

        /**
        * Functional secondary menu using tabs
        */
		//function to handle auto tab switching
		function tabAutoSwitch(tab)
		{
			if ($("nav#secondary").hasClass('disable-tabbing'))
			{
				$(".tab:first-child").show();
				return;
			}
				
			var $this = $("nav#secondary ul li:nth-child("+tab+") a");
			
			//if the tab exists
			if ($this.length > 0)
			{
				if(!$this.hasClass("current"))
				{
					$("nav#secondary ul li").removeClass("current");
					$this.parent().addClass("current");
				 	$(".tab").hide();
					var link = $this.attr("href");
				 	$(link).show();
					changeCurrentTab(link);
				}
				handleBodyHeight();
			}
		}
		
		/**
		* Make sure the background gradient reaches
		* the bottom of the page.
		*/
		function handleBodyHeight()
		{
			if($('#container').height() < $(document).height())
			{
				$('#container').height($(document).height());
			}
		}
		
		// Function to switch the workspace
		function changeCurrentTab(tab)
		{
			$currentTab = $(tab);
		}
		
		function deletecheck(message)
		{
		    var answer = confirm(message)
		    if (answer)
			{
		        return true;
		    }
		    
		    return false;  
		} 
		
		//handle auto tab switching	
		var $autoTabSwitch = '<?php echo (isset($_GET['switchTab']) ? (int)$_GET['switchTab'] : ''); ?>';
		
		$(function()
		{
			//switch to the tab we want to or the first one
	 		if ($autoTabSwitch != '')
			{
				tabAutoSwitch($autoTabSwitch);
			}
			else
			{
				tabAutoSwitch(1);
			}			
		});
		
		$(document).ready(function()
		{
			 //handle auto body height
			 handleBodyHeight();
			
			//bind the tab buttons  		
		  	$("nav#secondary ul li a").on('click', function()
		  	{
				var index = $(this).parent().index();
			    tabAutoSwitch(index + 1);
			    return false;
			});	
			
			//Disables
			$('nav.disable-tabbing ul li a').off('click');
        });		
	</script>
  	<script src="<?=base_url()?>/admin_template/js/excanvas.js" type="text/javascript"></script>     
	<script src="<?=base_url()?>/admin_template/js/jquery.livesearch.js" type="text/javascript"></script>
	<script src="<?=base_url()?>/admin_template/js/jquery.placeholder.js" type="text/javascript"></script>
	<script src="<?=base_url()?>/admin_template/js/jquery.validate.js" type="text/javascript"></script>
	<script src="<?=base_url()?>/admin_template/js/jquery.selectskin.js" type="text/javascript"></script>
	<script src="<?=base_url()?>/admin_template/js/jquery.checkboxes.js" type="text/javascript"></script>
	<script src="<?=base_url()?>/admin_template/js/jquery.visualize.js" type="text/javascript"></script>     
	<script src="<?=base_url()?>/admin_template/js/notifications.js" type="text/javascript"></script>
	<script src="<?=base_url()?>/admin_template/js/application.js?v=1" type="text/javascript"></script>
    <script src="<?=base_url()?>/template/js/tooltips.js" type="text/javascript"></script>

    <script type="text/javascript" src="<?=wowdb_url()?>/static/widgets/power.js?lang=<?=wowdb_lang()?>"></script>
    <script>var aowow_tooltips = { "colorlinks": false, "iconizelinks": false, "renamelinks": false };</script>
</body>
</html>