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

            <div class="error-holder">
                <?php $ERRORS->PrintAny('changemail'); ?>
            </div>
  
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Change E-mail Address</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
            <!-- Store Activity -->
            <div class="store-activity">
            
                <div class="page-desc-holder">
                    In order to change your e-mail address you have to answer your secret question.
                </div>
                
                <div class="container_3 account-wide">
                    
                    <form action="<?php echo base_url(), '/account/submit_changemail'; ?>" method="post" class="page-form">
                        
                        <div class="row">
                            <label>Select your Secret Question:</label>
                                
                                <span style="display: inline-block; float:right;">
                                    <select name="secretQuestion" id="select-style-2" data-stylized="true">
                                        
                                        <?php
                                        $Questions = new SecretQuestionData();
                                        
                                        foreach ($Questions->data as $key => $value)
                                        {
                                            echo '<option value="', $key, '">', $value, '</option>';
                                        }
                                        
                                        unset($Questions);		
                                        ?>
                                        
                                </select>
                            </span>
                        </div>
                        
                        <div class="row">
                            <label for="secretAnswer">Answer your Secret Question:</label>
                            <input type="text" name="secretAnswer" />
                        </div>
                        
                        <div class="row">
                            <label for="email">Enter your new E-mail Address:</label>
                            <input type="text" name="email" />
                        </div>
                        
                        <input type="submit" value="Change" />

                    </form>
                                
                </div>
                            
            </div>
            <!-- Store Activity.End -->
        
        </div>
	</div>
 
</div>