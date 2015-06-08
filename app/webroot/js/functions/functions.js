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

var calcUangJalan = function () {
    var qtyLen = $('#ttujDetail tbody tr').length;
    var uang_jalan_1 = $('.uang_jalan_1_ori').val();
    var uang_kuli_muat = $('.uang_kuli_muat_ori').val();
    var uang_kuli_bongkar = $('.uang_kuli_bongkar_ori').val();
    var uang_kawal = $('.uang_kawal_ori').val();
    var asdp = $('.asdp_ori').val();
    var uang_keamanan = $('.uang_keamanan_ori').val();
    var commission = $('.commission_ori').val();
    var uang_jalan_extra = $('.uang_jalan_extra_ori').val();
    var min_capacity = $('.min_capacity').val();
    var commission_extra = $('.commission_extra_ori').val();
    var uang_jalan_2 = $('.uang_jalan_2').val().replace(/,/gi, "");
    var uang_jalan_per_unit = $('.uang_jalan_per_unit').val();
    var uang_kuli_muat_per_unit = $('.uang_kuli_muat_per_unit').val();
    var uang_kuli_bongkar_per_unit = $('.uang_kuli_bongkar_per_unit').val();
    var asdp_per_unit = $('.asdp_per_unit').val();
    var uang_kawal_per_unit = $('.uang_kawal_per_unit').val();
    var uang_keamanan_per_unit = $('.uang_keamanan_per_unit').val();
    var commission_per_unit = $('.commission_per_unit').val();
    var uang_jalan_extra_per_unit = $('.uang_jalan_extra_per_unit').val();
    var commission_extra_per_unit = $('.commission_extra_per_unit').val();
    var total_muatan = 0;
    var uang_jalan_tipe_motor = 0;
    var uang_kuli_muat_tipe_motor = 0;
    var uang_kuli_bongkar_tipe_motor = 0;
    var asdp_tipe_motor = 0;
    var uang_kawal_tipe_motor = 0;
    var uang_keamanan_tipe_motor = 0;
    var commission_tipe_motor = 0;
    var uang_jalan_extra_tipe_motor = 0;
    var min_capacity_uang_jalan_extra = 0;
    var total_uang_jalan_extra = 0;
    var min_capacity_use = 0;
    var commission_extra_tipe_motor = 0;
    var min_capacity_commission_extra = 0;
    var total_commission_extra = 0;

    for (var i = 0; i < qtyLen; i++) {
        var tipe_motor_id = parseInt($('#ttujDetail tbody tr .tipe_motor_id[rel="'+i+'"] option:selected').val());
        var group_motor_id = parseInt($('#group_tipe_motor_id option[value="'+tipe_motor_id+'"]').text());
        var qtyMuatan = parseInt($('#ttujDetail tbody tr .qty-muatan[rel="'+i+'"]').val());
        
        if( isNaN(qtyMuatan) ) {
            qtyMuatan = 0;
        }
        total_muatan += qtyMuatan;

        if( typeof $('.uang-jalan-1-'+group_motor_id).html() != 'undefined' ) {
            uang_jalan_tipe_motor += parseInt($('.uang-jalan-1-'+group_motor_id).html()) * qtyMuatan;
        } else {
            uang_jalan_tipe_motor += uang_jalan_1 * qtyMuatan;
        }

        if( typeof $('.uang-kuli-muat-'+group_motor_id).html() != 'undefined' ) {
            uang_kuli_muat_tipe_motor += parseInt($('.uang-kuli-muat-'+group_motor_id).html()) * qtyMuatan;
        } else {
            uang_kuli_muat_tipe_motor += uang_kuli_muat * qtyMuatan;
        }

        if( typeof $('.uang-kuli-bongkar-'+group_motor_id).html() != 'undefined' ) {
            uang_kuli_bongkar_tipe_motor += parseInt($('.uang-kuli-bongkar-'+group_motor_id).html()) * qtyMuatan;
        } else {
            uang_kuli_bongkar_tipe_motor += uang_kuli_bongkar * qtyMuatan;
        }

        if( typeof $('.asdp-'+group_motor_id).html() != 'undefined' ) {
            asdp_tipe_motor += parseInt($('.asdp-'+group_motor_id).html()) * qtyMuatan;
        } else {
            asdp_tipe_motor += asdp * qtyMuatan;
        }

        if( typeof $('.uang-kawal-'+group_motor_id).html() != 'undefined' ) {
            uang_kawal_tipe_motor += parseInt($('.uang-kawal-'+group_motor_id).html()) * qtyMuatan;
        } else {
            uang_kawal_tipe_motor += uang_kawal * qtyMuatan;
        }

        if( typeof $('.uang-keamanan-'+group_motor_id).html() != 'undefined' ) {
            uang_keamanan_tipe_motor += parseInt($('.uang-keamanan-'+group_motor_id).html()) * qtyMuatan;
        } else {
            uang_keamanan_tipe_motor += uang_keamanan * qtyMuatan;
        }

        if( typeof $('.commission-'+group_motor_id).html() != 'undefined' ) {
            commission_tipe_motor += parseInt($('.commission-'+group_motor_id).html()) * qtyMuatan;
        } else {
            commission_tipe_motor += commission * qtyMuatan;
        }

        if( typeof $('.uang-jalan-extra-'+group_motor_id).html() != 'undefined' ) {
            uang_jalan_extra_tipe_motor = parseInt($('.uang-jalan-extra-'+group_motor_id).html());
            min_capacity_uang_jalan_extra = parseInt($('.uang-jalan-extra-min-capacity-'+group_motor_id).html());


            if( qtyMuatan > min_capacity_uang_jalan_extra ) {
                var capacityCost = qtyMuatan - min_capacity_uang_jalan_extra;
                total_uang_jalan_extra += uang_jalan_extra_tipe_motor*capacityCost;
                min_capacity_use += capacityCost;
            }
        }

        if( typeof $('.commission-extra-'+group_motor_id).html() != 'undefined' ) {
            commission_extra_tipe_motor = parseInt($('.commission-extra-'+group_motor_id).html());
            min_capacity_commission_extra = parseInt($('.commission-extra-min-capacity-'+group_motor_id).html());


            if( qtyMuatan > min_capacity_commission_extra ) {
                var capacityCost = qtyMuatan - min_capacity_commission_extra;
                total_commission_extra += commission_extra_tipe_motor*capacityCost;
                min_capacity_use += capacityCost;
            }
        }
    };

    $('.total-unit-muatan').html(total_muatan);

    if( isNaN( total_muatan ) || total_muatan == 0 ) {
        total_muatan = 1;
        $('.total-unit-muatan').html('');
    }

    if( uang_jalan_per_unit == 1 ) {
        if( uang_jalan_tipe_motor != 0 ) {
            uang_jalan_1 = uang_jalan_tipe_motor;
        }

        uang_jalan_2 = 0;
        $('.wrapper_uang_jalan_2').addClass('hide');
    } else {
        $('.wrapper_uang_jalan_2').removeClass('hide');
    }

    if( uang_kuli_muat_per_unit == 1 ) {
        uang_kuli_muat = uang_kuli_muat_tipe_motor;
    }

    if( uang_kuli_bongkar_per_unit == 1 ) {
        uang_kuli_bongkar = uang_kuli_bongkar_tipe_motor;
    }

    if( asdp_per_unit == 1 ) {
        asdp = asdp_tipe_motor;
    }

    if( uang_kawal_per_unit == 1 ) {
        uang_kawal = uang_kawal_tipe_motor;
    }

    if( uang_keamanan_per_unit == 1 ) {
        uang_keamanan = uang_keamanan_tipe_motor;
    }

    if( commission_per_unit == 1 ) {
        commission = commission_tipe_motor;
    }

    if( total_muatan > min_capacity ) {
        var capacityCost = total_muatan - min_capacity;
        
        if( capacityCost > min_capacity_use ) {
            capacityCost = capacityCost - min_capacity_use;
            total_uang_jalan_extra += uang_jalan_extra*capacityCost;
        }

        if( uang_jalan_extra_per_unit == 1 ) {
            uang_jalan_extra = total_uang_jalan_extra;
        }
    } else {
        uang_jalan_extra = 0;
    }

    if( total_muatan > min_capacity ) {
        var capacityCost = total_muatan - min_capacity;
        
        if( capacityCost > min_capacity_use ) {
            capacityCost = capacityCost - min_capacity_use;
            total_commission_extra += commission_extra*capacityCost;
        }

        if( commission_extra_per_unit == 1 ) {
            commission_extra = total_commission_extra;
        }
    } else {
        commission_extra = 0;
    }

    $('.uang_jalan_1').val( formatNumber( uang_jalan_1, 0 ) );

    if( uang_jalan_2 != 0 ) {
        $('.uang_jalan_2').val( formatNumber( uang_jalan_2, 0 ) );
        $('.wrapper_uang_jalan_2').removeClass('hide');
    } else {
        $('.wrapper_uang_jalan_2').addClass('hide');
    }

    if( uang_kuli_muat != 0 ) {
        $('.uang_kuli_muat').val( formatNumber( uang_kuli_muat, 0 ) );
        $('.wrapper_uang_kuli_muat').removeClass('hide');
    } else {
        $('.wrapper_uang_kuli_muat').addClass('hide');
    }

    if( uang_kuli_bongkar != 0 ) {
        $('.uang_kuli_bongkar').val( formatNumber( uang_kuli_bongkar, 0 ) );
        $('.wrapper_uang_kuli_bongkar').removeClass('hide');
    } else {
        $('.wrapper_uang_kuli_bongkar').addClass('hide');
    }

    if( asdp != 0 ) {
        $('.asdp').val( formatNumber( asdp, 0 ) );
        $('.wrapper_asdp').removeClass('hide');
    } else {
        $('.wrapper_asdp').addClass('hide');
    }

    if( uang_kawal != 0 ) {
        $('.uang_kawal').val( formatNumber( uang_kawal, 0 ) );
        $('.wrapper_uang_kawal').removeClass('hide');
    } else {
        $('.wrapper_uang_kawal').addClass('hide');
    }

    if( uang_keamanan != 0 ) {
        $('.uang_keamanan').val( formatNumber( uang_keamanan, 0 ) );
        $('.wrapper_uang_keamanan').removeClass('hide');
    } else {
        $('.wrapper_uang_keamanan').addClass('hide');
    }

    if( uang_jalan_extra != 0 ) {
        $('.uang_jalan_extra').val( formatNumber( uang_jalan_extra, 0 ) );
        $('.wrapper_uang_jalan_extra').removeClass('hide');
        $('.wrapper_min_capacity').removeClass('hide');
    } else {
        $('.wrapper_uang_jalan_extra').addClass('hide');
        $('.wrapper_min_capacity').addClass('hide');
    }

    if( commission_extra != 0 ) {
        $('.commission_extra').val( formatNumber( commission_extra, 0 ) );
    } else {
        $('.commission_extra').val(0);
    }
};

var qtyMuatanPress = function ( obj ) {
    obj.keyup(function() {
        calcUangJalan();
    });
}

var getUangjalan = function ( response ) {
    $('.truck_capacity').val($(response).filter('#truck_capacity').html());
    $('.driver_name').val($(response).filter('#driver_name').html());
    $('.driver_id').val($(response).filter('#driver_id').html());
    var uang_jalan_1 = $(response).filter('#uang_jalan_1').html().replace(/,/gi, "");

    if( $(response).filter('#sj_outstanding').html() != null ) {
        $('.sj_outstanding').html($(response).filter('#sj_outstanding').html());
    }

    var total_muatan = 0;
    var qtyLen = $('#ttujDetail tbody tr').length;

    // if( uang_jalan_1 != 0 ) {
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

        var commission = $(response).filter('#commission').html().replace(/,/gi, "");
        var commission_ori = commission;
        var commission_extra = $(response).filter('#commission_extra').html().replace(/,/gi, "");
        var commission_extra_ori = commission_extra;
        var commission_per_unit = $(response).filter('#commission_per_unit').html();
        var commission_extra_per_unit = $(response).filter('#commission_extra_per_unit').html();

        var uang_jalan_extra = $(response).filter('#uang_jalan_extra').html().replace(/,/gi, "");
        var uang_jalan_extra_ori = uang_jalan_extra;
        var min_capacity = $(response).filter('#min_capacity').html();
        var uang_jalan_extra_per_unit = $(response).filter('#uang_jalan_extra_per_unit').html();
        $('.list-tipe-motor').html( $(response).filter('#list-tipe-motor').html() );
    // } else {
    //     var uang_jalan_1 = '';
    //     var uang_jalan_1_ori = 0;
    //     var uang_jalan_2 = '';
    //     var uang_jalan_per_unit = '';
    //     var uang_kuli_muat = '';
    //     var uang_kuli_muat_ori = 0;
    //     var uang_kuli_muat_per_unit = '';
    //     var uang_kuli_bongkar = '';
    //     var uang_kuli_bongkar_ori = 0;
    //     var uang_kuli_bongkar_per_unit = '';
    //     var asdp = '';
    //     var asdp_ori = 0;
    //     var asdp_per_unit = '';
    //     var uang_kawal = '';
    //     var uang_kawal_ori = 0;
    //     var uang_kawal_per_unit = '';
    //     var uang_keamanan = '';
    //     var uang_keamanan_ori = 0;
    //     var uang_keamanan_per_unit = '';

    //     var uang_jalan_extra = '';
    //     var uang_jalan_extra_ori = 0;
    //     var uang_jalan_extra_per_unit = '';
    //     var min_capacity = 0;

    //     var commission = '';
    //     var commission_ori = '';
    //     var commission_extra = '';
    //     var commission_extra_ori = '';
    //     var commission_extra_per_unit = '';
    //     var commission_per_unit = '';
    // }

    $('.uang_jalan_1_ori').val( uang_jalan_1 );
    $('.uang_jalan_per_unit').val( uang_jalan_per_unit );

    $('.uang_jalan_2').val( formatNumber( uang_jalan_2, 0 ) );

    $('.uang_kuli_muat_ori').val( uang_kuli_muat_ori );
    $('.uang_kuli_muat_per_unit').val( uang_kuli_muat_per_unit );

    $('.uang_kuli_bongkar_ori').val( uang_kuli_bongkar_ori );
    $('.uang_kuli_bongkar_per_unit').val( uang_kuli_bongkar_per_unit );

    $('.asdp_ori').val( asdp_ori );
    $('.asdp_per_unit').val( asdp_per_unit );

    $('.uang_kawal_ori').val( uang_kawal_ori );
    $('.uang_kawal_per_unit').val( uang_kawal_per_unit );

    $('.uang_keamanan_ori').val( uang_keamanan_ori );
    $('.uang_keamanan_per_unit').val( uang_keamanan_per_unit );

    $('.uang_jalan_extra_ori').val( uang_jalan_extra_ori );
    $('.uang_jalan_extra_per_unit').val( uang_jalan_extra_per_unit );
    $('.min_capacity').val( min_capacity );
    $('.commission_ori').val( commission_ori );
    $('.commission_extra').val( commission_extra );
    $('.commission_extra_ori').val( commission_extra_ori );
    $('.commission_per_unit').val( commission_per_unit );
    $('.commission_extra_per_unit').val( commission_extra_per_unit );

    calcUangJalan();
}

