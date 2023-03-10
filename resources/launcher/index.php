<!doctype html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Warcry WoW Launcher</title>
<link href="style.css" rel="stylesheet" />
<script src="js/html5shiv.js" type="text/javascript"></script>
<script src="js/jquery-1.7.js" type="text/javascript"></script>
<script src="js/jquery.blueberry.js" type="text/javascript"></script>
<script>
    $(function() {
        var h = Math.max(document.documentElement.clientHeight, window.innerHeight || 0);
        var scale = h / 376;

        $('.holder').css('transform-origin', 'top left');
        $('.holder').css('transform', 'scale('+scale+')');
    });

	$(window).load(function() {
		$('.blueberry').blueberry();
	});
</script>
</head>

<body>
	<div class="holder">
    	<!-- SIDEBAR -->
    	<aside>
        	<!-- NEWS -->
        	<article id="news">
                <div class="blueberry">
                      <ul class="slides">
                        <li>
                        	<h1><a href="#">XP Rate Changer: Scale your experience!</a></h1>
                            <img src="./uploads/launcher_lich_test.png" />
                            <h4>09/02/2013 | 268 Views | 0 Comments</h4>
                            <p>
                            This is another of our amazing features at Warcry that you can't find anywhere else. 
                            This isn't a normal...
                            </p>
                        </li>
                        <li style="display: none;">
                        	<h1><a href="#">IceCrown Citadel & The Gunship Battle</a></h1>
                            <img src="./uploads/launcher_lich_test.png" />
                            <h4>09/02/2013 | 223 Views | 0 Comments</h4>
                            <p>
                            We value this raid greatly because Warcry, one of very few, have ICC fully working and fully completeable. 
                            Because of this, you can get...
                            </p>
                        </li>
                        <li style="display: none;">
                        	<h1><a href="#">The Frozen Halls: 3 wings of development</a></h1>
                            <img src="./uploads/launcher_lich_test.png" />
                            <h4>09/02/2013 | 195 Views | 0 Comments</h4>
                            <p>
                            These are a vital part of the blizzard-like experience, and we are proud to say we have made them 
                            fully available, working...
                            </p>
                        </li>
                      </ul>
                    <!-- Optional, see options below -->
                      <ul class="pager">
                        <li><a href="#"><span></span></a></li>
                        <li><a href="#"><span></span></a></li>
                        <li><a href="#"><span></span></a></li>
                      </ul>
                    <!-- Optional, see options below -->
                </div>
                </article>
                <!-- NEWS.End -->
                
                <!-- REVISION -->
                <article id="revision">
                    <div class="icon"></div>
                    <div class="revision">
                        <h1>Revision 451</h1>
                        <h2>
                        Nam eu massa sit amet elit sagittis bibendum. 
                        Mauris in neque nisi. Nulla eu velit mi, eu ullamcorper massa.
                        </h2>
                    </div>
            </article>
            <!-- REVISION.End -->
        </aside>
        <!-- SIDEBAR.End -->
        
        <!-- MESSAGE Bar -->
        	<div class="msg-bar">
            	
                <!-- Active Message -->
            	<div class="active-msg">
                	<div id="overlay"></div>
                	<h1>Fusce venenatis pulvinar neque, et egestas tellus volutpat vitae!</h1>
                </div>
                
                <!-- No Message -->
                <div class="no-msg inactive"></div>
            
            </div>
        <!-- MESSAGE Bar.End -->
        
    </div>
</body>
</html>