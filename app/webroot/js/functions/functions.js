var formatNumber = function( number, decimals, dec_point, thousands_sep ){
    // Set the default values here, instead so we can use them in the replace below.
    thousands_sep   = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
    dec_point       = (typeof dec_point === 'undefined') ? '.' : dec_point;
    decimals        = !isFinite(+decimals) ? 0 : Math.abs(decimals);

    // Work out the unicode representation for the decimal place.   
    var u_dec = ('\\u'+('0000'+(dec_point.charCodeAt(0).toString(16))).slice(-4));

    // Fix the number, so that it's an actual number.
    number = (number + '')
        .replace(new RegExp(u_dec,'g'),'.')
        .replace(new RegExp('[^0-9+\-Ee.]','g'),'');

    var n = !isFinite(+number) ? 0 : +number,
        s = '',
        toFixedFix = function (n, decimals) {
            var k = Math.pow(10, decimals);
            return '' + Math.round(n * k) / k;
        };

    // Fix for IE parseFloat(0.55).toFixed(0) = 0;
    s = (decimals ? toFixedFix(n, decimals) : '' + Math.round(n)).split('.');
    if (s[0].length > 3) {
        s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, thousands_sep);
    }
    if ((s[1] || '').length < decimals) {
        s[1] = s[1] || '';
        s[1] += new Array(decimals - s[1].length + 1).join('0');
    }
    return s.join(dec_point);
}

var qtyMuatanPress = function ( obj ) {
    obj.keyup(function() {
        var total_muatan = 0;
        var qtyLen = $('#ttujDetail tbody tr').length;

        for (var i = 0; i < qtyLen; i++) {
            total_muatan += parseInt($('#ttujDetail tbody tr .qty-muatan[rel="'+i+'"]').val());
        };

        $('.total-unit-muatan').html(total_muatan);

        if( isNaN( total_muatan ) || total_muatan == 0 ) {
            total_muatan = 1;
        }

        var uang_jalan_1 = $('.uang_jalan_1_ori').val();
        var uang_jalan_per_unit = $('.uang_jalan_per_unit').val();
        var uang_kuli_muat = $('.uang_kuli_muat_ori').val();
        var uang_kuli_muat_per_unit = $('.uang_kuli_muat_per_unit').val();
        var uang_kuli_bongkar = $('.uang_kuli_bongkar_ori').val();
        var uang_kuli_bongkar_per_unit = $('.uang_kuli_bongkar_per_unit').val();
        var asdp = $('.asdp_ori').val();
        var asdp_per_unit = $('.asdp_per_unit').val();
        var uang_kawal = $('.uang_kawal_ori').val();
        var uang_kawal_per_unit = $('.uang_kawal_per_unit').val();
        var uang_keamanan = $('.uang_keamanan_ori').val();
        var uang_keamanan_per_unit = $('.uang_keamanan_per_unit').val();

        if( uang_jalan_per_unit == 1 ) {
            uang_jalan_1 = uang_jalan_1*total_muatan;
            uang_jalan_2 = 0;
        }

        if( uang_kuli_muat_per_unit == 1 ) {
            uang_kuli_muat = uang_kuli_muat*total_muatan;
        }

        if( uang_kuli_bongkar_per_unit == 1 ) {
            uang_kuli_bongkar = uang_kuli_bongkar*total_muatan;
        }

        if( asdp_per_unit == 1 ) {
            asdp = asdp*total_muatan;
        }

        if( uang_kawal_per_unit == 1 ) {
            uang_kawal = uang_kawal*total_muatan;
        }

        if( uang_keamanan_per_unit == 1 ) {
            uang_keamanan = uang_keamanan*total_muatan;
        }

        $('.uang_jalan_1').val( formatNumber( uang_jalan_1, 0 ) );
        $('.uang_jalan_2').val( formatNumber( uang_jalan_2, 0 ) );
        $('.uang_kuli_muat').val( formatNumber( uang_kuli_muat, 0 ) );
        $('.uang_kuli_bongkar').val( formatNumber( uang_kuli_bongkar, 0 ) );
        $('.asdp').val( formatNumber( asdp, 0 ) );
        $('.uang_kawal').val( formatNumber( uang_kawal, 0 ) );
        $('.uang_keamanan').val( formatNumber( uang_keamanan, 0 ) );
    });
}

