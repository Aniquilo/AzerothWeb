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
			<!--TEXT BEGINNING-->	
			
				<div class="error-holder">
					<?php $ERRORS->PrintAny('submit_bug'); ?>
				</div>

				<!-- Bug Report Search -->
				<div class="bugs-search-bar container_3 clearfix">
					<form method="get" action="<?=base_url()?>/bugtracker/search">
						<input type="text" placeholder="Search..." name="q" />
							<select id="search-category" class="search-category" name="mainCategory" data-stylized="true">
								<option value="0" disabled="disabled">Select Category</option>
								<option value="<?=BT_CAT_WEBSITE?>">Website</option>
								<option value="<?=BT_CAT_WOTLK_CORE?>">Game Server</option>
							</select>
							<input type="hidden" name="search" value="1" />
						<input type="submit" value="Search" />
					</form>
				</div>
				<!-- Bug Report Search.End -->
				
				<!-- BUG TRACKER - Main Page -->
				<div class="holder-bugtracker">
					
					<div class="clearfix">
						<div class="bug-reports-holder reports">
							<h1><?=$total?></h1>
							<h3>Submited reports</h3>
						</div>
						
						<div class="bug-reports-holder confirmed">
							<h1><?=$countApproved?></h1>
							<h3>Approved Reports</h3>
						</div>
						
						<a href="<?=base_url()?>/bugtracker/new_report" class="submit-bug-report">
							<div class="plus-ico"></div>
							<h1>Submit Report</h1>
						</a>
					</div>
							
					<?php
					//check if we have current user
					if ($CORE->user->isOnline())
					{
						echo '
						<div class="bugs-submited-by-me">
							You have submitted ', $userCounts['total'], ' Bug Reports <span>(', $userCounts['approved'], ' of which are approved)</span> ', ($userCounts['total'] > 0 ? '<a id="see-all-reports" href="#">See All</a>' : ''), '
						</div>';
						
						//if we have report bind them script for load more
						if ($userCounts['total'] > 0)
						{
							?>
							<script type="text/javascript">
								$(document).ready(function() {
									Bugtracker.TotalReports = <?=$userCounts['total']?>;
									Bugtracker.PerPage = <?=$PerPage?>;
									Bugtracker.Initialize();
								});
							</script>
							<?php
						}
					}
					?>
							
					<!-- ALL REPORTS BY THIS USER - Will be displayed only if the user click on "SEE ALL" link! -->
					<div class="all-reports-by-me" style="display: none;">
						<ul class="reports" id="report-container">                                
						</ul>
					</div>
					<!--ALL REPORTS BY THIS USER . End -->
					
					<div class="bug-tracker-info">
						<h3>Bug Tracker Guidelines</h3>
						<p>
							We highly appreciate your efforts to report any problems you may discover on our site or ingame. In order to process and resolve all reported bugs, we ask you to follow the guidelines below.<br/><br/>
							<span>
							- Please search before submitting anything to our bug tracker. It's possible someone else has already reported the bug in question. <br/>
							- Use proper titles. E.g. the name of the quest, NPC or Item you may have problems with. <br/>
							- What is wrong? E.g. What happens and what is supposed to happen. <br/>
							- Add anything else you think might be useful for us to know.
							</span>
							<br/><br/>
							Please follow these guidelines and you'll make us work much easier. In return, we'll reward you with Silver Coins for each approved report.
						</p>
					</div>
						
				</div>
				<!-- BUG TRACKER - Main Page . End -->

			  <!--END OF ABOUT TEXT-->
			</div>
		</div>
	</div>
</div> 	
<!--end header-->



