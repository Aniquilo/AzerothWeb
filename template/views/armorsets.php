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
                <?php $ERRORS->PrintAny('pStore_armorsets'); ?>
            </div>
		  	
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Armor Sets</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>    
              
          	<!-- ARMOR SETS -->
          	<div class="armor-sets account-wide centered">
                
           		<!-- Top Bar (Charcater & Gear filter) -->
                <div class="container_3 account-wide" style="margin:40px auto 0 auto;">
               		<div style="padding:10px 0 10px 0;">
                    	<!-- Charcaters -->
                    	<div style="display:block; padding:0 20px 0 10px; float:left;">
							<?php
                            $realm = $CORE->realms->getRealm($RealmId);

                            //set the realm
                            if ($realm->checkCharactersConnection())
                            {
                                if ($characters = $realm->getCharacters()->getAccountCharacters())
                                {
                                    $selectOptions = '';
                                    
                                    //loop the characters
                                    foreach ($characters as $arr)
                                    {
										$ClassSimple = str_replace(' ', '', strtolower($CORE->realms->getClassString($arr['class'])));
										
                                        echo '
                                        <!-- Charcater ', $arr['guid'], ' -->
                                        <div id="character-option-', $arr['guid'], '" style="display:none;">
                                            <div class="character-holder">
                                                <div class="s-class-icon ', $ClassSimple, '" style="background-image:url(\'http://wow.zamimg.com/images/wow/icons/medium/class_', $ClassSimple, '.jpg\');"></div>
                                                <p>', $arr['name'], '</p><span>Level ', $arr['level'], ' ', $CORE->realms->getRaceString($arr['race']), ' ', ($arr['gender'] == 0 ? 'Male' : 'Female'), '</span>
                                            </div>
                                        </div>
                                        ';
                                        
                                        $selectOptions .= '<option value="'. $arr['name'] .'" getHtmlFrom="#character-option-'. $arr['guid'] .'"></option>';
										
										unset($ClassSimple);
                                    }
									unset($arr);
                                    
                                    echo '
                                    <div id="select-charcater-selected" style="display:none;">
                                        <p class="select-charcater-selected">Select character</p>
                                    </div>
                                    <div style="display:inline-block; float: left; margin: 16px 18px 16px 18px; vertical-align: middle;">
                                        <select id="character-select" class="character-select" name="character" data-stylized="true">
                                            <option selected="selected" disabled="disabled" getHtmlFrom="#select-charcater-selected"></option>
                                            ', $selectOptions, '
                                        </select>
                                    </div>';
                                    unset($selectOptions);
                                }
                                else
                                {
                                    echo '<p class="there-are-no-chars">There are no characters.</p>';
                                }
								unset($characters);
                            }
                            else
                            {
                                echo '<p class="there-are-no-chars">Error: Failed to load your characters.</p>';
                            }
                            ?>
                   		</div>
                   		<!-- Charcaters.End -->
                   
                    	<div style="float:right; padding:2px 12px 0 0; margin: 16px 18px 16px 18px;">
                            <select id="armors-filter-select" class="armors-filter-select" name="filter" data-stylized="true">
                                <option selected="selected" disabled="disabled">Apply Filter</option>
                                <option value="0">None</option>
                                <?php
                                
                                $res = $DB->query("SELECT id, name FROM `armorset_categories` ORDER BY name ASC;");
                                //check if we have any cats at all
                                if ($res->rowCount() > 0)
                                {
                                    while ($arr = $res->fetch())
                                    {
                                        echo '<option value="', $arr['id'], '" ', ($filter == $arr['id'] ? 'selected="selected"' : ''), '>', $arr['name'], '</option>';
                                    }
									unset($arr);
                                }
                                unset($res);
								
                                ?>
                            </select>
                    	</div>
                   
                   <div class="clear"></div>
                                  
                  </div>
                </div>
                <!-- Top Bar (Charcater & Gear filter) -->
                
                <div class="pagination-container account-wide centered clearfix" id="armorsets-pagination" style="display: none">
                    <ul class="pagination">
                        <li id="pagination-nav-first"><a href="#">First</a></li>
                        <li id="pagination-nav-prev"><a href="#">Previous</a></li>
                        <li id="pages"><p></p>0-0 of 0<p></p></li>
                        <li id="pagination-nav-next"><a href="#">Next</a></li>
                        <li id="pagination-nav-last"><a href="#">Last</a></li>                
                    </ul>
                </div>
    
                <script type="text/javascript" src="<?php echo base_url(); ?>/template/js/page.armorsets.js"></script>
                
                <script>
                    $(document).ready(function()
                    {
						$('#armorsets_loading').LoadingBar();
						
                        //Setup the store class
                        $('#armorsets-container').WarcryArmorsets(
                        {
                            currentPage: 0, 
                            totalPages: 0, 
                            perPage: <?php echo $perPage; ?>, 
                            totalRecords: 0,
							filter:
							{
								category: <?php echo ($filter ? $filter : 0); ?>,
								character: ''
							},
                            realm: <?php echo $RealmId; ?>,
                        });
                    });
                </script> 
             	
                <!-- MESSAGE before the items -->
                <div class="armor-sets-page-msg" id="armorsets-starting-message">
                    <strong>Please select a charcater to continue.</strong><br /> Selecting a character will sort the armor sets by your character's class,<br/>
                    you can also apply a filter which will help you find your desired armor set faster.
                </div>
                
                <div class="displayed-armor-sets">
    
                    <div id="armorsets_loading" style="width: 100%; display: none;"></div>

                    <!-- this is the sets container -->
                    <div id="armorsets-container" style="padding:0; margin:0;">
                    	<!-- ARMORSETS ARE HERE -->
                    </div>
                    <!-- sets container ends -->
    
                </div>
                
                <div class="armor-set-prepurchase-info" style="display: none;">
                
	                <br/>
	                <p id="armorsets-info-title">
	                    <b>Please select an armor set.</b>
	                </p>
	                <br/>
                    
	                <p id="armorsets-info-text">
	                    Your new armor set will be delivered via the in-game mail system.
	                </p>
	                <br/><br/>
	                
	                <form action="<?=base_url()?>/armorsets/purchase" method="post" id="armorset-purchase-form">
	                    <input type="hidden" name="character" id="selected-character" />
	                    <input type="hidden" name="armorset" id="selected-armorset" />
	                    <input type="submit" value="Purchase" />
	                </form>
                
                </div>
                
                <div class="clear"></div>
                
            </div>
          <!-- ARMOR SETS.End -->
    
     </div>
	</div>
 
</div>