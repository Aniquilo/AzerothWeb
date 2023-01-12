<?PHP
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

$ERRORS->PrintAny(array('permissions', 'important_notice'));           
?>

<!-- Secondary navigation -->
<nav id="secondary">
	<ul>
		<li class="current"><a href="#maintab">Website Notice</a></li>
	</ul>
</nav>
          
<!-- The content -->
<section id="content">

    <div class="tab" id="maintab">
        <h2>Website Notice Message</h2>
        <div class="form">
            <form method="post" action="<?php echo base_url(); ?>/admin/notice/save" id="nortice_form">
                <section>
                    <label for="enabled">Enabled</label>
                    <div>
                        <select id="enabled" name="enabled">
                            <option value="0" <?=($config['IMPORTANT_NOTICE_ENABLE'] ? '' : 'selected')?>>No</option>
                            <option value="1" <?=($config['IMPORTANT_NOTICE_ENABLE'] ? 'selected' : '')?>>Yes</option>
                        </select>
                    </div>
                </section>
                <section>
                    <label for="message">Message</label>
                    <div>
                        <textarea class="tinymce" rows="3" id="message" name="message"><?=$config['IMPORTANT_NOTICE_MESSAGE']?></textarea>
                    </div>
                </section>
                <br/>
                <p>
                    <input type="submit" class="button primary submit" value="Submit" />
                </p>
            </form>
        </div>

    </div>
</section>

<script src="<?=base_url()?>/admin_template/js/tinymce/tinymce.min.js" type="text/javascript"></script>
<script src="<?=base_url()?>/admin_template/js/tinymce/jquery.tinymce.min.js" type="text/javascript"></script>

<script type="text/javascript">
	$(document).ready(function(e)
	{
		tinymce.init({
            plugins: 'print preview searchreplace autolink directionality visualblocks visualchars fullscreen image link media template codesample table charmap hr pagebreak nonbreaking anchor toc insertdatetime advlist lists wordcount imagetools textpattern help',
            toolbar: 'formatselect | bold italic strikethrough forecolor backcolor permanentpen formatpainter | link image media pageembed | alignleft aligncenter alignright alignjustify  | numlist bullist outdent indent | removeformat | addcomment',
            image_advtab: true,
            selector: '.tinymce',
            height: 480
        });
    });
</script>