var add_custom_field = function( obj ){
    if( typeof obj == 'undefined' ) {
        obj = $('.add-custom-field');
    }

    obj.click(function (e) {
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
            case 'ttuj':
                var idx = $('#ttujDetail tbody tr').length;
                var optionTipeMotor = $('#tipe_motor_id select').html();
                var optionColorMotor = $('#color_motor_id select').html();
                var additionalField = '';

                if( data_custom == 'retail' ) {
                    var contentOptionCities = $('#data-cities-options select').html();

                    additionalField = '<td> \
                        <select name="data[TtujTipeMotor][city_id][]" class="form-control city-retail-id"> \
                        ' + contentOptionCities +
                        '</select> \
                    </td>';
                }

                $('#ttujDetail tbody').append(' \
                <tr rel="'+idx+'"> \
                    '+ additionalField +
                    '<td> \
                        <select name="data[TtujTipeMotor][tipe_motor_id][]" class="form-control tipe_motor_id" rel="'+idx+'"> \
                        ' + optionTipeMotor +
                        '</select> \
                    </td> \
                    <td> \
                        <select name="data[TtujTipeMotor][color_motor_id][]" class="form-control color_motor_id" rel="'+idx+'"> \
                        ' + optionColorMotor +
                        '</select> \
                    </td> \
                    <td> \
                        <input name="data[TtujTipeMotor][qty][]" class="form-control qty-muatan" type="text" rel="'+idx+'"> \
                    </td> \
                    <td> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="ttuj"><i class="fa fa-times"></i> Hapus</a> \
                    </td> \
                </tr>');

                if( data_custom == 'retail' ) {
                    $('#ttujDetail tbody tr:last-child .city-retail-id').val( $('#getTruck').val() );
                }

                delete_custom_field( $('#ttujDetail tbody tr:last-child .delete-custom-field') );
                qtyMuatanPress( $('#ttujDetail tbody tr:last-child .qty-muatan') );
                $('#ttujDetail tbody tr:last-child .tipe_motor_id').change(function() {
                    getNopol();
                });
                break;
            case 'alocation':
                $('#box-field-input #main-alocation .list-alocation').clone().appendTo('#advance-box-field-input');
                $('#advance-box-field-input .list-alocation:last-child select').val('');
                delete_custom_field( $('#advance-box-field-input .list-alocation:last-child .delete-custom-field') );
                break;
            case 'lku_tipe_motor':
                var content_clone = $('#first-row tr').html();
                var length_option = $('#first-row .lku-choose-tipe-motor option').length-1;
                var tipe_motor_table = $('.tipe-motor-table .lku-choose-tipe-motor').length;

                var lku_detail_len = $('.lku-detail').length+1;

                var html_tr = '<tr class="lku-detail lku-detail-'+lku_detail_len+'" rel="'+lku_detail_len+'">'+content_clone+'</tr>';

                // if( length_option > tipe_motor_table ){
                    $('.tipe-motor-table #field-grand-total-lku').before(html_tr);
                    
                    choose_item_info();
                    input_number();
                    input_price($('.lku-detail-'+lku_detail_len+' .input_price'));
                    price_tipe_motor();
                    delete_custom_field($('.lku-detail-'+lku_detail_len+' .delete-custom-field'));
                    part_motor_lku();
                // }
                break;
            case 'ksu_perlengkapan':
                var content_clone = $('#first-row tr').html();
                var length_option = $('#first-row .ksu-choose-tipe-motor option').length-1;
                var tipe_motor_table = $('.perlengkapan-table .ksu-choose-tipe-motor').length;

                var ksu_detail_len = $('.ksu-detail').length+1;

                var html_tr = '<tr class="ksu-detail ksu-detail-'+ksu_detail_len+'" rel="'+ksu_detail_len+'">'+content_clone+'</tr>';

                // if( length_option > tipe_motor_table ){
                    $('.perlengkapan-table #field-grand-total-ksu').before(html_tr);
                    
                    choose_item_info();
                    input_number();
                    input_price($('.ksu-detail-'+ksu_detail_len+' .input_price'));
                    price_perlengkapan();
                    delete_custom_field($('.ksu-detail-'+ksu_detail_len+' .delete-custom-field'));

                    if($('.handle-atpm').is(':checked')){
                        $('.price-perlengkapan').hide();
                    }
                // }
                break;
            case 'lku_ttuj':
                var content_clone = $('#first-row').html();
                var length_option = $('#first-row .lku-choose-ttuj option').length-1;
                var tipe_motor_table = $('.ttuj-info-table .lku-choose-ttuj').length+1;

                var html_tr = '<tr class="lku-detail lku-detail-'+tipe_motor_table+'" rel="'+tipe_motor_table+'">'+content_clone+'</tr>';

                if( length_option > tipe_motor_table-1 ){
                    $('.ttuj-info-table #field-grand-total-ttuj').before(html_tr);
                    choose_item_info();
                    input_number();
                    price_tipe_motor();
                    delete_custom_field($('.lku-detail-'+tipe_motor_table+' .delete-custom-field'));
                }
            break;
            case 'ksu_ttuj':
                var content_clone = $('#first-row').html();
                var length_option = $('#first-row .ksu-choose-ttuj option').length-1;
                var tipe_motor_table = $('.ttuj-info-table .ksu-choose-ttuj').length+1;

                var html_tr = '<tr class="ksu-detail ksu-detail-'+tipe_motor_table+'" rel="'+tipe_motor_table+'">'+content_clone+'</tr>';

                if( length_option > tipe_motor_table-1 ){
                    $('.ttuj-info-table #field-grand-total-ttuj').before(html_tr);
                    choose_item_info();
                    input_number();
                    price_tipe_motor();
                    delete_custom_field($('.ksu-detail-'+tipe_motor_table+' .delete-custom-field'));
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
            case 'uang_jalan':
                var class_count = $('#box-field-input .list-uang-jalan');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-field-input').append('<div rel="'+(idx-1)+'" class="row list-uang-jalan"> \
                    <div class="col-sm-4"> \
                        <div class="form-group"> \
                            <label for="UangJalanTipeMotorTipeMotorId'+(idx-1)+'">Group Motor</label> \
                            <select name="data[UangJalanTipeMotor][group_motor_id][]" class="form-control" id="UangJalanTipeMotorTipeMotorId'+(idx-1)+'"> \
                                '+$('#tipe_motor select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-6"> \
                        <div class="form-group"> \
                            <label for="UangJalanTipeMotorUangJalan1'+(idx-1)+'">Uang Jalan Pertama</label> \
                            <div class="input-group"> \
                                <span class="input-group-addon">IDR </span> \
                                <input name="data[UangJalanTipeMotor][uang_jalan_1][]" class="form-control input_price" type="text" id="UangJalanTipeMotorUangJalan1'+(idx-1)+'"> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-sm-2"> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="uang_jalan"> \
                            <i class="fa fa-times"></i> Hapus \
                        </a> \
                    </div> \
                </div>');
                input_price( $('#box-field-input .list-uang-jalan:last-child .input_price') );
                delete_custom_field( $('#box-field-input .list-uang-jalan:last-child .delete-custom-field') );

                if( $('.chk-uang-jalan').is(':checked') ) {
                    $('#box-field-input .list-uang-jalan:last-child .uang_jalan_2').addClass('hide');
                } else {
                    $('#box-field-input .list-uang-jalan:last-child .uang_jalan_2').removeClass('hide');
                }
              break;
            case 'uang_jalan_extra':
                var class_count = $('#box-field-input-extra .list-uang-jalan-extra');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-field-input-extra').append('<div rel="'+(idx-1)+'" class="row list-uang-jalan-extra"> \
                    <div class="col-sm-3"> \
                        <div class="form-group"> \
                            <label for="UangExtraGroupMotorGroupMotorId'+(idx-1)+'">Group Motor</label> \
                            <select name="data[UangExtraGroupMotor][group_motor_id][]" class="form-control" id="UangExtraGroupMotorGroupMotorId'+(idx-1)+'"> \
                                '+$('#tipe_motor select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-7"> \
                        <div class="form-group"> \
                            <label for="UangExtraGroupMotorUangJalanExtra'+(idx-1)+'">Uang Jalan Extra</label> \
                            <div class="row"> \
                                <div class="col-sm-4 no-pright"> \
                                    <div class="input-group"> \
                                        <span class="input-group-addon">></span> \
                                        <input name="data[UangExtraGroupMotor][min_capacity][]" class="form-control input_number" type="text" id="UangExtraGroupMotorMinCapacity'+(idx-1)+'"> \
                                    </div> \
                                </div> \
                                <div class="col-sm-8"> \
                                    <div class="input-group"> \
                                        <span class="input-group-addon">IDR </span> \
                                        <input name="data[UangExtraGroupMotor][uang_jalan_extra][]" class="form-control input_price" type="text" id="UangExtraGroupMotorUangJalanExtra'+(idx-1)+'"> \
                                    </div> \
                                </div> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-sm-2"> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="uang_jalan_extra"> \
                            <i class="fa fa-times"></i> Hapus \
                        </a> \
                    </div> \
                </div>');
                input_price( $('#box-field-input-extra .list-uang-jalan-extra:last-child .input_price') );
                delete_custom_field( $('#box-field-input-extra .list-uang-jalan-extra:last-child .delete-custom-field') );
              break;
            case 'commission':
                var class_count = $('#box-field-input-commission .list-commission');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-field-input-commission').append('<div rel="'+(idx-1)+'" class="row list-commission"> \
                    <div class="col-sm-4"> \
                        <div class="form-group"> \
                            <label for="CommissionGroupMotorGroupMotorId'+(idx-1)+'">Group Motor</label> \
                            <select name="data[CommissionGroupMotor][group_motor_id][]" class="form-control" id="CommissionGroupMotorGroupMotorId'+(idx-1)+'"> \
                                '+$('#tipe_motor select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-6"> \
                        <div class="form-group"> \
                            <label for="CommissionGroupMotorCommission'+(idx-1)+'">Komisi</label> \
                            <div class="input-group"> \
                                <span class="input-group-addon">IDR </span> \
                                <input name="data[CommissionGroupMotor][commission][]" class="form-control input_price" type="text" id="CommissionGroupMotorCommission'+(idx-1)+'"> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-sm-2"> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="commission"> \
                            <i class="fa fa-times"></i> Hapus \
                        </a> \
                    </div> \
                </div>');
                input_price( $('#box-field-input-commission .list-commission:last-child .input_price') );
                delete_custom_field( $('#box-field-input-commission .list-commission:last-child .delete-custom-field') );
              break;
            case 'commission_extra':
                var class_count = $('#box-field-input-commission-extra .list-commission-extra');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-field-input-commission-extra').append('<div rel="'+(idx-1)+'" class="row list-commission-extra"> \
                    <div class="col-sm-3"> \
                        <div class="form-group"> \
                            <label for="CommissionExtraGroupMotorGroupMotorId'+(idx-1)+'">Group Motor</label> \
                            <select name="data[CommissionExtraGroupMotor][group_motor_id]['+(idx-1)+']" class="form-control" id="CommissionExtraGroupMotorGroupMotorId'+(idx-1)+'"> \
                                '+$('#tipe_motor select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-7"> \
                        <div class="form-group"> \
                            <label for="CommissionExtraGroupMotorCommission'+(idx-1)+'">Komisi Extra</label> \
                            <div class="row"> \
                                <div class="col-sm-4 no-pright"> \
                                    <div class="input-group"> \
                                        <span class="input-group-addon">></span> \
                                        <input name="data[CommissionExtraGroupMotor][min_capacity]['+(idx-1)+']" class="form-control input_number" type="text" id="CommissionExtraGroupMotorMinCapacity'+(idx-1)+'"> \
                                    </div> \
                                </div> \
                                <div class="col-sm-8"> \
                                    <div class="input-group"> \
                                        <span class="input-group-addon">IDR </span> \
                                        <input name="data[CommissionExtraGroupMotor][commission]['+(idx-1)+']" class="form-control input_price" type="text" id="CommissionExtraGroupMotorCommission'+(idx-1)+'"> \
                                    </div> \
                                </div> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-sm-2"> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="commission_extra"> \
                            <i class="fa fa-times"></i> Hapus \
                        </a> \
                    </div> \
                </div>');
                input_price( $('#box-field-input-commission-extra .list-commission-extra:last-child .input_price') );
                delete_custom_field( $('#box-field-input-commission-extra .list-commission-extra:last-child .delete-custom-field') );
              break;
            case 'asdp':
                var class_count = $('#box-field-input-asdp .list-asdp');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-field-input-asdp').append('<div rel="'+(idx-1)+'" class="row list-asdp"> \
                    <div class="col-sm-4"> \
                        <div class="form-group"> \
                            <label for="AsdpGroupMotorGroupMotorId'+(idx-1)+'">Group Motor</label> \
                            <select name="data[AsdpGroupMotor][group_motor_id][]" class="form-control" id="AsdpGroupMotorGroupMotorId'+(idx-1)+'"> \
                                '+$('#tipe_motor select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-6"> \
                        <div class="form-group"> \
                            <label for="AsdpGroupMotorAsdp'+(idx-1)+'">Uang Penyebrangan</label> \
                            <div class="input-group"> \
                                <span class="input-group-addon">IDR </span> \
                                <input name="data[AsdpGroupMotor][asdp][]" class="form-control input_price" type="text" id="AsdpGroupMotorAsdp'+(idx-1)+'"> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-sm-2"> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="asdp"> \
                            <i class="fa fa-times"></i> Hapus \
                        </a> \
                    </div> \
                </div>');
                input_price( $('#box-field-input-asdp .list-asdp:last-child .input_price') );
                delete_custom_field( $('#box-field-input-asdp .list-asdp:last-child .delete-custom-field') );
              break;
            case 'uang_kawal':
                var class_count = $('#box-field-input-uang-kawal .list-uang-kawal');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-field-input-uang-kawal').append('<div rel="'+(idx-1)+'" class="row list-uang-kawal"> \
                    <div class="col-sm-4"> \
                        <div class="form-group"> \
                            <label for="UangKawalGroupMotorGroupMotorId'+(idx-1)+'">Group Motor</label> \
                            <select name="data[UangKawalGroupMotor][group_motor_id][]" class="form-control" id="UangKawalGroupMotorGroupMotorId'+(idx-1)+'"> \
                                '+$('#tipe_motor select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-6"> \
                        <div class="form-group"> \
                            <label for="UangKawalGroupMotorUangKawal'+(idx-1)+'">Uang Kawal</label> \
                            <div class="input-group"> \
                                <span class="input-group-addon">IDR </span> \
                                <input name="data[UangKawalGroupMotor][uang_kawal][]" class="form-control input_price" type="text" id="UangKawalGroupMotorUangKawal'+(idx-1)+'"> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-sm-2"> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="uang_kawal"> \
                            <i class="fa fa-times"></i> Hapus \
                        </a> \
                    </div> \
                </div>');
                input_price( $('#box-field-input-uang-kawal .list-uang-kawal:last-child .input_price') );
                delete_custom_field( $('#box-field-input-uang-kawal .list-uang-kawal:last-child .delete-custom-field') );
              break;
            case 'uang_keamanan':
                var class_count = $('#box-field-input-uang-keamanan .list-uang-kawal');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-field-input-uang-keamanan').append('<div rel="'+(idx-1)+'" class="row list-uang-keamanan"> \
                    <div class="col-sm-4"> \
                        <div class="form-group"> \
                            <label for="UangKeamananGroupMotorGroupMotorId'+(idx-1)+'">Group Motor</label> \
                            <select name="data[UangKeamananGroupMotor][group_motor_id][]" class="form-control" id="UangKeamananGroupMotorGroupMotorId'+(idx-1)+'"> \
                                '+$('#tipe_motor select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-6"> \
                        <div class="form-group"> \
                            <label for="UangKeamananGroupMotorUangKeamanan'+(idx-1)+'">Uang Keamanan</label> \
                            <div class="input-group"> \
                                <span class="input-group-addon">IDR </span> \
                                <input name="data[UangKeamananGroupMotor][uang_keamanan][]" class="form-control input_price" type="text" id="UangKeamananGroupMotorUangKeamanan'+(idx-1)+'"> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-sm-2"> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="uang_keamanan"> \
                            <i class="fa fa-times"></i> Hapus \
                        </a> \
                    </div> \
                </div>');
                input_price( $('#box-field-input-uang-keamanan .list-uang-keamanan:last-child .input_price') );
                delete_custom_field( $('#box-field-input-uang-keamanan .list-uang-keamanan:last-child .delete-custom-field') );
              break;
            case 'uang_kuli':
                var class_count = $('#box-uang-kuli .list-uang-kuli');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-uang-kuli').append('<div rel="'+(idx-1)+'" class="row list-uang-kuli"> \
                    <div class="col-sm-3"> \
                        <div class="form-group"> \
                            <label for="UangKuliGroupMotorId'+(idx-1)+'">Group Motor</label> \
                            <select name="data[UangKuliGroupMotor][group_motor_id][]" class="form-control" id="UangKuliGroupMotorTipeMotorId'+(idx-1)+'"> \
                                '+$('#group_motor select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-5"> \
                        <div class="form-group"> \
                            <label for="UangKuliGroupMotorUangKuli'+(idx-1)+'">Uang Kuli</label> \
                            <div class="input-group"> \
                                <span class="input-group-addon">IDR </span> \
                                <input name="data[UangKuliGroupMotor][uang_kuli][]" class="form-control input_price" type="text" id="UangKuliGroupMotorUangKuli'+(idx-1)+'"> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-sm-1"> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="uang_kuli"> \
                            <i class="fa fa-times"></i> Hapus \
                        </a> \
                    </div> \
                </div>');
                input_price( $('#box-uang-kuli .list-uang-kuli:last-child .input_price') );
                delete_custom_field( $('#box-uang-kuli .list-uang-kuli:last-child .delete-custom-field') );
              break;
            case 'uang_kuli_capacity':
                var class_count = $('#box-uang-kuli-capacity .list-uang-kuli-capacity');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-uang-kuli-capacity').append('<div rel="'+(idx-1)+'" class="row list-uang-kuli-capacity"> \
                    <div class="col-sm-3"> \
                        <div class="form-group"> \
                            <label for="UangKuliCapacityCapacity'+(idx-1)+'">Kapasitas</label> \
                            <input name="data[UangKuliCapacity][capacity][]" class="form-control input_number" type="text" id="UangKuliCapacityCapacity'+(idx-1)+'"> \
                        </div> \
                    </div> \
                    <div class="col-sm-5"> \
                        <div class="form-group"> \
                            <label for="UangKuliCapacityUangKuli'+(idx-1)+'">Uang Kuli</label> \
                            <div class="input-group"> \
                                <span class="input-group-addon">IDR </span> \
                                <input name="data[UangKuliCapacity][uang_kuli][]" class="form-control input_price" type="text" id="UangKuliCapacityUangKuli'+(idx-1)+'"> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-sm-1"> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="uang_kuli"> \
                            <i class="fa fa-times"></i> Hapus \
                        </a> \
                    </div> \
                </div>');
                input_price( $('#box-uang-kuli-capacity .list-uang-kuli-capacity:last-child .input_price') );
                input_number( $('#box-uang-kuli-capacity .list-uang-kuli-capacity:last-child .input_number') );
                delete_custom_field( $('#box-uang-kuli-capacity .list-uang-kuli-capacity:last-child .delete-custom-field') );
              break;
            case 'file-laka':
                var length = $('.laka-form-media input').length + 1;
                var html = '<input type="file" name="data[LakaMedias][name][]" class="form-control" id="LakaMediasName'+length+'">';
                $('.laka-form-media').append(html);
            break;
            case 'leasing': 
                var rel = $('.leasing-body tr.child').length;
                var length = rel + 1;
                var option_form = $('#form-truck-id').html();
                var content_clone = '<tr class="child child-'+length+'" rel="'+length+'">'+
                    '<td>'+option_form+'</td>'+
                    '<td align="right box-price">'+
                        '<input name="data[LeasingDetail][price][]" class="form-control price-leasing-truck input_price input_number" value="0" type="text" id="LeasingDetailPrice">'+
                    '</td>'+
                    '<td class="action-table">'+
                        '<a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="leasing_first"><i class="fa fa-times"></i> Hapus</a>'+
                    '</td>'+
                '</tr>';

                $('.leasing-body #field-grand-total-leasing').before(content_clone);
                input_price( $('#table-leasing tbody.leasing-body tr.child-'+length+' .input_price') );
                delete_custom_field( $('#table-leasing tbody.leasing-body tr.child-'+length+' .delete-custom-field') );
                leasing_action();
            break;
            case 'auth-cash-bank':
                var count_cash_auth = $('.wrapper-approval-setting').length-1;
                var count_next = count_cash_auth+1;
                var tempContent = $('#form-authorize').html();
                var content = '<div class="box box-primary wrapper-approval-setting" rel="'+count_next+'"> \
                    <div class="box-header"> \
                        <h3 class="box-title">List Approval #'+(count_next+1)+'</h3> \
                        <div class="pull-right box-tools"> \
                            <button class="btn btn-danger btn-sm" data-widget="remove" data-toggle="tooltip" title="" data-original-title="Remove"><i class="fa fa-times"></i></button> \
                        </div> \
                    </div> \
                    <div class="box-body"> \
                        <div class="form-group"> \
                            <label for="CashBankDetailMinAmount'+count_next+'">Range Jumlah Approval *</label> \
                            <div class="row"> \
                                <div class="col-sm-6"> \
                                    <input name="data[CashBankDetail][min_amount]['+count_next+']" class="form-control" placeholder="Jumlah dari" type="text" id="CashBankDetailMinAmount'+count_next+'"> \
                                </div> \
                                <div class="col-sm-6"> \
                                    <input name="data[CashBankDetail][max_amount]['+count_next+']" class="form-control" placeholder="Sampai Jumlah" type="text" id="CashBankDetailMaxAmount'+count_next+'"> \
                                </div> \
                            </div> \
                        </div> \
                        <div class="form-group action"> \
                            <a href="javascript:" class="btn btn-success add-custom-field btn-xs" action_type="auth-cash-bank-user-approval"><i class="fa fa-plus-square"></i> Tambah Otorisasi</a> \
                        </div> \
                        <table class="table table-bordered"> \
                            <thead> \
                                <tr> \
                                    <th>Nama Approval</th> \
                                    <th>Grup</th> \
                                    <th class="text-center">Approval Prioritas</th> \
                                    <th class="text-center">Action</th> \
                                </tr> \
                            </thead> \
                            <tbody class="cashbanks-auth-table"> \
                                <tr class="cash-auth-row" id="cash-auth" rel="0"> \
                                    <td> \
                                        <div class="col-sm-10"> \
                                            '+tempContent+' \
                                            <input type="hidden" name="data[CashBankAuthMaster][id]['+count_next+'][]" id="CashBankAuthMasterId'+count_next+'"> \
                                        </div> \
                                        <div class="col-sm-2"> \
                                            <a href="/ajax/getUserEmploye/0" class="btn bg-maroon ajaxModal" title="Data Karyawan" data-action="browse-form" data-change="cash-bank-auth-user-0"><i class="fa fa-search"></i></a> \
                                        </div> \
                                        <div class="clear"></div> \
                                    </td> \
                                    <td class="group_auth text-center"> \
                                        - \
                                    </td> \
                                    <td class="text-center"> \
                                        <label> \
                                            <input type="hidden" name="data[CashBankAuthMaster][is_priority]['+count_next+'][]" id="CashBankAuthMasterIsPriority'+count_next+'" value="0"><input type="checkbox" name="data[CashBankAuthMaster][is_priority]['+count_next+'][]" value="1" id="CashBankAuthMasterIsPriority'+count_next+'"> \
                                        </label> \
                                    </td> \
                                    <td class="action text-center"> \
                                        <a href="javascript:" class="btn btn-danger delete-custom-field btn-xs" action_type="auth-cash-bank-user-approval" rel="0"><i class="fa fa-times-circle"></i></a> \
                                    </td> \
                                </tr> \
                            </tbody> \
                        </table> \
                    </div> \
                </div>';

                $('.list-approval-setting').append(content);

                var objParentTable = '.wrapper-approval-setting[rel="'+count_next+'"]';
                var objParentTr = objParentTable + ' #cash-auth[rel="0"]';

                $(objParentTr+' .cash-bank-auth-user').addClass('cash-bank-auth-user-0');
                $(objParentTr+' .cash-bank-auth-user').attr('name', 'data[CashBankAuthMaster][employe_id]['+count_next+'][]');

                set_auth_cash_bank( objParentTr+' .cash-bank-auth-user' );
                delete_custom_field( $(objParentTr+' .delete-custom-field') );
                add_custom_field( $(objParentTable+' .add-custom-field') );
                ajaxModal( $(objParentTr+' .ajaxModal') );
                pickData();
            break;
            case 'auth-cash-bank-user-approval':
                var parent = self.parents('.wrapper-approval-setting');
                var rel = parent.attr('rel');
                var count_cash_auth = $('.cash-auth-row').length-1;
                var count_next = count_cash_auth+1;
                var tempContent = $('#form-authorize').html();
                var content = '<tr class="cash-auth-row" id="cash-auth" rel="'+count_next+'"> \
                    <td> \
                        <div class="col-sm-10">'+
                            tempContent +
                            '<input type="hidden" name="data[CashBankAuthMaster][id]['+rel+'][]" id="CashBankAuthMasterId'+rel+count_next+'"> \
                        </div> \
                        <div class="col-sm-2"> \
                            <a href="/ajax/getUserEmploye/'+count_next+'/" class="btn bg-maroon ajaxModal" title="Data Karyawan" data-action="browse-form" data-change="cash-bank-auth-user-'+count_next+'"><i class="fa fa-search"></i></a> \
                        </div> \
                        <div class="clear"></div> \
                    </td> \
                    <td class="group_auth text-center">-</td> \
                    <td class="text-center"> \
                        <label> \
                            <input type="checkbox" name="data[CashBankAuthMaster][is_priority]['+rel+'][]" value="1" id="CashBankAuthMaster'+rel+count_next+'"> \
                        </label> \
                    </td> \
                    <td class="action text-center"> \
                        <a href="javascript:" class="btn btn-danger delete-custom-field btn-xs" action_type="auth-cash-bank-user-approval" rel="'+count_next+'"><i class="fa fa-times-circle"></i></a> \
                    </td> \
                </tr>';

                var objParentTable = $('.wrapper-approval-setting[rel="'+rel+'"] .cashbanks-auth-table');
                var objParentTr = '#cash-auth[rel="'+count_next+'"]';

                objParentTable.append(content);
                $(objParentTr+' .cash-bank-auth-user').addClass('cash-bank-auth-user-'+count_next);
                $(objParentTr+' .cash-bank-auth-user').attr('name', 'data[CashBankAuthMaster][employe_id]['+rel+'][]');

                set_auth_cash_bank( objParentTr+' .cash-bank-auth-user' );
                delete_custom_field( $(objParentTr+' .delete-custom-field') );
                ajaxModal( $(objParentTr+' .ajaxModal') );
                pickData();
            break;
        }
    });
}

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
                getNopol();
            } else if( action_type == 'perlengkapan' ) {
                var length = parseInt($('#box-field-input .list-perlengkapan .seperator').length);
                var action_type = self.attr('action_type');
                var idx = length;
                $('#'+action_type+(idx-1)).remove();
            } else if( action_type == 'lku_first' || action_type == 'leasing_first' || action_type == 'invoice_first' || action_type == 'cashbank_first' ){
                self.parents('tr').remove();
                grandTotalLku();
                grandTotalLeasing();
                getTotalInvoicePayment();
            } else if( action_type == 'lku_second' || action_type == 'ksu_second'){
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
            } else if( action_type == 'uang_jalan' ) {
                var length = parseInt($('#box-field-input .list-uang-jalan').length);
                var parent = self.parents('.list-uang-jalan');
                parent.remove();
            } else if( action_type == 'uang_jalan_extra' ) {
                var length = parseInt($('#box-field-input-extra .list-uang-jalan-extra').length);
                var parent = self.parents('.list-uang-jalan-extra');
                parent.remove();
            } else if( action_type == 'commission' ) {
                var length = parseInt($('#box-field-input-commission .list-commission').length);
                var parent = self.parents('.list-commission');
                parent.remove();
            } else if( action_type == 'commission_extra' ) {
                var length = parseInt($('#box-field-input-commission-extra .list-commission-extra').length);
                var parent = self.parents('.list-commission-extra');
                parent.remove();
            } else if( action_type == 'asdp' ) {
                var length = parseInt($('#box-field-input-asdp .list-asdp').length);
                var parent = self.parents('.list-asdp');
                parent.remove();
            } else if( action_type == 'uang_kawal' ) {
                var length = parseInt($('#box-field-input-uang-kawal .list-uang-kawal').length);
                var parent = self.parents('.list-uang-kawal');
                parent.remove();
            } else if( action_type == 'uang_keamanan' ) {
                var length = parseInt($('#box-field-input-uang-keamanan .list-uang-keamanan').length);
                var parent = self.parents('.list-uang-keamanan');
                parent.remove();
            } else if( action_type == 'uang_kuli' ) {
                var length = parseInt($('#box-field-input .list-uang-kuli').length);
                var parent = self.parents('.list-uang-kuli');
                parent.remove();
            } else if( action_type == 'uang_kuli_capacity' ) {
                var length = parseInt($('#box-field-input .list-uang-kuli-capacity').length);
                var parent = self.parents('.list-uang-kuli-capacity');
                parent.remove();
            } else if( action_type == 'file-laka' ) {
                var lengthTable = $('.laka-form-media input').length;
                $('#LakaMediasName'+lengthTable).remove();
            } else if( action_type == 'delete-image-laka' ) {
                $.ajax({
                    url: self.attr('href'),
                    type: 'POST',
                    success: function(response, status) {
                        var status = $(response).filter('#status').text();
                        
                        if(status == 'success'){
                            self.parents('li').remove();
                        }else{
                            var msg = $(response).filter('#message').text();
                            if(typeof msg != 'undefined' && msg != ''){
                                alert(msg);
                            }
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                        return false;
                    }
                });

                return false;
            }else if( action_type == 'auth-cash-bank' ){
                var length = $('.cash-auth-row').length;
                $('#cash-auth-'+length).remove();
            }else if( action_type == 'auth-cash-bank-user-approval' ){
                var parent = self.parents('.wrapper-approval-setting');
                var rel = self.attr('rel');
                parent.find('.cash-auth-row[rel="'+rel+'"]').remove();
            }
        }

        return false;
    });
}

