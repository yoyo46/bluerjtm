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
            $('#UangJalanForm .per-unit').removeClass('hide');
        } else {
            $('#UangJalanForm .per-unit').addClass('hide');
        }
    });

    $('.input_price').priceFormat({
        doneFunc: function(obj, val) {
            currencyVal = val;
            currencyVal = currencyVal.replace(/,/gi, "")
            obj.next(".input_hidden").val(currencyVal);
        }
    });

    $('#getKotaAsal').change(function() {
        var self = $(this);

        if( self.val() != '' ) {
            $.ajax({
                url: '/ajax/getKotaAsal/'+self.val()+'/',
                type: 'POST',
                success: function(response, status) {
                    $('.from_city #getKotaTujuan').attr('disabled', false).html($(response).filter('#from_city_id').html());
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {
            $('#getKotaTujuan').val('').attr('disabled', true);
            $('#getTruck').val('').attr('disabled', true);
            $('#getInfoTruck').val('').attr('disabled', true);
        }
    });

    $('#getKotaTujuan').change(function() {
        var self = $(this);
        var customer_id = $('.customer').val();

        if( self.val() != '' ) {
            $.ajax({
                url: '/ajax/getKotaTujuan/'+self.val()+'/'+customer_id+'/',
                type: 'POST',
                success: function(response, status) {
                    $('.to_city #getTruck').attr('disabled', false).html($(response).filter('#to_city_id').html());
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {

        }
    });

    $('#getTruck').change(function() {
        var self = $(this);
        var customer_id = $('.customer').val();
        var from_city_id = $('.from_city #getKotaTujuan').val();

        if( self.val() != '' ) {
            $.ajax({
                url: '/ajax/getNopol/'+from_city_id+'/'+self.val()+'/'+'/'+customer_id+'/',
                type: 'POST',
                success: function(response, status) {
                    $('.truck_id #getInfoTruck').attr('disabled', false).html($(response).filter('#truck_id').html());
                    $('.uang_jalan_1').val($(response).filter('#uang_jalan_1').html());
                    $('.uang_jalan_2').val($(response).filter('#uang_jalan_2').html());
                    $('.commission').val($(response).filter('#commission').html());
                    $('.uang_kuli_muat').val($(response).filter('#uang_kuli_muat').html());
                    $('.uang_kuli_bongkar').val($(response).filter('#uang_kuli_bongkar').html());
                    $('.asdp').val($(response).filter('#asdp').html());
                    $('.uang_kawal').val($(response).filter('#uang_kawal').html());
                    $('.uang_keamanan').val($(response).filter('#uang_keamanan').html());
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {

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

    $('.add-custom-field').click(function (e) {
        var action_type = $(this).attr('action_type');

        if( action_type == 'transaction' ) {
            var voucher_number = $('#voucherNumber').val();
            var voucher_code = $('#voucherCode').val();
            var alias = $('#aliasHouse').val();
            var noted = $('#noted').val();
            var amount = $('#amount').val();

            if( amount == 0 || amount == '' ) {
                alert('Mohon masukan jumlah');
            } else if( noted == '' ) {
                alert('Mohon masukan keterangan');
            } else {
                if( voucher_number != '' ) {
                    voucher_code = voucher_number + '/' + voucher_code;
                }

                $('tr.removed').remove();
                var idx = $('#transDetail tbody tr').length;

                $('#transDetail tbody').append(''+
                '<tr>'+
                    '<td>'+
                        '<input name="data[TransactionDetail]['+idx+'][voucher_number]" class="form-control" type="text" value="'+voucher_code+'">'+
                    '</td>'+
                    '<td>'+
                        '<select name="data[TransactionDetail]['+idx+'][kiosk_id]" class="form-control chosen-select kioskList">'+$('#aliasHouse').html()+'</select>'+
                    '</td>'+
                    '<td>'+
                        '<input name="data[TransactionDetail]['+idx+'][note]" class="form-control" type="text" value="'+noted+'">'+
                    '</td>'+
                    '<td>'+
                        '<input name="data[TransactionDetail]['+idx+'][amount]" class="form-control input_price" type="text" value="'+amount+'">'+
                    '</td>'+
                    '<td>'+
                        '<a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="transaction"><i class="fa fa-times"></i> Hapus</a>'+
                    '</td>'+
                '</tr>');

                $('#voucherCode').val('');
                $('#noted').val('');
                $('#amount').val('');
                $('#transDetail tbody tr:last-child select').val(alias);
                $('#transDetail tbody tr:last-child .chosen-select').chosen({});

                amount_transaction('#transDetail tbody tr:last-child .input_price');
                $('#transDetail tbody tr:last-child .input_price').trigger('blur');
                delete_custom_field();
            }
        } else if( action_type == 'rent_pays' ) {
            var customer_id = $('#customerId').val();

            if( customer_id == '' ) {
                alert('Mohon pilih nama penyewa terlebih dahulu');
            } else {
                var transCode = $('#transCode').val();
                var alias = $('#aliasHouse').val();
                var noted = $('#noted').val();
                var amount = $('#amount').val();
                var monthFrom = $('#monthFrom').val();
                var monthTo = $('#monthTo').val();
                var noteDetail = $('#noteDetail').val();
                var url = '/ajax/getValidateKiosk/' + alias + '/' + customer_id + '/' + monthFrom + '/' + monthTo + '/' + '/' + transCode + '/';

                $.ajax({
                    url: url,
                    success: function(result) {
                        var message = $(result).filter('#message').html();
                        var status = $(result).filter('#status').html();

                        if( status == 'error' ) {
                            alert(message);
                        } else {
                            if( transCode == 0 || transCode == '' ) {
                                alert('Mohon pilih kode transaksi');
                            } else if( alias == 0 || alias == '' ) {
                                alert('Mohon pilih blok dan nomor');
                            } else if( amount == 0 || amount == '' ) {
                                alert('Mohon masukan jumlah');
                            } else if( monthFrom == '' || monthTo == '' ) {
                                alert('Mohon tanggal pembayaran');
                            } else if( noteDetail == '' ) {
                                alert('Mohon masukan keterangan');
                            } else {
                                $('tr.removed').remove();
                                var idx = $('#rentPaysDetail tbody tr').length;

                                $('#rentPaysDetail tbody').append(''+
                                '<tr>'+
                                    '<td>'+
                                        '<select name="data[RentPayDetail]['+idx+'][transaction_code_id]" class="form-control en-select trans_code">'+$('#transCode').html()+'</select>'+
                                    '</td>'+
                                    '<td>'+
                                        '<select name="data[RentPayDetail]['+idx+'][kiosk_id]" class="form-control chosen-select house kioskList">'+$('#aliasHouse').html()+'</select>'+
                                    '</td>'+
                                    '<td>'+
                                        '<input name="data[RentPayDetail]['+idx+'][amount]" class="form-control input_price" type="text" value="'+amount+'">'+
                                    '</td>'+
                                    '<td>'+
                                        '<input name="data[RentPayDetail]['+idx+'][paid_from_date]" class="form-control fromdatepicker" type="text" value="'+monthFrom+'">'+
                                    '</td>'+
                                    '<td>'+
                                        '<input name="data[RentPayDetail]['+idx+'][paid_to_date]" class="form-control todatepicker" type="text" value="'+monthTo+'">'+
                                    '</td>'+
                                    '<td>'+
                                        '<input name="data[RentPayDetail]['+idx+'][note]" class="form-control" type="text" value="'+noteDetail+'">'+
                                    '</td>'+
                                    '<td>'+
                                        '<a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="rent_pays"><i class="fa fa-times"></i> Hapus</a>'+
                                    '</td>'+
                                '</tr>');

                                $('#amount').val('');
                                $('#monthFrom').val('');
                                $('#monthTo').val('');
                                $('#noteDetail').val('');
                                $('#rentPaysDetail tbody tr:last-child select.trans_code').val(transCode);
                                $('#rentPaysDetail tbody tr:last-child select.house').val(alias);
                                $('#rentPaysDetail tbody tr:last-child .chosen-select').chosen({});

                                amount_rent_pay('#rentPaysDetail tbody tr:last-child .input_price');
                                classTodatepicker();
                                // monthYear('#rentPaysDetail tbody tr:last-child .monthYear');
                                classFromdatepicker( "#rentPaysDetail tbody tr:last-child .fromdatepicker", "#rentPaysDetail tbody tr:last-child .todatepicker" );
                                classTodatepicker( '#rentPaysDetail tbody tr:last-child .todatepicker', "#rentPaysDetail tbody tr:last-child .fromdatepicker" );
                                $('#rentPaysDetail tbody tr:last-child .input_price').trigger('blur');
                                delete_custom_field();
                            }
                        }
                    },
                });
            }
        }
    });

    var delete_custom_field = function() {
        $('.delete-custom-field').click(function (e) {
            var action_type = $(this).attr('action_type');

            if( action_type == 'transaction' ) {
                var lengthTable = $('#transDetail tbody tr').length;
                $(this).parents('tr').remove();

                if( lengthTable == 1 ) {
                    $('#transDetail tbody').append(''+
                    '<tr>'+
                        '<td>&nbsp;</td>'+
                        '<td>&nbsp;</td>'+
                        '<td>&nbsp;</td>'+
                        '<td>&nbsp;</td>'+
                        '<td>&nbsp;</td>'+
                    '</tr>');
                }
                // $('#transaction-'+(idx-1)).remove();
                // if( $('#transaction-0 .amount').html() != null ) {
                //  $('#transaction-0 .amount').trigger('blur');
                // }
                // $('.percent').trigger('blur');
                // grandtotal();
            } else if( action_type == 'rent_pays' ) {
                var lengthTable = $('#rentPaysDetail tbody tr').length;
                $(this).parents('tr').remove();

                if( lengthTable == 1 ) {
                    $('#rentPaysDetail tbody').append(''+
                    '<tr class="removed">'+
                        '<td>&nbsp;</td>'+
                        '<td>&nbsp;</td>'+
                        '<td>&nbsp;</td>'+
                        '<td>&nbsp;</td>'+
                        '<td>&nbsp;</td>'+
                        '<td>&nbsp;</td>'+
                        '<td>&nbsp;</td>'+
                    '</tr>');
                    $('.grandtotal').val(0);
                } else {
                    $('#rentPaysDetail tbody tr:last-child .input_price').trigger('blur');
                }
                // $('#transaction-'+(idx-1)).remove();
                // if( $('#transaction-0 .amount').html() != null ) {
                //  $('#transaction-0 .amount').trigger('blur');
                // }
                // $('.percent').trigger('blur');
                // grandtotal();
            }
        });
    }
    delete_custom_field();
});