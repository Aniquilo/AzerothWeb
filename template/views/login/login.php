<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>




<!--header-->

<div class="page-header">
	
<a href="<?=base_url()?>"><img id="logo-header" src="<?=base_url()?>/template/style/images/logo-header.png"/></a>
	
	 <div class="page">
	
		<div class="page-top"></div>
		<div class="page-body">
		

			<!--HERE IS ABOUT TEXT-->

			<div class="page-content">
			
			<h1>Iniciar sesión</h1>
			<img src="<?=base_url()?>/template/style/images/line-title.png"/>





			
			<div class="container_2 login-page">
			  
				<div class="error-holder">
					<?php $ERRORS->PrintAny('login'); ?>
				</div>

				<div class="container_3 account-wide">

					<!-- FORMS -->
					<form action="<?php echo base_url(); ?>/login/submit" method="post" name="loginForm" class="page-form">

						<?php if ($CORE->configItem('bnet', 'authentication')) { ?>
							<div class="row">
								<label><p><?=lang('E_mail_Address', 'login')?></p></label>
								<input type="text" name="email">
							</div>
						<?php } else { ?>
							<div class="row">
								<label for="username"><p><?=lang('account_name', 'login')?></p></label>
								<input type="text" id="username" name="username">
							</div>
						<?php } ?>

						<div class="row">
							<label for="password"><p><?=lang('password', 'login')?></p></label>
							<input type="password" id="password" name="password">
						</div>

						<?php // If recaptcha is enabled
						if ($CORE->configItem('enabled', 'recaptcha') && isset($_SESSION['login_attempts']) && (int)$_SESSION['login_attempts'] >= 3)
						{
							$CORE->loadLibrary('recaptcha');

							$recaptcha = new Recaptcha();

							echo '<div class="row">', $recaptcha->render(), '</div>';

							unset($recaptcha);
						}
						?>
						<!-- 
						<div class="row">
							<label class="label_check">
								<div></div>
								<input type="checkbox" value="1" id="rememberme" />
								<p><?=lang('remember_me', 'login')?></p>
							</label>
						</div>
						 -->
						 
						 
						 
						<br>
						<input type="submit" value="<?=lang('log_in', 'login')?>" style="margin-top: 0;" />

					</form>
				
					<div class="login-box-options login-links centered">
						<a href="<?php echo base_url(); ?>/recovery/password"><p><?=lang('forgot_your_password', 'login')?></p></a><br>
						<span><?=lang('Dont_have_an_account_yet', 'login')?><a href="<?php echo base_url(); ?>/register"><?=lang('register_now', 'login')?></a></span>
					</div>

					<!-- FORMS.End -->

				</div>
			   
			 </div>			





		  <!--END OF ABOUT TEXT-->
	</div>
	</div>

		
	</div>   

<!--end header-->











<?php
    $ERRORS->RestoreForm('login', 'loginForm');
?>