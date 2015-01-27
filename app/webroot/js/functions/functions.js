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
            var qtyMuatan = parseInt($('#ttujDetail tbody tr .qty-muatan[rel="'+i+'"]').val());

            if( isNaN(qtyMuatan) ) {
                qtyMuatan = 0;
            }
            total_muatan += qtyMuatan;
        };

        $('.total-unit-muatan').html(total_muatan);

        if( isNaN( total_muatan ) || total_muatan == 0 ) {
            total_muatan = 1;
        }

        var uang_jalan_1 = $('.uang_jalan_1_ori').val();
        var uang_jalan_2 = $('.uang_jalan_2').val().replace(/,/gi, "");
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
        var min_capacity = $('.min_capacity').val();
        var uang_jalan_extra = $('.uang_jalan_extra_ori').val();
        var uang_jalan_extra_per_unit = $('.uang_jalan_extra_per_unit').val();

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

        if( uang_jalan_extra != 0 && uang_jalan_extra != '' && min_capacity != 0 && min_capacity != '' ) {
            if( total_muatan > min_capacity ) {
                if( uang_jalan_extra_per_unit == 1 ) {
                    var capacityCost = total_muatan - min_capacity;
                    uang_jalan_extra = uang_jalan_extra*capacityCost;
                }
            } else {
                uang_jalan_extra = 0;
            }
        } else {
            uang_jalan_extra = 0;
        }

        $('.uang_jalan_1').val( formatNumber( uang_jalan_1, 0 ) );
        $('.uang_jalan_2').val( formatNumber( uang_jalan_2, 0 ) );
        $('.uang_kuli_muat').val( formatNumber( uang_kuli_muat, 0 ) );
        $('.uang_kuli_bongkar').val( formatNumber( uang_kuli_bongkar, 0 ) );
        $('.asdp').val( formatNumber( asdp, 0 ) );
        $('.uang_kawal').val( formatNumber( uang_kawal, 0 ) );
        $('.uang_keamanan').val( formatNumber( uang_keamanan, 0 ) );
        $('.uang_jalan_extra').val( formatNumber( uang_jalan_extra, 0 ) );
    });
}

