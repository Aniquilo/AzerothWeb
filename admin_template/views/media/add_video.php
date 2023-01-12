<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny('add_video');
?>

<!-- Secondary navigation -->
<nav id="secondary" class="disable-tabbing">
	<ul>
		<li><a href="<?=base_url()?>/admin/media">Videos</a></li>
        <li class="current"><a href="<?=base_url()?>/admin/media/add_video">New Video</a></li>
		<li><a href="<?=base_url()?>/admin/media/screenshots">Screenshots</a></li>
	</ul>
</nav>
  
<!-- The content -->
<section id="content">

	<div class="tab" id="maintab">
		<h2>Add a Video</h2>

        
        <div class="form">
    
            <form method="post" action="<?php echo base_url(); ?>/admin/media/submit_video" id="videoForm" name="addVideoForm">
            
                <section>
                  <label for="label">
                    Title*
                    <small>100 characters maximum.</small>
                  </label>
                
                  <div>
                    <input id="label" name="name" type="text" maxlength="100" class="required" />
                  </div>
                </section>
                
                <section>
                  <label for="label">
                    Youtube Link*
                    <small>The URL pointing to the video on Youtube.</small>
                  </label>
                
                  <div>
                    <input id="label" name="youtube" type="text" maxlength="150" class="required" />
                  </div>
                </section>
                
                <section>
                  	<label for="embed_code">
                        Embed Code
                        <small>Leave empty for auto fill if you have Youtube Link.</small>
                  	</label>
                  
                  	<div>
                    	<textarea rows="3" id="embed_code" name="embed_code"></textarea>
                  	</div>
                </section>
                
                <section>
                  	<label for="short_desc">
                        Short Description
                        <small>200 characters maximum.</small>
                  	</label>
                  
                  	<div>
                    	<textarea rows="3" id="short_desc" name="short_desc" maxlength="200"></textarea>
                  	</div>
                </section>

                <section>
                  <label for="textarea">
                    Description*
                  </label>
                  
                  <div>
                    <textarea class="tinymce" id="textarea" name="text"></textarea>
                  </div>
                </section>

                <input type="hidden" id="videoImage" name="image" />
            </form>
	
            <section>
              <label for="textarea">
                Video Thumbnail
                <small>Leave empty to grab youtube thumbnail.</small>
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
     		
       		<div class="clear"></div>
   
		</div>
       
        <br />  
        <p>
            <input type="button" class="button primary submit" value="Submit" onclick="return submitVideo();"/>
        </p>

	</div>
</section>

<script src="<?=base_url()?>/admin_template/js/forms.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.color.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/jquery.form.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/tinymce/tinymce.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/tinymce/jquery.tinymce.min.js" type="text/javascript"></script>

<script type="text/javascript">
	var $configURL = '<?php echo base_url(); ?>';
	
	$(document).ready(function(e)
	{
		<?php
		if ($formData = $ERRORS->GetFormData('add_video'))
		{	
			echo '
			var savedFormData = $.parseJSON(', json_encode(json_encode($formData)), ');
			restoreFormData(\'addVideoForm\', savedFormData);';
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
		$("#videoForm").validate({
			rules: {
				name: {
					minlength: 1,
					maxlength: 100
                },
                short_desc: {
                    minlength: 0,
					maxlength: 200
                },
				youtube: {
					minlength: 1,
					maxlength: 150
				},
			}
		});
	});
    
    function submitVideo() {
        $('#videoForm').submit();
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
                $("#videoImage", $currentTab).val("");
                
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
			image.style.width = '300px';
			$(previewSection).append(image);
		
			previewSection.fadeIn("slow");
			
			//populate the hidden input for the product
			$("#videoImage", $currentTab).val(imageName);
		}
	}
</script>