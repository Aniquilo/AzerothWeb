<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny('addNews');
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li><a href="<?=base_url()?>/admin/news">News</a></li>
		<li class="current"><a href="<?=base_url()?>/admin/news/post">Post News</a></li>
	</ul>
</nav>

<!-- The content -->
<section id="content">

	<div class="tab" id="maintab">
		<h2>Post News</h2>

        <div class="form">
    
            <form method="post" action="<?php echo base_url(); ?>/admin/news/submit_post" id="newsForm" name="addNewsForm">
            
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
                    <label for="textarea">
                        Short Text*
                        <small>500 characters maximum.</small>
                    </label>
                          
                    <div>
                        <textarea class="required" rows="8" id="textarea" name="shortText"></textarea>
                    </div>
                </section>
                
                <section>
                  <label for="textarea2">
                    Content*
                  </label>
                  
                  <div>
                    <textarea class="tinymce" id="textarea2" name="text"></textarea>
                  </div>
                </section>
                
                <input type="hidden" name="image" id="newsImage" />
            
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
        
                    <form id="croppingForm" method="POST" onsubmit="return false;" name="cropForm">
                        
                        <input type="hidden" name="path" value="/uploads/temp" />
                        <input type="hidden" id="jCrop-imageName" name="imgName" value="" />
                        <input type="hidden" name="resize" value="229" />
                        
                        <input type="hidden" id="x" name="x" />
                        <input type="hidden" id="y" name="y" />
                        <input type="hidden" id="w" name="w" />
                        <input type="hidden" id="h" name="h" />
                        
                        <div id="image_CroppingSection" style="display: none; margin-bottom: 5px;">
                            <div id="uploadedImagePreview" style="width: 229px !important; display: inline-block;"></div>
                            <div id="CropResult" style="display: inline-block; max-width: 210px; padding-left: 5px; vertical-align: top;">
                                <button class="button primary" onclick="cropFormSubmit();">Crop</button>
                            </div>
                        </div>
                        
                    </form>
                                    
                    <form id="uploadForm" method="POST" name="thumbForm" enctype="multipart/form-data">
                        <input type="hidden" name="MAX_FILE_SIZE" value="10485760">
                        <input id="label" type="file" name="file" accept="image/*" onchange="ajaxFormSubmit()"/>
                        <input type="submit" value="submit" style="display: none;" />
                    </form>
              </div>
            </section>
     
       		<div class="clear"></div>
   
		</div>
       
        <br />  
        <p>
            <input type="button" class="button primary submit" value="Submit" onclick="return submitNews();"/>
        </p>

	</div>
</section>

<script src="<?=base_url()?>/admin_template/js/forms.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.color.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.Jcrop.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/tinymce/tinymce.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/tinymce/jquery.tinymce.min.js" type="text/javascript"></script>

