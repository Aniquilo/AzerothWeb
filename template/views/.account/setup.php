<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

            <!--header-->
            
            <div class="page-header">
                
            <img id="logo-header" src="<?=base_url()?>/template/style/images/logo-header.png"/>
                
                 <div class="page">
                
                    <div class="page-top"></div>
                    <div class="page-body">
                    

                        <!--HERE IS ABOUT TEXT-->

                        <div class="page-content">
                        
                        <h1>Actualización de los datos</h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>

<div class="content_holder">
    <div class="container_2 account">
        <div class="cont-image">
    
            <div class="error-holder">
                <?php
                if ($error = $ERRORS->GetErrors('account_setup'))
                {
                    echo $error, '<br><br>';
                            
                    unset($error);
                }			
                ?>
            </div>
        
            <div class="page-desc-holder" style="padding-top: 0;">
                <p> Necesitamos más información para mantener tu cuenta segura.</p>
            </div>
            
            <div class="container_3 account-wide">
        
                <!-- FORMS -->
                <form action="<?=base_url()?>/account/setup_submit" method="post" name="accSetupForm" class="page-form">

                    <div class="row">
                        <label for="displayName"><p>Nombre del Foro</p></label>
                        <input type="text" name="displayname" id="displayName" value="<?php echo $CORE->user->get('displayName'); ?>" />
                    </div>
                    
                    <div class="seperator"></div>

                    <div class="row" style="display: none">
                        <label>Birthday</label>
							<input type="text" name="birthday[year]" value="1999" tabindex="7" />
							<input type="text" name="birthday[day]" value="01" tabindex="6" />
							<input type="text" name="birthday[month]" value="01" tabindex="6" />
                    </div>
                    
                    <div class="seperator"></div>
                    
					<div class="row" style="display: none">
					<label for="select-country"><p><?=lang('country', 'register')?></p></label>
					<input type="text" name="country" value="US" tabindex="6" />
					</div>
                    
                    <div class="seperator"></div>

                              <div class="row">
                                <label for="secret-question"><p><?=lang('secret_question', 'register')?></p></label>
                                  <select name="secretQuestion" style="width: 350px !important;" id="secret-question" class="form-select form-select-lg" data-stylized="true">
                                      <option disabled="disabled"><?=lang('select_uestion', 'register')?></option>
                                      
                              <?php
                              $Questions = new SecretQuestionData();
                              
                              foreach ($Questions->data as $key => $value)
                              {
                                        echo '<option value="', $key, '">', $value, '</option>';
                              }
                              
                              unset($Questions);		
                              ?>
                                      
                                  </select>
                              </div>
                    
                    <div class="row">
                        <label for="register-secretAnswer"><p>Respuesta secreta</p></label>
                        <input type="text" name="secretAnswer" id="register-secretAnswer" />
                    </div>

                    <input type="submit" value="complete" />
                
                </form>
                <!-- FORMS.End -->
            </div>
        </div>
    </div>
    
</div>

                      <!--END OF ABOUT TEXT-->
                </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->


<?php
    $ERRORS->RestoreForm('account_setup', 'accSetupForm');
?>