$(function() {
	$('.aset-handling').click(function(){
		if($('.aset-handling .aset-handling-form').is(':checked')) {
			$('.neraca-form').prop('disabled', false);
		}else{
			$('.neraca-form').prop('disabled', true);
		}
	});

    var add_custom_field = function(){
        $('.add-custom-field').click(function (e) {
            var self = $(this);
            var action_type = self.attr('action_type');
            var data_custom = self.attr('data-custom');

    		switch(action_type) {
                case 'perlengkapan':
                    var class_count = $('#box-field-input .form-group');
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
                // case 'ttuj':
                //     var idx = $('#ttujDetail tbody tr').length;
                //     var optionTipeMotor = $('#tipe_motor_id select').html();
                //         $('#box-field-input').append('<div class="form-group" id="'+action_type+idx+'">'+
                //             '<label for="PerlengkapanName'+idx+'">perlengkapan '+idx+'</label>'+
                //             '<input name="data[Perlengkapan][name]['+(idx-1)+']" class="form-control" placeholder="perlengkapan" type="text" id="name'+idx+'">'+
                //             '</div>');
                //     break;
                case 'ttuj':
                    var idx = $('#ttujDetail tbody tr').length;
                    var optionTipeMotor = $('#tipe_motor_id select').html();
                    var additionalField = '';

                    if( data_custom == 'depo' ) {
                        var contentOptionCities = $('#data-cities-options select').html();

                        additionalField = '<td> \
                            <select name="data[TtujTipeMotor][city_id]['+idx+']" class="form-control"> \
                            ' + contentOptionCities +
                            '</select> \
                        </td>';
                    }

                    $('#ttujDetail tbody').append(' \
                    <tr rel="'+idx+'"> \
                        '+ additionalField +
                        '<td> \
                            <select name="data[TtujTipeMotor][tipe_motor_id]['+idx+']" class="form-control"> \
                            ' + optionTipeMotor +
                            '</select> \
                        </td> \
                        <td> \
                            <input name="data[TtujTipeMotor][qty]['+idx+']" class="form-control qty-muatan" type="text" rel="'+idx+'"> \
                        </td> \
                        <td> \
                            <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="ttuj"><i class="fa fa-times"></i> Hapus</a> \
                        </td> \
                    </tr>');

                    delete_custom_field( $('#ttujDetail tbody tr:last-child .delete-custom-field') );
                    qtyMuatanPress( $('#ttujDetail tbody tr:last-child .qty-muatan') );
                    break;
                case 'alocation':
                    $('#box-field-input #main-alocation .list-alocation').clone().appendTo('#advance-box-field-input');
                    $('#advance-box-field-input .list-alocation:last-child select').val('');
                    delete_custom_field( $('#advance-box-field-input .list-alocation:last-child .delete-custom-field') );
                    break;
                case 'lku_tipe_motor':
                    var content_clone = $('#first-row').html();
                    var length_option = $('#first-row .lku-choose-tipe-motor option').length-1;
                    var tipe_motor_table = $('.tipe-motor-table .lku-choose-tipe-motor').length;

                    if( length_option > tipe_motor_table ){
                        $('.tipe-motor-table #field-grand-total-lku').before(content_clone);
                        
                        choose_item_info();
                        input_number();
                        price_tipe_motor();
                        delete_custom_field();
                    }
                    break;
                case 'lku_ttuj':
                    var content_clone = $('#first-row').html();
                    var length_option = $('#first-row .lku-choose-ttuj option').length-1;
                    var tipe_motor_table = $('.ttuj-info-table .lku-choose-ttuj').length;

                    if( length_option > tipe_motor_table ){
                        $('.ttuj-info-table #field-grand-total-ttuj').before(content_clone);
                        choose_item_info();
                        input_number();
                        price_tipe_motor();
                        delete_custom_field();
                    }
                break;
            }
        });
    }
    add_custom_field();

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

    // $('#UangJalanPerUnit input[type="checkbox"]').click(function() {
    $('.chk-uang-jalan').click(function() {
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
                    var total_muatan = parseInt($('.total-unit-muatan').html());
                    var uang_jalan_1 = $(response).filter('#uang_jalan_1').html().replace(/,/gi, "");
                    var uang_jalan_2 = $(response).filter('#uang_jalan_2').html().replace(/,/gi, "");
                    var uang_jalan_per_unit = $(response).filter('#uang_jalan_per_unit').html();

                    var uang_kuli_muat = $(response).filter('#uang_kuli_muat').html().replace(/,/gi, "");
                    var uang_kuli_muat_per_unit = $(response).filter('#uang_kuli_muat_per_unit').html();

                    var uang_kuli_bongkar = $(response).filter('#uang_kuli_bongkar').html().replace(/,/gi, "");
                    var uang_kuli_bongkar_per_unit = $(response).filter('#uang_kuli_bongkar_per_unit').html();

                    // var commission = $(response).filter('#commission').html().replace(/,/gi, "");
                    // var commission_per_unit = $(response).filter('#commission_per_unit').html();

                    var asdp = $(response).filter('#asdp').html().replace(/,/gi, "");
                    var asdp_per_unit = $(response).filter('#asdp_per_unit').html();

                    var uang_kawal = $(response).filter('#uang_kawal').html().replace(/,/gi, "");
                    var uang_kawal_per_unit = $(response).filter('#uang_kawal_per_unit').html();

                    var uang_keamanan = $(response).filter('#uang_keamanan').html().replace(/,/gi, "");
                    var uang_keamanan_per_unit = $(response).filter('#uang_keamanan_per_unit').html();

                    if( isNaN( total_muatan ) || total_muatan == 0 ) {
                        total_muatan = 1;
                    }

                    if( uang_jalan_per_unit == 1 ) {
                        uang_jalan_1 = uang_jalan_1*total_muatan;
                        uang_jalan_2 = 0;
                        $('.wrapper_uang_jalan_2').addClass('hide');
                    } else {
                        $('.wrapper_uang_jalan_2').removeClass('hide');
                    }

                    if( uang_kuli_muat_per_unit == 1 ) {
                        uang_kuli_muat = uang_kuli_muat*total_muatan;
                    }

                    if( uang_kuli_bongkar_per_unit == 1 ) {
                        uang_kuli_bongkar = uang_kuli_bongkar*total_muatan;
                    }

                    if( asdp_per_unit == 1 ) {
                        asdp = asdp*total_muatan;
                    }

                    if( uang_kawal_per_unit == 1 ) {
                        uang_kawal = uang_kawal*total_muatan;
                    }

                    if( uang_keamanan_per_unit == 1 ) {
                        uang_keamanan = uang_keamanan*total_muatan;
                    }

                    $('.truck_id #getInfoTruck').attr('readonly', false).html($(response).filter('#truck_id').html());
                    $('.uang_jalan_1').val( formatNumber( uang_jalan_1, 0 ) );
                    $('.uang_jalan_1_ori').val( uang_jalan_1 );
                    $('.uang_jalan_per_unit').val( uang_jalan_per_unit );

                    $('.uang_jalan_2').val( formatNumber( uang_jalan_2, 0 ) );

                    // $('.commission').val( formatNumber( commission, 0 ) );
                    $('.uang_kuli_muat').val( formatNumber( uang_kuli_muat, 0 ) );
                    $('.uang_kuli_muat_ori').val( uang_kuli_muat );
                    $('.uang_kuli_muat_per_unit').val( uang_kuli_muat_per_unit );

                    $('.uang_kuli_bongkar').val( formatNumber( uang_kuli_bongkar, 0 ) );
                    $('.uang_kuli_bongkar_ori').val( uang_kuli_bongkar );
                    $('.uang_kuli_bongkar_per_unit').val( uang_kuli_bongkar );

                    $('.asdp').val( formatNumber( asdp, 0 ) );
                    $('.asdp_ori').val( formatNumber( asdp, 0 ) );
                    $('.asdp_per_unit').val( asdp_ori );

                    $('.uang_kawal').val( formatNumber( uang_kawal, 0 ) );
                    $('.uang_kawal_ori').val( uang_kawal );
                    $('.uang_kawal_per_unit').val( uang_kawal_per_unit );

                    $('.uang_keamanan').val( formatNumber( uang_keamanan, 0 ) );
                    $('.uang_keamanan_ori').val( uang_keamanan );
                    $('.uang_keamanan_per_unit').val( uang_keamanan_per_unit );

                    $('.uang_jalan_extra').val($(response).filter('#uang_jalan_extra').html());
                    $('.min_capacity').val($(response).filter('#min_capacity').html());
                    // $('.is_unit').val($(response).filter('#is_unit').html());
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

    if( $('.ttuj-form, .laka-form').length > 0 && window.location.hash == '#step2' ) {
        $('#step1').hide();
        $('#step2').show();
    }

    $('#nextTTUJ, #nextLaka').click(function() {
        $( "#step1" ).fadeOut( "fast", function() {
            $( "#step2" ).fadeIn("fast");
        });
    });

    $('#backTTUJ, #backLaka').click(function() {
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
                } else if( action_type == 'lku_first'){
                    self.parents('tr').remove();
                    grandTotalLku();
                } else if( action_type == 'lku_second'){
                    self.parents('tr').remove();
                    getTotalLkuPayment();
                } else if( action_type == 'alocation' ) {
                    var length = parseInt($('#advance-box-field-input .list-alocation').length);
                    var parent = self.parents('.list-alocation');
                    parent.remove();
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

    $('.submit-change').change(function() {
        var frm = $(this).parents('form');
        var action_type = $(this).attr('action_type');

        if( action_type == 'bongkaran' ) {
            frm.attr('action', '/revenues/search/bongkaran_add/')
        } else if( action_type == 'balik' ) {
            frm.attr('action', '/revenues/search/balik_add/')
        } else if( action_type == 'pool' ) {
            frm.attr('action', '/revenues/search/pool_add/')
        } else if( action_type == 'truk_tiba' ) {
            frm.attr('action', '/revenues/search/truk_tiba_add/')
        } else if( action_type == 'nopol' ) {
            var data_action = $(this).attr('data-action');

            frm.attr('action', '/trucks/search/'+data_action+'/')
        }
        frm.submit();
    });

    $('.change-link').change(function() {
        var id = $(this).val();
        var url = $(this).attr('url') + '/' + id;

        location.href = url;

        return false;
    });

    $('.change-plat').click(function() {
        if($(this).is(':checked')) {
            $('.content-plat').removeClass('hide');
        } else {
            $('.content-plat').addClass('hide');
        }
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

    $('#getTtujInfo').change(function() {
        var self = $(this);

        if( self.val() != '' ) {
            $.ajax({
                url: '/ajax/getInfoTtuj/'+self.val()+'/',
                type: 'POST',
                success: function(response, status) {
                    $('#ttuj-info').html($(response).filter('#form-ttuj-main').html());
                    $('#detail-tipe-motor').html($(response).filter('#form-ttuj-detail').html());

                    choose_item_info();
                    add_custom_field();
                    input_number();
                    price_tipe_motor();
                    delete_custom_field();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {
            $('#getKotaTujuan').val('').attr('readonly', true);
            $('#getTruck').val('').attr('readonly', true);
            $('#getInfoTruck').val('').attr('readonly', true);
            $('.driver_name').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#laka-driver-change').change(function(){
        var self = $(this);
        var val = self.val();

        $.ajax({
            url: '/ajax/getInfoLaka/'+val+'/',
            type: 'POST',
            success: function(response, status) {
                $('#nopol-laka').html($(response).filter('#nopol-laka').html());
                $('#city-laka').html($(response).filter('#destination-laka').html());
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    });

    price_tipe_motor();

    $('#getTtujCustomerInfo').change(function(){
        var self = $(this);
        var val = self.val();

        $.ajax({
            url: '/ajax/getTtujCustomerInfo/'+val+'/',
            type: 'POST',
            success: function(response, status) {
                $('#detail-customer-info').html(response);
                add_custom_field();
                choose_item_info();
                delete_custom_field();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    });
});

var price_tipe_motor = function(){
    $('.price-tipe-motor').keyup(function(){
        getTotalLKU( $(this) );
    });

    $('.claim-number').change(function(){
        getTotalLKU( $(this) );
    });

    $('.price-lku').keyup(function(){
        var self = $(this);
        var val = self.val();
        var max_price = self.attr('max_price');

        if(val > parseInt(max_price)){
            return false;
        }else{
            getTotalLkuPayment();
        }
    });
}

function getTotalLkuPayment(){
    var target = $('.price-lku');
    var length = target.length;

    var grand_total = 0;
    for (var i = 0; i < length; i++) {
        if(typeof target[i] != 'undefined' && target[i].value != 0){
            grand_total += parseInt(target[i].value);
        }
    }

    $('#grand-total-payment').text('IDR '+grand_total);
}

function getTotalLKU(self){
    var parent = self.parents('tr');
    var qty = parent.find('td.qty-tipe-motor .claim-number').val();
    var val = parent.find('td .price-tipe-motor').val();

    if(typeof qty != 'undefined' && typeof val != 'undefined'){
        total = parseInt(val)*qty;
        parent.find('.total-price-claim').text('IDR '+total);
        grandTotalLku();
    }
}

function grandTotalLku(){
    var claim_number = $('.claim-number');
    var price_tipe_motor = $('.price-tipe-motor');
    var length = claim_number.length;

    var total_price = 0;
    for (var i = 0; i < length; i++) {
        if(typeof claim_number[i] != 'undefined' && typeof price_tipe_motor[i] != 'undefined'){
            total_price += claim_number[i].value * price_tipe_motor[i].value;
        }
    };

    $('#grand-total-lku').text('IDR '+total_price);
}

var choose_item_info = function(){
    $('.lku-choose-tipe-motor').change(function(){
        var self = $(this);
        var ttuj_id = $('#getTtujInfo').val();

        $.ajax({
            url: '/ajax/getColorTipeMotor/'+self.val()+'/'+ttuj_id+'/',
            type: 'POST',
            success: function(response, status) {
                self.parents('tr').find('td.lku-color-motor').html($(response).filter('#color-motor').html());
                self.parents('tr').find('td.qty-tipe-motor').html($(response).filter('#form-qty').html());

                price_tipe_motor();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    });

    $('.lku-choose-ttuj').change(function(){
        var self = $(this);

        $.ajax({
            url: '/ajax/getTtujInfoLku/'+self.val()+'/',
            type: 'POST',
            success: function(response, status) {
                self.parents('tr').find('td.data-nopol').html($(response).filter('#data-nopol').html());
                self.parents('tr').find('td.data-from-city').html($(response).filter('#data-from-city').html());
                self.parents('tr').find('td.data-to-city').html($(response).filter('#data-to-city').html());
                self.parents('tr').find('td.data-total-claim').html($(response).filter('#data-total-claim').html());
                self.parents('tr').find('td.data-total-price-claim').html($(response).filter('#data-total-price-claim').html());

                getTotalLkuPayment();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    });

    qtyMuatanPress( $('.qty-muatan') );
}
choose_item_info();