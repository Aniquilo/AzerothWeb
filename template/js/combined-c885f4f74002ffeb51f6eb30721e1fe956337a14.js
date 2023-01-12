/*! jQuery UI - v1.8.19 - 2012-04-16
* https://github.com/jquery/jquery-ui
* Includes: jquery.ui.core.js
* Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function(a,b){function c(b,c){var e=b.nodeName.toLowerCase();if("area"===e){var f=b.parentNode,g=f.name,h;return!b.href||!g||f.nodeName.toLowerCase()!=="map"?!1:(h=a("img[usemap=#"+g+"]")[0],!!h&&d(h))}return(/input|select|textarea|button|object/.test(e)?!b.disabled:"a"==e?b.href||c:c)&&d(b)}function d(b){return!a(b).parents().andSelf().filter(function(){return a.curCSS(this,"visibility")==="hidden"||a.expr.filters.hidden(this)}).length}a.ui=a.ui||{};if(a.ui.version)return;a.extend(a.ui,{version:"1.8.19",keyCode:{ALT:18,BACKSPACE:8,CAPS_LOCK:20,COMMA:188,COMMAND:91,COMMAND_LEFT:91,COMMAND_RIGHT:93,CONTROL:17,DELETE:46,DOWN:40,END:35,ENTER:13,ESCAPE:27,HOME:36,INSERT:45,LEFT:37,MENU:93,NUMPAD_ADD:107,NUMPAD_DECIMAL:110,NUMPAD_DIVIDE:111,NUMPAD_ENTER:108,NUMPAD_MULTIPLY:106,NUMPAD_SUBTRACT:109,PAGE_DOWN:34,PAGE_UP:33,PERIOD:190,RIGHT:39,SHIFT:16,SPACE:32,TAB:9,UP:38,WINDOWS:91}}),a.fn.extend({propAttr:a.fn.prop||a.fn.attr,_focus:a.fn.focus,focus:function(b,c){return typeof b=="number"?this.each(function(){var d=this;setTimeout(function(){a(d).focus(),c&&c.call(d)},b)}):this._focus.apply(this,arguments)},scrollParent:function(){var b;return a.browser.msie&&/(static|relative)/.test(this.css("position"))||/absolute/.test(this.css("position"))?b=this.parents().filter(function(){return/(relative|absolute|fixed)/.test(a.curCSS(this,"position",1))&&/(auto|scroll)/.test(a.curCSS(this,"overflow",1)+a.curCSS(this,"overflow-y",1)+a.curCSS(this,"overflow-x",1))}).eq(0):b=this.parents().filter(function(){return/(auto|scroll)/.test(a.curCSS(this,"overflow",1)+a.curCSS(this,"overflow-y",1)+a.curCSS(this,"overflow-x",1))}).eq(0),/fixed/.test(this.css("position"))||!b.length?a(document):b},zIndex:function(c){if(c!==b)return this.css("zIndex",c);if(this.length){var d=a(this[0]),e,f;while(d.length&&d[0]!==document){e=d.css("position");if(e==="absolute"||e==="relative"||e==="fixed"){f=parseInt(d.css("zIndex"),10);if(!isNaN(f)&&f!==0)return f}d=d.parent()}}return 0},disableSelection:function(){return this.bind((a.support.selectstart?"selectstart":"mousedown")+".ui-disableSelection",function(a){a.preventDefault()})},enableSelection:function(){return this.unbind(".ui-disableSelection")}}),a.each(["Width","Height"],function(c,d){function h(b,c,d,f){return a.each(e,function(){c-=parseFloat(a.curCSS(b,"padding"+this,!0))||0,d&&(c-=parseFloat(a.curCSS(b,"border"+this+"Width",!0))||0),f&&(c-=parseFloat(a.curCSS(b,"margin"+this,!0))||0)}),c}var e=d==="Width"?["Left","Right"]:["Top","Bottom"],f=d.toLowerCase(),g={innerWidth:a.fn.innerWidth,innerHeight:a.fn.innerHeight,outerWidth:a.fn.outerWidth,outerHeight:a.fn.outerHeight};a.fn["inner"+d]=function(c){return c===b?g["inner"+d].call(this):this.each(function(){a(this).css(f,h(this,c)+"px")})},a.fn["outer"+d]=function(b,c){return typeof b!="number"?g["outer"+d].call(this,b):this.each(function(){a(this).css(f,h(this,b,!0,c)+"px")})}}),a.extend(a.expr[":"],{data:function(b,c,d){return!!a.data(b,d[3])},focusable:function(b){return c(b,!isNaN(a.attr(b,"tabindex")))},tabbable:function(b){var d=a.attr(b,"tabindex"),e=isNaN(d);return(e||d>=0)&&c(b,!e)}}),a(function(){var b=document.body,c=b.appendChild(c=document.createElement("div"));c.offsetHeight,a.extend(c.style,{minHeight:"100px",height:"auto",padding:0,borderWidth:0}),a.support.minHeight=c.offsetHeight===100,a.support.selectstart="onselectstart"in c,b.removeChild(c).style.display="none"}),a.extend(a.ui,{plugin:{add:function(b,c,d){var e=a.ui[b].prototype;for(var f in d)e.plugins[f]=e.plugins[f]||[],e.plugins[f].push([c,d[f]])},call:function(a,b,c){var d=a.plugins[b];if(!d||!a.element[0].parentNode)return;for(var e=0;e<d.length;e++)a.options[d[e][0]]&&d[e][1].apply(a.element,c)}},contains:function(a,b){return document.compareDocumentPosition?a.compareDocumentPosition(b)&16:a!==b&&a.contains(b)},hasScroll:function(b,c){if(a(b).css("overflow")==="hidden")return!1;var d=c&&c==="left"?"scrollLeft":"scrollTop",e=!1;return b[d]>0?!0:(b[d]=1,e=b[d]>0,b[d]=0,e)},isOverAxis:function(a,b,c){return a>b&&a<b+c},isOver:function(b,c,d,e,f,g){return a.ui.isOverAxis(b,d,f)&&a.ui.isOverAxis(c,e,g)}})})(jQuery);/*! jQuery UI - v1.8.19 - 2012-04-16
* https://github.com/jquery/jquery-ui
* Includes: jquery.ui.widget.js
* Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function(a,b){if(a.cleanData){var c=a.cleanData;a.cleanData=function(b){for(var d=0,e;(e=b[d])!=null;d++)try{a(e).triggerHandler("remove")}catch(f){}c(b)}}else{var d=a.fn.remove;a.fn.remove=function(b,c){return this.each(function(){return c||(!b||a.filter(b,[this]).length)&&a("*",this).add([this]).each(function(){try{a(this).triggerHandler("remove")}catch(b){}}),d.call(a(this),b,c)})}}a.widget=function(b,c,d){var e=b.split(".")[0],f;b=b.split(".")[1],f=e+"-"+b,d||(d=c,c=a.Widget),a.expr[":"][f]=function(c){return!!a.data(c,b)},a[e]=a[e]||{},a[e][b]=function(a,b){arguments.length&&this._createWidget(a,b)};var g=new c;g.options=a.extend(!0,{},g.options),a[e][b].prototype=a.extend(!0,g,{namespace:e,widgetName:b,widgetEventPrefix:a[e][b].prototype.widgetEventPrefix||b,widgetBaseClass:f},d),a.widget.bridge(b,a[e][b])},a.widget.bridge=function(c,d){a.fn[c]=function(e){var f=typeof e=="string",g=Array.prototype.slice.call(arguments,1),h=this;return e=!f&&g.length?a.extend.apply(null,[!0,e].concat(g)):e,f&&e.charAt(0)==="_"?h:(f?this.each(function(){var d=a.data(this,c),f=d&&a.isFunction(d[e])?d[e].apply(d,g):d;if(f!==d&&f!==b)return h=f,!1}):this.each(function(){var b=a.data(this,c);b?b.option(e||{})._init():a.data(this,c,new d(e,this))}),h)}},a.Widget=function(a,b){arguments.length&&this._createWidget(a,b)},a.Widget.prototype={widgetName:"widget",widgetEventPrefix:"",options:{disabled:!1},_createWidget:function(b,c){a.data(c,this.widgetName,this),this.element=a(c),this.options=a.extend(!0,{},this.options,this._getCreateOptions(),b);var d=this;this.element.bind("remove."+this.widgetName,function(){d.destroy()}),this._create(),this._trigger("create"),this._init()},_getCreateOptions:function(){return a.metadata&&a.metadata.get(this.element[0])[this.widgetName]},_create:function(){},_init:function(){},destroy:function(){this.element.unbind("."+this.widgetName).removeData(this.widgetName),this.widget().unbind("."+this.widgetName).removeAttr("aria-disabled").removeClass(this.widgetBaseClass+"-disabled "+"ui-state-disabled")},widget:function(){return this.element},option:function(c,d){var e=c;if(arguments.length===0)return a.extend({},this.options);if(typeof c=="string"){if(d===b)return this.options[c];e={},e[c]=d}return this._setOptions(e),this},_setOptions:function(b){var c=this;return a.each(b,function(a,b){c._setOption(a,b)}),this},_setOption:function(a,b){return this.options[a]=b,a==="disabled"&&this.widget()[b?"addClass":"removeClass"](this.widgetBaseClass+"-disabled"+" "+"ui-state-disabled").attr("aria-disabled",b),this},enable:function(){return this._setOption("disabled",!1)},disable:function(){return this._setOption("disabled",!0)},_trigger:function(b,c,d){var e,f,g=this.options[b];d=d||{},c=a.Event(c),c.type=(b===this.widgetEventPrefix?b:this.widgetEventPrefix+b).toLowerCase(),c.target=this.element[0],f=c.originalEvent;if(f)for(e in f)e in c||(c[e]=f[e]);return this.element.trigger(c,d),!(a.isFunction(g)&&g.call(this.element[0],c,d)===!1||c.isDefaultPrevented())}}})(jQuery);/*! jQuery UI - v1.8.19 - 2012-04-16
* https://github.com/jquery/jquery-ui
* Includes: jquery.ui.mouse.js
* Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function(a,b){var c=!1;a(document).mouseup(function(a){c=!1}),a.widget("ui.mouse",{options:{cancel:":input,option",distance:1,delay:0},_mouseInit:function(){var b=this;this.element.bind("mousedown."+this.widgetName,function(a){return b._mouseDown(a)}).bind("click."+this.widgetName,function(c){if(!0===a.data(c.target,b.widgetName+".preventClickEvent"))return a.removeData(c.target,b.widgetName+".preventClickEvent"),c.stopImmediatePropagation(),!1}),this.started=!1},_mouseDestroy:function(){this.element.unbind("."+this.widgetName),a(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate)},_mouseDown:function(b){if(c)return;this._mouseStarted&&this._mouseUp(b),this._mouseDownEvent=b;var d=this,e=b.which==1,f=typeof this.options.cancel=="string"&&b.target.nodeName?a(b.target).closest(this.options.cancel).length:!1;if(!e||f||!this._mouseCapture(b))return!0;this.mouseDelayMet=!this.options.delay,this.mouseDelayMet||(this._mouseDelayTimer=setTimeout(function(){d.mouseDelayMet=!0},this.options.delay));if(this._mouseDistanceMet(b)&&this._mouseDelayMet(b)){this._mouseStarted=this._mouseStart(b)!==!1;if(!this._mouseStarted)return b.preventDefault(),!0}return!0===a.data(b.target,this.widgetName+".preventClickEvent")&&a.removeData(b.target,this.widgetName+".preventClickEvent"),this._mouseMoveDelegate=function(a){return d._mouseMove(a)},this._mouseUpDelegate=function(a){return d._mouseUp(a)},a(document).bind("mousemove."+this.widgetName,this._mouseMoveDelegate).bind("mouseup."+this.widgetName,this._mouseUpDelegate),b.preventDefault(),c=!0,!0},_mouseMove:function(b){return!a.browser.msie||document.documentMode>=9||!!b.button?this._mouseStarted?(this._mouseDrag(b),b.preventDefault()):(this._mouseDistanceMet(b)&&this._mouseDelayMet(b)&&(this._mouseStarted=this._mouseStart(this._mouseDownEvent,b)!==!1,this._mouseStarted?this._mouseDrag(b):this._mouseUp(b)),!this._mouseStarted):this._mouseUp(b)},_mouseUp:function(b){return a(document).unbind("mousemove."+this.widgetName,this._mouseMoveDelegate).unbind("mouseup."+this.widgetName,this._mouseUpDelegate),this._mouseStarted&&(this._mouseStarted=!1,b.target==this._mouseDownEvent.target&&a.data(b.target,this.widgetName+".preventClickEvent",!0),this._mouseStop(b)),!1},_mouseDistanceMet:function(a){return Math.max(Math.abs(this._mouseDownEvent.pageX-a.pageX),Math.abs(this._mouseDownEvent.pageY-a.pageY))>=this.options.distance},_mouseDelayMet:function(a){return this.mouseDelayMet},_mouseStart:function(a){},_mouseDrag:function(a){},_mouseStop:function(a){},_mouseCapture:function(a){return!0}})})(jQuery);/*! jQuery UI - v1.8.19 - 2012-04-16
* https://github.com/jquery/jquery-ui
* Includes: jquery.ui.draggable.js
* Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function(a,b){a.widget("ui.draggable",a.ui.mouse,{widgetEventPrefix:"drag",options:{addClasses:!0,appendTo:"parent",axis:!1,connectToSortable:!1,containment:!1,cursor:"auto",cursorAt:!1,grid:!1,handle:!1,helper:"original",iframeFix:!1,opacity:!1,refreshPositions:!1,revert:!1,revertDuration:500,scope:"default",scroll:!0,scrollSensitivity:20,scrollSpeed:20,snap:!1,snapMode:"both",snapTolerance:20,stack:!1,zIndex:!1},_create:function(){this.options.helper=="original"&&!/^(?:r|a|f)/.test(this.element.css("position"))&&(this.element[0].style.position="relative"),this.options.addClasses&&this.element.addClass("ui-draggable"),this.options.disabled&&this.element.addClass("ui-draggable-disabled"),this._mouseInit()},destroy:function(){if(!this.element.data("draggable"))return;return this.element.removeData("draggable").unbind(".draggable").removeClass("ui-draggable ui-draggable-dragging ui-draggable-disabled"),this._mouseDestroy(),this},_mouseCapture:function(b){var c=this.options;return this.helper||c.disabled||a(b.target).is(".ui-resizable-handle")?!1:(this.handle=this._getHandle(b),this.handle?(c.iframeFix&&a(c.iframeFix===!0?"iframe":c.iframeFix).each(function(){a('<div class="ui-draggable-iframeFix" style="background: #fff;"></div>').css({width:this.offsetWidth+"px",height:this.offsetHeight+"px",position:"absolute",opacity:"0.001",zIndex:1e3}).css(a(this).offset()).appendTo("body")}),!0):!1)},_mouseStart:function(b){var c=this.options;return this.helper=this._createHelper(b),this._cacheHelperProportions(),a.ui.ddmanager&&(a.ui.ddmanager.current=this),this._cacheMargins(),this.cssPosition=this.helper.css("position"),this.scrollParent=this.helper.scrollParent(),this.offset=this.positionAbs=this.element.offset(),this.offset={top:this.offset.top-this.margins.top,left:this.offset.left-this.margins.left},a.extend(this.offset,{click:{left:b.pageX-this.offset.left,top:b.pageY-this.offset.top},parent:this._getParentOffset(),relative:this._getRelativeOffset()}),this.originalPosition=this.position=this._generatePosition(b),this.originalPageX=b.pageX,this.originalPageY=b.pageY,c.cursorAt&&this._adjustOffsetFromHelper(c.cursorAt),c.containment&&this._setContainment(),this._trigger("start",b)===!1?(this._clear(),!1):(this._cacheHelperProportions(),a.ui.ddmanager&&!c.dropBehaviour&&a.ui.ddmanager.prepareOffsets(this,b),this.helper.addClass("ui-draggable-dragging"),this._mouseDrag(b,!0),a.ui.ddmanager&&a.ui.ddmanager.dragStart(this,b),!0)},_mouseDrag:function(b,c){this.position=this._generatePosition(b),this.positionAbs=this._convertPositionTo("absolute");if(!c){var d=this._uiHash();if(this._trigger("drag",b,d)===!1)return this._mouseUp({}),!1;this.position=d.position}if(!this.options.axis||this.options.axis!="y")this.helper[0].style.left=this.position.left+"px";if(!this.options.axis||this.options.axis!="x")this.helper[0].style.top=this.position.top+"px";return a.ui.ddmanager&&a.ui.ddmanager.drag(this,b),!1},_mouseStop:function(b){var c=!1;a.ui.ddmanager&&!this.options.dropBehaviour&&(c=a.ui.ddmanager.drop(this,b)),this.dropped&&(c=this.dropped,this.dropped=!1);if((!this.element[0]||!this.element[0].parentNode)&&this.options.helper=="original")return!1;if(this.options.revert=="invalid"&&!c||this.options.revert=="valid"&&c||this.options.revert===!0||a.isFunction(this.options.revert)&&this.options.revert.call(this.element,c)){var d=this;a(this.helper).animate(this.originalPosition,parseInt(this.options.revertDuration,10),function(){d._trigger("stop",b)!==!1&&d._clear()})}else this._trigger("stop",b)!==!1&&this._clear();return!1},_mouseUp:function(b){return this.options.iframeFix===!0&&a("div.ui-draggable-iframeFix").each(function(){this.parentNode.removeChild(this)}),a.ui.ddmanager&&a.ui.ddmanager.dragStop(this,b),a.ui.mouse.prototype._mouseUp.call(this,b)},cancel:function(){return this.helper.is(".ui-draggable-dragging")?this._mouseUp({}):this._clear(),this},_getHandle:function(b){var c=!this.options.handle||!a(this.options.handle,this.element).length?!0:!1;return a(this.options.handle,this.element).find("*").andSelf().each(function(){this==b.target&&(c=!0)}),c},_createHelper:function(b){var c=this.options,d=a.isFunction(c.helper)?a(c.helper.apply(this.element[0],[b])):c.helper=="clone"?this.element.clone().removeAttr("id"):this.element;return d.parents("body").length||d.appendTo(c.appendTo=="parent"?this.element[0].parentNode:c.appendTo),d[0]!=this.element[0]&&!/(fixed|absolute)/.test(d.css("position"))&&d.css("position","absolute"),d},_adjustOffsetFromHelper:function(b){typeof b=="string"&&(b=b.split(" ")),a.isArray(b)&&(b={left:+b[0],top:+b[1]||0}),"left"in b&&(this.offset.click.left=b.left+this.margins.left),"right"in b&&(this.offset.click.left=this.helperProportions.width-b.right+this.margins.left),"top"in b&&(this.offset.click.top=b.top+this.margins.top),"bottom"in b&&(this.offset.click.top=this.helperProportions.height-b.bottom+this.margins.top)},_getParentOffset:function(){this.offsetParent=this.helper.offsetParent();var b=this.offsetParent.offset();this.cssPosition=="absolute"&&this.scrollParent[0]!=document&&a.ui.contains(this.scrollParent[0],this.offsetParent[0])&&(b.left+=this.scrollParent.scrollLeft(),b.top+=this.scrollParent.scrollTop());if(this.offsetParent[0]==document.body||this.offsetParent[0].tagName&&this.offsetParent[0].tagName.toLowerCase()=="html"&&a.browser.msie)b={top:0,left:0};return{top:b.top+(parseInt(this.offsetParent.css("borderTopWidth"),10)||0),left:b.left+(parseInt(this.offsetParent.css("borderLeftWidth"),10)||0)}},_getRelativeOffset:function(){if(this.cssPosition=="relative"){var a=this.element.position();return{top:a.top-(parseInt(this.helper.css("top"),10)||0)+this.scrollParent.scrollTop(),left:a.left-(parseInt(this.helper.css("left"),10)||0)+this.scrollParent.scrollLeft()}}return{top:0,left:0}},_cacheMargins:function(){this.margins={left:parseInt(this.element.css("marginLeft"),10)||0,top:parseInt(this.element.css("marginTop"),10)||0,right:parseInt(this.element.css("marginRight"),10)||0,bottom:parseInt(this.element.css("marginBottom"),10)||0}},_cacheHelperProportions:function(){this.helperProportions={width:this.helper.outerWidth(),height:this.helper.outerHeight()}},_setContainment:function(){var b=this.options;b.containment=="parent"&&(b.containment=this.helper[0].parentNode);if(b.containment=="document"||b.containment=="window")this.containment=[b.containment=="document"?0:a(window).scrollLeft()-this.offset.relative.left-this.offset.parent.left,b.containment=="document"?0:a(window).scrollTop()-this.offset.relative.top-this.offset.parent.top,(b.containment=="document"?0:a(window).scrollLeft())+a(b.containment=="document"?document:window).width()-this.helperProportions.width-this.margins.left,(b.containment=="document"?0:a(window).scrollTop())+(a(b.containment=="document"?document:window).height()||document.body.parentNode.scrollHeight)-this.helperProportions.height-this.margins.top];if(!/^(document|window|parent)$/.test(b.containment)&&b.containment.constructor!=Array){var c=a(b.containment),d=c[0];if(!d)return;var e=c.offset(),f=a(d).css("overflow")!="hidden";this.containment=[(parseInt(a(d).css("borderLeftWidth"),10)||0)+(parseInt(a(d).css("paddingLeft"),10)||0),(parseInt(a(d).css("borderTopWidth"),10)||0)+(parseInt(a(d).css("paddingTop"),10)||0),(f?Math.max(d.scrollWidth,d.offsetWidth):d.offsetWidth)-(parseInt(a(d).css("borderLeftWidth"),10)||0)-(parseInt(a(d).css("paddingRight"),10)||0)-this.helperProportions.width-this.margins.left-this.margins.right,(f?Math.max(d.scrollHeight,d.offsetHeight):d.offsetHeight)-(parseInt(a(d).css("borderTopWidth"),10)||0)-(parseInt(a(d).css("paddingBottom"),10)||0)-this.helperProportions.height-this.margins.top-this.margins.bottom],this.relative_container=c}else b.containment.constructor==Array&&(this.containment=b.containment)},_convertPositionTo:function(b,c){c||(c=this.position);var d=b=="absolute"?1:-1,e=this.options,f=this.cssPosition=="absolute"&&(this.scrollParent[0]==document||!a.ui.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent,g=/(html|body)/i.test(f[0].tagName);return{top:c.top+this.offset.relative.top*d+this.offset.parent.top*d-(a.browser.safari&&a.browser.version<526&&this.cssPosition=="fixed"?0:(this.cssPosition=="fixed"?-this.scrollParent.scrollTop():g?0:f.scrollTop())*d),left:c.left+this.offset.relative.left*d+this.offset.parent.left*d-(a.browser.safari&&a.browser.version<526&&this.cssPosition=="fixed"?0:(this.cssPosition=="fixed"?-this.scrollParent.scrollLeft():g?0:f.scrollLeft())*d)}},_generatePosition:function(b){var c=this.options,d=this.cssPosition=="absolute"&&(this.scrollParent[0]==document||!a.ui.contains(this.scrollParent[0],this.offsetParent[0]))?this.offsetParent:this.scrollParent,e=/(html|body)/i.test(d[0].tagName),f=b.pageX,g=b.pageY;if(this.originalPosition){var h;if(this.containment){if(this.relative_container){var i=this.relative_container.offset();h=[this.containment[0]+i.left,this.containment[1]+i.top,this.containment[2]+i.left,this.containment[3]+i.top]}else h=this.containment;b.pageX-this.offset.click.left<h[0]&&(f=h[0]+this.offset.click.left),b.pageY-this.offset.click.top<h[1]&&(g=h[1]+this.offset.click.top),b.pageX-this.offset.click.left>h[2]&&(f=h[2]+this.offset.click.left),b.pageY-this.offset.click.top>h[3]&&(g=h[3]+this.offset.click.top)}if(c.grid){var j=c.grid[1]?this.originalPageY+Math.round((g-this.originalPageY)/c.grid[1])*c.grid[1]:this.originalPageY;g=h?j-this.offset.click.top<h[1]||j-this.offset.click.top>h[3]?j-this.offset.click.top<h[1]?j+c.grid[1]:j-c.grid[1]:j:j;var k=c.grid[0]?this.originalPageX+Math.round((f-this.originalPageX)/c.grid[0])*c.grid[0]:this.originalPageX;f=h?k-this.offset.click.left<h[0]||k-this.offset.click.left>h[2]?k-this.offset.click.left<h[0]?k+c.grid[0]:k-c.grid[0]:k:k}}return{top:g-this.offset.click.top-this.offset.relative.top-this.offset.parent.top+(a.browser.safari&&a.browser.version<526&&this.cssPosition=="fixed"?0:this.cssPosition=="fixed"?-this.scrollParent.scrollTop():e?0:d.scrollTop()),left:f-this.offset.click.left-this.offset.relative.left-this.offset.parent.left+(a.browser.safari&&a.browser.version<526&&this.cssPosition=="fixed"?0:this.cssPosition=="fixed"?-this.scrollParent.scrollLeft():e?0:d.scrollLeft())}},_clear:function(){this.helper.removeClass("ui-draggable-dragging"),this.helper[0]!=this.element[0]&&!this.cancelHelperRemoval&&this.helper.remove(),this.helper=null,this.cancelHelperRemoval=!1},_trigger:function(b,c,d){return d=d||this._uiHash(),a.ui.plugin.call(this,b,[c,d]),b=="drag"&&(this.positionAbs=this._convertPositionTo("absolute")),a.Widget.prototype._trigger.call(this,b,c,d)},plugins:{},_uiHash:function(a){return{helper:this.helper,position:this.position,originalPosition:this.originalPosition,offset:this.positionAbs}}}),a.extend(a.ui.draggable,{version:"1.8.19"}),a.ui.plugin.add("draggable","connectToSortable",{start:function(b,c){var d=a(this).data("draggable"),e=d.options,f=a.extend({},c,{item:d.element});d.sortables=[],a(e.connectToSortable).each(function(){var c=a.data(this,"sortable");c&&!c.options.disabled&&(d.sortables.push({instance:c,shouldRevert:c.options.revert}),c.refreshPositions(),c._trigger("activate",b,f))})},stop:function(b,c){var d=a(this).data("draggable"),e=a.extend({},c,{item:d.element});a.each(d.sortables,function(){this.instance.isOver?(this.instance.isOver=0,d.cancelHelperRemoval=!0,this.instance.cancelHelperRemoval=!1,this.shouldRevert&&(this.instance.options.revert=!0),this.instance._mouseStop(b),this.instance.options.helper=this.instance.options._helper,d.options.helper=="original"&&this.instance.currentItem.css({top:"auto",left:"auto"})):(this.instance.cancelHelperRemoval=!1,this.instance._trigger("deactivate",b,e))})},drag:function(b,c){var d=a(this).data("draggable"),e=this,f=function(b){var c=this.offset.click.top,d=this.offset.click.left,e=this.positionAbs.top,f=this.positionAbs.left,g=b.height,h=b.width,i=b.top,j=b.left;return a.ui.isOver(e+c,f+d,i,j,g,h)};a.each(d.sortables,function(f){this.instance.positionAbs=d.positionAbs,this.instance.helperProportions=d.helperProportions,this.instance.offset.click=d.offset.click,this.instance._intersectsWith(this.instance.containerCache)?(this.instance.isOver||(this.instance.isOver=1,this.instance.currentItem=a(e).clone().removeAttr("id").appendTo(this.instance.element).data("sortable-item",!0),this.instance.options._helper=this.instance.options.helper,this.instance.options.helper=function(){return c.helper[0]},b.target=this.instance.currentItem[0],this.instance._mouseCapture(b,!0),this.instance._mouseStart(b,!0,!0),this.instance.offset.click.top=d.offset.click.top,this.instance.offset.click.left=d.offset.click.left,this.instance.offset.parent.left-=d.offset.parent.left-this.instance.offset.parent.left,this.instance.offset.parent.top-=d.offset.parent.top-this.instance.offset.parent.top,d._trigger("toSortable",b),d.dropped=this.instance.element,d.currentItem=d.element,this.instance.fromOutside=d),this.instance.currentItem&&this.instance._mouseDrag(b)):this.instance.isOver&&(this.instance.isOver=0,this.instance.cancelHelperRemoval=!0,this.instance.options.revert=!1,this.instance._trigger("out",b,this.instance._uiHash(this.instance)),this.instance._mouseStop(b,!0),this.instance.options.helper=this.instance.options._helper,this.instance.currentItem.remove(),this.instance.placeholder&&this.instance.placeholder.remove(),d._trigger("fromSortable",b),d.dropped=!1)})}}),a.ui.plugin.add("draggable","cursor",{start:function(b,c){var d=a("body"),e=a(this).data("draggable").options;d.css("cursor")&&(e._cursor=d.css("cursor")),d.css("cursor",e.cursor)},stop:function(b,c){var d=a(this).data("draggable").options;d._cursor&&a("body").css("cursor",d._cursor)}}),a.ui.plugin.add("draggable","opacity",{start:function(b,c){var d=a(c.helper),e=a(this).data("draggable").options;d.css("opacity")&&(e._opacity=d.css("opacity")),d.css("opacity",e.opacity)},stop:function(b,c){var d=a(this).data("draggable").options;d._opacity&&a(c.helper).css("opacity",d._opacity)}}),a.ui.plugin.add("draggable","scroll",{start:function(b,c){var d=a(this).data("draggable");d.scrollParent[0]!=document&&d.scrollParent[0].tagName!="HTML"&&(d.overflowOffset=d.scrollParent.offset())},drag:function(b,c){var d=a(this).data("draggable"),e=d.options,f=!1;if(d.scrollParent[0]!=document&&d.scrollParent[0].tagName!="HTML"){if(!e.axis||e.axis!="x")d.overflowOffset.top+d.scrollParent[0].offsetHeight-b.pageY<e.scrollSensitivity?d.scrollParent[0].scrollTop=f=d.scrollParent[0].scrollTop+e.scrollSpeed:b.pageY-d.overflowOffset.top<e.scrollSensitivity&&(d.scrollParent[0].scrollTop=f=d.scrollParent[0].scrollTop-e.scrollSpeed);if(!e.axis||e.axis!="y")d.overflowOffset.left+d.scrollParent[0].offsetWidth-b.pageX<e.scrollSensitivity?d.scrollParent[0].scrollLeft=f=d.scrollParent[0].scrollLeft+e.scrollSpeed:b.pageX-d.overflowOffset.left<e.scrollSensitivity&&(d.scrollParent[0].scrollLeft=f=d.scrollParent[0].scrollLeft-e.scrollSpeed)}else{if(!e.axis||e.axis!="x")b.pageY-a(document).scrollTop()<e.scrollSensitivity?f=a(document).scrollTop(a(document).scrollTop()-e.scrollSpeed):a(window).height()-(b.pageY-a(document).scrollTop())<e.scrollSensitivity&&(f=a(document).scrollTop(a(document).scrollTop()+e.scrollSpeed));if(!e.axis||e.axis!="y")b.pageX-a(document).scrollLeft()<e.scrollSensitivity?f=a(document).scrollLeft(a(document).scrollLeft()-e.scrollSpeed):a(window).width()-(b.pageX-a(document).scrollLeft())<e.scrollSensitivity&&(f=a(document).scrollLeft(a(document).scrollLeft()+e.scrollSpeed))}f!==!1&&a.ui.ddmanager&&!e.dropBehaviour&&a.ui.ddmanager.prepareOffsets(d,b)}}),a.ui.plugin.add("draggable","snap",{start:function(b,c){var d=a(this).data("draggable"),e=d.options;d.snapElements=[],a(e.snap.constructor!=String?e.snap.items||":data(draggable)":e.snap).each(function(){var b=a(this),c=b.offset();this!=d.element[0]&&d.snapElements.push({item:this,width:b.outerWidth(),height:b.outerHeight(),top:c.top,left:c.left})})},drag:function(b,c){var d=a(this).data("draggable"),e=d.options,f=e.snapTolerance,g=c.offset.left,h=g+d.helperProportions.width,i=c.offset.top,j=i+d.helperProportions.height;for(var k=d.snapElements.length-1;k>=0;k--){var l=d.snapElements[k].left,m=l+d.snapElements[k].width,n=d.snapElements[k].top,o=n+d.snapElements[k].height;if(!(l-f<g&&g<m+f&&n-f<i&&i<o+f||l-f<g&&g<m+f&&n-f<j&&j<o+f||l-f<h&&h<m+f&&n-f<i&&i<o+f||l-f<h&&h<m+f&&n-f<j&&j<o+f)){d.snapElements[k].snapping&&d.options.snap.release&&d.options.snap.release.call(d.element,b,a.extend(d._uiHash(),{snapItem:d.snapElements[k].item})),d.snapElements[k].snapping=!1;continue}if(e.snapMode!="inner"){var p=Math.abs(n-j)<=f,q=Math.abs(o-i)<=f,r=Math.abs(l-h)<=f,s=Math.abs(m-g)<=f;p&&(c.position.top=d._convertPositionTo("relative",{top:n-d.helperProportions.height,left:0}).top-d.margins.top),q&&(c.position.top=d._convertPositionTo("relative",{top:o,left:0}).top-d.margins.top),r&&(c.position.left=d._convertPositionTo("relative",{top:0,left:l-d.helperProportions.width}).left-d.margins.left),s&&(c.position.left=d._convertPositionTo("relative",{top:0,left:m}).left-d.margins.left)}var t=p||q||r||s;if(e.snapMode!="outer"){var p=Math.abs(n-i)<=f,q=Math.abs(o-j)<=f,r=Math.abs(l-g)<=f,s=Math.abs(m-h)<=f;p&&(c.position.top=d._convertPositionTo("relative",{top:n,left:0}).top-d.margins.top),q&&(c.position.top=d._convertPositionTo("relative",{top:o-d.helperProportions.height,left:0}).top-d.margins.top),r&&(c.position.left=d._convertPositionTo("relative",{top:0,left:l}).left-d.margins.left),s&&(c.position.left=d._convertPositionTo("relative",{top:0,left:m-d.helperProportions.width}).left-d.margins.left)}!d.snapElements[k].snapping&&(p||q||r||s||t)&&d.options.snap.snap&&d.options.snap.snap.call(d.element,b,a.extend(d._uiHash(),{snapItem:d.snapElements[k].item})),d.snapElements[k].snapping=p||q||r||s||t}}}),a.ui.plugin.add("draggable","stack",{start:function(b,c){var d=a(this).data("draggable").options,e=a.makeArray(a(d.stack)).sort(function(b,c){return(parseInt(a(b).css("zIndex"),10)||0)-(parseInt(a(c).css("zIndex"),10)||0)});if(!e.length)return;var f=parseInt(e[0].style.zIndex)||0;a(e).each(function(a){this.style.zIndex=f+a}),this[0].style.zIndex=f+e.length}}),a.ui.plugin.add("draggable","zIndex",{start:function(b,c){var d=a(c.helper),e=a(this).data("draggable").options;d.css("zIndex")&&(e._zIndex=d.css("zIndex")),d.css("zIndex",e.zIndex)},stop:function(b,c){var d=a(this).data("draggable").options;d._zIndex&&a(c.helper).css("zIndex",d._zIndex)}})})(jQuery);/*! jQuery UI - v1.8.19 - 2012-04-16
* https://github.com/jquery/jquery-ui
* Includes: jquery.ui.accordion.js
* Copyright (c) 2012 AUTHORS.txt; Licensed MIT, GPL */
(function(a,b){a.widget("ui.accordion",{options:{active:0,animated:"slide",autoHeight:!0,clearStyle:!1,collapsible:!1,event:"click",fillSpace:!1,header:"> li > :first-child,> :not(li):even",icons:{header:"ui-icon-triangle-1-e",headerSelected:"ui-icon-triangle-1-s"},navigation:!1,navigationFilter:function(){return this.href.toLowerCase()===location.href.toLowerCase()}},_create:function(){var b=this,c=b.options;b.running=0,b.element.addClass("ui-accordion ui-widget ui-helper-reset").children("li").addClass("ui-accordion-li-fix"),b.headers=b.element.find(c.header).addClass("ui-accordion-header ui-helper-reset ui-state-default ui-corner-all").bind("mouseenter.accordion",function(){if(c.disabled)return;a(this).addClass("ui-state-hover")}).bind("mouseleave.accordion",function(){if(c.disabled)return;a(this).removeClass("ui-state-hover")}).bind("focus.accordion",function(){if(c.disabled)return;a(this).addClass("ui-state-focus")}).bind("blur.accordion",function(){if(c.disabled)return;a(this).removeClass("ui-state-focus")}),b.headers.next().addClass("ui-accordion-content ui-helper-reset ui-widget-content ui-corner-bottom");if(c.navigation){var d=b.element.find("a").filter(c.navigationFilter).eq(0);if(d.length){var e=d.closest(".ui-accordion-header");e.length?b.active=e:b.active=d.closest(".ui-accordion-content").prev()}}b.active=b._findActive(b.active||c.active).addClass("ui-state-default ui-state-active").toggleClass("ui-corner-all").toggleClass("ui-corner-top"),b.active.next().addClass("ui-accordion-content-active"),b._createIcons(),b.resize(),b.element.attr("role","tablist"),b.headers.attr("role","tab").bind("keydown.accordion",function(a){return b._keydown(a)}).next().attr("role","tabpanel"),b.headers.not(b.active||"").attr({"aria-expanded":"false","aria-selected":"false",tabIndex:-1}).next().hide(),b.active.length?b.active.attr({"aria-expanded":"true","aria-selected":"true",tabIndex:0}):b.headers.eq(0).attr("tabIndex",0),a.browser.safari||b.headers.find("a").attr("tabIndex",-1),c.event&&b.headers.bind(c.event.split(" ").join(".accordion ")+".accordion",function(a){b._clickHandler.call(b,a,this),a.preventDefault()})},_createIcons:function(){var b=this.options;b.icons&&(a("<span></span>").addClass("ui-icon "+b.icons.header).prependTo(this.headers),this.active.children(".ui-icon").toggleClass(b.icons.header).toggleClass(b.icons.headerSelected),this.element.addClass("ui-accordion-icons"))},_destroyIcons:function(){this.headers.children(".ui-icon").remove(),this.element.removeClass("ui-accordion-icons")},destroy:function(){var b=this.options;this.element.removeClass("ui-accordion ui-widget ui-helper-reset").removeAttr("role"),this.headers.unbind(".accordion").removeClass("ui-accordion-header ui-accordion-disabled ui-helper-reset ui-state-default ui-corner-all ui-state-active ui-state-disabled ui-corner-top").removeAttr("role").removeAttr("aria-expanded").removeAttr("aria-selected").removeAttr("tabIndex"),this.headers.find("a").removeAttr("tabIndex"),this._destroyIcons();var c=this.headers.next().css("display","").removeAttr("role").removeClass("ui-helper-reset ui-widget-content ui-corner-bottom ui-accordion-content ui-accordion-content-active ui-accordion-disabled ui-state-disabled");return(b.autoHeight||b.fillHeight)&&c.css("height",""),a.Widget.prototype.destroy.call(this)},_setOption:function(b,c){a.Widget.prototype._setOption.apply(this,arguments),b=="active"&&this.activate(c),b=="icons"&&(this._destroyIcons(),c&&this._createIcons()),b=="disabled"&&this.headers.add(this.headers.next())[c?"addClass":"removeClass"]("ui-accordion-disabled ui-state-disabled")},_keydown:function(b){if(this.options.disabled||b.altKey||b.ctrlKey)return;var c=a.ui.keyCode,d=this.headers.length,e=this.headers.index(b.target),f=!1;switch(b.keyCode){case c.RIGHT:case c.DOWN:f=this.headers[(e+1)%d];break;case c.LEFT:case c.UP:f=this.headers[(e-1+d)%d];break;case c.SPACE:case c.ENTER:this._clickHandler({target:b.target},b.target),b.preventDefault()}return f?(a(b.target).attr("tabIndex",-1),a(f).attr("tabIndex",0),f.focus(),!1):!0},resize:function(){var b=this.options,c;if(b.fillSpace){if(a.browser.msie){var d=this.element.parent().css("overflow");this.element.parent().css("overflow","hidden")}c=this.element.parent().height(),a.browser.msie&&this.element.parent().css("overflow",d),this.headers.each(function(){c-=a(this).outerHeight(!0)}),this.headers.next().each(function(){a(this).height(Math.max(0,c-a(this).innerHeight()+a(this).height()))}).css("overflow","auto")}else b.autoHeight&&(c=0,this.headers.next().each(function(){c=Math.max(c,a(this).height("").height())}).height(c));return this},activate:function(a){this.options.active=a;var b=this._findActive(a)[0];return this._clickHandler({target:b},b),this},_findActive:function(b){return b?typeof b=="number"?this.headers.filter(":eq("+b+")"):this.headers.not(this.headers.not(b)):b===!1?a([]):this.headers.filter(":eq(0)")},_clickHandler:function(b,c){var d=this.options;if(d.disabled)return;if(!b.target){if(!d.collapsible)return;this.active.removeClass("ui-state-active ui-corner-top").addClass("ui-state-default ui-corner-all").children(".ui-icon").removeClass(d.icons.headerSelected).addClass(d.icons.header),this.active.next().addClass("ui-accordion-content-active");var e=this.active.next(),f={options:d,newHeader:a([]),oldHeader:d.active,newContent:a([]),oldContent:e},g=this.active=a([]);this._toggle(g,e,f);return}var h=a(b.currentTarget||c),i=h[0]===this.active[0];d.active=d.collapsible&&i?!1:this.headers.index(h);if(this.running||!d.collapsible&&i)return;var j=this.active,g=h.next(),e=this.active.next(),f={options:d,newHeader:i&&d.collapsible?a([]):h,oldHeader:this.active,newContent:i&&d.collapsible?a([]):g,oldContent:e},k=this.headers.index(this.active[0])>this.headers.index(h[0]);this.active=i?a([]):h,this._toggle(g,e,f,i,k),j.removeClass("ui-state-active ui-corner-top").addClass("ui-state-default ui-corner-all").children(".ui-icon").removeClass(d.icons.headerSelected).addClass(d.icons.header),i||(h.removeClass("ui-state-default ui-corner-all").addClass("ui-state-active ui-corner-top").children(".ui-icon").removeClass(d.icons.header).addClass(d.icons.headerSelected),h.next().addClass("ui-accordion-content-active"));return},_toggle:function(b,c,d,e,f){var g=this,h=g.options;g.toShow=b,g.toHide=c,g.data=d;var i=function(){if(!g)return;return g._completed.apply(g,arguments)};g._trigger("changestart",null,g.data),g.running=c.size()===0?b.size():c.size();if(h.animated){var j={};h.collapsible&&e?j={toShow:a([]),toHide:c,complete:i,down:f,autoHeight:h.autoHeight||h.fillSpace}:j={toShow:b,toHide:c,complete:i,down:f,autoHeight:h.autoHeight||h.fillSpace},h.proxied||(h.proxied=h.animated),h.proxiedDuration||(h.proxiedDuration=h.duration),h.animated=a.isFunction(h.proxied)?h.proxied(j):h.proxied,h.duration=a.isFunction(h.proxiedDuration)?h.proxiedDuration(j):h.proxiedDuration;var k=a.ui.accordion.animations,l=h.duration,m=h.animated;m&&!k[m]&&!a.easing[m]&&(m="slide"),k[m]||(k[m]=function(a){this.slide(a,{easing:m,duration:l||700})}),k[m](j)}else h.collapsible&&e?b.toggle():(c.hide(),b.show()),i(!0);c.prev().attr({"aria-expanded":"false","aria-selected":"false",tabIndex:-1}).blur(),b.prev().attr({"aria-expanded":"true","aria-selected":"true",tabIndex:0}).focus()},_completed:function(a){this.running=a?0:--this.running;if(this.running)return;this.options.clearStyle&&this.toShow.add(this.toHide).css({height:"",overflow:""}),this.toHide.removeClass("ui-accordion-content-active"),this.toHide.length&&(this.toHide.parent()[0].className=this.toHide.parent()[0].className),this._trigger("change",null,this.data)}}),a.extend(a.ui.accordion,{version:"1.8.19",animations:{slide:function(b,c){b=a.extend({easing:"swing",duration:300},b,c);if(!b.toHide.size()){b.toShow.animate({height:"show",paddingTop:"show",paddingBottom:"show"},b);return}if(!b.toShow.size()){b.toHide.animate({height:"hide",paddingTop:"hide",paddingBottom:"hide"},b);return}var d=b.toShow.css("overflow"),e=0,f={},g={},h=["height","paddingTop","paddingBottom"],i,j=b.toShow;i=j[0].style.width,j.width(j.parent().width()-parseFloat(j.css("paddingLeft"))-parseFloat(j.css("paddingRight"))-(parseFloat(j.css("borderLeftWidth"))||0)-(parseFloat(j.css("borderRightWidth"))||0)),a.each(h,function(c,d){g[d]="hide";var e=(""+a.css(b.toShow[0],d)).match(/^([\d+-.]+)(.*)$/);f[d]={value:e[1],unit:e[2]||"px"}}),b.toShow.css({height:0,overflow:"hidden"}).show(),b.toHide.filter(":hidden").each(b.complete).end().filter(":visible").animate(g,{step:function(a,c){c.prop=="height"&&(e=c.end-c.start===0?0:(c.now-c.start)/(c.end-c.start)),b.toShow[0].style[c.prop]=e*f[c.prop].value+f[c.prop].unit},duration:b.duration,easing:b.easing,complete:function(){b.autoHeight||b.toShow.css("height",""),b.toShow.css({width:i,overflow:d}),b.complete()}})},bounceslide:function(a){this.slide(a,{easing:a.down?"easeOutBounce":"swing",duration:a.down?1e3:200})}}})})(jQuery);
//-----------------------------------------------------------------------//
//---------------- SelectTransform, jQuery plugin -----------------------//
//---------------------- Script by ChoMPi -------------------------------//
//-----------------------------------------------------------------------//

