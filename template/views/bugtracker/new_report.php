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
                        
                        <h1>Bugtracker</h1>
                        <img src="<?=base_url()?>/template/style/images/line-title.png"/>
						
						
						
						
      
        
            <!-- BUG TRACKER - Submit Form -->
            <div class="error-holder">
                <?php $ERRORS->PrintAny('submit_bug'); ?>
            </div>
        
        	<div class="report-title centered">
            	<h1>Bug Report</h1>
                <p>Please select the right category for your report and include as much info as you can about the found bug. All the reports will be checked by the staff and will be aproved or closed in both cases you will recieve an answer about the report you submit.</p>
            </div>
        	
            <div class="holder-bugtracker-form container_3 account-wide" style="padding:36px">
            
            	<form method="post" action="<?=base_url()?>/bugtracker/submit_report" name="BTSubmitForm">
                
                    <div style="display:inline-block">
                        <select name="mainCategory" onchange="return showCategories(this);" data-stylized="true">
                            <option value="0" selected="selected" disabled="disabled">Select category</option>
                            <option value="<?=BT_CAT_WEBSITE?>">Website</option>
                            <option value="<?=BT_CAT_WOTLK_CORE?>">Game Server</option>
                        </select>
                    </div>
                        
                    <div class="sub-selects">
                                                
                        <!-- Categories -->
                        <div id="category-select" style="display:inline-block; margin:0 0 0 9px; display:none;">
                        </div>
                     	<!-- End.Categories -->
                       	
                        <!-- Sub Categories -->
                        <div id="subcategory-select" style="display:inline-block; margin:0 0 0 9px; display:none;">
                        </div>
                        <!-- End.Sub Categories -->
                        
                    </div>
                            
                    <br/>
                        
                    <input name="title" type="text" placeholder="Enter report title"  style="margin:15px 0 15px 0;" />
                    
                    <textarea name="text" style="display:block; float:none; width:800px; height:300px; margin:0 0 15px 0;" placeholder="Please describe the bug as much detail as possible."></textarea>
					

                   
                    <div class="select-priority">
					    <label class="label_radio form-check-inline"><input class="form-check-input" type="radio" name="prio" value="<?=BT_PRIORITY_LOW?>"/><p>Low Priority</p></label>
                        <label class="label_radio form-check-inline"><input class="form-check-input" type="radio" name="prio" value="<?=BT_PRIORITY_NORMAL?>" checked="checked"/><p>Normal Priority</p></label>
                        <label class="label_radio form-check-inline"><input class="form-check-input" type="radio" name="prio" value="<?=BT_PRIORITY_HIGH?>"/><p>Hight Priority</p></label>
                    </div>
                        
                    <input type="submit" value="Report" />
                </form>
            
            </div>
         <!-- BUG TRACKER - Submit Form . End -->



                      <!--END OF ABOUT TEXT-->
                </div>
                </div>
            
                    
                </div>   
            
            <!--end header-->

<?php
    $ERRORS->RestoreForm('submit_bug', 'BTSubmitForm');
?>