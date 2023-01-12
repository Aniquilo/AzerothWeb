// JavaScript Document
$(document).ready(function() {
    UI.Initialize();
});

var UI = {
    notificationsDelay: 5000,

    Initialize: function() {
        // Addative delay
        var delay = UI.notificationsDelay;

        //Handle fading notifications
        $('.fading-notification').each(function()
        {
            $(this).delay(delay).fadeOut(500);
            
            delay = delay + 500;
        });

        // Handle item icons that need updating
        UI.UpdateItemIcons();

        // Start server time clock
        UI.ServerTimeCloack();

        // Update realm statuses
        $('.realm_st').each(function(i, e) {
            var id = parseInt($(e).attr('data-id'));

            WarcryQueue('onload').add(function() {
                UI.UpdateRealmStatus(id);
            });
        })

        // Update logon status
        WarcryQueue('onload').add(function() {
            UI.UpdateLogonStatus();
        });

        // Run the queue
        WarcryQueue('onload').goNext();
    },

    UpdateItemIcons: function() {
        $('[data-update-icon]').each(function(i, e) {
            var rel = false;
            if (typeof $(e).attr("rel") != 'undefined' && $(e).attr("rel").length > 0) {
                rel = $(e).attr("rel");
            } else if (typeof $(e).parent().attr("rel") != 'undefined' && $(e).parent().attr("rel").length > 0) {
                rel = $(e).parent().attr("rel");
            }
            
            if (rel) {
                if (/item=(\d+)/.test(rel)) {
                    var entry = rel.match(/item=(\d+)/)[1];
                    var realmId = ($CURUSER.selectedRealm || 1);
    
                    if (typeof $(e).attr("data-realm") != 'undefined' && $(e).attr("data-realm").length > 0) {
                        realmId = $(e).attr('data-realm');
                    }
    
                    UI.UpdateItemIcon(e, entry, realmId);
                }
            }
        });
    },

    UpdateItemIcon: function(targetElement, entry, realmId)
    {
        //load item
        $.get($BaseURL + "/ajax/getItem",
        {
            entry: entry,
            realm: realmId
        },
        function(data)
        {
            var icon = 'inv_misc_questionmark';

            if (typeof data.error == 'undefined') {
                icon = data.icon;
            }
            
            var size = 'small';
            if ($(targetElement).css('background-image').indexOf('medium/') > -1) {
                size = 'medium';
            } else if ($(targetElement).css('background-image').indexOf('large/') > -1) {
                size = 'large';
            }

            $(targetElement).css('background-image', 'url(\'http://wow.zamimg.com/images/wow/icons/'+size+'/'+icon+'.jpg\')');
            $(targetElement).removeAttr('data-update-icon');
        });
    },

    UpdateRealmStatus: function(id)
    {
        var $this = $('#realm-status-' + id);
        var $realm = id;
        
        if (typeof $this == 'undefined' || $this.length == 0) {
            //next in queue
            WarcryQueue('onload').goNext();
            return;
        }

        $.get($BaseURL + '/ajax/serverStatus', { 
            id: $realm,
        },
        function(data) {
            if (data == '1') {
                $this.addClass('online');
            } else {
                $this.addClass('offline');
            }
            //next in queue
            WarcryQueue('onload').goNext();
        });	
    },

    UpdateLogonStatus: function()
    {
        if (typeof $('#logon-status2') == 'undefined' || $('#logon-status2').length == 0) {
            //next in queue
            WarcryQueue('onload').goNext();
            return;
        }

        $.get($BaseURL + '/ajax/logonStatus', function(data) {
            if (data == '1') {
                $('#logon-status2').addClass('online');
                $('#logon-status2').html('Online');
            } else {
                $('#logon-status2').addClass('offline');
                $('#logon-status2').html('Offline');
            }
            //next in queue
            WarcryQueue('onload').goNext();
        });	
    },

    ServerTimeCloack: function() {
        var myClock = document.getElementById('server-time-cloack');
        
        if (typeof myClock == 'undefined' || myClock == null) {
            return;
        }

        var currentTime = new Date(calcTime($TIMEZONE, $TIMEZONEOFFSET));
        
        var h = currentTime.getHours();
        var m = currentTime.getMinutes();
        var s = currentTime.getSeconds();
        
        setTimeout(function() { UI.ServerTimeCloack(); }, 1000);

        if (h < 10) {
            h = "0" + h;
        }
        if (m < 10) {
            m = "0" + m;
        }
        if (s < 10) {
            s = "0" + s;
        }
        
        myClock.textContent = h + ":" + m + ":" + s;
        myClock.innerText = h + ":" + m + ":" + s;
    }
};

