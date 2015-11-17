(function($) {

	/****************
	* Main Function *
	*****************/
	$.fn.priceFormat = function(options)
	{

		var defaults =
		{
			prefix: ' ',
            suffix: '',
			centsSeparator: '',
			thousandsSeparator: ',',
			limit: false,
			centsLimit: 0,
			clearPrefix: false,
            clearSufix: false,
			allowNegative: false
		};

		var options = $.extend(defaults, options);

		return this.each(function()
		{

			var obj = $(this);
			var is_number = /[0-9]/;

			var prefix = options.prefix;
            var suffix = options.suffix;
			var centsSeparator = options.centsSeparator;
			var thousandsSeparator = options.thousandsSeparator;
			var limit = options.limit;
			var centsLimit = options.centsLimit;
			var clearPrefix = options.clearPrefix;
            var clearSuffix = options.clearSuffix;
			var allowNegative = options.allowNegative;

			function to_numbers (str)
			{
				var formatted = '';
				for (var i=0;i<(str.length);i++)
				{
					char_ = str.charAt(i);
					if (formatted.length==0 && char_==0) char_ = false;

					if (char_ && char_.match(is_number))
					{
						if (limit)
						{
							if (formatted.length < limit) formatted = formatted+char_;
						}
						else
						{
							formatted = formatted+char_;
						}
					}
				}

				return formatted;
			}

			function fill_with_zeroes (str)
			{
				while (str.length<(centsLimit+1)) str = '0'+str;
				return str;
			}

			function price_format (str)
			{
				var formatted = fill_with_zeroes(to_numbers(str));
				var thousandsFormatted = '';
				var thousandsCount = 0;

				var centsVal = formatted.substr(formatted.length-centsLimit,centsLimit);
				var integerVal = formatted.substr(0,formatted.length-centsLimit);

				formatted = integerVal+centsSeparator+centsVal;

				if (thousandsSeparator)
				{
					for (var j=integerVal.length;j>0;j--)
					{
						char_ = integerVal.substr(j-1,1);
						thousandsCount++;
						if (thousandsCount%3==0) char_ = thousandsSeparator+char_;
						thousandsFormatted = char_+thousandsFormatted;
					}
					if (thousandsFormatted.substr(0,1)==thousandsSeparator) thousandsFormatted = thousandsFormatted.substring(1,thousandsFormatted.length);
					formatted = thousandsFormatted+centsSeparator+centsVal;
				}

				if (allowNegative && str.indexOf('-') != -1 && (integerVal != 0 || centsVal != 0)) formatted = '-' + formatted;

				if (prefix) formatted = prefix+formatted;
                
				if (suffix) formatted = formatted+suffix;

				return formatted;
			}

			function key_check (e)
			{
				var code = (e.keyCode ? e.keyCode : e.which);
				var typed = String.fromCharCode(code);
				var functional = false;
				var str = obj.val();
				var newValue = price_format(str+typed);

				if((code >= 48 && code <= 57) || (code >= 96 && code <= 105)) functional = true;

				if (code ==  8) functional = true;
				if (code ==  9) functional = true;
				if (code == 13) functional = true;
				if (code == 46) functional = true;
				if (code == 37) functional = true;
				if (code == 39) functional = true;
				if (allowNegative && (code == 189 || code == 109)) functional = true;

				if (!functional)
				{
					e.preventDefault();
					e.stopPropagation();
					if (str!=newValue) obj.val(newValue);
				}

			}

			function price_it ()
			{
				var str = obj.val();
				var price = price_format(str);
				if (str != price) obj.val(price);
			}

			function add_prefix()
			{
				var val = obj.val();
				obj.val(prefix + val);
			}
            
            function add_suffix()
			{
				var val = obj.val();
				obj.val(val + suffix);
			}

			function clear_prefix()
			{
				if($.trim(prefix) != '' && clearPrefix)
				{
					var array = obj.val().split(prefix);
					obj.val(array[1]);
				}
			}
            
			function clear_suffix()
			{
				if($.trim(suffix) != '' && clearSuffix)
				{
					var array = obj.val().split(suffix);
					obj.val(array[0]);
				}
			}

			$(this).bind('keydown', key_check);
			$(this).bind('keyup', price_it);

			if(clearPrefix)
			{
				$(this).bind('focusout', function()
				{
					clear_prefix();
				});

				$(this).bind('focusin', function()
				{
					add_prefix();
				});
			}
			
			if(clearSuffix)
			{
				$(this).bind('focusout', function()
				{
                    clear_suffix();
				});

				$(this).bind('focusin', function()
				{
                    add_suffix();
				});
			}

			if ($(this).val().length>0)
			{
				price_it();
				clear_prefix();
                clear_suffix();
			}

		});

	};
	
	/******************
	* Unmask Function *
	*******************/
	jQuery.fn.unmask = function(){
		
		var field = $(this).val();
		var result = "";
		
		for(var f in field)
		{
			if(!isNaN(field[f]) || field[f] == "-") result += field[f];
		}
		
		return result;
	};

})(jQuery);