var getUangjalan = function ( response ) {
    $('.truck_capacity').val($(response).filter('#truck_capacity').html());
    $('.driver_name').val($(response).filter('#driver_name').html());

    var total_muatan = parseInt($('.total-unit-muatan').html());
    var uang_jalan_1 = $(response).filter('#uang_jalan_1').html().replace(/,/gi, "");

    if( uang_jalan_1 != 0 ) {
        var uang_jalan_1_ori = uang_jalan_1;
        var uang_jalan_2 = $(response).filter('#uang_jalan_2').html().replace(/,/gi, "");
        var uang_jalan_per_unit = $(response).filter('#uang_jalan_per_unit').html();

        var uang_kuli_muat = $(response).filter('#uang_kuli_muat').html().replace(/,/gi, "");
        var uang_kuli_muat_ori = uang_kuli_muat;
        var uang_kuli_muat_per_unit = $(response).filter('#uang_kuli_muat_per_unit').html();

        var uang_kuli_bongkar = $(response).filter('#uang_kuli_bongkar').html().replace(/,/gi, "");
        var uang_kuli_bongkar_ori = uang_kuli_bongkar;
        var uang_kuli_bongkar_per_unit = $(response).filter('#uang_kuli_bongkar_per_unit').html();

        var asdp = $(response).filter('#asdp').html().replace(/,/gi, "");
        var asdp_ori = asdp;
        var asdp_per_unit = $(response).filter('#asdp_per_unit').html();

        var uang_kawal = $(response).filter('#uang_kawal').html().replace(/,/gi, "");
        var uang_kawal_ori = uang_kawal;
        var uang_kawal_per_unit = $(response).filter('#uang_kawal_per_unit').html();

        var uang_keamanan = $(response).filter('#uang_keamanan').html().replace(/,/gi, "");
        var uang_keamanan_ori = uang_keamanan;
        var uang_keamanan_per_unit = $(response).filter('#uang_keamanan_per_unit').html();

        var uang_jalan_extra = $(response).filter('#uang_jalan_extra').html().replace(/,/gi, "");
        var uang_jalan_extra_ori = uang_jalan_extra;
        var min_capacity = $(response).filter('#min_capacity').html();
        var uang_jalan_extra_per_unit = $(response).filter('#uang_jalan_extra_per_unit').html();

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

        if( uang_jalan_extra != 0 && uang_jalan_extra != '' && min_capacity != 0 && min_capacity != '' ) {
            if( total_muatan > min_capacity ) {
                if( uang_jalan_extra_per_unit == 1 ) {
                    var capacityCost = total_muatan - min_capacity;
                    uang_jalan_extra = uang_jalan_extra*capacityCost;
                }
            } else {
                uang_jalan_extra = 0;
            }
        } else {
            uang_jalan_extra = 0;
        }
    } else {
        var uang_jalan_1 = '';
        var uang_jalan_1_ori = 0;
        var uang_jalan_2 = '';
        var uang_jalan_per_unit = '';
        var uang_kuli_muat = '';
        var uang_kuli_muat_ori = 0;
        var uang_kuli_muat_per_unit = '';
        var uang_kuli_bongkar = '';
        var uang_kuli_bongkar_ori = 0;
        var uang_kuli_bongkar_per_unit = '';
        var asdp = '';
        var asdp_ori = 0;
        var asdp_per_unit = '';
        var uang_kawal = '';
        var uang_kawal_ori = 0;
        var uang_kawal_per_unit = '';
        var uang_keamanan = '';
        var uang_keamanan_ori = 0;
        var uang_keamanan_per_unit = '';

        var uang_jalan_extra = '';
        var uang_jalan_extra_ori = 0;
        var uang_jalan_extra_per_unit = '';
        var min_capacity = 0;
    }

    $('.uang_jalan_1').val( formatNumber( uang_jalan_1, 0 ) );
    $('.uang_jalan_1_ori').val( uang_jalan_1 );
    $('.uang_jalan_per_unit').val( uang_jalan_per_unit );

    $('.uang_jalan_2').val( formatNumber( uang_jalan_2, 0 ) );

    $('.uang_kuli_muat').val( formatNumber( uang_kuli_muat, 0 ) );
    $('.uang_kuli_muat_ori').val( uang_kuli_muat_ori );
    $('.uang_kuli_muat_per_unit').val( uang_kuli_muat_per_unit );

    $('.uang_kuli_bongkar').val( formatNumber( uang_kuli_bongkar, 0 ) );
    $('.uang_kuli_bongkar_ori').val( uang_kuli_bongkar_ori );
    $('.uang_kuli_bongkar_per_unit').val( uang_kuli_bongkar_per_unit );

    $('.asdp').val( formatNumber( asdp, 0 ) );
    $('.asdp_ori').val( asdp_ori );
    $('.asdp_per_unit').val( asdp_per_unit );

    $('.uang_kawal').val( formatNumber( uang_kawal, 0 ) );
    $('.uang_kawal_ori').val( uang_kawal_ori );
    $('.uang_kawal_per_unit').val( uang_kawal_per_unit );

    $('.uang_keamanan').val( formatNumber( uang_keamanan, 0 ) );
    $('.uang_keamanan_ori').val( uang_keamanan_ori );
    $('.uang_keamanan_per_unit').val( uang_keamanan_per_unit );

    $('.uang_jalan_extra').val( formatNumber( uang_jalan_extra, 0 ) );
    $('.uang_jalan_extra_ori').val( uang_jalan_extra_ori );
    $('.uang_jalan_extra_per_unit').val( uang_jalan_extra_per_unit );
    $('.min_capacity').val( min_capacity );
}

