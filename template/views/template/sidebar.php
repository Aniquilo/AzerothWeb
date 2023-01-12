<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}
?>

<!-- Sidebar -->
<div class="sidebar">
    
    <div class="connect-box">
        <a href="<?=base_url()?>/support/howto?activate=0" id="support">
            <h1><?=lang('how_to_connect')?></h1>
            <p><?=lang('how_to_connect_to')?> <?=$CORE->configItem('SiteName')?></p>
        </a>
    </div>

    <div class="download-box">
        <a href="<?=base_url()?>/downloads#launcher" id="launcher_dw">
            <div class="dw-icon"></div>
            <h1><?=lang('download')?></h1>
            <p><?=lang('launcher_and_client')?></p>
        </a>
    </div>

    <!-- REALMLIST -->
    <div class="realmlist">
        <p>set realmlist <span><?=$config['realmlist']?></span></p>
    </div>
    <!-- REALMLIST.End -->
  
  	<div class="index-status-container home_container">

		<?php
        if ($CORE->getRealmsConfig())
        {
            foreach ($CORE->getRealmsConfig() as $id => $data)
            {
				$background = (isset($data['background']) ? $data['background'] : 'wotlk'); 
				
                echo '<!-- REALM -->
                    <div class="realm_st realm_st_', $background, '" data-id="', $id, '">
                        <a href="', base_url(), '/realminfo?id=', $id, '">
                            <div class="realmst_head">
                                <span id="realm-status-', $id, '"></span>
                                <div class="realm_name">', $data['name'], '</div>
                                <p class="realm-desc">', $data['descr'], '</p>
                            </div>
                        </a>
                    </div>
                <!-- REALM.End -->';
                
                unset($count, $stats);
            }
            unset($id, $data);
        }
        ?>
        
       	<div class="logon-status">
        	<div id="logon-status">
            	<h3><?=lang('logon_status')?><br /><p class="status" id="logon-status2"><?=lang('unknown')?></p></h3>
                <!--<p>2 days 2 hours 52 min Uptime</p>-->
            </div>
            <div id="server-time">
            	<span><?=lang('server_time')?></span>
                <p id="server-time-cloack">00:00:00</p>
            </div>
        </div>
    </div>
    
    <?php
    $CORE->loadLibrary('polls');

    $poll = PollsLib::GetPoll();
    
    if ($poll)
    {
        $showResults = true;

        if ($CORE->user->isOnline())
        {
            $showResults = PollsLib::HasAnswer($poll['id']);
        }
        ?>

        <div class="sidebar-box poll">
            <div class="sub_header">
                <h1><?=lang('poll')?></h1>
                <div class="title_overlay"></div>
            </div>

            <div class="content">
                <h1><?=$poll['question']?></h1>

                <ul class="options" style="display: <?=($showResults ? 'none' : 'block')?>;">
                    <?php if ($poll['answers']) { ?>
                        <?php foreach ($poll['answers'] as $answer) { ?>
                            <li>
                                <label class="label_radio">
                                    <div></div>
                                    <input type="radio" name="answer" value="<?=$answer['id']?>" data-poll="<?=$poll['id']?>" />
                                    <p><?=$answer['answer']?></p>
                                </label>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>

                <ul class="results" style="display: <?=($showResults ? 'block' : 'none')?>;">
                    <?php if ($poll['answers']) { ?>
                        <?php foreach ($poll['answers'] as $answer) { ?>
                            <li data-id="<?=$answer['id']?>" data-votes="<?=$answer['votes']?>">
                                <p>
                                    <?=$answer['answer']?>
                                    <span><b><?=$answer['votes']?></b> <?=lang('votes')?></span>
                                </p>
                                <div class="bar">
                                    <div class="fill" style="width: <?=$answer['pct']?>%;"></div>
                                </div>
                            </li>
                        <?php } ?>
                    <?php } ?>
                </ul>

            </div>

        </div>
        
        <script>
            $('.poll .options input').on('change', function(e) {
                var input = $(e.target);
                var pollId = input.attr('data-poll');
                var answer = input.val();

                $.post($BaseURL + '/ajax/pollVote', {
                    pollId: pollId,
                    answer: answer
                }, function(response) {
                    if (typeof response.error != 'undefined') {
                        $.fn.WarcryAlertBox('open', '<p>' + response.error + '</p>');
                    } else {
                        $.each(response.answers, function(i, answer) {
                            var elem = $('.poll .results li[data-id="'+answer.id+'"]');
                            if (elem) {
                                $('p span b', elem).html(answer.votes);
                                $('.fill', elem).css('width', answer.pct + '%');
                            }
                        });
                        $('.poll .options').fadeOut('fast', function() {
                            $('.poll .results').fadeIn('fast');
                        });
                    }
                });
            });
        </script>

    <?php } ?>
    <?php unset($poll); ?>

    <div class="sidebar-box spotlight">
    	
        <?php
		$CORE->loadLibrary('articles.base');
		
		$ArticlesLimit = 10;
		
		//Get the articles
		$res = $DB->prepare("SELECT `id`, `title`, `short_text`, `views`, `added`, `image` FROM `articles` ORDER BY `id` DESC LIMIT :limit;");
		$res->bindParam(':limit', $ArticlesLimit, PDO::PARAM_INT);
		$res->execute();
		
		if ($res->rowCount() > 0)
		{
			echo '
			<div class="sub_header">
				<h1>', lang('spotlight'), '</h1>
				<div class="title_overlay"></div>
			</div>
    
			<div class="blueberry">
				<ul class="slides">';
				
				$First = true;
				while ($arr = $res->fetch())
				{
					echo '
					<li', ($First ? ' style="position: relative"' : ' style="display: none"'), '>
						
						', ($arr['image'] != '' ? '<div class="spotlight_image" style="background-image: url(\''.base_url().'/uploads/articles/'.$arr['image'].'\');"></div>' : ''), '
						<h1><a href="', base_url(), '/articles/view?id=', $arr['id'], '">', $arr['title'], '</a></h1>
						<h4>', date('d M, Y', strtotime($arr['added'])), " &nbsp;&nbsp;&nbsp; ", $arr['views'], " ", lang('views'), " &nbsp;&nbsp;&nbsp; <span>", ArticlesLib::getCommentsCount($arr['id']), ' ', lang('comments'), '</span></h4>
						<p>', htmlspecialchars(stripslashes($arr['short_text'])), '</p>
					</li>';
					
					if ($First)
						$First = false;
				}
				unset($arr, $First);
				
				echo '
				</ul>
				
				<!-- Optional, see options below -->
				<ul class="pager">';
					
					//Set the buttons for the slides
					for ($i = 0; $i < $res->rowCount(); $i++)
						echo '<li><a href="#"><span></span></a></li>';
				
				echo '
				</ul>
				<!-- Optional, see options below -->
				
			</div>';
			
			//Initialize Blueberry only if we have more then one article
			if ($res->rowCount() > 1)
			{
				echo '
				<script>
					$(window).load(function()
					{
						$(\'.blueberry\').blueberry();
					});
				</script>';
			}
		}
		unset($res, $ArticlesLimit);
		
		?>
        
	</div>
    
</div>

<?php
$CORE->tpl->AddFooterJs('template/js/jquery.blueberry.js');
?>

<!-- Sidebar.End -->