/*! bootstrap-timepicker v0.2.5 
* http://jdewit.github.com/bootstrap-timepicker 
* Copyright (c) 2013 Joris de Wit 
* MIT License 
*/
!function(a,b,c){"use strict";var d=function(b,c){this.widget="",this.$element=a(b),this.defaultTime=c.defaultTime,this.disableFocus=c.disableFocus,this.disableMousewheel=c.disableMousewheel,this.isOpen=c.isOpen,this.minuteStep=c.minuteStep,this.modalBackdrop=c.modalBackdrop,this.orientation=c.orientation,this.secondStep=c.secondStep,this.showInputs=c.showInputs,this.showMeridian=c.showMeridian,this.showSeconds=c.showSeconds,this.template=c.template,this.appendWidgetTo=c.appendWidgetTo,this.showWidgetOnAddonClick=c.showWidgetOnAddonClick,this._init()};d.prototype={constructor:d,_init:function(){var b=this;this.showWidgetOnAddonClick&&(this.$element.parent().hasClass("input-append")||this.$element.parent().hasClass("input-prepend"))?(this.$element.parent(".input-append, .input-prepend").find(".add-on").on({"click.timepicker":a.proxy(this.showWidget,this)}),this.$element.on({"focus.timepicker":a.proxy(this.highlightUnit,this),"click.timepicker":a.proxy(this.highlightUnit,this),"keydown.timepicker":a.proxy(this.elementKeydown,this),"blur.timepicker":a.proxy(this.blurElement,this),"mousewheel.timepicker DOMMouseScroll.timepicker":a.proxy(this.mousewheel,this)})):this.template?this.$element.on({"focus.timepicker":a.proxy(this.showWidget,this),"click.timepicker":a.proxy(this.showWidget,this),"blur.timepicker":a.proxy(this.blurElement,this),"mousewheel.timepicker DOMMouseScroll.timepicker":a.proxy(this.mousewheel,this)}):this.$element.on({"focus.timepicker":a.proxy(this.highlightUnit,this),"click.timepicker":a.proxy(this.highlightUnit,this),"keydown.timepicker":a.proxy(this.elementKeydown,this),"blur.timepicker":a.proxy(this.blurElement,this),"mousewheel.timepicker DOMMouseScroll.timepicker":a.proxy(this.mousewheel,this)}),this.$widget=this.template!==!1?a(this.getTemplate()).on("click",a.proxy(this.widgetClick,this)):!1,this.showInputs&&this.$widget!==!1&&this.$widget.find("input").each(function(){a(this).on({"click.timepicker":function(){a(this).select()},"keydown.timepicker":a.proxy(b.widgetKeydown,b),"keyup.timepicker":a.proxy(b.widgetKeyup,b)})}),this.setDefaultTime(this.defaultTime)},blurElement:function(){this.highlightedUnit=null,this.updateFromElementVal()},clear:function(){this.hour="",this.minute="",this.second="",this.meridian="",this.$element.val("")},decrementHour:function(){if(this.showMeridian)if(1===this.hour)this.hour=12;else{if(12===this.hour)return this.hour--,this.toggleMeridian();if(0===this.hour)return this.hour=11,this.toggleMeridian();this.hour--}else this.hour<=0?this.hour=23:this.hour--},decrementMinute:function(a){var b;b=a?this.minute-a:this.minute-this.minuteStep,0>b?(this.decrementHour(),this.minute=b+60):this.minute=b},decrementSecond:function(){var a=this.second-this.secondStep;0>a?(this.decrementMinute(!0),this.second=a+60):this.second=a},elementKeydown:function(a){switch(a.keyCode){case 9:case 27:this.updateFromElementVal();break;case 37:a.preventDefault(),this.highlightPrevUnit();break;case 38:switch(a.preventDefault(),this.highlightedUnit){case"hour":this.incrementHour(),this.highlightHour();break;case"minute":this.incrementMinute(),this.highlightMinute();break;case"second":this.incrementSecond(),this.highlightSecond();break;case"meridian":this.toggleMeridian(),this.highlightMeridian()}this.update();break;case 39:a.preventDefault(),this.highlightNextUnit();break;case 40:switch(a.preventDefault(),this.highlightedUnit){case"hour":this.decrementHour(),this.highlightHour();break;case"minute":this.decrementMinute(),this.highlightMinute();break;case"second":this.decrementSecond(),this.highlightSecond();break;case"meridian":this.toggleMeridian(),this.highlightMeridian()}this.update()}},getCursorPosition:function(){var a=this.$element.get(0);if("selectionStart"in a)return a.selectionStart;if(c.selection){a.focus();var b=c.selection.createRange(),d=c.selection.createRange().text.length;return b.moveStart("character",-a.value.length),b.text.length-d}},getTemplate:function(){var a,b,c,d,e,f;switch(this.showInputs?(b='<input type="text" class="bootstrap-timepicker-hour" maxlength="2"/>',c='<input type="text" class="bootstrap-timepicker-minute" maxlength="2"/>',d='<input type="text" class="bootstrap-timepicker-second" maxlength="2"/>',e='<input type="text" class="bootstrap-timepicker-meridian" maxlength="2"/>'):(b='<span class="bootstrap-timepicker-hour"></span>',c='<span class="bootstrap-timepicker-minute"></span>',d='<span class="bootstrap-timepicker-second"></span>',e='<span class="bootstrap-timepicker-meridian"></span>'),f='<table><tr><td><a href="#" data-action="incrementHour"><i class="fa fa-chevron-up"></i></a></td><td class="separator">&nbsp;</td><td><a href="#" data-action="incrementMinute"><i class="fa fa-chevron-up"></i></a></td>'+(this.showSeconds?'<td class="separator">&nbsp;</td><td><a href="#" data-action="incrementSecond"><i class="fa fa-chevron-up"></i></a></td>':"")+(this.showMeridian?'<td class="separator">&nbsp;</td><td class="meridian-column"><a href="#" data-action="toggleMeridian"><i class="fa fa-chevron-up"></i></a></td>':"")+"</tr>"+"<tr>"+"<td>"+b+"</td> "+'<td class="separator">:</td>'+"<td>"+c+"</td> "+(this.showSeconds?'<td class="separator">:</td><td>'+d+"</td>":"")+(this.showMeridian?'<td class="separator">&nbsp;</td><td>'+e+"</td>":"")+"</tr>"+"<tr>"+'<td><a href="#" data-action="decrementHour"><i class="fa fa-chevron-down"></i></a></td>'+'<td class="separator"></td>'+'<td><a href="#" data-action="decrementMinute"><i class="fa fa-chevron-down"></i></a></td>'+(this.showSeconds?'<td class="separator">&nbsp;</td><td><a href="#" data-action="decrementSecond"><i class="fa fa-chevron-down"></i></a></td>':"")+(this.showMeridian?'<td class="separator">&nbsp;</td><td><a href="#" data-action="toggleMeridian"><i class="fa fa-chevron-down"></i></a></td>':"")+"</tr>"+"</table>",this.template){case"modal":a='<div class="bootstrap-timepicker-widget modal hide fade in" data-backdrop="'+(this.modalBackdrop?"true":"false")+'">'+'<div class="modal-header">'+'<a href="#" class="close" data-dismiss="modal">×</a>'+"<h3>Pick a Time</h3>"+"</div>"+'<div class="modal-content">'+f+"</div>"+'<div class="modal-footer">'+'<a href="#" class="btn btn-primary" data-dismiss="modal">OK</a>'+"</div>"+"</div>";break;case"dropdown":a='<div class="bootstrap-timepicker-widget dropdown-menu">'+f+"</div>"}return a},getTime:function(){return this.hour||this.minute||this.second?this.hour+":"+(1===this.minute.toString().length?"0"+this.minute:this.minute)+(this.showSeconds?":"+(1===this.second.toString().length?"0"+this.second:this.second):"")+(this.showMeridian?" "+this.meridian:""):""},hideWidget:function(){this.isOpen!==!1&&(this.$element.trigger({type:"hide.timepicker",time:{value:this.getTime(),hours:this.hour,minutes:this.minute,seconds:this.second,meridian:this.meridian}}),"modal"===this.template&&this.$widget.modal?this.$widget.modal("hide"):this.$widget.removeClass("open"),a(c).off("mousedown.timepicker, touchend.timepicker"),this.isOpen=!1,this.$widget.detach())},highlightUnit:function(){this.position=this.getCursorPosition(),this.position>=0&&this.position<=2?this.highlightHour():this.position>=3&&this.position<=5?this.highlightMinute():this.position>=6&&this.position<=8?this.showSeconds?this.highlightSecond():this.highlightMeridian():this.position>=9&&this.position<=11&&this.highlightMeridian()},highlightNextUnit:function(){switch(this.highlightedUnit){case"hour":this.highlightMinute();break;case"minute":this.showSeconds?this.highlightSecond():this.showMeridian?this.highlightMeridian():this.highlightHour();break;case"second":this.showMeridian?this.highlightMeridian():this.highlightHour();break;case"meridian":this.highlightHour()}},highlightPrevUnit:function(){switch(this.highlightedUnit){case"hour":this.showMeridian?this.highlightMeridian():this.showSeconds?this.highlightSecond():this.highlightMinute();break;case"minute":this.highlightHour();break;case"second":this.highlightMinute();break;case"meridian":this.showSeconds?this.highlightSecond():this.highlightMinute()}},highlightHour:function(){var a=this.$element.get(0),b=this;this.highlightedUnit="hour",a.setSelectionRange&&setTimeout(function(){b.hour<10?a.setSelectionRange(0,1):a.setSelectionRange(0,2)},0)},highlightMinute:function(){var a=this.$element.get(0),b=this;this.highlightedUnit="minute",a.setSelectionRange&&setTimeout(function(){b.hour<10?a.setSelectionRange(2,4):a.setSelectionRange(3,5)},0)},highlightSecond:function(){var a=this.$element.get(0),b=this;this.highlightedUnit="second",a.setSelectionRange&&setTimeout(function(){b.hour<10?a.setSelectionRange(5,7):a.setSelectionRange(6,8)},0)},highlightMeridian:function(){var a=this.$element.get(0),b=this;this.highlightedUnit="meridian",a.setSelectionRange&&(this.showSeconds?setTimeout(function(){b.hour<10?a.setSelectionRange(8,10):a.setSelectionRange(9,11)},0):setTimeout(function(){b.hour<10?a.setSelectionRange(5,7):a.setSelectionRange(6,8)},0))},incrementHour:function(){if(this.showMeridian){if(11===this.hour)return this.hour++,this.toggleMeridian();12===this.hour&&(this.hour=0)}return 23===this.hour?(this.hour=0,void 0):(this.hour++,void 0)},incrementMinute:function(a){var b;b=a?this.minute+a:this.minute+this.minuteStep-this.minute%this.minuteStep,b>59?(this.incrementHour(),this.minute=b-60):this.minute=b},incrementSecond:function(){var a=this.second+this.secondStep-this.second%this.secondStep;a>59?(this.incrementMinute(!0),this.second=a-60):this.second=a},mousewheel:function(b){if(!this.disableMousewheel){b.preventDefault(),b.stopPropagation();var c=b.originalEvent.wheelDelta||-b.originalEvent.detail,d=null;switch("mousewheel"===b.type?d=-1*b.originalEvent.wheelDelta:"DOMMouseScroll"===b.type&&(d=40*b.originalEvent.detail),d&&(b.preventDefault(),a(this).scrollTop(d+a(this).scrollTop())),this.highlightedUnit){case"minute":c>0?this.incrementMinute():this.decrementMinute(),this.highlightMinute();break;case"second":c>0?this.incrementSecond():this.decrementSecond(),this.highlightSecond();break;case"meridian":this.toggleMeridian(),this.highlightMeridian();break;default:c>0?this.incrementHour():this.decrementHour(),this.highlightHour()}return!1}},place:function(){if(!this.isInline){var c=this.$widget.outerWidth(),d=this.$widget.outerHeight(),e=10,f=a(b).width(),g=a(b).height(),h=a(b).scrollTop(),i=parseInt(this.$element.parents().filter(function(){}).first().css("z-index"),10)+10,j=this.component?this.component.parent().offset():this.$element.offset(),k=this.component?this.component.outerHeight(!0):this.$element.outerHeight(!1),l=this.component?this.component.outerWidth(!0):this.$element.outerWidth(!1),m=j.left,n=j.top;this.$widget.removeClass("timepicker-orient-top timepicker-orient-bottom timepicker-orient-right timepicker-orient-left"),"auto"!==this.orientation.x?(this.picker.addClass("datepicker-orient-"+this.orientation.x),"right"===this.orientation.x&&(m-=c-l)):(this.$widget.addClass("timepicker-orient-left"),j.left<0?m-=j.left-e:j.left+c>f&&(m=f-c-e));var o,p,q=this.orientation.y;"auto"===q&&(o=-h+j.top-d,p=h+g-(j.top+k+d),q=Math.max(o,p)===p?"top":"bottom"),this.$widget.addClass("timepicker-orient-"+q),"top"===q?n+=k:n-=d+parseInt(this.$widget.css("padding-top"),10),this.$widget.css({top:n,left:m,zIndex:i})}},remove:function(){a("document").off(".timepicker"),this.$widget&&this.$widget.remove(),delete this.$element.data().timepicker},setDefaultTime:function(a){if(this.$element.val())this.updateFromElementVal();else if("current"===a){var b=new Date,c=b.getHours(),d=b.getMinutes(),e=b.getSeconds(),f="AM";0!==e&&(e=Math.ceil(b.getSeconds()/this.secondStep)*this.secondStep,60===e&&(d+=1,e=0)),0!==d&&(d=Math.ceil(b.getMinutes()/this.minuteStep)*this.minuteStep,60===d&&(c+=1,d=0)),this.showMeridian&&(0===c?c=12:c>=12?(c>12&&(c-=12),f="PM"):f="AM"),this.hour=c,this.minute=d,this.second=e,this.meridian=f,this.update()}else a===!1?(this.hour=0,this.minute=0,this.second=0,this.meridian="AM"):this.setTime(a)},setTime:function(a,b){if(!a)return this.clear(),void 0;var c,d,e,f,g;"object"==typeof a&&a.getMonth?(d=a.getHours(),e=a.getMinutes(),f=a.getSeconds(),this.showMeridian&&(g="AM",d>12&&(g="PM",d%=12),12===d&&(g="PM"))):(g=null!==a.match(/p/i)?"PM":"AM",a=a.replace(/[^0-9\:]/g,""),c=a.split(":"),d=c[0]?c[0].toString():c.toString(),e=c[1]?c[1].toString():"",f=c[2]?c[2].toString():"",d.length>4&&(f=d.substr(4,2)),d.length>2&&(e=d.substr(2,2),d=d.substr(0,2)),e.length>2&&(f=e.substr(2,2),e=e.substr(0,2)),f.length>2&&(f=f.substr(2,2)),d=parseInt(d,10),e=parseInt(e,10),f=parseInt(f,10),isNaN(d)&&(d=0),isNaN(e)&&(e=0),isNaN(f)&&(f=0),this.showMeridian?1>d?d=1:d>12&&(d=12):(d>=24?d=23:0>d&&(d=0),13>d&&"PM"===g&&(d+=12)),0>e?e=0:e>=60&&(e=59),this.showSeconds&&(isNaN(f)?f=0:0>f?f=0:f>=60&&(f=59))),this.hour=d,this.minute=e,this.second=f,this.meridian=g,this.update(b)},showWidget:function(){if(!this.isOpen&&!this.$element.is(":disabled")){this.$widget.appendTo(this.appendWidgetTo);var b=this;a(c).on("mousedown.timepicker, touchend.timepicker",function(a){b.$element.parent().find(a.target).length||b.$widget.is(a.target)||b.$widget.find(a.target).length||b.hideWidget()}),this.$element.trigger({type:"show.timepicker",time:{value:this.getTime(),hours:this.hour,minutes:this.minute,seconds:this.second,meridian:this.meridian}}),this.place(),this.disableFocus&&this.$element.blur(),this.hour||(this.defaultTime?this.setDefaultTime(this.defaultTime):this.setTime("0:0:0")),"modal"===this.template&&this.$widget.modal?this.$widget.modal("show").on("hidden",a.proxy(this.hideWidget,this)):this.isOpen===!1&&this.$widget.addClass("open"),this.isOpen=!0}},toggleMeridian:function(){this.meridian="AM"===this.meridian?"PM":"AM"},update:function(a){this.updateElement(),a||this.updateWidget(),this.$element.trigger({type:"changeTime.timepicker",time:{value:this.getTime(),hours:this.hour,minutes:this.minute,seconds:this.second,meridian:this.meridian}})},updateElement:function(){this.$element.val(this.getTime()).change()},updateFromElementVal:function(){this.setTime(this.$element.val())},updateWidget:function(){if(this.$widget!==!1){var a=this.hour,b=1===this.minute.toString().length?"0"+this.minute:this.minute,c=1===this.second.toString().length?"0"+this.second:this.second;this.showInputs?(this.$widget.find("input.bootstrap-timepicker-hour").val(a),this.$widget.find("input.bootstrap-timepicker-minute").val(b),this.showSeconds&&this.$widget.find("input.bootstrap-timepicker-second").val(c),this.showMeridian&&this.$widget.find("input.bootstrap-timepicker-meridian").val(this.meridian)):(this.$widget.find("span.bootstrap-timepicker-hour").text(a),this.$widget.find("span.bootstrap-timepicker-minute").text(b),this.showSeconds&&this.$widget.find("span.bootstrap-timepicker-second").text(c),this.showMeridian&&this.$widget.find("span.bootstrap-timepicker-meridian").text(this.meridian))}},updateFromWidgetInputs:function(){if(this.$widget!==!1){var a=this.$widget.find("input.bootstrap-timepicker-hour").val()+":"+this.$widget.find("input.bootstrap-timepicker-minute").val()+(this.showSeconds?":"+this.$widget.find("input.bootstrap-timepicker-second").val():"")+(this.showMeridian?this.$widget.find("input.bootstrap-timepicker-meridian").val():"");this.setTime(a,!0)}},widgetClick:function(b){b.stopPropagation(),b.preventDefault();var c=a(b.target),d=c.closest("a").data("action");d&&this[d](),this.update(),c.is("input")&&c.get(0).setSelectionRange(0,2)},widgetKeydown:function(b){var c=a(b.target),d=c.attr("class").replace("bootstrap-timepicker-","");switch(b.keyCode){case 9:if(this.showMeridian&&"meridian"===d||this.showSeconds&&"second"===d||!this.showMeridian&&!this.showSeconds&&"minute"===d)return this.hideWidget();break;case 27:this.hideWidget();break;case 38:switch(b.preventDefault(),d){case"hour":this.incrementHour();break;case"minute":this.incrementMinute();break;case"second":this.incrementSecond();break;case"meridian":this.toggleMeridian()}this.setTime(this.getTime()),c.get(0).setSelectionRange(0,2);break;case 40:switch(b.preventDefault(),d){case"hour":this.decrementHour();break;case"minute":this.decrementMinute();break;case"second":this.decrementSecond();break;case"meridian":this.toggleMeridian()}this.setTime(this.getTime()),c.get(0).setSelectionRange(0,2)}},widgetKeyup:function(a){(65===a.keyCode||77===a.keyCode||80===a.keyCode||46===a.keyCode||8===a.keyCode||a.keyCode>=46&&a.keyCode<=57)&&this.updateFromWidgetInputs()}},a.fn.timepicker=function(b){var c=Array.apply(null,arguments);return c.shift(),this.each(function(){var e=a(this),f=e.data("timepicker"),g="object"==typeof b&&b;f||e.data("timepicker",f=new d(this,a.extend({},a.fn.timepicker.defaults,g,a(this).data()))),"string"==typeof b&&f[b].apply(f,c)})},a.fn.timepicker.defaults={defaultTime:"current",disableFocus:!1,disableMousewheel:!1,isOpen:!1,minuteStep:15,modalBackdrop:!1,orientation:{x:"auto",y:"auto"},secondStep:15,showSeconds:!1,showInputs:!0,showMeridian:!0,template:"dropdown",appendWidgetTo:"body",showWidgetOnAddonClick:!0},a.fn.timepicker.Constructor=d}(jQuery,window,document);