(function($)
{
	var $isListOpen = false;
	var $currentlyOpenList = null;
	var $lastScrollTimestamp = null;
	var $minTimeBetweenScroll = 200; //Time in miliseconds
	
	var methods =
	{
		listState: 'closed',
		
		defaults:
		{
			container: null,
			list: null,
			selected: null,
			arrow: null,
			scrollConfig: { scrollBy: 3, },
			searchQueue: null,
			isScrollable: false,
		},
		
		init: function(config)
		{
			//if we have the element
			if ($(this).length < 1)
			{
				return;
			}
			
			//If the init hasent been called yet
			if (typeof $(this).data('SelectTransform') == 'undefined')
			{
				$(this).data('SelectTransform', {config: null});

				//merge the defaults with the passed config				
				$(this).data('SelectTransform').config = $.extend({}, methods.defaults, config);
			}
			else
			{
				//merge the old config with the passed one
				$(this).data('SelectTransform').config = $.extend({}, $(this).data('SelectTransform').config, config);
			}
		
			var config = $(this).data('SelectTransform').config;

			//get the instance of the element
			var $element = $(this);
			
			//hide the select form
			$element.css({display: 'none'});
			
			//create new element which will represent the select
			var container = document.createElement('div');
            if (typeof $element.attr('class') != 'undefined')
			{
				$(container).attr('class', $element.attr('class'));
            }
            $(container).addClass('js-select');
			//append the new element
			$element.after(container);
			//bind click event to open the dropdown list
			$(container).bind('click', function(event)
			{
				event.stopPropagation();
				$element.SelectTransform('clickEvent');
			});
			
			config.container = $(container);
			
			//create the div which will contain the selected option
			var selected = document.createElement('div');
			$(selected).attr('class', 'js-select-selected');
			$(container).append(selected);
			
			config.selected = $(selected);

			//create the div which will contain the arrow
			var arrow = document.createElement('div');
			$(arrow).attr('class', 'js-select-arrow');
			$(selected).after(arrow);
			
			config.arrow = $(arrow);
			
			//create new div which will be the container of the list
			var dropdownCont = document.createElement('div');
			$(dropdownCont).attr('class', 'js-select-list-container');
			$(dropdownCont).attr('id', 'js-list-container');
			$(dropdownCont).css({display: 'none', zIndex: 101});
			$(config.container).append(dropdownCont);
			
			config.listContainer = $(dropdownCont);

			//scrollbar manager
			var listItemCount = $element.find('option').length;
				
			//if the items are more than scrollBy variable, append scrollbar
			if (listItemCount > config.scrollConfig.scrollBy)
			{
				config.isScrollable = true;
				
				//create the scrollbar
				//create the top controller
				var topController = document.createElement('div');
				$(topController).attr('class', 'js-select-list-top-controller');
				$(topController).attr('id', 'js-list-top-controller');
				$(topController).attr('align', 'center');
				$(config.listContainer).append(topController);
				$(topController).append('<p></p>');
				$(topController).bind('click', function(event)
				{
					event.stopPropagation();
					$element.SelectTransform('ScrollUp');
				});

				config.topController = $(topController);
				
				//create new div which will be the scroller of the list
				var dropdownScroller = document.createElement('div');
				$(dropdownScroller).attr('class', 'js-select-list-scroller');
				$(dropdownScroller).attr('id', 'js-list-scroller');
				$(config.listContainer).append(dropdownScroller);
			
				config.listScroller = $(dropdownScroller);
							
				//Create new div which will be the container of the options
				var dropdown = document.createElement('div');
				$(dropdown).attr('class', 'js-select-list-scrollable');
				$(dropdown).attr('id', 'js-list');
				$(config.listScroller).append(dropdown);

				config.list = $(dropdown);
				
				//create the bottom controller
				var bottomController = document.createElement('div');
				$(bottomController).attr('class', 'js-select-list-bottom-controller');
				$(bottomController).attr('id', 'js-list-bottom-controller');
				$(bottomController).attr('align', 'center');
				$(config.listContainer).append(bottomController);
				$(bottomController).append('<p></p>');
				$(bottomController).bind('click', function(event)
				{
					event.stopPropagation();
					$element.SelectTransform('ScrollDown');
				});
				
				config.bottomController = $(bottomController);
			}
			else
			{
				//Create new div which will be the container of the options
				var dropdown = document.createElement('div');
				$(dropdown).attr('class', 'js-select-list');
				$(dropdown).attr('id', 'js-list');
				$(config.listContainer).append(dropdown);

				config.list = $(dropdown);
			}

			//append the options to the container
			$element.find('option').each(function(index, element)
			{
				var option = document.createElement('ul');
				$(option).attr('id', index);
				$(option).attr('class', 'js-select-list-option');
				$(option).html($(element).html());
				
				//check if HTML var is assigned
				if (typeof $(element).attr('getHtmlFrom') != 'undefined')
				{
					var htmlElement = $(element).attr('getHtmlFrom');
					
					//check if the assigned element exists
					if ($(htmlElement).length > 0)
					{
						$(option).html($(htmlElement).html());
					}
				}
				
				//copy the element style param
				if (typeof $(element).attr('style') != 'undefined')
				{
					$(option).attr('style', $(element).attr('style'));
				}
				
				//copy the element class param
				if (typeof $(element).attr('class') != 'undefined')
				{
					$(option).addClass($(element).attr('class'));
				}
				
				///bind the event handlers
				if(typeof $(element).attr('selected') != 'undefined')
				{
					//check if HTML var is assigned
					if (typeof $(element).attr('getHtmlFrom') != 'undefined')
					{
						var htmlElement = $(element).attr('getHtmlFrom');
						
						//check if the assigned element exists
						if ($(htmlElement).length > 0)
						{
							$(config.selected).html($(htmlElement).html());
						}
					}
					else
					{
						$(config.selected).html($(element).html());
					}
					
					$(option).addClass('js-select-list-option-selected');

					if(typeof $(element).attr('disabled') != 'undefined')
					{
						$(option).addClass('js-select-list-option-disabled');

						//wont bind anything on this one
						$(option).unbind('click');
						$(option).bind('click', function(event){ event.stopPropagation(); });
					}
					else
					{
						//bind click event to simply close the list
						$(option).bind('click', function(event)
						{ 
							event.stopPropagation();
							$element.SelectTransform('selectEvent', {option: element, index: index});
						});
					}
				}
				else
				{
					//bind the select handler
					if(typeof $(element).attr('disabled') != 'undefined')
					{
						$(option).addClass('js-select-list-option-disabled');
					
						//wont bind anything on this one
						$(option).bind('click', function(event)
						{ 
							event.stopPropagation();
						});
					}
					else
					{
						$(option).bind('click', function(event)
						{ 
							event.stopPropagation();
							$element.SelectTransform('selectEvent', {option: element, index: index});
						});
					}
				}
				
				//append the option to the container
            	$(config.list).append(option);
            });
			
		},
		
		clickEvent : function()
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
						
			//check if we have opened list
			if ($isListOpen)
			{
				//deactivate the arrow
				$currentlyOpenList.data('SelectTransform').config.arrow.removeClass('js-select-arrow-active');
				//close the option list
				$currentlyOpenList.SelectTransform('closeList');
				
				//unbind HTML click event
				$('html').unbind('click');
			}
			else					
			//if the list is closed
			{
				//activate the arrow
				$(config.arrow).addClass('js-select-arrow-active');
				//open the option list
				$(this).SelectTransform('openList');
				
				//bind click element to the HTML to close the dropdown list if the click is outside the select form
				$('html').bind('click', function(event)
				{
					event.stopPropagation();
					$element.SelectTransform('clickEvent');
				});
			}
		},
		
		openList : function()
		{
			var $element = $(this);
			var config = $(this).data('SelectTransform').config;
			
			//open the options list
			$(config.listContainer).slideDown('fast');
			
			//define that one list is already opened
			$isListOpen = true;
			$currentlyOpenList = $(this);
			
			//check if we have scrolling list
			if (config.isScrollable)
			{
				if (!$(config.listContainer).hasClass('js-select-list-container-scrollable'))
					$(config.listContainer).addClass('js-select-list-container-scrollable');
				
			  	//create the shearchbox element
				var searchbox = document.createElement('input');
				$(searchbox).css({ opacity: 0, position: 'fixed', top: '0px', left: '0px' });
				$(searchbox).attr('type', 'text');
				$(searchbox).attr('id', 'js-select-searchbox');
			
				$('body').append(searchbox);
				//focus the searchbox
				$(searchbox).focus();
			
				config.searchBox = $(searchbox);
			
				$(searchbox).on('keyup', function(event)
				{
					//prevent Enter key
					if (event.keyCode == '13')
					{
    	 				event.preventDefault();					
						return false;
					}
				
					//get the searchbox text
					var text = $(searchbox).val();
				
					$(config.list).children('ul').each(function(index, element)
					{
                    	var thisString = $(this).html();
					
						//convert both strings to lower case
						text = text.toLowerCase();
						thisString = thisString.toLowerCase();
					
						//if there is no text just go to the top
						if (text == '' || text == null)
						{
							clearTimeout(config.searchQueue);
							config.searchQueue = setTimeout(function(){ $element.SelectTransform('ScrollTo', 0); }, 300);
						}
						else if (thisString.indexOf(text) >= 0)
						{
							clearTimeout(config.searchQueue);
							config.searchQueue = setTimeout(function(){ $element.SelectTransform('ScrollTo', index); }, 300);
						}
                	});
            	});
				
				//Bind the mouse wheel events
				config.list.on('mousewheel', function(event, delta)
				{
					//stop the page from being scrolled
					event.preventDefault();
					
					//break if the last scroll was too soon
					if ($lastScrollTimestamp != null && (parseInt($lastScrollTimestamp) + $minTimeBetweenScroll) >= parseInt(event.timeStamp))
					{
						//console.log('Mouse wheel too soon.');
						return false;
					}
					
					//update the last scroll timestamp
					$lastScrollTimestamp = event.timeStamp;
					
					//get the direction			
					var dir = delta > 0 ? 'Up' : 'Down';
					
					//if scrolling up
					if (dir == 'Up')
					{
						$element.SelectTransform('ScrollUp');
					}
					else
					{
						$element.SelectTransform('ScrollDown');
					}
										
		            return false;
				});
			}			
		},
		
		closeList : function()
		{
			var config = $(this).data('SelectTransform').config;

			$isListOpen = false;
			$currentlyOpenList = null;

			//close the options list
			$(config.listContainer).slideUp('fast');
			
			//check if we have scrolling list
			if (config.isScrollable)
			{
				//destroy the searchbox
				$(config.searchBox).detach();
			}
			
			//off the mousewheel event
			config.list.off('mousewheel');
		},
		
		unselectOption: function()
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
			
			$option = $(this).find(':selected');
			
			//remove the selected
			$option.removeAttr('selected');
			
			//find the selected option in our custom list
			$selectedInList = $(config.list).find('.js-select-list-option-selected');
			$selectedInList.removeClass('js-select-list-option-selected');

			/*if (!$selectedInList.hasClass('js-select-list-option-disabled'))
			{
				//bind the select handler
				$selectedInList.unbind('click').bind('click', function(event)
				{ 
					event.stopPropagation();
					$element.SelectTransform('selectEvent', {option: $option, index: $option.index()});
				});
			}*/
		},
		
		selectOption: function(options)
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
						
			//set the option attr selected			
			$(options.option).attr('selected', 'selected');
			
			//find the option in our custom list
			$selectedInList = $(config.list).find('#'+options.index);
						
			//add class selected
			$selectedInList.addClass('js-select-list-option-selected');
					
			/*//bind click events
			$selectedInList.unbind('click').bind('click', function(event)
			{ 
				event.stopPropagation();
				$element.SelectTransform('clickEvent');
			});*/
		},
		
		selectEvent: function(options)
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
						
			var text = $(options.option).html();
			
			//remove the selected
			$(this).SelectTransform('unselectOption');
			
			//check if HTML var is assigned
			if (typeof $(options.option).attr('getHtmlFrom') != 'undefined')
			{
				var htmlElement = $(options.option).attr('getHtmlFrom');
						
				//check if the assigned element exists
				if ($(htmlElement).length > 0)
				{
					$(config.selected).html($(htmlElement).html());
				}
			}
			else
			{
				//update the selected text
				$(config.selected).html(text);
			}
			
			//select the option
			$(this).SelectTransform('selectOption', {option: options.option, index: options.index});
			
			//close the list
			$element.SelectTransform('clickEvent');
			
			//trigger change event
			$element.trigger('change');			
		},
		
		quickSelect: function(options)
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
						
			var text = $(options.option).html();
			
			//remove the selected
			$(this).SelectTransform('unselectOption');
			
			//check if HTML var is assigned
			if (typeof $(options.option).attr('getHtmlFrom') != 'undefined')
			{
				var htmlElement = $(options.option).attr('getHtmlFrom');
						
				//check if the assigned element exists
				if ($(htmlElement).length > 0)
				{
					$(config.selected).html($(htmlElement).html());
				}
			}
			else
			{
				//update the selected text
				$(config.selected).html(text);
			}
			
			//select the option
			$(this).SelectTransform('selectOption', {option: options.option, index: options.index});		
		},
		
		ScrollUp : function()
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
			
			//check if we need setup
			var isSetupDone = $(config.list).attr('isSetupDone');
			if (typeof isSetupDone == 'undefined' || isSetupDone != '1')
			{
				$element.SelectTransform('ScrollSetup');
			}

			//get some config variables
			var currentOffset = parseInt($(config.list).attr('currentOffset'));
			var totalOptions = parseInt($(config.list).attr('totalOptions'));
			
			var scrollToOffset = currentOffset - config.scrollConfig.scrollBy;
			
			if (scrollToOffset < 0)
			{
				scrollToOffset = 0;
			}
			
			//find the option we want to scroll to
			var $find = config.list.children('#' + scrollToOffset );
								
			$(config.list).stop(true,true).animate({ marginTop: '-' + ($find.position().top) + 'px' }, 400);
						
			//update the current offset
			$(config.list).attr('currentOffset', scrollToOffset);
					
			//focus the searchbox if exists
			if (typeof config.searchBox != 'undefined')
			{
				config.searchBox.attr('value', '');
				config.searchBox.focus();
			}
		},
		
		ScrollDown : function()
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
			
			//check if we need setup
			var isSetupDone = $(config.list).attr('isSetupDone');
			if (typeof isSetupDone == 'undefined' || isSetupDone != '1')
			{
				$element.SelectTransform('ScrollSetup');
			}
			
			//get some config variables
			var currentOffset = parseInt($(config.list).attr('currentOffset'));
			var totalOptions = parseInt($(config.list).attr('totalOptions'));
			
			var scrollToOffset = currentOffset + config.scrollConfig.scrollBy;
						
			//if the next scroll offset is greater than the total options
			if (scrollToOffset > (totalOptions - config.scrollConfig.scrollBy))
			{
				//null the next scroll offset to the total options - scroll by value
				scrollToOffset = totalOptions - config.scrollConfig.scrollBy;
			}

			//find the option we want to scroll to
			var $find = config.list.children('#' + scrollToOffset );
								
			$(config.list).stop(true,true).animate({ marginTop: '-' + ($find.position().top) + 'px' }, 400);
			
			//update the current offset
			$(config.list).attr('currentOffset', scrollToOffset);
			
			//focus the searchbox if exists
			if (typeof config.searchBox != 'undefined')
			{
				config.searchBox.attr('value', '');
				config.searchBox.focus();
			}
		},
		
		ScrollTo : function(index)
		{
			var config = $(this).data('SelectTransform').config;
			var $element = $(this);

			//check if we need setup
			var isSetupDone = $(config.list).attr('isSetupDone');
			if (typeof isSetupDone == 'undefined' || isSetupDone != '1')
			{
				$element.SelectTransform('ScrollSetup');
			}

			//get some config variables
			var currentOffset = parseInt($(config.list).attr('currentOffset'));
			var totalOptions = parseInt($(config.list).attr('totalOptions'));
									
			//if the next scroll offset is greater than the total options
			if (index > (totalOptions - config.scrollConfig.scrollBy))
			{
				//null the next scroll offset to the total options - scroll by value
				index = totalOptions - config.scrollConfig.scrollBy;
			}
			
			//find the option we want to scroll to
			var $find = config.list.children('#' + index );
								
			$(config.list).stop(true,true).animate({ marginTop: '-' + ($find.position().top) + 'px' }, 400);
			
			//update the current offset
			$(config.list).attr('currentOffset', index);		
			
		},
		
		ScrollSetup : function()
		{
			var config = $(this).data('SelectTransform').config;
			$element = $(this);
			
			//set some default values to the element directly
			$(config.list).attr('totalOptions', config.list.children('ul').length);
			$(config.list).attr('currentOffset', '0');
			
			$(config.list).attr('isSetupDone', '1');
		},
	}
	
  	$.fn.SelectTransform = function(method)
  	{
  		if (methods[method])
		{
     		return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
    	}
		else if (typeof method === 'object' || ! method)
		{
      		return methods.init.apply(this, arguments);
    	}
		else
		{
      		$.error( 'Method ' +  method + ' does not exist on jQuery.SelectTransform' );
    	}    
  	};

})(jQuery);
//-----------------------------------------------------------------------//
//-------------------- Loading Bar, jQuery plugin -----------------------//
//---------------------- Script by ChoMPi -------------------------------//
//-----------------------------------------------------------------------//

