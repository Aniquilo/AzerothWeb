<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny('addArticle');
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li><a href="<?=base_url()?>/admin/articles">Articles</a></li>
        <li class="current"><a href="<?=base_url()?>/admin/articles/post">New Article</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">
    <div class="tab" id="maintab">
        <h2>Post new Article</h2>
        
        <div class="form">
    
        	<form method="post" action="<?php echo base_url(); ?>/admin/articles/submit_post" name="add-article" id="add-article">
                <section>
                  <label for="label">
                    Headline*
                    <small>250 characters maximum.</small>
                  </label>
                
                  <div>
                    <input id="label" name="title" type="text" class="required" />
                  </div>
                </section>
                
                <section>
                  <label for="textarea_s">
                    Short Text*
                    <small>350 characters maximum.</small>
                  </label>
                  
                  <div>
                    <textarea class="required" id="textarea_s" name="short_text" rows="5"></textarea>
                  </div>
                </section>
                
                <section>
                  <label for="textarea">
                    Content*
                    <small>Only part of this contect will be displayed on the home page.</small>
                  </label>
                  
                  <div>
                    <textarea class="tinymce" id="textarea" name="text"></textarea>
                  </div>
                </section>
                
                <section>
                	<label>
                        Comments
                        <small>Should the article have comments enabled.</small>
			     	</label>
                  
                    <div>
                        <div class="column">
                          	<input type="checkbox" value="1" id="comments" name="comments" checked="checked" />
                          	<label for="comments" class="prettyCheckbox checkbox list">
                                Enable comments
                         	</label>
                        </div>
                    </div>
                </section>
                
                <input type="hidden" name="image" id="image" />
         	</form>
                
            <section>
              <label for="textarea">
                Image
                <small>Leave bank to set the default image.</small>
              </label>
              
              <div>
                    <div id="image_Loading" style="display: none;">
                        Loading...<br /><br /><br />
                    </div>
                    
                    <div id="image_PreviewSection" style="display: none; margin-bottom: 5px;">
                    </div>
                                    
                    <form id="uploadForm" method="POST" name="thumbForm" enctype="multipart/form-data">
                        <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
                        <input id="label" type="file" name="file" onchange="ajaxFormSubmit()"/>
                        <input type="submit" value="submit" style="display: none;" />
                    </form>
              </div>
              
            </section>
            
            <br />
            <p>
                <input type="submit" class="button primary submit" value="Submit" onclick="submit_article();" />
            </p>

        </div>
    </div>
</section>

<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/forms.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/tinymce/tinymce.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/tinymce/jquery.tinymce.min.js" type="text/javascript"></script>

<script type="text/javascript">
	var $configURL = '<?php echo base_url(); ?>';
	
	$(document).ready(function(e)
	{
		<?php
		if ($formData = $ERRORS->GetFormData('addArticle'))
		{	
			echo '
			var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
			restoreFormData(\'add-article\', savedFormData);';
		}
		unset($formData);
		?>
		
		tinymce.init({
            plugins: 'print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help',
            toolbar: 'formatselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | addcomment',
            image_advtab: true,
            selector: '.tinymce',
            height: 480
        });
		
		//custom settings for validation
		$("#add-article").validate(
		{
			rules:
			{
				title:
				{
					minlength: 10,
					maxlength: 250
				},
				short_text:
				{
					minlength: 10,
					maxlength: 350
				},
		  	}
		});
	});
	
	function submit_article()
	{
		$('#add-article').submit();
	}
	
    //////////////////////////////////////////////////////////////////
    // Ajax Upload
    //////////////////////////////////////////////////////////////////

	var ajaxOptions = {
		url: $BaseURL + '/admin/ajax/imageUpload',
	    beforeSubmit: function(a,f,o) {
			$('#image_PreviewSection', $currentTab).hide();
			$('#image_Loading', $currentTab).html('Loading...');
	        $('#image_Loading', $currentTab).show();
	    },
	    success: function(data) {
            $('#image_Loading', $currentTab).hide();

			if (typeof data.error != 'undefined') {
				//unpopulate the hidden input for the product
                $("#image", $currentTab).val("");
                
                new Notification(data.error, 'error', 'urgent');
			} else {		
				//update preview
				updatePreview($configURL + "/uploads/temp/"+ data.fileName, data.fileName);
			}
	    }
	}
		
	function ajaxFormSubmit()
	{
		$('#uploadForm', $currentTab).ajaxSubmit(ajaxOptions);	
	}

    //////////////////////////////////////////////////////////////////
    // A function to help me switch stuff
    //////////////////////////////////////////////////////////////////

	function updatePreview(imageSrc, imageName)
	{
		var previewSection = $('#image_PreviewSection', $currentTab);
		var $imageHeight;
		var $imageWidth;
			
		//hide the section in case we switched images
		previewSection.hide();
		
		function getWidthAndHeight()
		{
			$imageHeight = this.height;
			$imageWidth = this.width;
			
			proceed();
	
	    	return true;
		}
		
		var myImage = new Image();
		myImage.name = imageName;
		myImage.onload = getWidthAndHeight;
		myImage.src = imageSrc;
			
		function proceed()
		{
			//empty the container
			previewSection.html('');

			//append image
			var image = document.createElement("img");
			image.src = imageSrc;
			$(previewSection).append(image);
			
			previewSection.fadeIn("slow");
			
			//populate the hidden input for the product
			$("#image", $currentTab).val(imageName);
		}
	}
</script>