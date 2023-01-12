<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

  	<div class="container_2 account">
     <div class="cont-image">    
      <!-- Upload Screanshot -->
 
        <div class="error-holder">
            <?php $ERRORS->PrintAny('screenshots'); ?>
        </div>
      
		<div class="container_4 account_sub_header">
			<div class="grad">
				<div class="page-title">Upload Screenshot</div>
				<a href="<?php echo base_url(), '/media'; ?>">Back to media</a>
			</div>
		</div>
      
		<div class="page-desc-holder">
            Upload your screenshots made on our realms.<br/>
            After aproval from the staff you will receive 1 Silver Coint for each screenshot submitted.<br/>
            Please try to upload funny and unique screenshots.
		</div>
      
		<div class="container_3 account-wide">
        	<div class="upload-screanshot">
	        	<form method="post" action="<?php echo base_url(); ?>/media/upload_submit" enctype="multipart/form-data" class="page-form">
                	<div class="row">
                    	<label for="screanshot-name">Screenshot Title:</label>
                		<input type="text" id="screanshot-name" name="title" />
                    </div>
                
                	<div class="row">
                        <label for="screanshot-file">Select file:</label>
                        <input type="file" id="screanshot-file" name="file" />
	                </div>
                
                	<div class="row textarea-row">
	                    <label for="screanshot-descr">Description:</label>
	                    <textarea id="screanshot-descr" spellcheck="false" name="descr"></textarea>
	                    <div class="clear"></div>
                    </div>
                    
                	<input type="submit" value="Submit" />
                </form>
            </div>
		</div>
      
      <!-- Upload Screanshot.End -->
     </div>
	</div>
 
</div>

<script>
    $(document).ready(function()
    {
        $('#screanshot-file').customFileInput();
    });
</script>