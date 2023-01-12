/*
 * jQuery Blueberry Slider v0.4 BETA
 * http://marktyrrell.com/labs/blueberry/
 *
 * Copyright (C) 2011, Mark Tyrrell <me@marktyrrell.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

(function($)
{
	$.fn.extend(
	{
		blueberry: function(options)
		{
			//default values for plugin options
			var defaults =
			{
				interval: 5000,
				duration: 500,
				lineheight: 1,
				height: 'auto', //reserved
				hoverpause: false,
				pager: true,
				nav: true, //reserved
				keynav: true
			};
			
			var options =  $.extend(defaults, options);
 
			return this.each(function()
			{
				var o = options;
				var obj = $(this);
				
				//Lock
				$(this).data('slideLocked', false);
				
				//store the slide and pager li
				var slides = $('.slides li', obj);
				var pager = $('.pager li', obj);

				//set initial current and next slide index values
				var current = 0;
				var next = current+1;

				//get height and width of initial slide image and calculate size ratio
				var imgHeight = slides.eq(current).find('img').height();
				var imgWidth = slides.eq(current).find('img').width();
				var imgRatio = imgWidth/imgHeight;

				//define vars for setsize function
				var sliderWidth = 0;
				var cropHeight = 0;

				//hide all slides, fade in the first, add active class to first slide
				slides.hide().eq(current).fadeIn(o.duration).addClass('active');
				
				//build pager if it doesn't already exist and if enabled
				if (pager.length)
				{
					pager.eq(current).addClass('active');
				}
				else if(o.pager)
				{
					obj.append('<ul class="pager"></ul>');
					slides.each(function(index)
					{
						$('.pager', obj).append('<li><a href="#"><span>'+index+'</span></a></li>')
					});
					pager = $('.pager li', obj);
					pager.eq(current).addClass('active');
				}

				//rotate to selected slide on pager click
				if (pager)
				{
					$('a', pager).click(function()
					{
						//stop the timer
						clearTimeout(obj.play);
						//set the slide index based on pager index
						next = $(this).parent().index();
						//rotate the slides
						rotate();
						return false;
					});
				}
				
				//primary function to change slides
				var rotate = function()
				{
					if (obj.data('slideLocked'))
						return;
					
					//Lock the slider
					obj.data('slideLocked', true);
					
					//save some data
					slides.eq(current).data('bb_current', current);
					slides.eq(current).data('bb_next', next);
					slides.eq(current).data('bb_duration', o.duration);
					slides.eq(current).data('bb_slides', slides);
					
					//fadeout
					slides.eq(current).fadeOut(o.duration, function()
					{
						var current = $(this).data('bb_current')
						var next = $(this).data('bb_next');
						var duration = $(this).data('bb_duration');
						var slides = $(this).data('bb_slides');
						
						//remove the class active
						$(this).removeClass('active');
						
						//fade in the next
						slides.eq(next).fadeIn(duration).addClass('active').queue(function()
						{
							obj.data('slideLocked', false);
							//add rotateTimer function to end of animation queue
							//this prevents animation buildup caused by requestAnimationFrame
							//rotateTimer starts a timer for the next rotate
							rotateTimer();
							$(this).dequeue()
						});
					});

					//update pager to reflect slide change
					if (pager)
					{
						pager.eq(current).removeClass('active')
							.end().eq(next).addClass('active');
					}

					//update current and next vars to reflect slide change
					//set next as first slide if current is the last
					current = next;
					next = current >= slides.length-1 ? 0 : current+1;
				};
				
				//create a timer to control slide rotation interval
				var rotateTimer = function()
				{
					obj.play = setTimeout(function()
					{
						//trigger slide rotate function at end of timer
						rotate();
					}, o.interval);
				};
				//start the timer for the first time
				rotateTimer();

				//pause the slider on hover
				//disabled by default due to bug
				if (o.hoverpause)
				{
					slides.hover(function()
					{
						//stop the timer in mousein
						clearTimeout(obj.play);
					},
					function()
					{
						//start the timer on mouseout
						rotateTimer();
					});
				}

				//calculate and set height based on image width/height ratio and specified line height
				var setsize = function()
				{
					sliderWidth = $('.slides', obj).width();
					cropHeight = Math.floor(((sliderWidth/imgRatio)/o.lineheight))*o.lineheight;

					//$('.slides', obj).css({height: cropHeight});
				};
				setsize();

				//bind setsize function to window resize event
				$(window).resize(function()
				{
					setsize();
				});
				
				//Add keyboard navigation
				if(o.keynav)
				{
					$(document).keyup(function(e)
					{
						switch (e.which)
						{
							case 39: case 32: //right arrow & space
								clearTimeout(obj.play);
								rotate();
								break;

							case 37: // left arrow
								clearTimeout(obj.play);
								next = current - 1;
								rotate();
								break;
						}
					});
				}
			});
		}
	});
})(jQuery);

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
