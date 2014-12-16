$(function() {
	$('.aset-handling').click(function(){
		if($('.aset-handling .aset-handling-form').is(':checked')) {
			$('.neraca-form').prop('disabled', false);
		}else{
			$('.neraca-form').prop('disabled', true);
		}
	});

	$('.add-custom-field').click(function (e) {
		var self = $(this);
		var class_count = $('#box-field-input .form-group');
	 	
	 	var length = parseInt(class_count.length);

	 	var idx = length+1;
 		$('#box-field-input').append('<div class="form-group">'+
        	'<label for="PerlengkapanName'+idx+'">perlengkapan '+idx+'</label>'+
        	'<input name="data[Perlengkapan][name]['+(idx-1)+']" class="form-control" placeholder="perlengkapan" type="text" id="name'+idx+'">'+
        	'</div>');
    });

    $('.delete-custom-field').click(function (e) {
	 	var length = parseInt($('#box-field-input .form-group').length);
	 	var idx = length;
 		$('#perlengkapan'+(idx-1)).remove();
    });
});