var input_number = function ( obj ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.input_number');
    }

    obj.keypress(function(event) {    
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

var duplicate_row = function(){
    $('.duplicate-row').click(function(){
        var self = $(this);
        var parent = self.parents('tr');
        var tag_element = parent.html();
        var uniqid = Date.now();
        var html = '<tr rel="'+uniqid+'" class="list-revenue">'+
            '<td class="city-data">'+$(tag_element).filter('.city-data').html()+'</td>'+
            '<td class="no-do-data" align="center">'+$(tag_element).filter('.no-do-data').html()+'</td>'+
            '<td class="no-sj-data">'+$(tag_element).filter('.no-sj-data').html()+'</td>'+
            '<td class="tipe-motor-data">'+$(tag_element).filter('.tipe-motor-data').html()+'</td>'+
            '<td class="qty-tipe-motor-data" align="center">'+$(tag_element).filter('.qty-tipe-motor-data').html()+'</td>'+
            '<td class="additional-charge-data" align="center">'+$(tag_element).filter('.additional-charge-data').html()+'</td>'+
            '<td class="price-data text-right">'+$(tag_element).filter('.price-data').html()+'</td>'+
            '<td class="total-price-revenue text-right">'+$(tag_element).filter('.total-price-revenue').html()+'</td>'+
            '<td class="handle-row"><a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="revenue_detail" title="hapus baris"><i class="fa fa-times"></i></a></td>'+
        '</tr>';

        parent.after(html);

        $('tr[rel="'+uniqid+'"] .city-revenue-change').val(parent.find('.city-revenue-change').val());
        $('tr[rel="'+uniqid+'"] .revenue-qty').val('');
        $('tr[rel="'+uniqid+'"] .additional-charge').attr('checked', false);
        $('tr[rel="'+uniqid+'"] .total-revenue-perunit').html('');
        $('tr[rel="'+uniqid+'"] .total-price-perunit').html('');

        revenue_detail();
        grandTotalRevenue();
        delete_custom_field( $('tr[rel="'+uniqid+'"] .delete-custom-field') );
        city_revenue_change( $('tr[rel="'+uniqid+'"] .city-revenue-change'), $('tr[rel="'+uniqid+'"] .revenue-group-motor') );
        checkCharge( $('tr[rel="'+uniqid+'"] .additional-charge') );

    });
}

var changeDetailRevenue = function ( parent, city_id, group_motor_id, is_charge ) {
    var ttuj_id = $('#getTtujInfoRevenue').val();
    var customer_id = $('.change-customer-revenue').val();
    var to_city_id = $('#toCityId').val();

    if( typeof is_charge == 'undefined' ){
        is_charge = 0;
    }

    $.ajax({
        url: '/ajax/getInfoRevenueDetail/'+ttuj_id+'/'+customer_id+'/'+city_id+'/'+group_motor_id+'/'+is_charge+'/'+to_city_id+'/',
        type: 'POST',
        success: function(response, status) {
            $('.revenue_tarif_type').html($(response).filter('#main_jenis_unit').html());
            parent.find('td.city-data .tarif_angkutan_id').val($(response).filter('#tarif_angkutan_id').val());

            parent.find('td.city-data .tarif_angkutan_type').val($(response).filter('#tarif_angkutan_type').val());
            parent.find('td.price-data').html($(response).filter('#price-data').html());
            parent.find('td.qty-tipe-motor-data .jenis_unit').val($(response).find('.jenis_unit').val());
            // parent.find('td.qty-tipe-motor-data').html($(response).filter('#qty-tipe-motor-data').html());
            parent.find('td.additional-charge-data').html($(response).filter('#additional-charge-data').html());

            if( is_charge == 1 ) {
                parent.find('td.total-price-revenue').html(0);
                parent.find('td.total-price-revenue').html($(response).filter('#total-price-revenue').html());
            } else {
                parent.find('td.total-price-revenue').html($(response).filter('#total-price-revenue').html());
            }

            // if( parent.attr('rel') == 0 ) {
            //     parent.find('td.handle-row').html($(response).filter('#handle-row').html());
            // }

            revenue_detail();
            grandTotalRevenue();
            checkCharge( parent.find('td.additional-charge-data .additional-charge') );
            // duplicate_row();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
            return false;
        }
    });
}

var city_revenue_change = function( obj_city, obj_tipe_motor ){
    if( typeof obj_city == 'undefined' ){
        obj_city = $('.city-revenue-change');
    }
    if( typeof obj_tipe_motor == 'undefined' ){
        obj_tipe_motor = $('.revenue-group-motor')
    }

    obj_city.change(function(){
        var self = $(this);
        var val = self.val();
        var parent = self.parents('tr');
        var group_motor_id = parent.find('.revenue-group-motor').val();

        changeDetailRevenue( parent, val, group_motor_id );
    });

    obj_tipe_motor.change(function() {
        var self = $(this);
        var val = self.val();
        var parent = self.parents('tr');
        var city_id = parent.find('.city-revenue-change').val();

        changeDetailRevenue( parent, city_id, val );

        return false;
    });
}

function grandTotalRevenue(){
    var qty = $('.revenue-qty');
    var price = $('.price-unit-revenue');
    var total_price = $('.total-price-perunit');
    var is_additional_charge = $('.additional-charge');
    var jenis_unit = $('.jenis_unit');
    var length = $('.tipe-motor-table tr.list-revenue').length;
    var listRevenue = $('.tipe-motor-table tr.list-revenue');
    var total_temp = $('#total_retail_revenue').val();
    var revenue_tarif_type = $('.revenue_tarif_type').val();
    var total = 0;
    var additional_charge = 0;
    var ppn = 0;
    var revenue_ppn = $('.revenue-ppn').val();
    var formatAdditionalCharge = 0;
    var totalQty = 0;

    for (var i = 0; i < length; i++) {
        var rel = listRevenue[i].getAttribute('rel');

        if( typeof price[i] != 'undefined' && price[i].value != '' ) {
            priceUnit = parseInt(price[i].value);
        } else {
            priceUnit = 0;
        }

        if( typeof qty[i] != 'undefined' && qty[i].value != '' ) {
            qtyUnit = parseInt(qty[i].value);
        } else {
            qtyUnit = 0;
        }

        totalQty += qtyUnit;

        if( revenue_tarif_type != 'per_truck' || is_additional_charge[i].checked == true ) {
            if( is_additional_charge[i].checked == true ) {
                addCharge = parseInt(total_price[i].value);

                if( isNaN(addCharge) ) {
                    addCharge = 0;
                }

                additional_charge += addCharge;
            } else if( typeof jenis_unit[i] != 'undefined' && jenis_unit[i].value == 'per_unit'){
                total += priceUnit * qtyUnit;
            }else{
                total += parseInt(total_price[i].value);
            }
        }

        if( revenue_tarif_type != 'per_truck' ){
            var totalPrice = priceUnit * qtyUnit;
            $('.tipe-motor-table tr.list-revenue[rel="'+rel+'"]').find('.total-revenue-perunit').html('IDR '+formatNumber(totalPrice));
            $('.tipe-motor-table tr.list-revenue[rel="'+rel+'"]').find('.total-price-perunit').val(totalPrice);
        }
    };

    $('#qty-revenue').html(totalQty);

    if( revenue_tarif_type == 'per_truck' ) {
        total = parseInt(total_temp);

        if( isNaN(total) ) {
            total = 0;
        }
    }

    if( revenue_tarif_type != 'per_truck' ) {
        $('#grand-total-revenue').html('IDR '+formatNumber(total));
        $('.tarif_per_truck').val(total);
    } else if( additional_charge != 0 ) {
        total += additional_charge;
        formatAdditionalCharge = 'IDR '+formatNumber(additional_charge);
    }

    $('#additional-total-revenue').html(formatAdditionalCharge);
    $('.additional_charge').val(additional_charge);

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

function calcPPNPPH(){
    var total = parseInt($('#grand-total-revenue').html().replace(/,/gi, "").replace(/IDR/gi, ""));
    var additional_charge = parseInt($('#additional-total-revenue').html().replace(/,/gi, "").replace(/IDR/gi, ""));
    var ppn = 0;
    var pph = 0;
    var revenue_ppn = $('.revenue-ppn').val();
    var revenue_pph = $('.revenue-pph').val();

    if( isNaN(total) ) {
        total = 0;
    }

    if( isNaN(additional_charge) ) {
        additional_charge = 0;
    }

    total += additional_charge;

    if(typeof revenue_ppn != 'undefined' && revenue_ppn != ''){
        ppn = total * (parseInt(revenue_ppn) / 100);
    }

    $('#ppn-total-revenue').html(formatNumber(ppn));

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

var part_motor_lku = function(){
    $('.part-motor-lku').change(function(){
        var self = $(this);
        var val = self.val();

        if(val != ''){
            $.ajax({
                url: '/ajax/getPricePartMotor/'+val+'/',
                type: 'POST',
                success: function(response, status) {
                    self.parents('tr').find('.price-tipe-motor').val($(response).filter('#price').html());

                    price_tipe_motor();

                    self.parents('tr').find('.claim-number').trigger('change');
                    getTotalLKU(self);
                    grandTotalLku();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        }
    });
}

var price_tipe_motor = function(){
    $('.price-tipe-motor').keyup(function(){
        getTotalLKU( $(this) );
    });

    $('.claim-number').keyup(function(){
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

var price_perlengkapan = function(){
    $('.price-perlengkapan, .claim-number').keyup(function(){
        getTotalKSU( $(this) );
    });

    $('.price-perlengkapan, .claim-number').blur(function(){
        getTotalKSU( $(this) );
    });

    $('.price-ksu').keyup(function(){
        var self = $(this);
        var val = self.val();
        var max_price = self.attr('max_price');

        if(val > parseInt(max_price)){
            return false;
        }else{
            getTotalKsuPayment();
        }
    });
}

// function getTotalLkuPayment(){
//     var target = $('.price-lku');
//     var length = target.length;

//     var grand_total = 0;
//     for (var i = 0; i < length; i++) {
//         if(typeof target[i] != 'undefined' && target[i].value != 0){
//             grand_total += parseInt(target[i].value);
//         }
//     }

//     $('#grand-total-payment').text('IDR '+formatNumber(grand_total));
// }

function getTotalKsuPayment(){
    var target = $('.price-ksu');
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
        var arr_string = val.split(',');
        var text_val = '';
        for (var a = 0; a < arr_string.length; a++) {
            text_val += arr_string[a].toString();
        };
        
        val = text_val;
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
            var val = price_tipe_motor[i].value;
            var arr_string = val.split(',');
            var text_val = '';
            for (var a = 0; a < arr_string.length; a++) {
                text_val += arr_string[a].toString();
            };
            
            val = text_val;
            console.log(claim_number[i].value);
            total_price += claim_number[i].value * val;
        }
    };

    $('#grand-total-lku').text('IDR '+formatNumber(total_price));
}

function getTotalKSU(self){
    var parent = self.parents('tr');
    var qty = parent.find('td.qty-perlengkapan .claim-number').val();
    var val = parent.find('td .price-perlengkapan').val();

    if(typeof qty != 'undefined' && typeof val != 'undefined' && !$('.handle-atpm').is(':checked') ){
        var arr_string = val.split(',');
        var text_val = '';
        for (var a = 0; a < arr_string.length; a++) {
            text_val += arr_string[a].toString();
        };
        
        val = text_val;
        total = parseInt(val)*qty;
        parent.find('.total-price-claim').text('IDR '+formatNumber(total));
    }else{
        if(!$('.handle-atpm').is(':checked')){
            parent.find('.total-price-claim').text('IDR 0');
        }
    }
    grandTotalKSU();
}

function grandTotalKSU(){
    var claim_number = $('.claim-number');
    var price_perlengkapan = $('.price-perlengkapan');
    var length = claim_number.length;

    var total_price = 0;
    for (var i = 0; i < length; i++) {
        if(typeof claim_number[i] != 'undefined' && typeof price_perlengkapan[i] != 'undefined'){
            var val = price_perlengkapan[i].value;
            var arr_string = val.split(',');
            var text_val = '';
            for (var a = 0; a < arr_string.length; a++) {
                text_val += arr_string[a].toString();
            };
            
            val = text_val;
            total_price += claim_number[i].value * val;
        }
    };

    $('#grand-total-ksu').text('IDR '+formatNumber(total_price));
}

function grandTotalLeasing(){
    var price_tipe_motor = $('.price-leasing-truck');
    var length = price_tipe_motor.length;
    
    var total_price = 0;
    for (var i = 0; i < length; i++) {
        if(typeof price_tipe_motor[i] != 'undefined'){
            var price = price_tipe_motor[i].value;
            var arr_string = price.split(',');
            var text_val = '';
            for (var a = 0; a < arr_string.length; a++) {
                text_val += arr_string[a].toString();
            };
            
            price = text_val;
            price = parseInt(price);
            total_price += price;
        }
    };

    $('#grand-total-leasing').text('IDR '+formatNumber(total_price));
}

var choose_item_info = function(){
    $('.lku-choose-tipe-motor').change(function(){
        var self = $(this);
        var ttuj_id = $('#getTtujInfo').val();
        var val = self.val();

        if(val != ''){
            $.ajax({
                url: '/ajax/getColorTipeMotor/'+val+'/'+ttuj_id+'/',
                type: 'POST',
                success: function(response, status) {
                    self.parents('tr').find('td.qty-tipe-motor').html($(response).filter('#form-qty').html());

                    price_tipe_motor();
                    part_motor_lku();

                    getTotalLKU(self);
                    grandTotalLku();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        }else{
            self.parents('tr').find('td.qty-tipe-motor input').val(0).attr('readonly', 'readonly');
            getTotalLKU(self);
            grandTotalLku();
        }
    });

    $('.ksu-choose-tipe-motor').change(function(){
        var self = $(this);
        var ttuj_id = $('#getTtujInfoKsu').val();

        $.ajax({
            url: '/ajax/getValuePerlengkapan/'+self.val()+'/'+ttuj_id+'/',
            type: 'POST',
            success: function(response, status) {
                self.parents('tr').find('td.qty-perlengkapan').html($(response).filter('#form-qty').html());

                price_perlengkapan();
                input_price(self.parents('tr').find('td.qty-perlengkapan .input_price'));
                getTotalKSU(self.parents('tr').find('td.qty-perlengkapan .input_price'));
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    });

    $('.lku-choose-ttuj').change(function(){
        var self = $(this);
        // var val_type_lku = $('.type-lku').val();

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

    $('.ksu-choose-ttuj').change(function(){
        var self = $(this);

        $.ajax({
            url: '/ajax/getTtujInfoKsu/'+self.val()+'/',
            type: 'POST',
            success: function(response, status) {
                self.parents('tr').find('td.data-nopol').html($(response).filter('#data-nopol').html());
                self.parents('tr').find('td.data-from-city').html($(response).filter('#data-from-city').html());
                self.parents('tr').find('td.data-to-city').html($(response).filter('#data-to-city').html());
                self.parents('tr').find('td.data-total-claim').html($(response).filter('#data-total-claim').html());
                self.parents('tr').find('td.data-total-price-claim').html($(response).filter('#data-total-price-claim').html());

                getTotalKsuPayment();
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

    if( $(".colorpicker").length > 0 ) {
        $(".colorpicker").colorpicker();
    }

    qtyMuatanPress( $('.qty-muatan') );
}
choose_item_info();

var revenue_detail = function(){
    $('.revenue-qty').keyup(function(event){
        var self = $(this);
        // var max = parseInt(self.attr('max'));
        // var val = parseInt(self.val());

        // if(val > max){
        //     alert('Qty muatan tidak boleh lebih besar dari '+max)
        //     self.val(max);
        // }else if(val <= 0){
        //     self.val(1);
        // }
        total_revenue_unit(self);
    });

    $('.price-unit-revenue').keyup(function(){
        total_revenue_unit(self);
    });

    $('.revenue-pph, .revenue-ppn').keyup(function(){
        calcPPNPPH();
    });
}

function total_revenue_unit(self){
    var target = self.parents('tr');
    var price = target.find('.price-unit-revenue').val();
    var qty = target.find('.revenue-qty').val();
    var jenis_unit = $('.jenis_unit');
    var revenue_tarif_type = $('.revenue_tarif_type').val();
    var total = 0;

    grandTotalRevenue();
}

revenue_detail();
var datepicker = function( obj ){
    if( typeof obj == 'undefined' ) {
        obj = $( ".custom-date" );
    }

    obj.datepicker({
        format: 'dd/mm/yyyy',
    });
}

var timepicker = function( obj ){
    if( typeof obj == 'undefined' ) {
        obj = $('.timepicker');
    }

    if( obj.length > 0 ) {
        obj.timepicker({
            showMeridian: false
        });
    }
}

var daterangepicker = function( obj ){
    if( typeof obj == 'undefined' ) {
        obj = $('.date-range');
    }

    if( obj.length > 0 ) {
        obj.daterangepicker({
            format: 'DD/MM/YYYY',
        });
    }
}

var pickIcon = function(){
    $('.pick-icon select').change(function(){
        var self = $(this);
        var val = self.val();
        var parent = self.parents('.pick-icon');

        parent.children('.pick-icon-show').html( $('.icon-calendar[rel='+val+']').html() );
    });
}

var pickColor = function(){
    $('.pick-color select').change(function(){
        var self = $(this);
        var val = self.val();
        var parent = self.parents('.pick-color');

        parent.children('.pick-color-show').html( '<i class="fa fa-square" style="color: '+$('.color-calendar[rel='+val+']').html()+'"></i>' );
    });
}

var pickData = function () {
    $('.browse-form table tr').click(function(){
        var vthis = $(this);
        var data_value = vthis.attr('data-value');
        var data_change = vthis.attr('data-change');

        if(data_change == '.cash-bank-auth-user'){
            data_change = vthis.attr('data-rel')+' '+data_change;
        }

        $(data_change).val(data_value);
        $(data_change).trigger('change');
        
        if(data_change == '#receiver-id'){
            $('#receiver-type').val(vthis.attr('data-type'));
            $('#cash-bank-user,#ttuj-receiver').val(vthis.attr('data-text'));

            if( vthis.attr('data-type') == 'Driver' ) {
                $('#tag-receiver-type').html('(Supir)');
            } else if( vthis.attr('data-type') == 'Empoye' ) {
                $('#tag-receiver-type').html('(Karyawan)');
            } else {
                $('#tag-receiver-type').html('('+vthis.attr('data-type')+')');
            }
        }

        $('#myModal').modal('hide');
        return false;
    });
}

var ajaxModal = function ( obj, prettyPhoto ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.ajaxModal');
    }
    if( typeof prettyPhoto == 'undefined' ) {
        prettyPhoto = false;
    }

    obj.click(function(msg) {
        var type_action = '';
        var vthis = $(this);
        var url = vthis.attr('href');
        var alert_msg = vthis.attr('alert');
        var url_attr = vthis.attr('url');
        var parent = vthis.attr('data-parent');
        var custom_backdrop_modal = vthis.attr('backdrop-modal');
        var data = false;
        
        $('#myModal .modal-body').html('');

        if( prettyPhoto ) {
            $.prettyPhoto.close();
        }

        if( parent == 1 ) {
            var parents = vthis.parents('form');
            data = parents.serialize();
            url = parents.attr('action');
        }

        if( url == '#myModal' ) {
            url = vthis.attr('url');
        }

        if( url.indexOf('javascript:') != -1 && url_attr != null ) {
            url = url_attr;
        }

        if( alert_msg != null ) {
            if ( !confirm(alert_msg) ) { 
                return false;
            }
        }

        var goAjax = true;
        if( vthis.attr('data-action') !== undefined ) {
            type_action = vthis.attr('data-action');

            if(type_action == 'browse-invoice'){
                if(typeof $('#customer-val, #getTtujCustomerInfo, #getTtujCustomerInfoKsu').val() == 'undefined' || $('#customer-val, #getTtujCustomerInfo, #getTtujCustomerInfoKsu').val() == ''){
                    goAjax = false;
                    alert('Mohon pilih customer terlebih dahulu');
                }else{
                    url += '/'+$('#customer-val, #getTtujCustomerInfo, #getTtujCustomerInfoKsu').val()+'/not-list'
                }
            }
        }

        if(vthis.attr('data-change') == 'laka-ttuj-change' && $('#laka-driver-change').val() != 0){
            url += '/'+$('#laka-driver-change').val();
        }

        if(goAjax == true){
            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function(response, status) {
                    if( vthis.attr('title') !== undefined && vthis.attr('title') != '' ) {
                        $('h4#myModalLabel').html(vthis.attr('title'));
                        $('h4#myModalLabel').show();
                    } else {
                        $('h4#myModalLabel').hide();
                    }

                    if( vthis.attr('data-action') !== undefined ) {
                        type_action = vthis.attr('data-action');
                    }

                    $('#myModal .modal-body').html(response);

                    var backdrop_modal = true;                  
                    if( typeof custom_backdrop_modal != 'undefined' ){
                        backdrop_modal = custom_backdrop_modal;
                    }

                    $('#myModal').modal({
                        show: true,
                        backdrop : backdrop_modal
                    });

                    if( type_action == 'event' ) {
                        $('.popover-hover-top-click.in,.popover-hover-bottom-click.in').trigger('click');
                        pickIcon();
                        pickColor()
                        submitForm();
                        timepicker();
                        datepicker();
                    } else if( type_action == 'browse-form' ) {
                        ajaxModal( $('#myModal .modal-body .pagination li a, #myModal .modal-body .ajaxModal') );
                        pickData();
                        daterangepicker( $('#myModal .modal-body .date-range') );
                    } else if( type_action == 'browse-invoice' || type_action == 'getTtujCustomerInfo' || type_action == 'getTtujCustomerInfoKsu' ) {
                        ajaxModal( $('#myModal .modal-body .pagination li a, #myModal .modal-body .ajaxModal') );
                        pickData();
                        datepicker($('#myModal .modal-body .custom-date'));
                        check_all_checkbox();
                    } else if( type_action == 'browse-cash-banks' || type_action == 'browse-check-docs' ) {
                        ajaxModal( $('#myModal .modal-body .pagination li a, #myModal .modal-body .ajaxModal') );

                        if( type_action == 'browse-check-docs' ) {
                            input_price($('#myModal .modal-body .input_price'));
                            sisa_ttuj($('#myModal .modal-body .sisa-ttuj'));
                            popup_checkbox();
                            daterangepicker( $('#myModal .modal-body .date-range') );
                        } else {
                            check_all_checkbox();
                        }
                    } else if(type_action == 'cancel_invoice'){
                        submitForm();
                        datepicker();
                    }

                    return false;
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        }
        
        return false;
    });

    $('#myModal').on('hidden.bs.modal', function () {
        $('.popover-hover-top-click.in,.popover-hover-bottom-click.in').trigger('click');
        $('.modal-dialog').removeClass('expand-modal');
        $('.modal-content').removeClass('expand-content-modal');
    });
}

var closeModal = function () {
    $('.close-modal').click(function(){
        $('.popover-hover-top-click.in,.popover-hover-bottom-click.in').trigger('click');
        $('#myModal').modal('hide');
        return false;
    });
    $('.close-popup-modal').click(function(){
        $('.popover-hover-top-click.in,.popover-hover-bottom-click.in').trigger('click');
        $('.popup-modal').modal('hide');
        return false;
    });
}

var formInput = function( obj ) {
    obj.click(function(){
        var self = $(this);
        var type_action = self.attr('data-action');

        var parents = self.parents('form');
        var url_ajax = parents.attr('action');
        var data_parsed = self.attr('data-parsed');
        var data = parents.serialize();
        var name = self.val();
        var alert_msg = self.attr('alert');
        $('.dropdown').removeClass('open');

        if( alert_msg != null ) {
            if ( !confirm(alert_msg) ) { 
                return false;
            }
        }

        if( type_action == 'submit_form' ) {
            var form_name = self.attr('form_name');
            $('#'+form_name).submit();
        } else {
            var load_icon = 'Loading..';

            self.html(load_icon).val('Loading..');

            $.ajax({
                data: data,
                url: url_ajax,
                type: 'POST',
                success: function(result) {
                    var output = $(result).find('#form-content').html();
                    var message = $(result).find('#message').html();
                    var status = $(result).find('#status').html();

                    if( message == null ) {
                        message = $(result).filter('#message').html();
                    }

                    if( status == null ) {
                        status = $(result).filter('#status').html();
                    }

                    if( status == 'success' ) {
                        var modal_title = $(result).find('.modal-title').html();
                        var modal_content = $(result).find('.modal-content').html();

                        if( type_action == 'event' || type_action == 'canceled-date' ) {
                            window.location.reload();
                        } else {
                            $('#myModal #myModalLabel').html(modal_title);
                            $('#myModal .modal-body').html(modal_content);
                            $('h4#myModalLabel').show();
                            $('#myModal').modal({
                                show: true,
                            });
                            closeModal();
                        }
                    } else {
                        if(typeof output == 'undefined'){
                            output = $(result).filter('#form-content').html();
                        }
                        $('#form-content').html(output);

                        if( type_action == 'event' || type_action == 'canceled-date') {
                            pickIcon();
                            pickColor()
                            submitForm();
                            $('.timepicker').timepicker({
                                showMeridian: false
                            });
                        }
                    }
                    self.val(name);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });

            return false
        }
        return false;
    });
}

var submitForm = function ( obj ) {
    var obj = (typeof obj == 'undefined' ? $('#btn-submit-form, .btn-submit-form') : obj);
    formInput( obj );
    closeModal();
}

function findInfoTTujRevenue(url){
    $.ajax({
        url: url,
        type: 'POST',
        success: function(response, status) {
            $('#date_revenue').val($(response).filter('#date_revenue').val());
            $('#ttuj-info').html($(response).filter('#form-ttuj-main').html());
            $('#detail-tipe-motor').html($(response).filter('#form-ttuj-detail').html());
            $('#customer-form').html($(response).filter('#form-customer').html());
            $('.revenue_tarif_type').val($(response).filter('#revenue_tarif_type').val());

            revenue_detail();
            duplicate_row();
            datepicker();
            city_revenue_change();
            // change_customer_revenue();
            checkCharge( $('.additional-charge') );
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
            return false;
        }
    });
}

function findInfoTTujPayment(url){
    $.ajax({
        dataType: "json",
        url: url,
        type: 'POST',
        success: function(response, status) {
            $('.total-payment-ttuj').val(formatNumber(response.total));
            $('.customer-name').val(response.customer_name);
            $('.driver-name').val(response.driver_name);
            $('.change-driver-name').val(response.change_driver_name);
            $('.from-city-name').val(response.from_name);
            $('.to-city-name').val(response.to_name);

            if( response.driver_penganti_id != '' ) {
                $('.driver-id').val(response.driver_penganti_id);
            } else if( response.driver_id != '' ) {
                $('.driver-id').val(response.driver_id);
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
            return false;
        }
    });
}

var input_price = function ( obj ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.input_price');
    }

    obj.priceFormat({
        doneFunc: function(obj, val) {
            currencyVal = val;
            currencyVal = currencyVal.replace(/,/gi, "")
            obj.next(".input_hidden").val(currencyVal);
        }
    });
}

var change_customer_revenue = function(){
    $('.change-customer-revenue').change(function(){
        var self = $(this);
        var ttuj_id = $('#getTtujInfoRevenue').val();
        var val_id = self.val();

        if( ttuj_id != '' && self.val() != '' ) {
            findInfoTTujRevenue('/ajax/getInfoTtujRevenue/'+ttuj_id+'/'+val_id+'/');
        }
    });
}

var getNopol = function () {
    var self = $('#truckID');
    var from_city_id = $('.from_city #getKotaTujuan').val();
    var to_city_id = $('.to_city #getTruck').val();
    var customer_id = $('#getKotaAsal').val();

    if( self.val() != '' ) {
        $.ajax({
            url: '/ajax/getInfoTruck/' + from_city_id + '/' + to_city_id + '/' + self.val() + '/' + customer_id + '/',
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
        $('.driver_id').val('');
        $('.truck_capacity').val('');
        $('#biaya-uang-jalan input').val('');

        var qtyLen = $('#ttujDetail tbody tr').length;
        var total_muatan = 0;

        for (var i = 0; i < qtyLen; i++) {
            var qtyMuatan = parseInt($('#ttujDetail tbody tr .qty-muatan[rel="'+i+'"]').val());

            if( isNaN(qtyMuatan) ) {
                qtyMuatan = 0;
            }
            total_muatan += qtyMuatan;
        };

        if( isNaN( total_muatan ) || total_muatan == 0 ) {
            total_muatan = 1;
        }
        $('.total-unit-muatan').html(total_muatan);
    }
}

var checkCharge = function ( obj ) {
    obj.click(function(){
        var parent = $(this).parents('tr');
        var val = 0;
        var city_id = parent.find('.city-revenue-change').val();
        var group_motor_id = parent.find('.revenue-group-motor').val();

        if( $(this).is(':checked') ) {
            val = 1;
        }

        parent.find('.additional-charge-hidden').val(val);
        changeDetailRevenue( parent, city_id, group_motor_id, val );
    });
}
var leasing_action = function(){
    $('.price-leasing-truck').keyup(function(){
        grandTotalLeasing()
    });
}

var invoice_price_payment = function(){
    $('.invoice-price-payment').keyup(function(){
        getTotalInvoicePayment();
    });
}
function getTotalInvoicePayment(){
    var invoice_price = $('.invoice-info-detail .invoice-price-payment');
    var length = invoice_price.length;
    var total = 0;
    var total_price = 0;
    for (var i = 0; i < length; i++) {
        if(typeof invoice_price[i] != 'undefined'){
            var price = invoice_price[i].value;
            var arr_string = price.split(',');
            var text_val = '';
            for (var a = 0; a < arr_string.length; a++) {
                text_val += arr_string[a].toString();
            };
            
            price = text_val;
            price = parseInt(price);
            total = total_price += price;
        }
    };

    var ppn = 0;
    var pph = 0;
    var revenue_ppn = $('.invoice-ppn').val();
    var revenue_pph = $('.invoice-pph').val();

    if( isNaN(total) ) {
        total = 0;
    }

    if(typeof revenue_ppn != 'undefined' && revenue_ppn != ''){
        ppn = total * (parseInt(revenue_ppn) / 100);
    }

    $('#ppn-total-invoice').html(formatNumber(ppn));

    if(typeof revenue_pph != 'undefined' && revenue_pph != ''){
        pph = total * (parseInt(revenue_pph) / 100);
    }
    
    $('#pph-total-invoice').html(formatNumber(pph));
    
    if(pph > 0){
        total -= pph;
    }
    if(ppn > 0){
        total += ppn;
    }

    $('#all-total-invoice').html('IDR '+formatNumber(total));

    $('#grand-total-payment').text('IDR '+formatNumber(total_price));
}
function getTotalLkuPayment(){
    var invoice_price = $('.lku-price-payment');
    var length = invoice_price.length;
    var total = 0;
    var total_price = 0;
    for (var i = 0; i < length; i++) {
        if(typeof invoice_price[i] != 'undefined'){
            var price = invoice_price[i].value;
            var arr_string = price.split(',');
            var text_val = '';
            for (var a = 0; a < arr_string.length; a++) {
                text_val += arr_string[a].toString();
            };
            
            price = text_val;
            price = parseInt(price);
            total = total_price += price;
        }
    };

    var ppn = 0;
    var pph = 0;
    var revenue_ppn = $('.invoice-ppn').val();
    var revenue_pph = $('.invoice-pph').val();

    if( isNaN(total) ) {
        total = 0;
    }

    if(typeof revenue_ppn != 'undefined' && revenue_ppn != ''){
        ppn = total * (parseInt(revenue_ppn) / 100);
    }

    $('#ppn-total-invoice').html(formatNumber(ppn));

    if(typeof revenue_pph != 'undefined' && revenue_pph != ''){
        pph = total * (parseInt(revenue_pph) / 100);
    }
    
    $('#pph-total-invoice').html(formatNumber(pph));
    
    if(pph > 0){
        total -= pph;
    }
    if(ppn > 0){
        total += ppn;
    }

    $('#all-total-invoice').html('IDR '+formatNumber(total));

    $('#grand-total-payment').text('IDR '+formatNumber(total_price));
}
var check_all_checkbox = function(){
    $('.checkAll').click(function(){
        $('.check-option').not(this).prop('checked', this.checked);

        if($('.child-search .check-option').length > 0){
            jQuery.each( $('.child-search .check-option'), function( i, val ) {
                var self = $(this);
                check_option(self)
            });
        }
    });

    $('.checkAll-coa').click(function(){
        $('#box-info-coa .check-option').not(this).prop('checked', this.checked);

        if($('.child-search .check-option').length > 0){
            jQuery.each( $('.child-search .check-option'), function( i, val ) {
                var self = $(this);
                check_option_coa(self)
            });
        }
    });

    $('#box-info-coa .child-search .check-option').click(function(){
        var self = $(this);
        check_option_coa(self)
    });
    
    $('.child-search .check-option').click(function(){
        var self = $(this);
        check_option(self)
    });

    function check_option(self){
        var parent = self.parents('.child-search');
        $('.invoice-info-detail').removeClass('hide');
        var rel_id = parent.attr('rel');

        if(self.is(':checked')){
            if($('.child-'+rel_id).length <= 0){
                var html_content = '<tr class="child child-'+rel_id+'" rel="'+rel_id+'">'+parent.html()+'</tr>';
                $('#field-grand-total-ttuj').before(html_content);

                $('.child-'+rel_id).find('.action-search').removeClass('hide');
                $('.child-'+rel_id).find('.checkbox-detail').remove();

                input_price($('.child-'+rel_id+' .input_price'));
                delete_custom_field($('.child-'+rel_id+' .delete-custom-field'));

                invoice_price_payment();
            }
        }else{
            $('.child-'+rel_id).remove();
            getTotalInvoicePayment();
        } 
    }

    function check_option_coa(self){
        var parent = self.parents('.child-search');
        $('.cashbank-info-detail').removeClass('hide');
        var rel_id = parent.attr('rel');

        if(self.is(':checked')){
            if($('.child-'+rel_id).length <= 0){
                var html_content = '<tr class="child child-'+rel_id+'" rel="'+rel_id+'">'+parent.html()+'</tr>';
                $('.cashbanks-info-table').append(html_content);

                $('.child-'+rel_id).find('.action-search').removeClass('hide');
                $('.child-'+rel_id).find('.checkbox-detail').remove();

                input_price($('.child-'+rel_id+' .input_price'));
                delete_custom_field($('.child-'+rel_id+' .delete-custom-field'));
            }
        }else{
            $('.child-'+rel_id).remove();
        } 
    }
}

var popup_checkbox = function(){
    $('.checkAll').click(function(){
        $('.check-option').not(this).prop('checked', this.checked);

        if($('.child .check-option').length > 0){
            jQuery.each( $('.child .check-option'), function( i, val ) {
                var self = $(this);
                check_option(self)
            });
        }
    });
    
    $('.child .check-option').click(function(){
        var self = $(this);
        check_option(self)
    });

    function check_option(self){
        var parent = self.parents('.child');
        $('.checkbox-info-detail').removeClass('hide');
        var id = parent.attr('data-value');

        if(self.is(':checked')){
            if( $('#checkbox-info-table .child-'+id).length <= 0 ) {
                if($('.child-'+id).length <= 0){
                    var html_content = '<tr class="child child-'+id+'">'+parent.html()+'</tr>';
                    var sisa = convert_number(parent.find('.sisa-ttuj').val(), 'int');

                    $('#checkbox-info-table').append(html_content);
                    $('#checkbox-info-table .child-'+id+' .sisa-ttuj').val(sisa);
                    $('.child-'+id).find('.checkbox-action').remove();
                    $('#checkbox-info-table .child-'+id+' .ttuj-payment-action').removeClass('hide');
                    $('.action-biaya-ttuj').removeClass('hide');

                    input_price( $('.child-'+id+' .input_price') );
                    sisa_ttuj($('#checkbox-info-table .child-'+id+' .sisa-ttuj'));
                    delete_biaya_ttuj($('#checkbox-info-table .child-'+id+' .ttuj-payment-action a'));
                }
            }
        }else{
            $('.child-'+id).remove();
        } 

        if( $('#total-biaya').length > 0 ) {
            calcTotalBiayaTtuj();
        }
    }
}

var laka_ttuj_change = function(){
    $('#laka-ttuj-change').change(function(){
        var self = $(this);
        var val = self.val();
        
        if(val != ''){
            $.ajax({
                url: '/ajax/getInfoLaka/'+val+'/',
                type: 'POST',
                success: function(response, status) {
                    $('#city-laka').html($(response).filter('#destination-laka').html());

                    if($(response).find('#laka-driver-change-id').val() != ''){
                        $('#laka-driver-change-id').val($(response).find('#laka-driver-change-id').val());
                        $('#laka-driver-change-id').attr('disabled', true);
                    }else{
                        $('#laka-driver-change-id').val('');
                        $('#laka-driver-change-id').attr('disabled', false);
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        }else{
            $('#laka-driver-change-id').val('');
            $('#laka-driver-change-id').attr('disabled', false);
        }
    });
}

var set_auth_cash_bank = function(obj){
    $(obj).change(function(){
        var self = $(this);
        var val = self.val();
        var parentList = self.parents('.wrapper-approval-setting');
        var parentTr = self.parents('tr');
        var rel = parentTr.attr('rel');
        var content;

        if(val != ''){
            $.ajax({
                url: '/ajax/getUserEmploye/'+rel+'/'+val+'/',
                type: 'POST',
                success: function(response, status) {
                    parentList.find('#cash-auth[rel="'+rel+'"]').find('.group_auth').html(response);
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        }else{
            $('#cash-auth-'+rel).find('.group_auth').html('-');
        }

        
    });
}

var get_document_cashbank = function(){
    $('#document-id').change(function(){
        var self = $(this);
        var val = self.val();
        var doc_type = $('.cash-bank-handle').val();
        var url = '/ajax/getCustomer/revenue_id:'+val+'/';

        if(doc_type == 'prepayment_in'){
            url = '/ajax/getCustomer/prepayment_id:'+val+'/';
        }

        if(val != ''){
            $.ajax({
                url: url,
                type: 'POST',
                success: function(response, status) {
                    var customer_id = $(response).filter('#customer-id').html();
                    var customer_name = $(response).filter('#customer-name').html();
                    var receiver_type = $(response).filter('#receiver-type').html();
                    var coa_code = $(response).filter('#coa_code').html();
                    var coa_id = $(response).filter('#coa_id').html();
                    var coa_name = $(response).filter('#coa_name').html();
                    var ppn = $(response).filter('#ppn').html();
                    var content_table = $(response).filter('#content-table').html();

                    if( customer_id != '' && typeof customer_id != 'undefined' ) {
                        $('#receiver-type').val(receiver_type);
                        $('#receiver-id').val(customer_id);
                        $('#cash-bank-user').val(customer_name);
                        $('.cashbank-info-detail').removeClass('hide');

                        if(doc_type == 'prepayment_in'){
                            content_table = content_table.replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/opentd/gi, "td").replace(/closetd/gi, "/td").replace(/opentr/gi, "tr").replace(/closetr/gi, "/tr");
                            $('.cashbanks-info-table').empty();
                            $('.cashbanks-info-table').html(content_table);
                            input_price( $('.cashbanks-info-table .child .input_price') );
                        } else {
                            var html_content = '<tr class="child child-'+coa_id+'" rel="'+coa_id+'"> \
                                <td>\
                                    '+coa_code+' \
                                    <input type="hidden" name="data[CashBankDetail][coa_id][]" value="'+coa_id+'" id="CashBankDetailCoaId"> \
                                </td> \
                                <td> \
                                    '+coa_name+' \
                                </td> \
                                <td class="action-search"> \
                                    <input name="data[CashBankDetail][total][]" class="form-control input_price" type="text" id="CashBankDetailTotal" value="'+ppn+'"> \
                                </td> \
                                <td class="action-search"> \
                                    <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="cashbank_first"><i class="fa fa-times"></i> Hapus</a> \
                                </td> \
                            </tr>';
                            $('.cashbanks-info-table').append(html_content);
                            input_price( $('.cashbanks-info-table .child:last-child .input_price') );
                        }
                    } else {
                        $('#receiver-type').val('');
                        $('#receiver-id').val('');
                        $('#cash-bank-user').val('');
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {
            $('#receiver-type').val('');
            $('#receiver-id').val('');
            $('#cash-bank-user').val('');
        }
    });
}

var convert_number = function ( num, type ) {
    num = num.replace(/,/gi, "").replace(/ /gi, "");

    if( type == 'int' ) {
        num = parseInt(num);
    } else if( type == 'float' ) {
        num = parseFloat(num);
    }

    if( isNaN(num) ) {
        num = 0;
    }

    return num;
}

var calcTotalBiayaTtuj = function () {
    var biayaObj = $('#checkbox-info-table .sisa-ttuj');
    var biayaLen = $('#checkbox-info-table .sisa-ttuj').length;
    var totalBiaya = 0;

    for (i = 0; i < biayaLen; i++) {
        totalBiaya += convert_number(biayaObj[i].value, 'int');
    };

    $('#total-biaya').html('IDR '+formatNumber( totalBiaya, 0 ));
}

var sisa_ttuj = function ( obj ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.sisa-ttuj');
    }

    obj.blur(function(){
        var self = $(this);
        var parent = self.parents('tr');
        var total = convert_number(parent.children('.total-ttuj').html(), 'int');
        var sisa = convert_number(self.val(), 'int');

        if( sisa > total ) {
            alert('Sisa tidak boleh melebihi total biaya');
            self.val(formatNumber( total, 0 ));
        } else if( sisa == 0 ) {
            alert('Silahkan isi sisa biaya yang akan dibayar');
        }

        calcTotalBiayaTtuj();
    });
}

var delete_biaya_ttuj = function ( obj ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.ttuj-payment-action a');
    }

    obj.click(function(){
        var self = $(this);
        var id = self.attr('data-id');
        var parent = $('tr.'+id);
        
        if( confirm('Anda ingin menghapus biaya ini?') ) {
            parent.remove();
        }
    });
}

$(function() {
    leasing_action();
    laka_ttuj_change();
    invoice_price_payment();
    part_motor_lku();
    price_tipe_motor();
    price_perlengkapan();
    sisa_ttuj();

    set_auth_cash_bank($('.cash-bank-auth-user'));

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
            $('.biaya-per-unit').removeClass('hide');
        } else {
            $('.uang_jalan_2').removeClass('hide');
            $('.biaya-per-unit').addClass('hide');
        }
    });

    $('.chk-uang-jalan-extra').click(function() {
        var self = $(this);

        if( self.is(':checked') ) {
            $('.uang-extra-unit').removeClass('hide');
        } else {
            $('.uang-extra-unit').addClass('hide');
        }
    });

    $('.chk-commission').click(function() {
        var self = $(this);

        if( self.is(':checked') ) {
            $('.commission-unit').removeClass('hide');
        } else {
            $('.commission-unit').addClass('hide');
        }
    });

    $('.chk-commission-extra').click(function() {
        var self = $(this);

        if( self.is(':checked') ) {
            $('.commission-extra-unit').removeClass('hide');
        } else {
            $('.commission-extra-unit').addClass('hide');
        }
    });

    $('.chk-asdp').click(function() {
        var self = $(this);

        if( self.is(':checked') ) {
            $('.asdp-unit').removeClass('hide');
        } else {
            $('.asdp-unit').addClass('hide');
        }
    });

    $('.chk-uang-kawal').click(function() {
        var self = $(this);

        if( self.is(':checked') ) {
            $('.uang-kawal-unit').removeClass('hide');
        } else {
            $('.uang-kawal-unit').addClass('hide');
        }
    });

    $('.chk-uang-keamanan').click(function() {
        var self = $(this);

        if( self.is(':checked') ) {
            $('.uang-keamanan-unit').removeClass('hide');
        } else {
            $('.uang-keamanan-unit').addClass('hide');
        }
    });

    input_price( $('.input_price') );

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
    //         $('#truckID').val('').attr('readonly', true);
            // $('#truckBrowse').attr('disabled', true);
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
            $('#truckID').val('').attr('disabled', true);
            $('#truckBrowse').attr('disabled', true);
            $('.driver_name').val('');
            $('.driver_id').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#getTruck').change(function() {
        var self = $(this);
        // var customer_id = $('.customer').val();
        var from_city_id = $('.from_city #getKotaTujuan').val();
        var nopol = $('#truckID').val();
        var customer_id = $('#getKotaAsal').val();
        var lenCity = $('.city-retail-id').length;
        var cityRetail = $('.city-retail-id');

        if( self.val() != '' ) {
            // $.ajax({
            //     // url: '/ajax/getNopol/'+from_city_id+'/'+self.val()+'/'+'/'+customer_id+'/',
            //     url: '/ajax/getNopol/'+from_city_id+'/'+self.val()+'/',
            //     type: 'POST',
            //     success: function(response, status) {
            $('#truckID,#truckBrowse').attr('disabled', false);
            
            for (i = 0; i < lenCity; i++) {
                if( cityRetail[i].value == '' ) {
                    cityRetail[i].value = self.val();
                }
            };
            
            if( nopol != '' ) {
                $.ajax({
                    url: '/ajax/getInfoTruck/' + from_city_id + '/' + self.val() + '/' + nopol + '/' + customer_id + '/',
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
            $('#truckID').val('').attr('disabled', true);
            $('#truckBrowse').attr('disabled', true);
            $('.driver_name').val('');
            $('.driver_id').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#truckID,.tipe_motor_id,#getKotaAsal').change(function() {
        getNopol();
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

    
    delete_custom_field();

    $('.submit-form').click(function() {
        var action_type = $(this).attr('action_type');

        if( action_type == 'commit' ) {
            if( confirm('Anda yakin ingin mengunci data ini?') ) {
                $('#is_draft').val(0);
            } else {
                return false;
            }
        } else if(action_type == 'posting' || action_type == 'unposting'){
            if(action_type == 'posting'){
                if( confirm('Anda yakin ingin mengunci posting data ini?') ) {
                    $('#transaction_status').val(action_type);
                } else {
                    return false;
                }
            }else{
                $('#transaction_status').val(action_type);
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
                    input_price();
                    price_tipe_motor();
                    delete_custom_field();
                    part_motor_lku();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {
            $('#getKotaTujuan').val('').attr('readonly', true);
            $('#getTruck').val('').attr('readonly', true);
            $('#truckID').val('').attr('disabled', true);
            $('#truckBrowse').attr('disabled', true);
            $('.driver_name').val('');
            $('.driver_id').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#getTtujInfoKsu').change(function() {
        var self = $(this);
        var atpm = $('.handle-atpm').is(':checked');

        if( self.val() != '' ) {
            $.ajax({
                url: '/ajax/getInfoTtujKsu/'+self.val()+'/'+atpm+'',
                type: 'POST',
                success: function(response, status) {
                    $('#ttuj-info').html($(response).filter('#form-ttuj-main').html());
                    $('#detail-perlengkapan').html($(response).filter('#form-ttuj-detail').html());

                    price_perlengkapan();
                    add_custom_field();
                    input_number();
                    input_price();
                    delete_custom_field();
                    choose_item_info();
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {
            $('#getKotaTujuan').val('').attr('readonly', true);
            $('#getTruck').val('').attr('readonly', true);
            $('#truckID').val('').attr('disabled', true);
            $('#truckBrowse').attr('disabled', true);
            $('.driver_name').val('');
            $('.driver_id').val('');
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#getTtujInfoRevenue').change(function() {
        var self = $(this);

        if( self.val() != '' ) {
            findInfoTTujRevenue('/ajax/getInfoTtujRevenue/'+self.val()+'/');
        }
    });
    // change_customer_revenue();

    $('.ttuj-invoice-ajax').change(function() {
        var self = $(this);
        var data_action = $(this).attr('data-action');

        if( self.val() != '' ) {
            findInfoTTujPayment('/ajax/getInfoTtujPayment/'+self.val()+'/'+data_action+'/');
        }
    });

    $('#laka-driver-change').change(function(){
        var self = $(this);
        var val = self.val();

        $.ajax({
            url: '/ajax/getInfoDriver/'+val+'/',
            type: 'POST',
            success: function(response, status) {
                $('#laka-driver-name').val($(response).filter('#data-driver-name').html());
                $('#laka-no-sim').val($(response).filter('#data-no-sim').html());
                $('#ttuj-form').html($(response).filter('#data-ttuj-form').html());
                $('#laka-ttuj-change').attr('readonly', false);

                laka_ttuj_change();
                $('.supir-pengganti-val').val('');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    });

    price_tipe_motor();

    $('.type-lku').change(function(){
        $('#getTtujCustomerInfo').trigger('change');
    });

    $('#getTtujCustomerInfo').change(function(){
        var self = $(this);
        var val = self.val();
        // var val_type_lku = $('.type-lku').val();

        // $.ajax({
        //     url: '/ajax/getTtujCustomerInfo/'+val+'/',
        //     type: 'POST',
        //     success: function(response, status) {
        //         $('#detail-customer-info').html(response);
        //         add_custom_field();
        //         choose_item_info();
        //         delete_custom_field();
        //     },
        //     error: function(XMLHttpRequest, textStatus, errorThrown) {
        //         alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
        //         return false;
        //     }
        // });
    });

    $('#getTtujCustomerInfoKsu').change(function(){
        var self = $(this);
        var val = self.val();

        // $.ajax({
        //     url: '/ajax/getTtujCustomerInfoKsu/'+val+'/',
        //     type: 'POST',
        //     success: function(response, status) {
        //         $('#detail-customer-info').html(response);
        //         add_custom_field();
        //         choose_item_info();
        //         delete_custom_field();
        //     },
        //     error: function(XMLHttpRequest, textStatus, errorThrown) {
        //         alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
        //         return false;
        //     }
        // });
    });

    $('.popover-hover-bottom').popover({
        trigger: 'hover',
        html: true,
        placement: 'bottom',
        container: 'body',
    });
    $('.popover-hover-top').popover({
        trigger: 'hover',
        html: true,
        placement: 'top',
        container: 'body',
    });
    $('.popover-hover-top-click').popover({
        html: true,
        placement: 'top',
        container: 'body',
    });

    $('.popover-hover-top-click').on('show.bs.popover', function () {
        $('.popover-hover-top-click.in').trigger('click');
    })

    $('.popover-hover-top-click').on('shown.bs.popover', function () {
        $(this).addClass('in');
        ajaxModal($('.popover-content .ajaxModal'));
        $('.popover-close').click(function () {
            $('.popover-hover-top-click.in').trigger('click');
        });
    })

    $('.popover-hover-top-click,.popover-hover-bottom-click').on('hidden.bs.popover', function () {
        $(this).removeClass('in');
    })

    $('.popover-hover-bottom-click').popover({
        html: true,
        placement: 'bottom',
        container: 'body',
    });

    $('.popover-hover-bottom-click').on('show.bs.popover', function () {
        $('.popover-hover-bottom-click').popover('hide');
    })

    $('.popover-hover-bottom-click').on('shown.bs.popover', function () {
        $(this).addClass('in');
        ajaxModal($('.popover-content .ajaxModal'));
        $('.popover-close').click(function () {
            $('.popover-hover-bottom-click').popover('hide');
        });
    })

    if( $("#acos").length > 0 ) {
        $("#acos").treeview({collapsed: true});
        var btn = $('#generate-acl').click(function () {
            btn.button('loading');
            $.get('/user_permissions/sync/', {},
                function(data){
                    btn.button('reset');
                    $("#acos").html(data);
                }
            );        
        })
    }

    duplicate_row();
    ajaxModal();
    city_revenue_change();
    checkCharge( $('.additional-charge') );
    check_all_checkbox();

    $('.custom-find-invoice,#invoiceType').change(function(){
        var customer_id = $('.custom-find-invoice').val();
        var invoiceType = $('#invoiceType').val();
        var url = '/ajax/getInvoiceInfo/'+customer_id+'/';

        if( invoiceType != '' ) {
            url += invoiceType+'/';
        }

        $.ajax({
            url: url,
            type: 'POST',
            success: function(response, status) {
                var message = $(response).filter('#message').val();
                var error = $(response).filter('#status').val();
                
                if( error == 1 ) {
                    $('.custom-find-invoice,#invoiceType').val('');
                    $('.custom-find-invoice,#invoiceType').trigger('change');
                    alert(message);
                } else {
                    $('#invoice-info').html(response);
                    $('#no_invoice').val($(response).filter('#pattern-code').val());
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    });

    $('#preview-invoice').click(function(){
        var self = $(this);
        var rel = self.attr('rel');
        var customer_id = $('.custom-find-invoice').val();
        var invoiceType = $('#invoiceType').val();
        var left = (screen.width/2)-(800/2);
        var top = (screen.height/2)-(460/2);
        var url = '/ajax/previewInvoice/';

        if(typeof customer_id != 'undefined' && customer_id != ''){
            url += customer_id+'/';
        }

        if( invoiceType != '' ) {
            url += invoiceType+'/';
        }

        if(typeof rel != 'undefined' && rel != ''){
            url += rel+'/';
        }

        window.open(url, '1366621940374','width=800,height=460,toolbar=0,menubar=0,location=0,status=0,scrollbars=0,resizable=0,left='+left+',top='+top+'');
    });

    $('.btn-invoice').click(function(){
        if(confirm('Apakah Anda yakin ingin membuat invoice ini?')){
            return true;
        }else{
            return false;
        }
    });

    $('.list-module a').click(function() {
        var self = $(this);
        var url = self.attr('href');

        $.ajax({
            url: url,
            success: function(result) {
                self.children('i').removeClass().addClass(result);
            },
        });

        return false;
    });

    $('.jenis-unit').change(function() {
        var self = $(this);
        var val = self.val();
        var group_motor = $('.group-motor');

        if( val == 'per_truck' ) {
            group_motor.addClass('hide');
        } else {
            group_motor.removeClass('hide');
        }

        return false;
    });

    $('.print-window').click(function(){
        window.print();
    });

    $('.invoice-ajax').change(function(){
        var self = $(this);
        var val = self.val();

        $.ajax({
            url: '/ajax/getInfoInvoicePayment/'+val+'/',
            type: 'POST',
            success: function(response, status) {
                $('#invoice-info').html(response);
                $('#invoice-info').removeClass('hide');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    });

    $('.customer-ajax').change(function(){
        var self = $(this);
        var val = self.val();

        // if(val != ''){
        //     $.ajax({
        //         url: '/ajax/getInfoInvoicePaymentDetail/'+val+'/',
        //         type: 'POST',
        //         success: function(response, status) {
        //             $('#invoice-info').html(response);
        //             $('#invoice-info').removeClass('hide');

        //             delete_custom_field();
        //             input_price();
        //             invoice_price_payment();
        //         },
        //         error: function(XMLHttpRequest, textStatus, errorThrown) {
        //             alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
        //             return false;
        //         }
        //     });
        // }else{
            $('#invoice-info').html('');
            $( '.ttuj-info-table .child' ).remove();
        // }
    });

    timepicker();
    daterangepicker();

    $('.date-resign-handle').click(function(){
        if($('.date-resign-handle input').is(':checked')) {
            $('#resign-date').removeClass('hide');
        }else{
            $('#resign-date').addClass('hide');
        }
    });

    $('.completed-handle').click(function(){
        if($('.completed-handle input').is(':checked')) {
            $('#desc-laka-complete').removeClass('hide');
            $('#date-laka-complete').removeClass('hide');
        }else{
            $('#desc-laka-complete').addClass('hide');
            $('#date-laka-complete').addClass('hide');
        }
    });

    $('.uang_kuli').change(function(){
        if( $(this).val() == 'per_unit' ) {
            $('.biaya-per-unit').removeClass('hide');
            $('.capacity_truck').addClass('hide');
        } else if( $(this).val() == 'per_truck' ) {
            $('.biaya-per-unit').addClass('hide');
            $('.capacity_truck').removeClass('hide');
        } else {
            $('.biaya-per-unit').addClass('hide');
            $('.capacity_truck').addClass('hide');
        }
    });

    $('.submit_butt').click(function(){
        if($('.check-option:checked').length > 0){
            var val = $(this).attr('data-val');
            $('#posting_type').val(val);

            if(confirm('Apakah Anda yakin ingin '+val+' revenue yang Anda pilih?')){
                $('#rev_post_form').submit();
            }
        }else{
            alert('Mohon pilih revenue!')
        }
        return false;
    });

    $('.multiple-modal').on('hide.bs.modal', function () {
        $('.popover-hover-top-click.in,.popover-hover-bottom-click.in').trigger('click');
    });

    $( '.scroll-monitoring' ).scroll(function() {
        $('.popover-hover-top-click.in,.popover-hover-bottom-click.in').trigger('click');
    });

    $('.cash-bank-handle').change(function(){
        var value = $(this).val();

        if(value == 'in' || value == 'ppn_in' || value == 'prepayment_in'){
            $('.cash_bank_user_type').html('Diterima dari');
        }else{
            $('.cash_bank_user_type').html('Dibayar kepada');
        }

        if( value == 'ppn_in' || value == 'prepayment_in' ) {
            $.ajax({
                url: '/ajax/get_cashbank_doc/'+value+'/',
                type: 'POST',
                success: function(response, status) {
                    var formContent = $(response).filter('#form-document').html();
                    var status = $(response).filter('#status').text();

                    if(status == 'success'){
                        $('#form-content-document').html(formContent);
                        ajaxModal( $('#form-content-document .ajaxModal') );
                        get_document_cashbank();
                    } else {
                        $('#form-content-document').html('');
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        } else {
            $('#form-content-document').html('');
        }
    });
    get_document_cashbank();

    $('.invoice-pph, .invoice-ppn').keyup(function(){
        getTotalInvoicePayment();
    });

    $(document).ajaxStart(function() {
        $("#ajaxLoading").slideDown(100);
    }).ajaxStop(function() {
        $("#ajaxLoading").slideUp(200);
    });

    $('.handle-atpm').click(function(){
        if($(this).is(':checked')) {
            $('#atpm-box').removeClass('hide');
            $('.price-perlengkapan').removeClass('show');
            $('.price-perlengkapan').addClass('hide');
            $('.total-ksu').hide();
            $('.total-price-claim').text('-');
            $('#grand-total-ksu').text('IDR 0');
        }else{
            $('#atpm-box').addClass('hide');
            $('.price-perlengkapan').removeClass('hide');
            $('.price-perlengkapan').addClass('show');
            $('.total-ksu').show();
            $('.price-perlengkapan').show();
        }
    });

    if($('.handle-atpm').length > 0){
        if($(this).is(':checked')){
            $('.price-perlengkapan').hide();
        }else{
            $('.price-perlengkapan').show();
        }
    }
    
    $('.employe-field').change(function(){
        var val = $(this).val();

        if(val != ''){
            $.ajax({
                url: '/ajax/getInfoEmploye/'+val+'/',
                type: 'POST',
                success: function(response, status) {
                    $('.first-name-box').val($(response).filter('#data-first-name').html());
                    $('.last-name-box').val($(response).filter('#data-last-name').html());
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        }
    });
});