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
                        
                        <h1>Terms of Service</h1>
                        <script type="text/javascript" src="<?=base_url()?>/template/js/jquery.tinyscrollbar.min.js"></script>
                        <script type="text/javascript">
                            $(document).ready(function()
                            {
                                $('#terms-container').tinyscrollbar({ size: 694 });
                                
                                $('#i-agree').click(function(e)
                                {
                                    //redirect to the registration page
                                    $.get($BaseURL + '/ajax/acceptTerms', function(data)
                                    {
                                        window.location = data;
                                    });
                                });
                            });
                        </script>						
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>
                              
                        <h2>TERMS OF USE</h2>
                        <p> By accessing or using www.azeroth-project.com (the "Site") and affiliated services (the "Services") that belongs to Quel'dorei WoW, you (the "User") agree to comply with the terms and conditions governing the User's use of any areas of the Site and affiliated services as set forth below. </p>
                        <h2>USE OF SITE</h2>
                        <p> This Site or any portion of the Site as well as the Services may not be reproduced, duplicated, copied, sold, resold, or otherwise exploited for any commercial purpose except as expressly permitted by Azeroth Project. Azeroth Project reserves the right to refuse service in its discretion, without limitation, if Quel'dorei WoW believes that User conduct violates applicable law or is harmful to the interests of Azeroth Project, other users of the Site and the Services or its affiliates. </p>
                        <h2>SITE ACCOUNT</h2>
                        <p> You may register a regular account and password for the service for free. You are responsible for all activity under your account, associated accounts, and passwords. The Site is NOT responsible for unauthorised access to your account, and any loss of virtual items associated with it. </p>
                        <h2>ACCESS TO THE SITE AND THE SERVICES</h2>
                        <p> Azeroth Project provides free and unlimited access to the Site and the Services. </p>
                        <h2>SUBMISSION</h2>
                        <p> Azeroth Project does not assume any obligation with respect to any Submission and no confidential or fiduciary understanding or relationship is established by the Site's receipt or acceptance of any submission. All submissions become the exclusive property of the Site and its affiliates. The Site and its affiliates may use any submission without restriction and the User shall not be entitled to any compensation. </p>
                        <h2>VERIFICATION</h2>
                        <p> THE USER MAY BE REQUIRED TO UNDERGO A VERIFICATION PROCEDURE INCLUDING, AND NOT LIMITED TO, SUBMISSION OF NECESSARY INFORMATION AND/OR DOCUMENTS TO ENSURE LEGITIMACY OF ANY PAYMENTS OR DONATIONS SHOULD WE CONSIDER ANY PAYMENT OR DONATION SUSPICIOUS. ACCOUNTS UNDERGOING VERIFICATION PROCEDURE REMAIN DISABLED UNTIL VERIFICATION PROCEDURE IS COMPLETE. SUBMITTED INFORMATION MAY BE DISCLOSED TO OUR AFFILIATES IN OUR MUTUAL EFFORTS TO PREVENT UNAUTHORISED PAYMENTS/DONATIONS. REQUESTED INFORMATION IS TO BE SUBMITTED BY EMAIL/FAX/ONLINE FORM AND MAY INCLUDE VERIFICATION OF THE USER'S IDENTITY. </p>                        


                      <!--END OF ABOUT TEXT-->
                      <div class="d-options">
                          <input type="submit" class="option1" id="i-agree" value="¡SI ESTOY DE ACUERDO!" />
                          <a href="<?php echo base_url(); ?>/home" target="_blank" class="option1">¡NO ESTOY DE ACUERDO!</a>
                        </div>
					</div>
                </div>
            
                    
                </div>   
            
            <!--end header-->

                