(function($)
{
	var methods =
	{
		init : function()
		{
			//if we have the element
			if ($(this).length < 1)
			{
				return;
			}
			
			//get the instance of the element
			var $element = $(this);
			
			//create the bar
			$element.append('<div class="loading-bar" align="left"><span id="bar"></span></div>');
			
			//run to state 1
            $element.LoadingBar('state1'); 
		},
		
		state1 : function(callback)
		{
			var $element = $(this);

			$element.find('#bar').stop(true,true).animate({ width: '100px' }, 1000, function()
			{
				$(this).css('width', '100px');
				
				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		state2 : function(callback)
		{
			var $element = $(this);
			
			$element.find('#bar').stop(true,true).animate({ width: '200px' }, 500, function()
			{
				$(this).css('width', '200px');
				
				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		state3 : function(callback)
		{
			var $element = $(this);

			$element.find('#bar').stop(true,true).animate({ width: '300px' }, 500, function()
			{
				$(this).css('width', '300px');

				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		state4 : function(callback)
		{
			var $element = $(this);

			$element.find('#bar').stop(true,true).animate({ width: '400px' }, 500, function()
			{
				$(this).css('width', '400px');
				
				if (typeof callback == 'function')
				{
					callback();
				}
			});
		},
		
		restart : function()
		{
			var $element = $(this);
			
			$element.find('#bar').css('width', '0px');
		},
	}
	
  	$.fn.LoadingBar = function(method)
  	{
  		if (methods[method])
		{
     		return methods[method].apply(this, Array.prototype.slice.call( arguments, 1 ));
    	}
		else if (typeof method === 'object' || ! method)
		{
      		return methods.init.apply(this, arguments);
    	}
		else
		{
      		$.error( 'Method ' +  method + ' does not exist on jQuery.LoadingBar');
    	}    
  	};

})(jQuery);
var Tooltip = {
    isOver: false,
    request: null,
    cache: {},

    /**
	 * Add event-listeners
	 */
	initialize: function() {
		// Add the tooltip element
		$("body").prepend('<div id="tooltip" class="tooltip"></div>');

		// Add mouse-over event listeners
		this.addEvents();

		// Add mouse listener
		$(document).mousemove(function(e) {
			Tooltip.move(e.pageX, e.pageY);
		});
    },
    
    /**
	 * Used to support Ajax content
	 * Reloads the tooltip elements
	 */
    refresh: function() {
        
    },

    addEvents: function() {
        // Add mouse-over event listeners
        $('body').on("mouseenter", "[data-tip]", function(e) {
            Tooltip.isOver = true;
			Tooltip.tipHandler(e);
        });
        $('body').on("mouseleave", "[data-tip]", Tooltip.mouseLeaveHandler);

        // Characters
		$('body').on("mouseleave", "[data-character-tip]", function(e)
		{
			Tooltip.isOver = true;
			Tooltip.characterHandler(e);
		});
        $('body').on("mouseleave", "[data-character-tip]", Tooltip.mouseLeaveHandler);
	},

	/**
	 * Moves tooltip
	 * @param Int x
	 * @param Int y
	 */
	move: function(x, y) {
        if (!$("#tooltip").is(':visible'))
            return;
        
		// Get half of the width
		var width = ($("#tooltip").css("width").replace("px", "") / 2);

		// Position it at the mouse, and center
		$("#tooltip").css("left", x - width).css("top", y + 25);
	},

    mouseLeaveHandler: function(e)
	{
		Tooltip.isOver = false;
		$("#tooltip").hide();
    },
    
	/**
	 * Displays the tooltip
	 * @param Object element
	 */
	show: function(data) {
		$("#tooltip").html(data).show();
    },
    
    tipHandler: function(e)
	{
		if (typeof $(e.currentTarget).attr('data-tip') == 'undefined' || $(e.currentTarget).attr('data-tip').length == 0)
			return;
		
		Tooltip.show($(e.currentTarget).attr("data-tip"));
    },
    
    characterHandler: function(e)
	{
		if (typeof $(e.currentTarget).attr('data-character-tip') == 'undefined' || $(e.currentTarget).attr('data-character-tip').length == 0)
			return;
		
		if (typeof $(e.currentTarget).attr('data-realm') == 'undefined' || $(e.currentTarget).attr('data-realm').length == 0)
			return;
		
		var characterName = $(e.currentTarget).attr('data-character-tip');
		var realmId = $(e.currentTarget).attr('data-realm');
		
		// Interrupt previous requests
		if (this.request != null)
		{ 
			this.request.abort();
			this.request = null;
		}
		
		// Check for cache
		if (typeof this.cache[realmId + '-' + characterName] != 'undefined')
		{
			if (Tooltip.isOver)
			{
				Tooltip.show(Tooltip.cache[realmId + '-' + characterName]);
			}
		}
		else
		{
			Tooltip.show('Loading...');
			
			// Make the request
			this.request = $.ajax(
			{
				type: "GET",
				url: $BaseURL + '/armory/character/tooltip?realm=' + realmId + '&character=' + characterName,
			})
			.done(function(msg)
			{
				if (Tooltip.isOver)
				{
					Tooltip.show(msg);
				}
				
				// Cache it
				Tooltip.cache[realmId + '-' + characterName] = msg;
			});
		}
	},
};

$(document).ready(function() {
    // Enable tooltip
    Tooltip.initialize();
});
// JavaScript Document

//on document ready
$(document).ready(function() {
	//transform the select forms
	$(document).find('select').each(function(index, element) {
        if (typeof $(element).attr('data-stylized') != 'undefined') {
			if ($(element).hasClass('character-select')) {
				$(element).SelectTransform({scrollConfig: { scrollBy: 3, }});
			} else {
				$(element).SelectTransform();
			}
		}
    });
});

//do the vertical centring, for all with class vertical_center
$(function()
{
	if ($('.vertical_center').length > 0)
	{
		$('.vertical_center').each(function()
		{
			var parentHeight = $(this).parent().height();
			var height = $(this).outerHeight();
				
			$(this).css('position', 'relative');
		
			//center it
			$(this).parent().css('height', (height+15) + 'px');
			$(this).css({ top: (parentHeight/2) + 'px', marginTop: '-' + (height/2) + 'px' });
		});
	}
});

//bind the login button handler
$(function()
{
	//if the CURUSER is not Online
	if (!$CURUSER.isOnline)
	{
		$LoginBox.closeEvent = false;
		
		$('.member-side-left .not-logged-menu > li > a#login').on('click', function()
		{			
			if (!$LoginBox.isLoaded)
			{
				//load the login box HTML
				//append new element
				$('body').append('<div id="Login-box_container" align="center"><div class="login-box-holder"></div></div>');

				//bind some close events
				$('#Login-box_container > .login-box-holder').on('mouseenter', function()
				{
					$LoginBox.closeEvent = false;
				});
				
				//bind the close event after 1500 ms
				setTimeout(function()
				{
					$('#Login-box_container').on('click', function()
					{
						if ($LoginBox.closeEvent)
						{
							$('#Login-box_container').fadeOut('fast');
						}
					});
					//close the box on escape
					$(document).keyup(function(e)
					{
						//if escape key
						if (e.keyCode == 27) 
						{
							//if the container is visible only
							if ($('#Login-box_container').is(':visible'))
							{
								$('#Login-box_container').fadeOut('fast');
							}
						}
                    });
				}, 1500);
		
				$('#Login-box_container').stop().animate({ opacity: 1 }, "fast", function()
				{
					$('#temp-login-form > .login-box').appendTo('#Login-box_container > .login-box-holder');
					
					$LoginBox.isLoaded = true;
					$LoginBox.closeEvent = true;
					
					$('#Login-box_container > .login-box-holder').on('mouseleave', function()
					{
						$LoginBox.closeEvent = true;
					});		
				});
			}
			else
			{
				//the HTML is loaded, fade in
				$('#Login-box_container').stop().fadeIn('fast');
			}
						
			return false;
		});		
	}
});

//Custom Radio Buttons script
function setupLabel()
{
	if ($('.label_check input').length)
	{
		$('.label_check').each(function()
		{ 
			$(this).removeClass('c_on');
		});
		$('.label_check input:checked').each(function()
		{ 
			$(this).parent('label').addClass('c_on');
		});                
	};
	if ($('.label_radio input').length)
	{
		$('.label_radio').each(function()
		{ 
			$(this).removeClass('r_on');
		});
		$('.label_radio input:checked').each(function()
		{ 
			$(this).parent('label').addClass('r_on');
		});
	};
};

$(document).ready(function()
{
	$('body').addClass('has-js');
	$('.label_check, .label_radio').click(function() {
		setupLabel();
	});
	setupLabel(); 
});