/*
 *
 * Copyright (c) 2006-2009 Sam Collett (http://www.texotela.co.uk)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Version 2.2.4
 * Demo: http://www.texotela.co.uk/code/jquery/select/
 *
 * $LastChangedDate: 2009-02-08 00:28:12 +0000 (Sun, 08 Feb 2009) $ 
 * $Rev: 6185 $
 *
 */
eval(function(p,a,c,k,e,r){e=function(c){return(c<a?'':e(parseInt(c/a)))+((c=c%a)>35?String.fromCharCode(c+29):c.toString(36))};if(!''.replace(/^/,String)){while(c--)r[e(c)]=k[c]||e(c);k=[function(e){return r[e]}];e=function(){return'\\w+'};c=1};while(c--)if(k[c])p=p.replace(new RegExp('\\b'+e(c)+'\\b','g'),k[c]);return p}(';(6($){$.u.N=6(){5 j=6(a,v,t,b,c){5 d=11.12("U");d.p=v,d.H=t;5 o=a.z;5 e=o.q;3(!a.A){a.A={};x(5 i=0;i<e;i++){a.A[o[i].p]=i}}3(c||c==0){5 f=d;x(5 g=c;g<=e;g++){5 h=a.z[g];a.z[g]=f;o[g]=f;a.A[o[g].p]=g;f=h}}3(9 a.A[v]=="V")a.A[v]=e;a.z[a.A[v]]=d;3(b){d.s=8}};5 a=W;3(a.q==0)7 4;5 k=8;5 m=B;5 l,v,t;3(9(a[0])=="D"){m=8;l=a[0]}3(a.q>=2){3(9(a[1])=="O"){k=a[1];E=a[2]}n 3(9(a[2])=="O"){k=a[2];E=a[1]}n{E=a[1]}3(!m){v=a[0];t=a[1]}}4.y(6(){3(4.F.C()!="G")7;3(m){x(5 a 13 l){j(4,a,l[a],k,E);E+=1}}n{j(4,v,t,k,E)}});7 4};$.u.14=6(b,c,d,e,f){3(9(b)!="J")7 4;3(9(c)!="D")c={};3(9(d)!="O")d=8;4.y(6(){5 a=4;$.15(b,c,6(r){$(a).N(r,d);3(9 e=="6"){3(9 f=="D"){e.16(a,f)}n{e.P(a)}}})});7 4};$.u.X=6(){5 a=W;3(a.q==0)7 4;5 d=9(a[0]);5 v,K;3(d=="J"||d=="D"||d=="6"){v=a[0];3(v.I==Y){5 l=v.q;x(5 i=0;i<l;i++){4.X(v[i],a[1])}7 4}}n 3(d=="17")K=a[0];n 7 4;4.y(6(){3(4.F.C()!="G")7;3(4.A)4.A=Z;5 b=B;5 o=4.z;3(!!v){5 c=o.q;x(5 i=c-1;i>=0;i--){3(v.I==Q){3(o[i].p.R(v)){b=8}}n 3(o[i].p==v){b=8}3(b&&a[1]===8)b=o[i].s;3(b){o[i]=Z}b=B}}n{3(a[1]===8){b=o[K].s}n{b=8}3(b){4.18(K)}}});7 4};$.u.19=6(f){5 g=$(4).10();5 a=9(f)=="V"?8:!!f;4.y(6(){3(4.F.C()!="G")7;5 o=4.z;5 d=o.q;5 e=[];x(5 i=0;i<d;i++){e[i]={v:o[i].p,t:o[i].H}}e.1a(6(b,c){L=b.t.C(),M=c.t.C();3(L==M)7 0;3(a){7 L<M?-1:1}n{7 L>M?-1:1}});x(5 i=0;i<d;i++){o[i].H=e[i].t;o[i].p=e[i].v}}).S(g,8);7 4};$.u.S=6(b,d){5 v=b;5 e=9(b);3(e=="D"&&v.I==Y){5 f=4;$.y(v,6(){f.S(4,d)})};5 c=d||B;3(e!="J"&&e!="6"&&e!="D")7 4;4.y(6(){3(4.F.C()!="G")7 4;5 o=4.z;5 a=o.q;x(5 i=0;i<a;i++){3(v.I==Q){3(o[i].p.R(v)){o[i].s=8}n 3(c){o[i].s=B}}n{3(o[i].p==v){o[i].s=8}n 3(c){o[i].s=B}}}});7 4};$.u.1b=6(b,c){5 w=c||"s";3($(b).1c()==0)7 4;4.y(6(){3(4.F.C()!="G")7 4;5 o=4.z;5 a=o.q;x(5 i=0;i<a;i++){3(w=="1d"||(w=="s"&&o[i].s)){$(b).N(o[i].p,o[i].H)}}});7 4};$.u.1e=6(b,c){5 d=B;5 v=b;5 e=9(v);5 f=9(c);3(e!="J"&&e!="6"&&e!="D")7 f=="6"?4:d;4.y(6(){3(4.F.C()!="G")7 4;3(d&&f!="6")7 B;5 o=4.z;5 a=o.q;x(5 i=0;i<a;i++){3(v.I==Q){3(o[i].p.R(v)){d=8;3(f=="6")c.P(o[i],i)}}n{3(o[i].p==v){d=8;3(f=="6")c.P(o[i],i)}}}});7 f=="6"?4:d};$.u.10=6(){5 v=[];4.T().y(6(){v[v.q]=4.p});7 v};$.u.1f=6(){5 t=[];4.T().y(6(){t[t.q]=4.H});7 t};$.u.T=6(){7 4.1g("U:s")}})(1h);',62,80,'|||if|this|var|function|return|true|typeof||||||||||||||else||value|length||selected||fn|||for|each|options|cache|false|toLowerCase|object|startindex|nodeName|select|text|constructor|string|index|o1t|o2t|addOption|boolean|call|RegExp|match|selectOptions|selectedOptions|option|undefined|arguments|removeOption|Array|null|selectedValues|document|createElement|in|ajaxAddOption|getJSON|apply|number|remove|sortOptions|sort|copyOptions|size|all|containsOption|selectedTexts|find|jQuery'.split('|'),0,{}));
/*
 *
 * Developed by Nick Busey (http://nickbusey.com/)
 * Dual licensed under the MIT (http://www.opensource.org/licenses/mit-license.php)
 * and GPL (http://www.opensource.org/licenses/gpl-license.php) licenses.
 *
 * Version 2.0.0
 * Demo: http://nickabusey.com/jquery-date-select-boxes-plugin/
 * 
 */
