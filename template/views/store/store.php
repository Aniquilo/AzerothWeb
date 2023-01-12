<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<div class="content_holder">

	<div class="container_2 account store">
 		<div class="cont-image">
    
            <div class="error-holder">
                <?php $ERRORS->PrintAny('store'); ?>
            </div>
				
			<div class="container_4 account_sub_header">
				<div class="grad">
					<div class="page-title">Store</div>
					<a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
				</div>
			</div>
			
			<script type="text/javascript" src="<?php echo base_url(); ?>/template/js/page.store.js"></script>
			<script type="text/javascript" src="<?php echo base_url(); ?>/template/js/jquery.tinyscrollbar.min.js"></script>
			<script type="text/javascript">
				$(document).ready(function()
				{
					$('#left_scrollbable').tinyscrollbar();
					$('.store_items_list').tinyscrollbar({ size: 600 });
					$('.store_body').WarcryStore();
				});
			</script>
			<!-- Store -->
            
            <form id="store_form" onsubmit="return false;">

                <div class="container_3 account-wide">
                    
                    <div class="store_header">
                        
                        <input type="text" id="store_search" placeholder="Search by item name" name="search" />
                        
                        <select id="store_quality" class="store_quality" name="quality" data-stylized="true">
                            <option selected="selected" value="-1">Item quality</option>
                            <?php
                            echo '
                            <option value="0" style="color: #9D9D9D;"         ', ($quality == '0' ? 'selected="selected"' : ''), '>Poor</option>
                            <option value="1" style="color: white;"           ', ($quality == '1' ? 'selected="selected"' : ''), '>Common</option>
                            <option value="2" style="color: #1EFF00;"         ', ($quality == '2' ? 'selected="selected"' : ''), '>Uncommon</option>
                            <option value="3" style="color: #0070DD;"         ', ($quality == '3' ? 'selected="selected"' : ''), '>Rare</option>
                            <option value="4" style="color: #A335EE;"         ', ($quality == '4' ? 'selected="selected"' : ''), '>Epic</option>
                            <option value="5" style="color: #FF8000;"         ', ($quality == '5' ? 'selected="selected"' : ''), '>Legendary</option>
                            <!--<option value="6" style="color: #E5CC80;"     ', ($quality == '6' ? 'selected="selected"' : ''), '>Artifact</option>-->
                            <option value="7" style="color: #E5CC80;"         ', ($quality == '7' ? 'selected="selected"' : ''), '>Bind to Account</option>';
                            ?>
                        </select>
                        <section id="level_filters">
                            <span>Required level</span>
                            <input type="text" maxlength="3" id="min_level" name="minlevel" value="0" onkeypress="return isNumberKey(event)" />
                            <span class="sep">-</span>
                            <input type="text" maxlength="3" id="max_level" name="maxlevel" value="255" onkeypress="return isNumberKey(event)" />
                        </section>
                        
                    </div>
                </div>

                <div class="store_footer account-wide clearfix">

                    <div class="store_footer_filters">
                        <label class="label_check">
                            <div></div>
                            <input type="checkbox" value="1" id="store_have_currency" name="havecurrency">
                            <p>Have Currency</p>
                        </label>
                    </div>

                    <div class="store_footer_character clearfix">
                        <?php
                        $realm = $CORE->realms->getRealm($RealmId);

                        //check db connection
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
                                <select id="store_character_select" class="character-select" name="character" data-stylized="true">
                                    <option selected="selected" disabled="disabled" getHtmlFrom="#select-charcater-selected"></option>
                                    ', $selectOptions, '
                                </select>';
                                
                                unset($selectOptions);
                            }
                            else
                            {
                                echo '
                                <select id="store_character_select" class="character-select" name="character" data-stylized="true">
                                    <option selected="selected" disabled="disabled">You have no characters</option>
                                </select>';
                            }
                            unset($characters);
                        }
                        else
                        {
                            echo '<p class="there-are-no-chars">Error: Failed to load your characters.</p>';
                        }
                        ?>
                    </div>
                    
                    <input type="submit" value="Search" />
                </div>
            
                <div class="store_body centered">
                    <div class="store_inner_body">
                    
                        <div class="store_left_side">
                            <div class="scrollable clearfix" id="left_scrollbable">
                                <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
                                <div class="viewport">
                                    <div class="overview" id="store_categories">
                                        
                                        <?php
                                        
                                            $Categories = new StoreCategories();
                                            
                                            // Print the categories
                                            foreach ($Categories->GetAll() as $category)
                                            {
                                                $HasSubs = (isset($category['sub_categories'])) ? true : false;
                                                
                                                echo '
                                                <div class="store_category" data-id="', $category['id'], '">
                                                    <a href="#" class="store_category_button">
                                                        <span>', $category['name'], '</span>
                                                        ', ($HasSubs ? '<p id="arrow"></p>' : ''), '
                                                        <div class="clear"></div>
                                                    </a>';
                                                    
                                                    if ($HasSubs)
                                                    {
                                                        echo '<div class="store_sub_categories" align="left" style="display: none;">';
                                                        
                                                        foreach ($category['sub_categories'] as $sub)
                                                        {
                                                            echo '
                                                            <a href="#" class="store_sub_category_button" data-id="', $sub['id'], '">
                                                                <span>', $sub['name'], '</span>
                                                            </a>';
                                                        }
                                                        
                                                        echo '</div>';
                                                    }
                                                
                                                echo '</div>';
                                            }
                                            unset($Categories);
                                        ?>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="store_right_side">
                            
                            <div class="store_items_list scrollable clearfix">
                                <div class="scrollbar"><div class="track"><div class="thumb"><div class="end"></div></div></div></div>
                                <div class="viewport">
                                    <div class="overview">
                                        <ul class="items">
                                            
                                            <!--
                                            <li class="item">
                                                <div id="hover"></div>
                                                <a id="icon" class="q7" href="#" rel="item=34233" style="background-image:url(http://wow.zamimg.com/images/wow/icons/medium/inv_shoulder_29.jpg);"></a>
                                                <div id="middle">
                                                    <a href="#" class="q7" rel="item=34233">Champion's Deathdealer Breastplate</a>
                                                    <p><font color="#927a4b">6</font> Gold Coins &nbsp; / &nbsp; <font color="#847f7a">59</font> Silver Coins</p>
                                                </div>
                                                <input type="button" value="Purchase" class="simple_button" />
                                                <div class="clear"></div>
                                            </li>
                                            -->
                                            
                                            <li class="info">
                                                Please select a category or enter a search <br />to begin.
                                            </li>
                                            
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="clear"></div>
                    </div>
                    
                </div>
                <!-- Store.End -->
            </form>
        
     	</div>
	</div>

</div>

<div class="store_item_purchase_popup" style="display: none">
	<div class="store_popup_box">
		<div class="popup_box_top">
		</div>
		<div class="popup_box_bottom popup_purchase_bottom">
			<form onsubmit="return false;" id="store_popup_form">
				<span>Select Currency & Purchase</span>
				<div class="popup_currency_choice">
					<label class="label_radio" id="gold" data-amount="0">
						<div></div>
						<input type="radio" name="currency" value="gold" checked="checked">
						<p><span class="gold">0</span> Gold Coins</p>
					</label>
					
					<label class="label_radio" id="silver" data-amount="0">
						<div></div>
						<input type="radio" name="currency" value="silver">
						<p><span class="silver">0</span> Silver Coins</p>
					</label>
				</div>
				<input type="submit" value="Purchase" />
			</form>
        </div>
        <div class="popup_box_bottom popup_complete_bottom" style="display: none">
            <p>Purchase Complete</p>
            <span>The item above was sent to <b id="charname"></b>.</span>
		</div>
	</div>
</div>