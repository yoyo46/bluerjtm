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
		var type_action = self.attr('type_action');
		var class_count = $('#box-field-input .form-group');
		var length = parseInt(class_count.length);
		var idx = length+1;

		switch(type_action) {
		    case 'perlengkapan':
		        $('#box-field-input').append('<div class="form-group" id="'+type_action+idx+'">'+
		        	'<label for="PerlengkapanName'+idx+'">perlengkapan '+idx+'</label>'+
		        	'<input name="data[Perlengkapan][name]['+(idx-1)+']" class="form-control" placeholder="perlengkapan" type="text" id="name'+idx+'">'+
		        	'</div>');
		    break;
		    case 'alocation':
		    	$('#box-field-input #main-alocation .form-group').clone().appendTo('#advance-box-field-input');
		    break;
		}
    });

    $('.delete-custom-field').click(function (e) {
	 	var length = parseInt($('#box-field-input .form-group').length);
	 	var type_action = self.attr('type_action');
	 	var idx = length;
 		$('#'+type_action+(idx-1)).remove();
    });
});