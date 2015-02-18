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
        var rel = $(this).attr('rel');
        var uang_jalan_1 = $('.uang_jalan_1_ori').val();
        var uang_kuli_muat = $('.uang_kuli_muat_ori').val();
        var uang_kuli_bongkar = $('.uang_kuli_bongkar_ori').val();
        var uang_jalan_tipe_motor = 0;
        var uang_kuli_bongkar_tipe_motor = 0;
        var uang_kuli_muat_tipe_motor = 0;

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
        };

        $('.total-unit-muatan').html(total_muatan);

        if( isNaN( total_muatan ) || total_muatan == 0 ) {
            total_muatan = 1;
        }

        var uang_jalan_2 = $('.uang_jalan_2').val().replace(/,/gi, "");
        var uang_jalan_per_unit = $('.uang_jalan_per_unit').val();
        var uang_kuli_muat_per_unit = $('.uang_kuli_muat_per_unit').val();
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
            uang_jalan_1 = uang_jalan_tipe_motor;
            uang_jalan_2 = 0;
        }

        if( uang_kuli_muat_per_unit == 1 ) {
            uang_kuli_muat = uang_kuli_muat_tipe_motor;
        }

        if( uang_kuli_bongkar_per_unit == 1 ) {
            uang_kuli_bongkar = uang_kuli_bongkar_tipe_motor;
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

    var total_muatan = 0;
    var qtyLen = $('#ttujDetail tbody tr').length;
    var uang_jalan_1 = $(response).filter('#uang_jalan_1').html().replace(/,/gi, "");
    var uang_jalan_tipe_motor = 0;
    var uang_kuli_bongkar_tipe_motor = 0;
    var uang_kuli_muat_tipe_motor = 0;

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
        $('.list-tipe-motor').html( $(response).filter('#list-tipe-motor').html() );

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
        };

        if( isNaN( total_muatan ) || total_muatan == 0 ) {
            total_muatan = 1;
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

        if( uang_kuli_muat_per_unit == 1 && uang_kuli_muat_tipe_motor != 0 ) {
            uang_kuli_muat = uang_kuli_muat_tipe_motor;
        }

        if( uang_kuli_bongkar_per_unit == 1 && uang_kuli_bongkar_tipe_motor != 0 ) {
            uang_kuli_bongkar = uang_kuli_bongkar_tipe_motor;
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
            case 'ttuj':
                var idx = $('#ttujDetail tbody tr').length;
                var optionTipeMotor = $('#tipe_motor_id select').html();
                var optionColorMotor = $('#color_motor_id select').html();
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
                        <select name="data[TtujTipeMotor][tipe_motor_id]['+idx+']" class="form-control tipe_motor_id" rel="'+idx+'"> \
                        ' + optionTipeMotor +
                        '</select> \
                    </td> \
                    <td> \
                        <select name="data[TtujTipeMotor][color_motor_id]['+idx+']" class="form-control color_motor_id" rel="'+idx+'"> \
                        ' + optionColorMotor +
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
            case 'uang_jalan':
                var class_count = $('#box-field-input .list-uang-jalan');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-field-input').append('<div rel="'+(idx-1)+'" class="row list-uang-jalan"> \
                    <div class="col-sm-3"> \
                        <div class="form-group"> \
                            <label for="UangJalanTipeMotorTipeMotorId'+(idx-1)+'">Group Motor</label> \
                            <select name="data[UangJalanTipeMotor][tipe_motor_id]['+(idx-1)+']" class="form-control" id="UangJalanTipeMotorTipeMotorId'+(idx-1)+'"> \
                                '+$('#tipe_motor select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-4"> \
                        <div class="form-group"> \
                            <label for="UangJalanTipeMotorUangJalan1'+(idx-1)+'">Uang Jalan Pertama</label> \
                            <div class="input-group"> \
                                <span class="input-group-addon">IDR </span> \
                                <input name="data[UangJalanTipeMotor][uang_jalan_1]['+(idx-1)+']" class="form-control input_price" type="text" id="UangJalanTipeMotorUangJalan1'+(idx-1)+'"> \
                            </div> \
                        </div> \
                    </div> \
                    <div class="col-sm-1"> \
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
            case 'uang_kuli':
                var class_count = $('#box-uang-kuli .list-uang-kuli');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-uang-kuli').append('<div rel="'+(idx-1)+'" class="row list-uang-kuli"> \
                    <div class="col-sm-3"> \
                        <div class="form-group"> \
                            <label for="UangKuliGroupMotorId'+(idx-1)+'">Group Motor</label> \
                            <select name="data[UangKuliGroupMotor][group_motor_id]['+(idx-1)+']" class="form-control" id="UangKuliGroupMotorTipeMotorId'+(idx-1)+'"> \
                                '+$('#group_motor select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-5"> \
                        <div class="form-group"> \
                            <label for="UangKuliGroupMotorUangKuli'+(idx-1)+'">Uang Kuli</label> \
                            <div class="input-group"> \
                                <span class="input-group-addon">IDR </span> \
                                <input name="data[UangKuliGroupMotor][uang_kuli]['+(idx-1)+']" class="form-control input_price" type="text" id="UangKuliGroupMotorUangKuli'+(idx-1)+'"> \
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
            case 'file-laka':
                var length = $('.laka-form-media input').length + 1;
                var html = '<input type="file" name="data[LakaMedias][name][]" class="form-control" id="LakaMediasName'+length+'">';
                $('.laka-form-media').append(html);
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
            } else if( action_type == 'uang_jalan' ) {
                var length = parseInt($('#box-field-input .list-uang-jalan').length);
                var parent = self.parents('.list-uang-jalan');
                parent.remove();
            } else if( action_type == 'uang_kuli' ) {
                var length = parseInt($('#box-field-input .list-uang-kuli').length);
                var parent = self.parents('.list-uang-kuli');
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
            }
        }

        return false;
    });
}

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

        $('tr[rel="'+uniqid+'"] .city-revenue-change').val('');
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

    if( typeof is_charge == 'undefined' ){
        is_charge = 0;
    }

    $.ajax({
        url: '/ajax/getInfoRevenueDetail/'+ttuj_id+'/'+customer_id+'/'+city_id+'/'+group_motor_id+'/'+is_charge+'/',
        type: 'POST',
        success: function(response, status) {
            parent.find('td.price-data').html($(response).filter('#price-data').html());
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
        var city_id = $('.city-revenue-change').val();

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
    var total_temp = $('#total_retail_revenue').val();
    var revenue_tarif_type = $('.revenue_tarif_type').val();
    var total = 0;
    var additional_charge = 0;
    var ppn = 0;
    var revenue_ppn = $('.revenue-ppn').val();
    var formatAdditionalCharge = 0;

    for (var i = 0; i < length; i++) {
        if( revenue_tarif_type != 'per_truck' || is_additional_charge[i].checked == true ) {
            if( is_additional_charge[i].checked == true ) {
                addCharge = parseInt(total_price[i].value);

                if( isNaN(addCharge) ) {
                    addCharge = 0;
                }

                additional_charge += addCharge;
            } else if(typeof qty[i] != 'undefined' && qty[i] != '' && typeof jenis_unit[i] != 'undefined' && jenis_unit[i].value == 'per_unit'){
                total += parseInt(price[i].value) * parseInt(qty[i].value);
            }else{
                total += parseInt(total_price[i].value);
            }
        }
    };

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

    if( price != '' && qty != '' && revenue_tarif_type != 'per_truck' ){
        total += price * qty;
        target.find('.total-revenue-perunit').html('IDR '+formatNumber(total));
        target.find('.total-price-perunit').val(total);
    }

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
        $(data_change).val(data_value);
        $(data_change).trigger('change');
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
        
        $('.modal-body').html('');

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

                $('.modal-body').html(response);

                var backdrop_modal = true;                  
                if( typeof custom_backdrop_modal != 'undefined' ){
                    backdrop_modal = custom_backdrop_modal;
                }

                $('#myModal').modal({
                    show: true,
                    backdrop : backdrop_modal
                });

                if( type_action == 'event' ) {
                    pickIcon();
                    pickColor()
                    submitForm();
                    $('.timepicker').timepicker({
                        showMeridian: false
                    });
                } else if( type_action == 'browse-form' ) {
                    ajaxModal( $('.modal-body .pagination li a, .modal-body .ajaxModal') );
                    pickData();
                    daterangepicker( $('.modal-body .date-range') );
                }

                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
        
        return false;
    });

    $('#myModal').on('hidden.bs.modal', function () {
        $('.modal-dialog').removeClass('expand-modal');
        $('.modal-content').removeClass('expand-content-modal');
    });
}