<script type="text/javascript">
	var $configURL = '<?php echo base_url(); ?>';
	
	$(document).ready(function(e)
	{
		<?php
		if ($formData = $ERRORS->GetFormData('addNews'))
		{	
			echo '
			var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
			restoreFormData(\'addNewsForm\', savedFormData);';
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
		$("#newsForm").validate(
		{
			rules:
			{
				shortText:
				{
					minlength: 1,
					maxlength: 500
				},
			}
		});
	});
	
	//A function to submit the news
	function submitNews()
	{
		$('#newsForm').submit();
	}
	
	//////////////////////////////////////////////////////////////////
	// CROPPING & RESIZING
	//////////////////////////////////////////////////////////////////

	// Create variables (in this scope) to hold the API and image size
	var $jcrop_api, $boundx, $boundy;
	var $maxWidth = 229;
	
	var $jcrop_update = function(c)
	{	
		$('#x', $currentTab).val(c.x);
	    $('#y', $currentTab).val(c.y);
	    $('#w', $currentTab).val(c.w);
	    $('#h', $currentTab).val(c.h);
	}
	
	var $jcrop_clearCoords = function()
	{
		$('#croppingForm input', $currentTab).val('');
	}
	
	var $jcrop_options =
	{
		onChange: $jcrop_update,
		onSelect: $jcrop_update,
		onRelease: $jcrop_clearCoords,
		allowResize: true,
		allowSelect: false,
		setSelect: [229, 148, 0, 0],
		trueSize: [],
		aspectRatio: 1.555
	}
	
	var $jcrop_arg2 = function()
	{
		// Use the API to get the real image size
		var bounds = this.getBounds();
		$boundx = bounds[0];
		$boundy = bounds[1];
		// Store the API in the jcrop_api variable
		$jcrop_api = this;
	}

	function cropFormSubmit()
	{
		var cropForm = $("#croppingForm", $currentTab);
	    var cropppingSection = $('#image_CroppingSection', $currentTab);
			
		cropppingSection.hide();
		
		$('#image_Loading', $currentTab).html('Loading...');
	    $('#image_Loading', $currentTab).css('display', 'block');
		
		cropForm.ajaxSubmit(
		{
			url: $BaseURL + '/admin/ajax/imageCrop',
	    	success: function(data)
			{
				//hide the loading
				$('#image_Loading', $currentTab).fadeOut('fast');
		
				if (typeof data.error != 'undefined')
				{
					//unpopulate the hidden input for the product
                    $("#newsImage", $currentTab).val("");

                    new Notification(data.error, 'error', 'urgent');
				}
				else
				{
					//update preview
					updatePreview($configURL + "/uploads/temp/" + data.fileName, data.fileName);
				}
	  		}
		});	
		
		return false;		
	}
	
	//////////////////////////////////////////////////////////////////
	// Ajax Upload
	//////////////////////////////////////////////////////////////////

	var ajaxOptions = {
		url: $BaseURL + '/admin/ajax/imageUpload',
	    beforeSubmit: function(a,f,o) {
	   	 	$('#image_CroppingSection', $currentTab).hide();
			$('#image_PreviewSection', $currentTab).hide();
			$('#image_Loading', $currentTab).html('Loading...');
	     	$('#image_Loading', $currentTab).show();
	    },
	    success: function(data) {
            $('#image_Loading', $currentTab).hide();

			if (typeof data.error != 'undefined') {
				//unpopulate the hidden input for the product
                $("#newsImage", $currentTab).val("");
                
                new Notification(data.error, 'error', 'urgent');
			} else {
				//update preview
				updatePreview($configURL + "/uploads/temp/" + data.fileName, data.fileName);
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
	    var cropppingSection = $('#image_CroppingSection', $currentTab);
		var previewSection = $('#image_PreviewSection', $currentTab);
		var $imageHeight;
		var $imageWidth;
			
		//hide the section in case we switched images
		cropppingSection.hide();
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
			if ($imageWidth > 229 || $imageHeight > 148)
			{
				//open the cropping section
				var previewContainer = cropppingSection.find('#uploadedImagePreview');
				
				previewContainer.fadeIn();
				
				//empty the container
				previewContainer.html('');
				
				//append image
				var image = document.createElement("img");
				image.src = imageSrc;
				
				$(image).css({ maxWidth: $maxWidth, });
				
				previewContainer.append(image);
					
				cropppingSection.fadeIn("slow");
				
				//set image name input
				$("#jCrop-imageName", $currentTab).val(imageName);
				
				$jcrop_options.trueSize = [$imageWidth, $imageHeight];
				//start the crop
				$(image).Jcrop($jcrop_options , $jcrop_arg2);
			}
			else
			{
				//empty the container
				previewSection.html('');
	
				//append image
				var image = document.createElement("img");
				image.src = imageSrc;
				$(previewSection).append(image);
			
				previewSection.fadeIn("slow");
				
				//populate the hidden input for the product
				$("#newsImage", $currentTab).val(imageName);
			}
		}
	}
</script>