(function(jQuery)
	{
		jQuery.fn.dateSelectBoxes = function(options)
			{
				var defaults = {
					keepLabels: false,
					yearMax: new Date().getFullYear(),
					yearMin: 1900,
					generateOptions: false,
					yearLabel: 'Tahun',
					monthLabel: 'Bulan',
					dayLabel: 'Hari'
				}
				var settings = $.extend({}, defaults, options);

				if (settings.keepLabels) {
					var dayLabel = settings.dayElement.val();
				}
				var allDays = {};
				for (var ii=1;ii<=31;ii++) {
					allDays[ii]=ii;
				}
				
				if (settings.generateOptions) {
					var years = [];
					if (settings.yearLabel && settings.keepLabels) {
						years.push(settings.yearLabel)
					}
					for (var ii=settings.yearMax;ii>=settings.yearMin;ii--) {
						years.push(ii);
					}
					settings.yearElement.addOption(years, false);

					var months = {
						1:'Januari',
						2:'Februari',
						3:'Maret',
						4:'April',
						5:'Mei',
						6:'Juni',
						7:'Juli',
						8:'Augustus',
						9:'September',
						10:'Oktober',
						11:'November',
						12:'Desember'
					};
					if (settings.monthLabel && settings.keepLabels) {
						jQuery.extend(months,{"0":settings.monthLabel});
					}
					settings.monthElement.addOption(months, false);
					if (settings.dayLabel && settings.keepLabels) {
						settings.dayElement.addOption({0:settings.dayLabel}, false);
					}
					settings.dayElement.addOption(allDays, false);
				}

				function isLeapYear() {
					var selected = settings.yearElement.selectedValues();
					return ( selected === "" || ( ( selected % 4 === 0 ) && ( selected % 100 !== 0 ) ) || ( selected % 400 === 0) );
				}
				function updateDays() {
					var selected = parseInt(settings.dayElement.val()), days = [], i;

					settings.dayElement.removeOption(/./);

					var month = parseInt(settings.monthElement.val(),10);
					if (!month) {
						//Default to 31 days if no month selected.
						month = 1;
					}
					
					switch (month) {
						case 1:
						case 3:
						case 5:
						case 7:
						case 8:
						case 10:
						case 12:
							for (ii=1;ii<=31;ii++) {
								days[ii]=allDays[ii];
							}
						break;
						case 2:
							var febDays = 0;
							if (isLeapYear()) {
								febDays = 29;
							} else {
								febDays = 28;
							}
							for (ii=1;ii<=febDays;ii++) {
								days[ii]=allDays[ii];
							}
						break;
						case 4:
						case 6:
						case 9:
						case 11:
							for (ii=1;ii<=30;ii++) {
								days[ii]=allDays[ii];
							}
						break;
					}
					if (settings.dayLabel && settings.keepLabels) {
						days[0] = settings.dayLabel;
					}
					settings.dayElement.addOption(days, false);
					settings.dayElement.selectOptions(selected);
					settings.dayElement.val(selected);
				}

				settings.yearElement.change( function() {
					updateDays();
				});
				settings.monthElement.change( function() {
					updateDays();
				});

			};
}(jQuery));