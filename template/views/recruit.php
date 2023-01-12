<?php
if (!defined('init_engine'))
{	
	header('HTTP/1.0 404 not found');
	exit;
}

//load the raf lib
$CORE->loadLibrary('raf');

//setup the raf class
$raf = new RAF();
?>

<div class="content_holder">

  	<div class="container_2 account">
        <div class="cont-image">
    
            <div class="error-holder">
                <?php $ERRORS->PrintAny('raf'); ?>
            </div>
   
            <div class="container_4 account_sub_header">
                <div class="grad">
                    <div class="page-title">Recruit a Friend</div>
                    <a href="<?php echo base_url(), '/account'; ?>"><?=lang('back_to_account')?></a>
                </div>
            </div>
      
      	    <div class="recruit-a-friend">
      		
                <div class="page-desc-holder">
                    By recruiting friends you will benefit greatly, but before that your friends must become <br/>
                    eligible for the program.To become eligible your referrals must have atleast one charcater level 60 or level 80 <br/>
                    for Death Knight. <br/>
                    For each five votes by your friend, you will receive 1 Silver coin.<br/>
                    If your friend  purchases 50 Gold coins, you will receive 5 Gold coins as reward.<br/>
                </div>
            
                <div class="container_3 account-wide">
                
                    <!-- RECRUIT Link -->
                    <div class="recruit-link-holder">
                        <h2>Your referal link</h2>
                        <div class="recruit-link">
                            <input readonly="readonly" type="text" value="<?=base_url()?>/register?raf=<?=$raf->GetCuruserHash()?>" id="raf-hash" />
                            <a class="simple_button" href="javascript: void(0);" id="raf-hash-btn">Copy</a>
                        </div>
                    </div>
                    
                    <!-- RECRUITED -->
                    <div class="recruited">
                            
                            <!-- ACTIVE Referals -->              
                            <div class="raf-table active-recruited-members">
                            
                                <div class="table-title">
                                    <h2>Active referrals</h2>
                                </div>
                                <div class="table-header">
                                    <div>Display Name</div>
                                    <div>Registration Date</div>
                                    <div>Completion Date</div>
                                    <div>Status</div>
                                </div>
                                
                                <ul>
                                    <?php
                                    if ($res = $raf->GetActiveLinks($CORE->user->get('id')))
                                    {
                                        while ($arr = $res->fetch())
                                        {
                                            //get the account info
                                            $res2 = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
                                            $res2->bindParam(':acc', $arr['account'], PDO::PARAM_INT);
                                            $res2->execute();
                                            
                                            if ($res2->rowCount() > 0)
                                            {
                                                $row = $res2->fetch();
                                                $displayName = $row['displayName'];
                                                unset($row);
                                            }
                                            else
                                            {
                                                $displayName = 'Unknown';
                                            }
                                            unset($res2);
                                            
                                            echo '<li><div>', $displayName, '</div><div>', $arr['date'], '</div><div>', $arr['cDate'], '</div><div>Active</div></li>';
                                            
                                            unset($displayName);
                                        }
                                        unset($arr);
                                    }
                                    else
                                    {
                                        echo '<li><p class="raf-no-records"><strong>There are no records.</strong></p></li>';
                                    }
                                    unset($res);
                                    ?>
                                </ul>

                            </div>
                            
                            <!-- PENDING Referals -->              
                            <div class="raf-table pending-ref">
                            
                                <div class="table-title">
                                    <h2>Pending referrals</h2>
                                    <a href="#" id="update_pendings" onclick="return UpdatePedingRAFLinks();" style="float: right; top: -14px;">Update all</a>
                                </div>
                                <div class="table-header">
                                    <div>Display Name</div>
                                    <div>Registration Date</div>
                                    <div>Character level</div>
                                </div>
                                
                                <ul>
                                    <?php
                                    if ($res = $raf->GetPendingLinks($CORE->user->get('id')))
                                    {
                                        while ($arr = $res->fetch())
                                        {
                                            //status text
                                            $statusText = $arr['statusText'] != '' ? $arr['statusText'] : 'Waiting for update';
                                            //get the account info
                                            $res2 = $DB->prepare("SELECT `displayName` FROM `account_data` WHERE `id` = :acc LIMIT 1;");
                                            $res2->bindParam(':acc', $arr['account'], PDO::PARAM_INT);
                                            $res2->execute();
                                            
                                            if ($res2->rowCount() > 0)
                                            {
                                                $row = $res2->fetch();
                                                $displayName = $row['displayName'];
                                                unset($row);
                                            }
                                            else
                                            {
                                                $displayName = 'Unknown';
                                            }
                                            unset($res2);
                                            
                                            echo '<li class="pending-link" id="link', $arr['id'], '"><div>', $displayName, '</div><div>', $arr['date'], '</div><div id="status_text">', $statusText, '</div></li>';
                                            
                                            unset($displayName);
                                        }
                                        unset($arr);
                                    }
                                    else
                                    {
                                        echo '<li><p class="raf-no-records"><strong>There are no records found.</strong></p></li>';
                                    }
                                    unset($res);
                                    ?>
                                </ul>
                                
                            </div>
                            
                            <div class="referal-pending-info"></div>
                                            
                    </div>
                
                </div>
            
      	    </div>
        
        </div>
	</div>
 
</div>

<script type="text/javascript">
	$(function()
	{
        $('#raf-hash-btn').click(function() {
            /* Get the text field */
            var copyText = document.getElementById("raf-hash");

            /* Select the text field */
            copyText.select();

            /* Copy the text inside the text field */
            document.execCommand("copy");
        });
	});                               
</script>

<?php
	unset($raf);
?>