function css_browser_selector(u) {
	var ua=u.toLowerCase();
	var is=function(t) {
		return ua.indexOf(t)>-1
	};
	
	var g='gecko',w='webkit',s='safari',o='opera',m='mobile';
	var h=document.documentElement;
	var b=[(!(/opera|webtv/i.test(ua))&&/msie\s(\d)/.test(ua))?('ie ie'+RegExp.$1):is('firefox/2')?g+' ff2':is('firefox/3.5')?g+' ff3 ff3_5':is('firefox/3.6')?g+' ff3 ff3_6':is('firefox/3')?g+' ff3':is('gecko/')?g:is('opera')?o+(/version\/(\d+)/.test(ua)?' '+o+RegExp.$1:(/opera(\s|\/)(\d+)/.test(ua)?' '+o+RegExp.$2:'')):is('konqueror')?'konqueror':is('blackberry')?m+' blackberry':is('android')?m+' android':is('chrome')?w+' chrome':is('iron')?w+' iron':is('applewebkit/')?w+' '+s+(/version\/(\d+)/.test(ua)?' '+s+RegExp.$1:''):is('mozilla/')?g:'',is('j2me')?m+' j2me':is('iphone')?m+' iphone':is('ipod')?m+' ipod':is('ipad')?m+' ipad':is('mac')?'mac':is('darwin')?'mac':is('webtv')?'webtv':is('win')?'win'+(is('windows nt 6.0')?' vista':''):is('freebsd')?'freebsd':(is('x11')||is('linux'))?'linux':'','js'];
	
	c = b.join(' ');
	h.className += ' '+c;
	
	return c;
}

$(function() {
	css_browser_selector(navigator.userAgent);
});

function convertDateToUTC(date) { 
    return new Date(date.getUTCFullYear(), date.getUTCMonth(), date.getUTCDate(), date.getUTCHours(), date.getUTCMinutes(), date.getUTCSeconds()); 
}

// function to calculate local time
// in a different city
// given the city's UTC offset
function calcTime(city, offset) {
    // create Date object for current location
    var d = new Date();

    // convert to msec
    // add local time zone offset
    // get UTC time in msec
    var utc = d.getTime() + (d.getTimezoneOffset() * 60000);

    // create new Date object for different city
    // using supplied offset
    var nd = new Date(utc + (3600000*parseInt(offset)));

    // return time as a string
    return nd.toString();
}

//create queue
var warcryQueues = [];
warcryQueues['internal'] = [];

var WarcryQueue = function(queueName) 
{
    var add = function(fnc) {
		if (typeof queueName != 'undefined') {
			if (typeof warcryQueues[queueName] == 'undefined') {
				warcryQueues[queueName] = []
			}
			warcryQueues[queueName].push(fnc);
		} else {
        	warcryQueues['internal'].push(fnc);
		}
    };
		
    var goNext = function() {
		if (typeof queueName != 'undefined') {
			//check if we have some functions in the queue
			if ($(warcryQueues[queueName]).size() < 1) {
				return false;
			}
			
			var fnc = warcryQueues[queueName].shift();
			if (typeof fnc == 'function') {
        		fnc();
			} else {
				console.log('WarcryQueue: There are no other functions in the queue "'+queueName+'".');
			}
		} else {
 			//check if we have some functions in the queue
			if ($(warcryQueues['internal']).size() < 1) {
				return false;
			}
			
       		var fnc = warcryQueues['internal'].shift();
			if (typeof fnc == 'function') {
        		fnc();
			} else {
				console.log('WarcryQueue: There are no other functions in the queue.');
			}
		}
    };
		
	var clear = function() {
		if (typeof queueName != 'undefined') {
			warcryQueues[queueName] = []
		} else {
			warcryQueue['internal'] = []
		}
	};
		
	var size = function() {
		if (typeof queueName != 'undefined') {
			return $(warcryQueues[queueName]).size();
		} else {
			return $(warcryQueues['internal']).size();
		}
	};
			
    return {
        add: add,
        goNext: goNext,
		clear: clear,
		size: size,
    };
};