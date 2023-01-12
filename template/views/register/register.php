<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//get raf hash
$rafHash = isset($_GET['raf']) ? $_GET['raf'] : false;
?>

            <!--header-->
            
            <div class="page-header">
                
            <a href="<?=base_url()?>"><img id="logo-header" src="<?=base_url()?>/template/style/images/logo-header.png"/></a>
                
                 <div class="page">
                
                    <div class="page-top"></div>
                    <div class="page-body">
                    
                        <div class="page-content">
                        <h1>Registrar</h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>
                     





                        <div class="container_2">
 
                          <div class="error-holder">
                              <span class="red"><?php $ERRORS->PrintAny('register'); ?></span> 
                          </div>

                          <div class="container_3 account-wide">

                          <!-- FORMS -->
                          <form action="<?=base_url()?>/register/submit" method="post" name="registrationForm" class="page-form">
                              
                              <?php
                            //RAF system input
                            if ($rafHash)
                              echo '<input type="hidden" name="raf" value="', $rafHash, '" />';
                          ?>
                              
                              <?php if (!$CORE->configItem('bnet', 'authentication')) { ?>
                              <div class="row">
                                
                                <label for="register-username" class="form-label"><p><?=lang('account_name', 'register')?></p></label>
                                <input type="text" name="username" id="register-username" tabindex="1" />
                              </div>
                              <?php } ?>
                              
                              <div class="row">
                                <label for="register-displayName" class="form-label"><p><?=lang('display_name', 'register')?></p></label>
                                <input type="text" name="displayname" id="register-displayName" tabindex="2" />
                              </div>
                              
                                <div class="seperator"></div>
                              
                              <div class="row">
                                <label for="register-password"><p><?=lang('password', 'register')?></p></label>
                                <input type="password" name="password" id="register-password" tabindex="3" />
                              </div>
                              
                              <div class="row">
                                <label for="register-password2"><p><?=lang('repeat_password', 'register')?></p></label>
                                <input type="password" name="password2" id="register-password2" tabindex="4" />
                              </div>
                              
                              <div class="seperator"></div>
                              
                              <div class="row">
                                <label for="register-email"><p><?=lang('email_address', 'register')?></p></label>
                                <input type="text" name="email" id="register-email" tabindex="5" />
                              </div>        
                              
                              <div class="seperator"></div>
                              
                              <div class="row" style="display: none">
                                <label><p><?=lang('birthday', 'register')?></p></label>
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
                                <label for="register-secretAnswer"><p><?=lang('secret_answer', 'register')?></p></label>
                                <input type="text" name="secretAnswer" id="register-secretAnswer" tabindex="8" />
                              </div>
                              
                              <?php // If recaptcha is enabled
                                  if ($CORE->configItem('enabled', 'recaptcha'))
                                  {
                                      $CORE->loadLibrary('recaptcha');

                                      $recaptcha = new Recaptcha();

                                      echo '<div class="seperator"></div><div class="row">', $recaptcha->render(), '</div>';

                                      unset($recaptcha);
                                  }
                              ?>

                                <input type="submit" value="Registrar" tabindex="10">

                          </form>
                          <!-- FORMS.End -->

                          </div>


                          </div>











                        </div>
                </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->


<?php
    $ERRORS->RestoreForm('register', 'registrationForm');
?>