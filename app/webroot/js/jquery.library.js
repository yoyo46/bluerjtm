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