$(function() {
	$('.aset-handling').click(function(){
		if($('.aset-handling .aset-handling-form').is(':checked')) {
			$('.neraca-form').prop('disabled', false);
		}else{
			$('.neraca-form').prop('disabled', true);
		}
	});

    $('#sj-handle').click(function(){
        if($('#sj-handle').is(':checked')) {
            $('.sj-date').removeClass('hide');
        }else{
            $('.sj-date').addClass('hide');
            $('.sj-date input').val('');
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

                    if( data_custom == 'retail' ) {
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
                case 'target-unit':
                    var class_count = $('#box-field-input .list-month');
                    var length = parseInt(class_count.length);
                    var idx = length+1;

                    $('#box-field-input').append('<div rel="'+(idx-1)+'" class="list-month form-group"> \
                        <div class="row"> \
                            <div class="col-sm-6"> \
                                <select name="data[CustomerTargetUnitDetail][month]['+(idx-1)+']" class="form-control"> \
                                '+$('#target_unit select').html()+' \
                                </select> \
                            </div> \
                            <div class="col-sm-5"> \
                                <input name="data[CustomerTargetUnitDetail][unit]['+(idx-1)+']" class="form-control" placeholder="Target Unit" type="text"> \
                            </div> \
                            <div class="col-sm-1 no-pleft"> \
                                <a href="javascript:" class="delete-custom-field has-danger" action_type="target-unit"><i class="fa fa-times"></i></a> \
                            </div> \
                        </div> \
                    </div>');
                    delete_custom_field( $('#box-field-input .list-month:last-child .delete-custom-field') );
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
            $('#getInfoTruck').val('').attr('disabled', true);
            $('.driver_name').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#getTruck').change(function() {
        var self = $(this);
        // var customer_id = $('.customer').val();
        var from_city_id = $('.from_city #getKotaTujuan').val();
        var nopol = $('#getInfoTruck').val();

        if( self.val() != '' ) {
            // $.ajax({
            //     // url: '/ajax/getNopol/'+from_city_id+'/'+self.val()+'/'+'/'+customer_id+'/',
            //     url: '/ajax/getNopol/'+from_city_id+'/'+self.val()+'/',
            //     type: 'POST',
            //     success: function(response, status) {
            $('.truck_id #getInfoTruck').attr('disabled', false);
            
            if( nopol != '' ) {
                $.ajax({
                    url: '/ajax/getInfoTruck/' + from_city_id + '/' + self.val() + '/' + nopol + '/',
                    type: 'POST',
                    success: function(response, status) {
                        getUangjalan( response );
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                        return false;
                    }
                });
            }
            //     },
            //     error: function(XMLHttpRequest, textStatus, errorThrown) {
            //         alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
            //         return false;
            //     }
            // });
        } else {
            $('#getInfoTruck').val('').attr('disabled', true);
            $('.driver_name').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#getInfoTruck').change(function() {
        var self = $(this);
        var from_city_id = $('.from_city #getKotaTujuan').val();
        var to_city_id = $('.to_city #getTruck').val();

        if( self.val() != '' ) {
            $.ajax({
                url: '/ajax/getInfoTruck/' + from_city_id + '/' + to_city_id + '/' + self.val() + '/',
                type: 'POST',
                success: function(response, status) {
                    getUangjalan( response );
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {
            $('.driver_name').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
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
                } else if( action_type == 'revenue_detail'){
                    self.parents('tr').remove();
                    grandTotalRevenue();
                } else if( action_type == 'alocation' ) {
                    var length = parseInt($('#advance-box-field-input .list-alocation').length);
                    var parent = self.parents('.list-alocation');
                    parent.remove();
                } else if( action_type == 'target-unit' ) {
                    var length = parseInt($('#box-field-input .list-month').length);
                    var parent = self.parents('.list-month');
                    parent.remove();
                }
            }

            return false;
        });
    }
    delete_custom_field();

    var duplicate_row = function(){
        $('.duplicate-row').click(function(){
            var self = $(this);
            var parent = self.parents('tr');
            var tag_element = parent.html();
            var html = '<tr>'+
                '<td class="city-data">'+$(tag_element).filter('.city-data').html()+'</td>'+
                '<td class="no-do-data" align="center">'+$(tag_element).filter('.no-do-data').html()+'</td>'+
                '<td class="no-sj-data">'+$(tag_element).filter('.no-sj-data').html()+'</td>'+
                '<td class="tipe-motor-data">'+$(tag_element).filter('.tipe-motor-data').html()+'</td>'+
                '<td class="qty-tipe-motor-data" align="center">'+$(tag_element).filter('.qty-tipe-motor-data').html()+'</td>'+
                '<td class="price-data">'+$(tag_element).filter('.price-data').html()+'</td>'+
                '<td class="total-price-revenue" align="right">'+$(tag_element).filter('.total-price-revenue').html()+'</td>'+
                '<td class="handle-row"><a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="revenue_detail" title="hapus baris"><i class="fa fa-times"></i></a></td>'+
            '</tr>';
            
            parent.after(html);

            delete_custom_field();
            revenue_detail();
            grandTotalRevenue();
        });
    }
    duplicate_row();

    if( $('.timepicker').length > 0 ) {
        $('.timepicker').timepicker({
            showMeridian: false
        });
    }

    if( $('.date-range').length > 0 ) {
        $('.date-range').daterangepicker();
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
            $('#getInfoTruck').val('').attr('disabled', true);
            $('.driver_name').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#getTtujInfoRevenue').change(function() {
        var self = $(this);

        if( self.val() != '' ) {
            $.ajax({
                url: '/ajax/getInfoTtujRevenue/'+self.val()+'/',
                type: 'POST',
                success: function(response, status) {
                    $('#ttuj-info').html($(response).filter('#form-ttuj-main').html());
                    $('#detail-tipe-motor').html($(response).filter('#form-ttuj-detail').html());

                    revenue_detail();
                    duplicate_row();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
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

    $('.popover-hover-bottom').popover({
        trigger: 'hover',
        html: true,
        placement: 'bottom'
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

    $('#grand-total-payment').text('IDR '+formatNumber(grand_total));
}

function getTotalLKU(self){
    var parent = self.parents('tr');
    var qty = parent.find('td.qty-tipe-motor .claim-number').val();
    var val = parent.find('td .price-tipe-motor').val();

    if(typeof qty != 'undefined' && typeof val != 'undefined'){
        total = parseInt(val)*qty;
        parent.find('.total-price-claim').text('IDR '+formatNumber(total));
    }else{
        parent.find('.total-price-claim').text('IDR 0');
    }
    grandTotalLku();
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

    $('#grand-total-lku').text('IDR '+formatNumber(total_price));
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

    $('.list-field input[type="checkbox"]').click(function(){
        var data_field = $(this).attr('data-field');
        var data_parent = $(this).attr('data-parent');
        var idx = 0;

        if($(this).is(':checked')) {
            $('.'+data_field).removeClass('hide');
            idx = 1;
        } else {
            $('.'+data_field).addClass('hide');
            idx = -1;
        }

        if( typeof data_parent != 'undefined' ) {
            var parent = $('.'+data_parent);
            var colspan = parseInt(parent.attr('colspan')) + idx;

            if( colspan == 0 ) {
                parent.addClass('hide');
            } else {
                parent.removeClass('hide');
            }
            
            parent.attr('colspan', colspan);
        }
    });

    $('.list-field a.show').click(function(){
        $('.list-field ul').slideToggle('fast');
    });

    $('.list-field a.close').click(function(){
        $('.list-field ul').slideUp('fast');
    });

    qtyMuatanPress( $('.qty-muatan') );
}
choose_item_info();
function grandTotalRevenue(){
    var qty = $('.revenue-qty');
    var price = $('.price-unit-revenue');
    var length = price.length;

    var total = 0;
    for (var i = 0; i < length; i++) {
        if(typeof qty[i] != 'undefined' && qty[i] != '' && typeof price[i] != 'undefined' && price[i] != ''){
            total += price[i].value * qty[i].value;
        }
    };

    $('#grand-total-revenue').html('IDR '+formatNumber(total));

    var ppn = 0;
    var revenue_ppn = $('.revenue-ppn').val();
    if(typeof revenue_ppn != 'undefined' && revenue_ppn != ''){
        ppn = total * (parseInt(revenue_ppn) / 100);
    }
    $('#ppn-total-revenue').html(formatNumber(ppn));

    var pph = 0;
    var revenue_pph = $('.revenue-pph').val();
    if(typeof revenue_pph != 'undefined' && revenue_pph != ''){
        pph = total * (parseInt(revenue_pph) / 100);
    }
    $('#pph-total-revenue').html(formatNumber(pph));
    
    if(pph > 0){
        total -= pph;
    }
    if(ppn > 0){
        total += ppn;
    }

    $('#all-total-revenue').html('IDR '+formatNumber(total));
}
var revenue_detail = function(){
    $('.revenue-qty').keyup(function(event){
        var self = $(this);
        var max = parseInt(self.attr('max'));
        var val = parseInt(self.val());

        if(val > max){
            alert('qty tidak boleh lebih besar dari '+max)
            self.val(max);
        }else if(val <= 0){
            self.val(1);
        }
        total_revenue_unit(self);
    });

    $('.price-unit-revenue').keyup(function(){
        total_revenue_unit(self);
    });

    $('.revenue-pph, .revenue-ppn').keyup(function(){
        grandTotalRevenue();
    });

    function total_revenue_unit(self){
        var target = self.parents('tr');
        var price = target.find('.price-unit-revenue').val();
        var qty = target.find('.revenue-qty').val();

        total = 0;
        if(typeof qty != 'undefined' && qty != '' && typeof price != 'undefined' && price != ''){
            total += price * qty;
            target.find('.total-price-revenue').html('IDR '+formatNumber(total));
        }

        grandTotalRevenue();
    }
}

revenue_detail();