var closeModal = function () {
    $('.close-modal').click(function(){
        $('#myModal').modal('hide');
        return false;
    });
    $('.close-popup-modal').click(function(){
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

                        if( type_action == 'event' ) {
                            window.location.reload();
                        } else {
                            $('#myModal #myModalLabel').html(modal_title);
                            $('.modal-body').html(modal_content);
                            $('h4#myModalLabel').show();
                            $('#myModal').modal({
                                show: true,
                            });
                            closeModal();
                        }
                    } else {
                        $('#form-content').html(output);

                        if( type_action == 'event' ) {
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
            change_customer_revenue();
            checkCharge( $('.additional-charge') );
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
            return false;
        }
    });
}

var input_price = function () {
    $('.input_price').priceFormat({
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
        $('.truck_capacity').val('');
        $('#biaya-uang-jalan input').val('');
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

        if( self.val() != '' ) {
            // $.ajax({
            //     // url: '/ajax/getNopol/'+from_city_id+'/'+self.val()+'/'+'/'+customer_id+'/',
            //     url: '/ajax/getNopol/'+from_city_id+'/'+self.val()+'/',
            //     type: 'POST',
            //     success: function(response, status) {
            $('#truckID,#truckBrowse').attr('disabled', false);
            
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
            $('.truck_capacity').val('');
            $('#biaya-uang-jalan input').val('');
        }
    });

    $('#truckID,.tipe_motor_id').change(function() {
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
            $('#truckID').val('').attr('disabled', true);
            $('#truckBrowse').attr('disabled', true);
            $('.driver_name').val('');
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
    change_customer_revenue();

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

    $('.custom-find-invoice').change(function(){
        var self = $(this);
        var val = self.val();

        $.ajax({
            url: '/ajax/getInvoiceInfo/'+val+'/',
            type: 'POST',
            success: function(response, status) {
                $('#invoice-info').html(response);
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
        var left = (screen.width/2)-(800/2);
        var top = (screen.height/2)-(460/2);

        if(typeof customer_id != 'undefined' && customer_id != ''){
            window.open('/ajax/previewInvoice/'+customer_id+'/'+rel+'/', '1366621940374','width=800,height=460,toolbar=0,menubar=0,location=0,status=0,scrollbars=0,resizable=0,left='+left+',top='+top+'');
        }

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

    timepicker();
    daterangepicker();

    $('.date-resign-handle').click(function(){
        if($('.date-resign-handle input').is(':checked')) {
            $('#resign-date').removeClass('hide');
        }else{
            $('#resign-date').addClass('hide');
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
});