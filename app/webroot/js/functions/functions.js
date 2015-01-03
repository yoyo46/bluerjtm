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
		var action_type = self.attr('action_type');

		switch(action_type) {
		    case 'perlengkapan':
                var class_count = $('#box-field-input .list-perlengkapan .seperator');
                var length = parseInt(class_count.length);
                var idx = length+1;

		        $('#box-field-input .list-perlengkapan').append('<div id="perlengkapan'+(idx-1)+'" class="seperator"> \
                    <div class="row"> \
                        <div class="col-sm-9"> \
                            <div class="form-group"> \
                                <label for="TruckPerlengkapanPerlengkapanId'+idx+'">Perlengkapan '+idx+'</label> \
                                <select name="data[TruckPerlengkapan][perlengkapan_id]['+(idx-1)+']" class="form-control" id="TruckPerlengkapanPerlengkapanId'+idx+'"> \
                                '+$('#perlengkapan_id select').html()+' \
                                </select> \
                            </div> \
                        </div> \
                        <div class="col-sm-3"> \
                            <div class="form-group"> \
                                <label for="TruckPerlengkapanQty'+idx+'">Jumlah Perlengkapan '+idx+'</label> \
                                <input name="data[TruckPerlengkapan][qty]['+(idx-1)+']" class="form-control input_number" type="text" value="" id="TruckPerlengkapanQty'+idx+'"> \
                            </div> \
                        </div> \
                    </div> \
                </div>');
		    break;
            case 'ttuj':
                var idx = $('#ttujDetail tbody tr').length;
                var optionTipeMotor = $('#tipe_motor_id select').html();

                $('#ttujDetail tbody').append(''+
                '<tr rel="'+idx+'">'+
                    '<td>'+
                        '<select name="data[TtujTipeMotor][tipe_motor_id]['+idx+']" class="form-control">'+
                        optionTipeMotor +
                        '</select>'+
                    '</td>'+
                    '<td>'+
                        '<input name="data[TtujTipeMotor][qty]['+idx+']" class="form-control" type="text">'+
                    '</td>'+
                    '<td>'+
                        '<a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="ttuj"><i class="fa fa-times"></i> Hapus</a>'+
                    '</td>'+
                '</tr>');

                delete_custom_field( $('#ttujDetail tbody tr:last-child .delete-custom-field') );
            break;
		    case 'alocation':
		    	$('#box-field-input #main-alocation .form-group').clone().appendTo('#advance-box-field-input');
		    break;
		}
    });

    $('.tree li:has(ul)').addClass('parent_li').find(' > span').attr('title', 'Collapse this branch');
    $('.tree li.parent_li > span').on('click', function (e) {
        var children = $(this).parent('li.parent_li').find(' > ul > li');
        if (children.is(":visible")) {
            children.hide('fast');
            $(this).attr('title', 'Expand this branch').find(' > i').addClass('icon-plus-sign').removeClass('icon-minus-sign');
        } else {
            children.show('fast');
            $(this).attr('title', 'Collapse this branch').find(' > i').addClass('icon-minus-sign').removeClass('icon-plus-sign');
        }
        e.stopPropagation();
    });

    $('#UangJalanPerUnit input[type="checkbox"]').click(function() {
        var self = $(this);

        if( self.is(':checked') ) {
            $('.uang_jalan_2').addClass('hide');
        } else {
            $('.uang_jalan_2').removeClass('hide');
        }
    });

    $('.input_price').priceFormat({
        doneFunc: function(obj, val) {
            currencyVal = val;
            currencyVal = currencyVal.replace(/,/gi, "")
            obj.next(".input_hidden").val(currencyVal);
        }
    });

    // $('#getKotaAsal').change(function() {
    //     var self = $(this);

    //     if( self.val() != '' ) {
    //         $.ajax({
    //             url: '/ajax/getKotaAsal/'+self.val()+'/',
    //             type: 'POST',
    //             success: function(response, status) {
    //                 $('.from_city #getKotaTujuan').attr('readonly', false).html($(response).filter('#from_city_id').html());
    //             },
    //             error: function(XMLHttpRequest, textStatus, errorThrown) {
    //                 alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
    //                 return false;
    //             }
    //         });
    //     } else {
    //         $('#getKotaTujuan').val('').attr('readonly', true);
    //         $('#getTruck').val('').attr('readonly', true);
    //         $('#getInfoTruck').val('').attr('readonly', true);
    //         $('.driver_name').val('');
    //         $('.truck_capacity').val('');
    //         $('#biaya-uang-jalan input').val('');
    //     }
    // });

    $('#getKotaTujuan').change(function() {
        var self = $(this);
        // var customer_id = $('.customer').val();

        if( self.val() != '' ) {
            $.ajax({
                // url: '/ajax/getKotaTujuan/'+self.val()+'/'+customer_id+'/',
                url: '/ajax/getKotaTujuan/'+self.val()+'/',
                type: 'POST',
                success: function(response, status) {
                    $('.to_city #getTruck').attr('readonly', false).html($(response).filter('#to_city_id').html());
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {
            $('#getTruck').val('').attr('readonly', true);
            $('#getInfoTruck').val('').attr('readonly', true);
            $('.driver_name').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#getTruck').change(function() {
        var self = $(this);
        // var customer_id = $('.customer').val();
        var from_city_id = $('.from_city #getKotaTujuan').val();

        if( self.val() != '' ) {
            $.ajax({
                // url: '/ajax/getNopol/'+from_city_id+'/'+self.val()+'/'+'/'+customer_id+'/',
                url: '/ajax/getNopol/'+from_city_id+'/'+self.val()+'/',
                type: 'POST',
                success: function(response, status) {
                    $('.truck_id #getInfoTruck').attr('readonly', false).html($(response).filter('#truck_id').html());
                    $('.uang_jalan_1').val($(response).filter('#uang_jalan_1').html());
                    $('.uang_jalan_2').val($(response).filter('#uang_jalan_2').html());
                    $('.commission').val($(response).filter('#commission').html());
                    $('.uang_kuli_muat').val($(response).filter('#uang_kuli_muat').html());
                    $('.uang_kuli_bongkar').val($(response).filter('#uang_kuli_bongkar').html());
                    $('.asdp').val($(response).filter('#asdp').html());
                    $('.uang_kawal').val($(response).filter('#uang_kawal').html());
                    $('.uang_keamanan').val($(response).filter('#uang_keamanan').html());
                    $('.uang_jalan_extra').val($(response).filter('#uang_jalan_extra').html());
                    $('.min_capacity').val($(response).filter('#min_capacity').html());
                    $('.is_unit').val($(response).filter('#is_unit').html());

                    if( $(response).filter('#is_unit').html() == 1 ) {
                        $('.wrapper_uang_jalan_2').addClass('hide');
                    } else {
                        $('.wrapper_uang_jalan_2').removeClass('hide');
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {
            $('#getInfoTruck').val('').attr('readonly', true);
            $('.driver_name').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#getInfoTruck').change(function() {
        var self = $(this);

        if( self.val() != '' ) {
            $.ajax({
                url: '/ajax/getInfoTruck/'+self.val()+'/',
                type: 'POST',
                success: function(response, status) {
                    $('.truck_capacity').val($(response).filter('#truck_capacity').html());
                    $('.driver_name').val($(response).filter('#driver_name').html());
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {
            $('.driver_name').val('');
            $('.truck_capacity').val('');
        }
    });

    if( $('.ttuj-form').length > 0 && window.location.hash == '#step2' ) {
        $('#step1').hide();
        $('#step2').show();
    }

    $('#nextTTUJ').click(function() {
        $( "#step1" ).fadeOut( "fast", function() {
            $( "#step2" ).fadeIn("fast");
        });
    });

    $('#backTTUJ').click(function() {
        $( "#step2" ).fadeOut( "fast", function() {
            $( "#step1" ).fadeIn("fast");
        });
    });

    var delete_custom_field = function( obj ) {
        if( typeof obj == 'undefined' ) {
            obj = $('.delete-custom-field');
        }

        obj.click(function (e) {
            var self = $(this);
            var action_type = self.attr('action_type');

            if( confirm('Anda yakin ingin menghapus data ini?') ) {
                if( action_type == 'ttuj' ) {
                    var lengthTable = $('#ttujDetail tbody tr').length;
                    $(this).parents('tr').remove();
                } else if( action_type == 'perlengkapan' ) {
                    var length = parseInt($('#box-field-input .list-perlengkapan .seperator').length);
                    var action_type = self.attr('action_type');
                    var idx = length;
                    $('#'+action_type+(idx-1)).remove();
                }
            }

            return false;
        });
    }
    delete_custom_field();

    if( $('.timepicker').length > 0 ) {
        $('.timepicker').timepicker({
            showMeridian: false
        });
    }

    $('.submit-form').click(function() {
        var action_type = $(this).attr('action_type');

        if( action_type == 'commit' ) {
            if( confirm('Anda yakin ingin mengunci data ini?') ) {
                $('#is_draft').val(0);
            } else {
                return false;
            }
        }
    });

    if( $('#month').length > 0 && $('#day').length > 0 && $('#year').length > 0 ) {
        $().dateSelectBoxes({
            monthElement: $('#month'),
            dayElement: $('#day'),
            yearElement: $('#year'),
        });
    }

    $('#no_ttuj').change(function() {
        var frm = $(this).parents('form');
        var action_type = $(this).attr('action_type');

        if( action_type == 'bongkaran' ) {
            frm.attr('action', '/revenues/search/bongkaran_add/')
        } else if( action_type == 'balik' ) {
            frm.attr('action', '/revenues/search/balik_add/')
        } else if( action_type == 'pool' ) {
            frm.attr('action', '/revenues/search/pool_add/')
        } else {
            frm.attr('action', '/revenues/search/truk_tiba_add/')
        }
        frm.submit();
    });

    $('.change-link').change(function() {
        var id = $(this).val();
        var url = $(this).attr('url') + '/' + id;

        location.href = url;

        return false;
    });

    $('.submit-link').click(function() {
        var frm = $(this).parents('form');
        var alert_msg = $(this).attr('alert');
        var action_type = $(this).attr('action_type');

        if( alert_msg != null ) {
            if ( !confirm(alert_msg) ) { 
                return false;
            }
        }

        if( action_type == 'rejected' ) {
            $('#rejected').val(1);
        }

        frm.submit();
        return false;
    });

    var input_number = function () {
        $('.input_number').keypress(function(event) {    
            if( (this.value.length == 0 && event.which == 46) || event.keyCode == 33 || event.keyCode == 64 || event.keyCode == 35 || event.keyCode == 36 || event.keyCode == 37 || event.keyCode == 94 || event.keyCode == 38 || event.keyCode == 42 || event.keyCode == 40 || event.keyCode == 41
                ){
                return false;
            } else {
                if (
                    event.keyCode == 8 ||  /*backspace*/
                    event.keyCode == 46 || /*point*/
                    event.keyCode == 9 || /*Tab*/
                    event.keyCode == 27 || /*esc*/
                    event.keyCode == 13 || /*enter*/
                    // event.keyCode == 97 || 
                    // Allow: Ctrl+A
                    // (event.keyCode == 65 && event.ctrlKey === true) ||
                    // Allow: home, end, left, right
                    (event.keyCode >= 35 && event.keyCode < 39) || ( event.which >= 48 && event.which <= 57 )
                    ) 
                {
                    return true;
                }else if (          
                    (event.which != 46 || ($(this).val().indexOf('.') != -1)) || 
                    (event.which < 48 || event.which > 57)) 
                {
                    event.preventDefault();
                }
            }
        });     
    }
    input_number();
});