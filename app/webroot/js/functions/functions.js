var disableAjax = false;
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

var getUangjalan = function ( response ) {
    $('.truck_capacity').val($(response).filter('#truck_capacity').html());
    $('.driver_name').val($(response).filter('#driver_name').html());
    $('.driver_id').val($(response).filter('#driver_id').html());
    $('.wrapper-ttuj-alert-msg').html($(response).filter('.wrapper-ttuj-alert-msg').html());

    $('#converter-uang-jalan-extra').html($(response).filter('#list-converter-uang-jalan-extra').html());

    if( $(response).filter('#sj_outstanding').html() != null ) {
        $('.sj_outstanding').html($(response).filter('#sj_outstanding').html());
        view_sj_list( $('.sj_outstanding #view_sj_outstanding') );
    } else {
        $('.sj_outstanding').html('');
    }

    var uang_jalan_1 = $(response).filter('#uang_jalan_1').html().replace(/,/gi, "");
    var total_muatan = 0;
    var qtyLen = $('#ttujDetail .tbody > div').length;

    var lead_time = $(response).find('#ttuj-lanjutan-lead-time').html();
    $('#ttuj-lanjutan-lead-time').html(lead_time);
    datepicker( $('#ttuj-lanjutan-lead-time .custom-date') );
    timepicker( $('#ttuj-lanjutan-lead-time .timepicker') );

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
        var commission_min_qty = $(response).filter('#commission_min_qty').html();
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
    $('.commission_min_qty').val( commission_min_qty );
    $('.commission_ori').val( commission_ori );
    $('.commission_extra').val( commission_extra );
    $('.commission_extra_ori').val( commission_extra_ori );
    $('.commission_per_unit').val( commission_per_unit );
    $('.commission_extra_per_unit').val( commission_extra_per_unit );

    $.calcUangJalan();
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
                        <select name="data[TtujTipeMotor][city_id][]" class="form-control city-retail-id chosen-select"> \
                        ' + contentOptionCities +
                        '</select> \
                    </td>';
                }

                $('#ttujDetail tbody').append(' \
                <tr rel="'+idx+'"> \
                    '+ additionalField +
                    '<td> \
                        <select name="data[TtujTipeMotor][tipe_motor_id][]" class="form-control tipe_motor_id chosen-select" rel="'+idx+'"> \
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
                    <td class="text-center"> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="ttuj"><i class="fa fa-times"></i></a> \
                    </td> \
                </tr>');

                if( data_custom == 'retail' ) {
                    $('#ttujDetail tbody tr:last-child .city-retail-id').val( $('#getTruck').val() );
                }

                delete_custom_field( $('#ttujDetail tbody tr:last-child .delete-custom-field') );
                $.qtyMuatanPress({
                    obj: $('#ttujDetail tbody tr:last-child .qty-muatan'),
                });
                
                $.callChoosen({
                    obj: $('#ttujDetail tbody tr:last-child .chosen-select'),
                });

                $('#ttujDetail tbody tr:last-child .tipe_motor_id').change(function() {
                    $.getNopol();
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
                    $.inputNumber();
                    $.inputPrice({
                        obj: $('.lku-detail-'+lku_detail_len+' .input_price'),
                    });
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
                    $.inputNumber();
                    $.inputPrice({
                        obj: $('.ksu-detail-'+ksu_detail_len+' .input_price'),
                    });
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
                    $('.ttuj-info-table #field-grand-total-document').before(html_tr);
                    choose_item_info();
                    $.inputNumber();
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
                    $('.ttuj-info-table #field-grand-total-document').before(html_tr);
                    choose_item_info();
                    $.inputNumber();
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
                
                $.inputPrice({
                    obj: $('#box-field-input .list-uang-jalan:last-child .input_price'),
                });
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
                
                $.inputPrice({
                    obj: $('#box-field-input-extra .list-uang-jalan-extra:last-child .input_price'),
                });
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

                $.inputPrice({
                    obj: $('#box-field-input-commission .list-commission:last-child .input_price'),
                });
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

                $.inputPrice({
                    obj: $('#box-field-input-commission-extra .list-commission-extra:last-child .input_price'),
                });
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

                $.inputPrice({
                    obj: $('#box-field-input-asdp .list-asdp:last-child .input_price'),
                });
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

                $.inputPrice({
                    obj: $('#box-field-input-uang-kawal .list-uang-kawal:last-child .input_price'),
                });
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

                $.inputPrice({
                    obj: $('#box-field-input-uang-keamanan .list-uang-keamanan:last-child .input_price'),
                });
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

                $.inputPrice({
                    obj: $('#box-uang-kuli .list-uang-kuli:last-child .input_price'),
                });
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

                $.inputPrice({
                    obj: $('#box-uang-kuli-capacity .list-uang-kuli-capacity:last-child .input_price'),
                });
                $.inputNumber();
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
                var asset_groups = $('#form-asset-groups').html();
                var content_clone = '<tr class="child child-'+length+'" rel="'+length+'">'+
                    '<td>'+
                        '<input name="data[LeasingDetail][nopol][]" class="form-control" value="" type="text" id="LeasingDetailNopol">'+
                    '</td>'+
                    '<td>'+
                        '<input name="data[LeasingDetail][note][]" class="form-control" value="" type="text" id="LeasingDetailNote">'+
                    '</td>'+
                    '<td>'+asset_groups+'</td>'+
                    '<td align="right box-price">'+
                        '<input name="data[LeasingDetail][price][]" class="form-control price-leasing-truck input_price input_number text-right" value="0" type="text" id="LeasingDetailPrice">'+
                    '</td>'+
                    '<td class="action-table">'+
                        '<a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="leasing_first"><i class="fa fa-times"></i> Hapus</a>'+
                    '</td>'+
                '</tr>';
                $('.leasing-body').append(content_clone);

                $.inputPrice({
                    obj: $('#table-leasing tbody.leasing-body tr.child-'+length+' .input_price'),
                });
                delete_custom_field( $('#table-leasing tbody.leasing-body tr.child-'+length+' .delete-custom-field') );
                leasing_action();

                $('#table-leasing tbody.leasing-body tr.child-'+length+' .price-leasing-truck').blur(function(){
                    calc_pokok_leasing();
                });
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
                            <label for="ApprovalDetailMinAmount'+count_next+'">Range Jumlah Approval *</label> \
                            <div class="row"> \
                                <div class="col-sm-6"> \
                                    <input name="data[ApprovalDetail][min_amount]['+count_next+']" class="form-control input_price" placeholder="Jumlah dari" type="text" id="ApprovalDetailMinAmount'+count_next+'"> \
                                </div> \
                                <div class="col-sm-6"> \
                                    <input name="data[ApprovalDetail][max_amount]['+count_next+']" class="form-control input_price" placeholder="Sampai Jumlah" type="text" id="ApprovalDetailMaxAmount'+count_next+'"> \
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
                                    <th class="text-center">Approval Prioritas</th> \
                                    <th class="text-center">Action</th> \
                                </tr> \
                            </thead> \
                            <tbody class="cashbanks-auth-table"> \
                                <tr class="cash-auth-row" id="cash-auth" rel="0"> \
                                    <td> \
                                        '+tempContent+' \
                                    </td> \
                                    <td class="text-center"> \
                                        <label> \
                                            <input type="hidden" name="data[ApprovalDetailPosition][is_priority]['+count_next+'][0]" id="ApprovalDetailPositionIsPriority'+count_next+'0" value="0"><input type="checkbox" name="data[ApprovalDetailPosition][is_priority]['+count_next+'][0]" value="1" id="ApprovalDetailPositionIsPriority'+count_next+'0"> \
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

                $(objParentTr+' .approval-position').addClass('approval-position-0');
                $(objParentTr+' .approval-position').attr('name', 'data[ApprovalDetailPosition][employe_position_id]['+count_next+'][0]');

                delete_custom_field( $(objParentTr+' .delete-custom-field') );
                add_custom_field( $(objParentTable+' .add-custom-field') );
                ajaxModal( $(objParentTr+' .ajaxModal') );
                $.pickData();
                $.inputPrice({
                    obj: $(objParentTable+' .input_price'),
                });

                $(objParentTable+' [data-widget="remove"]').click(function() {
                    //Find the box parent        
                    var box = $(this).parents(".box").first();
                    box.slideUp( "slow", function() {
                        box.remove();
                    });

                    return false;
                });
            break;
            case 'auth-cash-bank-user-approval':
                var parent = self.parents('.wrapper-approval-setting');
                var rel = parent.attr('rel');
                var count_cash_auth = $('.wrapper-approval-setting[rel="'+rel+'"] .cash-auth-row').length-1;
                var count_next = count_cash_auth+1;
                var tempContent = $('#form-authorize').html();
                var content = '<tr class="cash-auth-row" id="cash-auth" rel="'+count_next+'"> \
                    <td> \
                        '+tempContent+' \
                    </td> \
                    <td class="text-center"> \
                        <label> \
                            <input type="checkbox" name="data[ApprovalDetailPosition][is_priority]['+rel+']['+count_next+']" value="1" id="ApprovalDetailPosition'+rel+count_next+'"> \
                        </label> \
                    </td> \
                    <td class="action text-center"> \
                        <a href="javascript:" class="btn btn-danger delete-custom-field btn-xs" action_type="auth-cash-bank-user-approval" rel="'+count_next+'"><i class="fa fa-times-circle"></i></a> \
                    </td> \
                </tr>';

                var objParentTable = $('.wrapper-approval-setting[rel="'+rel+'"] .cashbanks-auth-table');
                var objParentTr = '#cash-auth[rel="'+count_next+'"]';

                objParentTable.append(content);
                $(objParentTr+' .approval-position').addClass('approval-position-'+count_next);
                $(objParentTr+' .approval-position').attr('name', 'data[ApprovalDetailPosition][employe_position_id]['+rel+']['+count_next+']');

                delete_custom_field( $(objParentTr+' .delete-custom-field') );
                ajaxModal( $(objParentTr+' .ajaxModal') );
                $.pickData();
            break;
            case 'revenue-detail':
                var customer_id = $('#customer-revenue-manual').val();
                var truck_id = $('.truck-revenue-id').val();
                var from_city_id = $('#from-city-revenue-id').val();
                var to_city_id = $('#to-city-revenue-id').val();

                if( customer_id != '' && truck_id != '' && from_city_id != '' && to_city_id != '' ) {
                    var idx = $('#muatan-revenue-detail tbody.tipe-motor-table tr').length;
                    var optionCity = $('#city-revenue').html();
                    var groupMotorRevenue = $('#group-motor-revenue').html();
                    var to_city_id = $('#to-city-revenue-id').val();
                    var count_next = idx+1;

                    $('#muatan-revenue-detail tbody.tipe-motor-table').append(' \
                    <tr rel="'+count_next+'" class="list-revenue"> \
                        <td class="city-data"> \
                            <select name="data[RevenueDetail][city_id][]" class="form-control city-revenue-change chosen-select" id="RevenueDetailCityId">'+optionCity+'</select> \
                            <input type="hidden" name="data[RevenueDetail][tarif_angkutan_id][]" class="tarif_angkutan_id" id="RevenueDetailTarifAngkutanId"> \
                            <input type="hidden" name="data[RevenueDetail][tarif_angkutan_type][]" class="tarif_angkutan_type" id="RevenueDetailTarifAngkutanType"> \
                        </td> \
                        <td class="no-do-data" align="center"> \
                            <input name="data[RevenueDetail][no_do][]" class="form-control" type="text" id="RevenueDetailNoDo"> \
                        </td> \
                        <td class="no-sj-data"> \
                            <input name="data[RevenueDetail][no_sj][]" class="form-control" type="text" id="RevenueDetailNoSj"> \
                        </td> \
                        <td class="tipe-motor-data"> \
                            <select name="data[RevenueDetail][group_motor_id][]" class="form-control revenue-group-motor" id="RevenueDetailGroupMotorId">'+groupMotorRevenue+'</select> \
                        </td> \
                        <td class="qty-tipe-motor-data" align="center"> \
                            <input name="data[RevenueDetail][qty_unit][]" class="form-control revenue-qty text-center input_number" type="text" id="RevenueDetailQtyUnit"> \
                            <input type="hidden" name="data[RevenueDetail][payment_type][]" class="jenis_unit" id="RevenueDetailPaymentType"> \
                        </td> \
                        <td class="price-data text-right"> \
                            <span></span> \
                            <input type="hidden" name="data[RevenueDetail][price_unit][]" class="form-control price-unit-revenue input_price text-right" id="RevenueDetailPriceUnit"> \
                        </td> \
                        <td class="additional-charge-data" align="center"> \
                            <input type="checkbox" name="data[RevenueDetail][is_charge_temp][]" class="additional-charge" value="1" id="RevenueDetailIsChargeTemp"> \
                            <input type="hidden" name="data[RevenueDetail][is_charge][]" value="0" class="additional-charge-hidden" id="RevenueDetailIsCharge"> \
                        </td> \
                        <td class="total-price-revenue text-right"> \
                            <span class="total-revenue-perunit"></span> \
                        </td> \
                        <td class="handle-row"> \
                            <a href="#" class="duplicate-row btn btn-warning btn-xs" title="Duplicate"><i class="fa fa-copy "></i></a> \
                            <a action_type="revenue-detail" href="#" class="delete-custom-field btn btn-danger btn-xs" title="Hapus Muatan" rel="'+count_next+'"><i class="fa fa-times"></i></a> \
                        </td> \
                    </tr>');

                    objQty = $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .revenue-qty');
                    objPrice = $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .price-unit-revenue');
                    objTotalPrice = $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .total-revenue-perunit');
                    objPPhPPn = $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .pph-persen, #muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .ppn-persen');

                    delete_custom_field( $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .delete-custom-field') );
                    city_revenue_change( $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .city-revenue-change'), $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .revenue-group-motor') );
                    checkCharge( $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .additional-charge') );
                    revenue_detail( objQty, objPrice, objPPhPPn, objTotalPrice );
                    $.inputPrice({
                        obj: $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .input_price'),
                    });
                    duplicate_row();

                    $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .city-revenue-change').val( to_city_id );
                    $.callChoosen({
                        obj: $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+count_next+'"] .chosen-select'),
                    });
                } else {
                    alert('Mohon pilih Customer, No. Pol dan Tujuan');
                }
                break;
            case 'add_auth':
                var content_select = $('#temp-auth').html();
                var idx = $('.auth-form-open').length;
                // var content = '<div class="box-body" rel="'+idx+'">'+
                //     '<div class="form-group">'+
                //         '<label for="city_id">Cabang '+idx+'</label>'+
                //         '<a href="javascript:" class="delete-custom-field pull-right" action_type="delete-auth-branch" group-branch-id="0">'+
                //             '<i class="fa fa-times"></i>'+
                //         '</a>'+
                //         content_select+
                //     '</div>'+
                // '</div>';

                var content = '<div class="box collapsed-box" rel="'+idx+'">'+
                    '<div class="box-header">'+
                        '<div class="box-title">'+
                            content_select+
                            '<div class="branch-action">'+
                                '<a href="javascript:" class="branch-check" type-action="checkall">checkall</a> / '+
                                '<a href="javascript:" class="branch-check" type-action="uncheckall">uncheckall</a>'+
                            '</div>'+
                        '</div>'+
                        '<div class="box-tools pull-right">'+
                            '<button class="btn btn-default btn-sm trigger-collapse" rel="plus"><i class="fa fa-plus"></i></button>'+
                            '<a href="javascript:" class="btn btn-default btn-sm delete-custom-field" action_type="delete-auth-branch" group-branch-id="0"><i class="fa fa-times"></i></a>'+
                        '</div>'+
                    '</div>'+
                    '<div class="box-body auth-action-box" style="display: none;"></div>'+
                '</div>';

                $('#auth-box').append(content);

                delete_custom_field( $('#auth-box div.box[rel="'+idx+'"] .delete-custom-field') );
                auth_form_open($('#auth-box div.box[rel="'+idx+'"] .auth-form-open'));
                accordion_toggle($('#auth-box div.box[rel="'+idx+'"] .trigger-collapse'));
                branch_check($('#auth-box div.box[rel="'+idx+'"] .branch-check'));
                break;
            case 'branch_city':
                var class_count = $('#box-branch-city .list-branch-city');
                var length = parseInt(class_count.length);
                var idx = length+1;

                $('#box-branch-city').append('<div rel="'+(idx-1)+'" class="row list-branch-city"> \
                    <div class="col-sm-10"> \
                        <div class="form-group"> \
                            <label>Cabang</label> \
                            <select name="data[BranchCity][branch_city_id][]" class="form-control chosen-select"> \
                                '+$('#branch_city_input select').html()+' \
                            </select> \
                        </div> \
                    </div> \
                    <div class="col-sm-2"> \
                        <label class="block">&nbsp;</label> \
                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="branch_city"> \
                            <i class="fa fa-times"></i> Hapus \
                        </a> \
                    </div> \
                </div>');

                $.inputPrice({
                    obj: $('#box-branch-city .list-branch-city:last-child .input_price'),
                });
                delete_custom_field( $('#box-branch-city .list-branch-city:last-child .delete-custom-field') );
              break;
        }
    });
}

var delete_custom_field = function( obj ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.delete-custom-field');
    }

    obj.off('click');
    obj.click(function (e) {
        var self = $(this);
        var action_type = self.attr('action_type');

        if( confirm('Anda yakin ingin menghapus data ini?') ) {
            if( action_type == 'ttuj' ) {
                var lengthTable = $('#ttujDetail tbody tr').length;
                $(this).parents('tr').remove();
                $.getNopol();
            } else if( action_type == 'perlengkapan' ) {
                var length = parseInt($('#box-field-input .list-perlengkapan .seperator').length);
                var action_type = self.attr('action_type');
                var idx = length;
                $('#'+action_type+(idx-1)).remove();
            } else if( action_type == 'lku_first' || action_type == 'leasing_first' || action_type == 'invoice_first' || action_type == 'cashbank_first' || action_type == 'document_first' ){
                self.parents('tr').remove();
                grandTotalLku();
                grandTotalLeasing();
                getTotalPick();
                calcPPNPPH();
                $.getLeasingGrandtotal();

                if( action_type == 'cashbank_first' ) {
                    calcTotalBiaya();
                }
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
            } else if( action_type == 'revenue-detail' ) {
                var rel = self.attr('rel');
                $('#muatan-revenue-detail tbody.tipe-motor-table tr.list-revenue[rel="'+rel+'"]').remove();
                grandTotalRevenue();
            } else if( action_type == 'delete-auth-branch'){
                var group_branch_id = self.attr('group-branch-id');
                
                if(group_branch_id != 0){
                    $.ajax({
                        url: '/ajax/delete_branch_group/'+group_branch_id+'/',
                        type: 'POST',
                        success: function(response, status) {
                            var status = $(response).filter('#status').text();
                            
                            if(status == 'success'){
                                self.parents('.box').remove();
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
                }else{
                    self.parents('.box').remove();
                }

                return false;
            } else if( action_type == 'branch_city' ) {
                var parent = self.parents('.list-branch-city');
                parent.remove();
            }
        }

        return false;
    });
}

var duplicate_row = function(){
    $('.duplicate-row').off('click');
    $('.duplicate-row').click(function(){
        var self = $(this);
        var parent = self.parents('tr');
        
        $.callChoosen({
            obj: parent.find('.chosen-select'),
            init: 'destroy',
        });

        var tag_element = parent.html();
        var uniqid = Date.now();
        var jenis_unit = $('.jenis_unit').val();

        var html = '<tr rel="'+uniqid+'" class="list-revenue">'+
            '<td class="city-data">'+$(tag_element).filter('.city-data').html()+'</td>'+
            '<td class="no-do-data" align="center">'+$(tag_element).filter('.no-do-data').html()+'</td>'+
            '<td class="no-sj-data">'+$(tag_element).filter('.no-sj-data').html()+'</td>'+
            '<td class="tipe-motor-data">'+$(tag_element).filter('.tipe-motor-data').html()+'</td>'+
            '<td class="qty-tipe-motor-data" align="center">'+$(tag_element).filter('.qty-tipe-motor-data').html()+'</td>'+
            '<td class="price-data text-right">'+$(tag_element).filter('.price-data').html()+'</td>'+
            '<td class="additional-charge-data" align="center">'+$(tag_element).filter('.additional-charge-data').html()+'</td>'+
            '<td class="total-price-revenue text-right">'+$(tag_element).filter('.total-price-revenue').html()+'</td>'+
            '<td class="handle-row action text-center"><a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="revenue_detail" title="hapus baris"><i class="fa fa-times"></i></a></td>'+
        '</tr>';

        parent.after(html);

        $('tr[rel="'+uniqid+'"] .city-revenue-change').val(parent.find('.city-revenue-change').val());
        $('tr[rel="'+uniqid+'"] .revenue-qty').val('');
        $('tr[rel="'+uniqid+'"] .from-ttuj').val('');
        $('tr[rel="'+uniqid+'"] .no-do-data input').val('');
        $('tr[rel="'+uniqid+'"] .no-sj-data input').val('');

        revenue_detail();
        grandTotalRevenue();
        delete_custom_field( $('tr[rel="'+uniqid+'"] .delete-custom-field') );
        city_revenue_change( $('tr[rel="'+uniqid+'"] .city-revenue-change'), $('tr[rel="'+uniqid+'"] .revenue-group-motor') );
        checkCharge( $('tr[rel="'+uniqid+'"] .additional-charge') );

        $.inputPrice({
            obj: $('tr[rel="'+uniqid+'"] .input_price'),
        });
        $.callChoosen({
            obj: parent.find('.chosen-select'),
        });
        $.callChoosen({
            obj: $('tr[rel="'+uniqid+'"] .chosen-select'),
        });

        return false;
    });
}

var changeDetailRevenue = function ( parent, city_id, group_motor_id, is_charge, qty ) {
    var ttuj_id = $('#getTtujInfoRevenue').val();
    var customer_id = $('.change-customer-revenue').val();
    var to_city_id = $('#toCityId').val();
    var data_type = $.checkUndefined($('.revenue-data-type').val(), 0);

    if( typeof customer_id == 'undefined' || isNaN(customer_id) || customer_id == '' ){
        customer_id = 0;
    }
    if( typeof is_charge == 'undefined' ){
        is_charge = 0;
    }
    if( typeof qty == 'undefined' || isNaN(qty) || qty == '' ){
        qty = 0;
    }
    if( typeof group_motor_id == 'undefined' || isNaN(group_motor_id) || group_motor_id == '' ){
        group_motor_id = 0;
    }
    
    var url = '/ajax/getInfoRevenueDetail/'+ttuj_id+'/'+customer_id+'/'+city_id+'/'+group_motor_id+'/'+is_charge+'/'+to_city_id+'/'+qty+'/0/0/';
    
    if( data_type == 'manual' ) {
        customer_id = $('#customer-revenue-manual').val();
        truck_id = $('.truck-revenue-id').val();
        from_city_id = $.checkUndefined($('#from-city-revenue-id').val(), 0);
        to_city_id = $.checkUndefined($('#to-city-revenue-id').val(), 0);

        url = '/ajax/getInfoRevenueDetail/0/'+customer_id+'/'+city_id+'/'+group_motor_id+'/'+is_charge+'/'+to_city_id+'/'+qty+'/'+from_city_id+'/'+truck_id+'/manual';
    }

    $.ajax({
        url: url,
        type: 'POST',
        success: function(response, status) {
            var jenis_unit_angkutan = $(response).find('.jenis_unit').val();

            parent.find('td.city-data .tarif_angkutan_id').val($(response).filter('#tarif_angkutan_id').val());
            parent.find('td.city-data .tarif_angkutan_type').val($(response).filter('#tarif_angkutan_type').val());
            parent.find('td.price-data').html($(response).filter('#price-data').html());
            parent.find('td.qty-tipe-motor-data .jenis_unit').val(jenis_unit_angkutan);
            parent.find('td.additional-charge-data').html($(response).filter('#additional-charge-data').html());
            parent.find('td.total-price-revenue').html($(response).filter('#total-price-revenue').html());

            revenue_detail();
            grandTotalRevenue();
            checkCharge( parent.find('td.additional-charge-data .additional-charge') );
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

    obj_city.off('change');
    obj_city.change(function(){
        var self = $(this);
        var val = self.val();
        var parent = self.parents('tr');
        var group_motor_id = parent.find('.revenue-group-motor').val();
        var qty = parent.find('.revenue-qty').val();

        changeDetailRevenue( parent, val, group_motor_id, 1, qty );
    });

    obj_tipe_motor.off('change');
    obj_tipe_motor.change(function() {
        var self = $(this);
        var val = self.val();
        var parent = self.parents('tr');
        var city_id = parent.find('.city-revenue-change').val();
        var qty = parent.find('.revenue-qty').val();

        changeDetailRevenue( parent, city_id, val, 1, qty );
        return false;
    });
}

function grandTotalRevenue(){
    var qty = $('.revenue-qty');
    var price = $('.price-unit-revenue');
    var totalPrices = $('.total-revenue-perunit');
    var is_additional_charge = $('.additional-charge');
    var jenis_unit = $('.jenis_unit');
    var length = $('.tipe-motor-table tr.list-revenue').length;
    var listRevenue = $('.tipe-motor-table tr.list-revenue');
    var total = 0;
    var ppn = 0;
    var revenue_ppn = $('.ppn-persen').val();
    var formatAdditionalCharge = 0;
    var totalQty = 0;

    for (var i = 0; i < length; i++) {
        var rel = listRevenue[i].getAttribute('rel');
        var msg = '';
        // var totalPriceUnit = 0;

        if( typeof price[i] != 'undefined' && price[i].value != '' ) {
            priceUnit = $.convertNumber(price[i].value, 'int');
            checkString = parseInt(price[i].value);

            if( isNaN(checkString) ) {
                msg = price[i].value;
            }

            // if( typeof totalPrices[i] != 'undefined' ) {
            //     totalPriceUnit = $.convertNumber(totalPrices[i].value, 'int');
            // }
        } else {
            priceUnit = 0;
        }

        if( typeof qty[i] != 'undefined' && qty[i].value != '' ) {
            qtyUnit = $.convertNumber(qty[i].value, 'int');
        } else {
            qtyUnit = 0;
        }

        totalQty += qtyUnit;

        if( jenis_unit[i].value == 'per_truck'){
            totalPrice = priceUnit;
        } else {
            totalPrice = priceUnit * qtyUnit;
        }

        if( is_additional_charge[i].checked == true ) {
            total += totalPrice;

            // if( jenis_unit[i].value != 'per_truck'){
                $('.tipe-motor-table tr.list-revenue[rel="'+rel+'"]').find('.total-revenue-perunit').val(formatNumber(totalPrice));
                $('.tipe-motor-table tr.list-revenue[rel="'+rel+'"]').find('.total-revenue-perunit-text').html(formatNumber(totalPrice));
            // }
        }

        if( msg != '' ) {
            $('.tipe-motor-table tr.list-revenue[rel="'+rel+'"]').find('.price-data span').html(msg);
        } else {
            $('.tipe-motor-table tr.list-revenue[rel="'+rel+'"]').find('.price-unit-revenue').val(formatNumber(priceUnit));
        }
    };

    $('#qty-revenue').html(totalQty);
    $('#grand-total-document').html(formatNumber(total));

    if(typeof revenue_ppn != 'undefined' && revenue_ppn != ''){
        ppn = total * (parseFloat(revenue_ppn) / 100);
    }
    $('#ppn-total').val(formatNumber(ppn));

    var pph = 0;
    var revenue_pph = $('.pph-persen').val();
    if(typeof revenue_pph != 'undefined' && revenue_pph != ''){
        pph = total * (parseFloat(revenue_pph) / 100);
    }
    $('#pph-total').val(formatNumber(pph));
    
    if(ppn > 0){
        total += ppn;
    }

    $('#all-total').html(formatNumber(total));
}

function calcPPNPPH(){
    if( $('#grand-total-document').length > 0 ) {
        var total = parseInt($('#grand-total-document').html().replace(/,/gi, "").replace(/IDR/gi, ""));
        var ppn = 0;
        var pph = 0;
        var revenue_ppn = $.convertNumber($('.ppn-persen').val(), 'float');
        var revenue_pph = $.convertNumber($('.pph-persen').val(), 'float');

        if( isNaN(total) ) {
            total = 0;
        }

        ppn = total * (revenue_ppn / 100);
        $('#ppn-total').val(formatNumber(ppn));

        pph = total * (revenue_pph / 100);
        $('#pph-total').val(formatNumber(pph));
        
        if(ppn > 0){
            total += ppn;
        }

        $('#all-total').html(formatNumber(total));
    }
}

function calcTax( type, val, total ){
    var result = 0;

    if( type == 'percent' ) {
        result = total * (val / 100);
    } else if( type == 'nominal' ) {
        result = (val/total) * 100;
    }
    
    return result;
}

function calcTaxtotal( type, percent, nominal ){
    var tax_percent = [];
    var tax_nominal = [];

    $.each( $('.document-calc .tax-percent'), function( i, val ) {
        var self = $(this);
        var rel = self.attr('rel');

        var total = $.checkUndefined(tax_percent[rel], 0);
        total = parseFloat(total);

        var val = $.convertNumber(self.val(), 'float');
        val = parseFloat(val);
        tax_percent[rel] = total + val;
    });

    $.each( $('.document-calc .tax-nominal'), function( i, val ) {
        var self = $(this);
        var rel = self.attr('rel');

        var total = $.checkUndefined(tax_nominal[rel], 0);
        total = parseFloat(total);

        var val = $.convertNumber(self.val(), 'float');
        tax_nominal[rel] = total + val;
    });

    $('#total-ppn-percent').html($.formatDecimal(tax_percent['ppn'], 2));
    $('#total-ppn-nominal').html($.formatDecimal(tax_nominal['ppn']));

    $('#total-pph-percent').html($.formatDecimal(tax_percent['pph'], 2));
    $('#total-pph-nominal').html($.formatDecimal(tax_nominal['pph']));
}

function calcPPNPPHNominal(){
    var total = $.convertNumber($('#grand-total-document').html().replace(/,/gi, "").replace(/IDR/gi, ""), 'int');
    var ppn = 0;
    var pph = 0;
    var revenue_ppn = $.convertNumber($('#ppn-total').val(), 'float');
    var revenue_pph = $.convertNumber($('#pph-total').val(), 'float');

    ppn = ( revenue_ppn / total ) * 100;
    $('.ppn-persen').val(formatNumber(ppn, 2));

    pph = ( revenue_pph / total ) * 100;
    $('.pph-persen').val(formatNumber(pph, 2));

    total += revenue_ppn;

    $('#all-total').html(formatNumber(total));
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

    $('#grand-total-leasing').text(formatNumber(total_price));
    $('#hid-total-leasing').val(total_price);
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

                $.inputPrice({
                    obj: self.parents('tr').find('td.qty-perlengkapan .input_price'),
                });
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
            $('.'+data_field).show();
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
                parent.show();
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

    $.qtyMuatanPress();
}
choose_item_info();

var revenue_detail = function( objQty, objPrice, objPPhPPn, objTotalPrice ){
    if( typeof objQty == 'undefined' ) {
        objQty = $('.revenue-qty');
    }
    if( typeof objPrice == 'undefined' ) {
        objPrice = $('.price-unit-revenue');
    }
    if( typeof objPPhPPn == 'undefined' ) {
        objPPhPPn = $('.pph-persen, .ppn-persen');
    }
    if( typeof objTotalPrice == 'undefined' ) {
        objTotalPrice = $('.total-revenue-perunit');
    }

    objQty.off('keyup');
    objQty.keyup(function(event){
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

    objPrice.off('keyup');
    objPrice.keyup(function(){
        total_revenue_unit(self);
    });

    objTotalPrice.off('keyup');
    objTotalPrice.keyup(function(){
        grandTotalRevenue();
    });

    objPPhPPn.off('keyup');
    objPPhPPn.keyup(function(){
        calcPPNPPH();
    });

    $('#pph-total,#ppn-total').off('keyup');
    $('#pph-total,#ppn-total').keyup(function(){
        calcPPNPPHNominal();
    });
}

function total_revenue_unit(self){
    grandTotalRevenue();
}

if( $('.revenue-qty').length ) {
    revenue_detail();
}

var datepicker = function( obj ){
    if( typeof obj == 'undefined' ) {
        obj = $( ".custom-date" );
    }

    if( obj.length > 0 ) {
        $('.datepicker.datepicker-dropdown').remove();
        var min_date = $.checkUndefined($('body').attr('datapicker-mindate'), 'false');
        var date_options = {
            format: 'dd/mm/yyyy',
            todayHighlight: true,
        };

        if( min_date != 'false' ) {
            date_options['startDate'] = min_date;
        }

        obj.datepicker(date_options);
    }
}

var timepicker = function( obj ){
    if( typeof obj == 'undefined' ) {
        obj = $('.timepicker');
    }

    if( obj.length > 0 ) {
        var timeDefault = obj.attr('data-default');

        if( timeDefault == 'false' ) {
            timeDefault = false;
        } else {
            timeDefault = 'current';
        }

        $('.bootstrap-timepicker-widget').remove();
        obj.timepicker({
            showMeridian: false,
            defaultTime: timeDefault,
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

var ajaxModal = function ( obj, prettyPhoto ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.ajaxModal');
    }
    if( typeof prettyPhoto == 'undefined' ) {
        prettyPhoto = false;
    }

    obj.off('click');
    obj.click(function(msg) {
        var type_action = '';
        var vthis = $(this);
        var url = vthis.attr('href');
        var alert_msg = vthis.attr('alert');
        var url_attr = vthis.attr('url');
        var parent = vthis.attr('data-parent');
        var custom_backdrop_modal = vthis.attr('backdrop-modal');
        var data_change = vthis.attr('data-change');
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
            var data_check = vthis.attr('data-check');
            var data_check_title = vthis.attr('data-check-title');
            type_action = vthis.attr('data-action');

            if( typeof data_check_title == 'undefined' ) {
                data_check_title = '';
            }

            if(type_action == 'browse-invoice'){
                var dataTrigger = vthis.attr('data-trigger');
                var errorMsg = vthis.attr('data-change-message');

                if( typeof dataTrigger != 'undefined' ) {
                    if( $(dataTrigger).val() == '' ) {
                        goAjax = false;
                        alert(errorMsg);
                    } else if( $('#id-choosen').length > 0 && $('#id-choosen').val() != '' ) {
                        url += '/'+$('#id-choosen').val()+'/'
                    }
                } else if(typeof $('#customer-val, #getTtujCustomerInfo, #getTtujCustomerInfoKsu, #id-choosen').val() == 'undefined' || $('#customer-val, #getTtujCustomerInfo, #getTtujCustomerInfoKsu, #id-choosen').val() == ''){
                    goAjax = false;
                    alert('Mohon pilih customer terlebih dahulu');
                } else {
                    url += '/'+$('#customer-val, #getTtujCustomerInfo, #getTtujCustomerInfoKsu, #id-choosen').val()+'/not-list'
                }
            } else if( typeof data_check != 'undefined' && $(data_check).length > 0 ) {
                if( $(data_check).val() == '' ){
                    goAjax = false;
                    alert('Mohon pilih '+data_check_title+' terlebih dahulu');
                } else {
                    url += '/'+$(data_check).val()+'/';
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
                        $.pickData();
                        $.daterangepicker({
                            obj: $('#myModal .modal-body .date-range'),
                        });
                        $.rebuildFunction();
                    } else if( type_action == 'browse-invoice' || type_action == 'getTtujCustomerInfo' || type_action == 'getTtujCustomerInfoKsu' ) {
                        ajaxModal( $('#myModal .modal-body .pagination li a, #myModal .modal-body .ajaxModal') );
                        $.pickData();
                        datepicker($('#myModal .modal-body .custom-date'));
                        $.daterangepicker({
                            obj: $('#myModal .modal-body .date-range'),
                        });
                        check_all_checkbox();
                    } else if( type_action == 'browse-cash-banks' || type_action == 'browse-check-docs' ) {
                        ajaxModal( $('#myModal .modal-body .pagination li a, #myModal .modal-body .ajaxModal') );
                        $.checkAll();
                        $.callChoosen({
                            obj: $('#myModal .modal-body .chosen-select'),
                        });

                        if( type_action == 'browse-check-docs' ) {
                            $.inputPrice({
                                obj: $('#myModal .modal-body .input_price'),
                            });
                            input_price_min($('#myModal .modal-body .input_price_min'));
                            sisa_amount($('#myModal .modal-body .sisa-amount'));
                            popup_checkbox();
                            $.daterangepicker({
                                obj: $('#myModal .modal-body .date-range'),
                            });
                            $.dropdownFix({
                                obj: $('#myModal .modal-body .columnDropdown'),
                            });
                        } else {
                            check_all_checkbox();
                        }
                    } else if(type_action == 'cancel_invoice' || type_action == 'submit_form'){
                        submitForm();
                        datepicker();
                    } else {
                        ajaxModal();
                        $.checkAll();
                        $.daterangepicker({
                            obj: $('#myModal .modal-body .date-range'),
                        });
                    }
                    
                    popup_checkbox();

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

    $('#myModal,.staticModal').on('hidden.bs.modal', function () {
        $('.popover-hover-top-click.in,.popover-hover-bottom-click.in').trigger('click');
        $('.modal-dialog').removeClass('expand-modal');
        $('.modal-content').removeClass('expand-content-modal');
    });
    $('#myModal,.staticModal').on('shown.bs.modal', function () {
        if( $('#myModal .modal-body .on-focus').length > 0 ) {
            $('#myModal .modal-body .on-focus').focus();
        }
    })
}

var ajaxLink = function ( obj, prettyPhoto ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.ajaxLink');
    }

    obj.click(function(msg) {
        var type_action = '';
        var vthis = $(this);
        var url = vthis.attr('href');
        var alert_msg = vthis.attr('alert');
        var getData = vthis.attr('data-request');
        var form = $(getData);
        var urlAction = form.attr('action');
        var data = false;

        if( alert_msg != null ) {
            if ( !confirm(alert_msg) ) { 
                return false;
            }
        }

        if( typeof getData != 'undefined' ) {
            data = form.serialize();
            form.attr('action', url);
            form.submit();

            form.attr('action', urlAction);
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
    obj.off('click');
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
                            datepicker();

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
    
var _callRevManualChange = function () {
    // $('#customer-revenue-manual,.truck-revenue-id,#from-city-revenue-id,#to-city-revenue-id').off('change');
    // $('#customer-revenue-manual,.truck-revenue-id,#from-city-revenue-id,#to-city-revenue-id').change(function(){
    //     var customer_id = $('#customer-revenue-manual').val();
    //     var truck_id = $('.truck-revenue-id').val();
    //     var from_city_id = $('#from-city-revenue-id').val();
    //     var to_city_id = $('#to-city-revenue-id').val();
    //     var revenue_data_type = $.checkUndefined($('.revenue-data-type').val(), 0);

    //     if( customer_id != '' && truck_id != '' && from_city_id != '' && to_city_id != '' ) {
    //         $.ajax({
    //             url: '/ajax/getInfoRevenueDetail/0/'+customer_id+'/'+to_city_id+'/0/0/'+to_city_id+'/0/'+from_city_id+'/'+truck_id+'/'+revenue_data_type+'/',
    //             type: 'POST',
    //             success: function(response, status) {
    //                 var jenis_unit = $(response).find('.jenis_unit').val();
    //                 $('.revenue_tarif_type').val( jenis_unit );

    //                 if( jenis_unit == 'per_truck' ) {
    //                     var totalPriceFormat = $(response).find('.total-revenue-perunit').val();

    //                     $('#grand-total-document').html( totalPriceFormat );
    //                 }
                    
    //                 grandTotalRevenue();
    //             },
    //             error: function(XMLHttpRequest, textStatus, errorThrown) {
    //                 alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
    //                 return false;
    //             }
    //         });
    //     }
    // });

    $('.truck-revenue-id').off('change');
    $('.truck-revenue-id').change(function(){
        var truck_id = $(this).val();

        $.ajax({
            url: '/ajax/getDataTruck/'+truck_id+'/',
            type: 'POST',
            success: function(response, status) {
                $('#revenue-truck-capacity').val( $(response).filter('#truck_capacity').html() );
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    });

    $('#truckID,.tipe_motor_id,#getKotaAsal').off('change');
    $('#truckID,.tipe_motor_id,#getKotaAsal').change(function() {
        $.getNopol();
    });
}
_callRevManualChange();

function findInfoTTujRevenue(url){
    $.ajax({
        url: url,
        type: 'POST',
        success: function(response, status) {
            $('#date_revenue').val($(response).filter('#date_revenue').val());
            $('#ttuj-info').html($(response).filter('#form-ttuj-main').html());
            $('#detail-tipe-motor,#muatan-revenue-detail').html($(response).filter('#form-ttuj-detail').html());
            $('#customer-form').html($(response).filter('#form-customer').html());
            $('.revenue_tarif_type').val($(response).filter('#revenue_tarif_type').val());

            revenue_detail();
            duplicate_row();
            delete_custom_field();
            datepicker();
            city_revenue_change();
            checkCharge( $('.additional-charge') );
            _callRevManualChange();
            ajaxModal( $('#ttuj-info .ajaxModal') );

            if( $('#customer-form .chosen-select').length > 0 ) {
                $.callChoosen({
                    obj: $('#customer-form .chosen-select,#ttuj-info .chosen-select,.city-revenue-change.chosen-select'),
                });
            }
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

            if( response.driver_pengganti_id != '' ) {
                $('.driver-id').val(response.driver_pengganti_id);
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

var input_price_min = function ( obj ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.input_price_min');
    }

    if( obj.length > 0 ) {
        obj.priceFormat({
            allowNegative: true,
            doneFunc: function(obj, val) {
                currencyVal = val;
                currencyVal = currencyVal.replace(/,/gi, "")
                obj.next(".input_hidden").val(currencyVal);
            }
        });
    }
}

var checkCharge = function ( obj ) {
    obj.off('click');
    obj.click(function(){
        var parent = $(this).parents('tr');
        var val = 0;
        var city_id = parent.find('.city-revenue-change').val();
        var group_motor_id = parent.find('.revenue-group-motor').val();
        var qty = parent.find('.revenue-qty').val();

        if( $(this).is(':checked') ) {
            val = 1;
        }

        parent.find('.additional-charge-hidden').val(val);

        changeDetailRevenue( parent, city_id, group_motor_id, val, qty );
    });
}
var leasing_action = function(){
    $('.price-leasing-truck').keyup(function(){
        grandTotalLeasing()
    });
}

function _callTotalInvoice () {
    var invoice_price = $('.document-pick-info-detail .total-payment,.document-pick-price');
    var length = invoice_price.length;
    var total_price = 0;
    var grand_total = 0;
    
    if( $('.document-calc .tax-percent').length > 0 ) {
        calcTaxtotal();
    }

    $.each( invoice_price, function( i, val ) {
        var self = $(this);
        var type = self.attr('type');
        var parent = self.parents('tr.child');
        var ppn = $.convertNumber( parent.find('.tax-nominal[rel="ppn"]').val() );
        var total_document = parent.find('.total-document');

        if( type == 'text' ) {
            var price = self.val();
        } else {
            var price = self.html();
        }

        var price = $.convertNumber( price );

        total = price + ppn;

        if( total_document.length > 0 ) {
            total_document.html($.formatDecimal(total));
        }

        grand_total += total;
        total_price += price;
    });

    if( $('#grandtotal-document').length > 0 ) {
        $('#grandtotal-document').html( $.formatDecimal(grand_total) );
    }

    return total_price;
}

var invoice_price_payment = function(){
    $('.document-pick-price').off('keyup').keyup(function(){
        getTotalPick();
        calcPPNPPH();
    });

    $('.document-pick-price').off('blur').blur(function(){
        var self = $(this);
        var parent = self.parents('tr.child');

        parent.find('.tax-percent').trigger('change');
    });

    $('.tax-percent,.tax-nominal').off('change');
    $('.tax-percent,.tax-nominal').change(function(){
        var self = $(this);
        var val = self.val();
        var type = self.attr('data-type');
        var rel = self.attr('rel');
        
        var parent = self.parents('tr.child');
        var total = $.convertNumber( parent.find('.total-payment').html() );

        result = calcTax(type, val, total);

        if( type == 'percent' ) {
            parent.find('.tax-nominal[rel="'+rel+'"]').val( $.formatDecimal(result) );
            // tax_nominal[rel] += result;
        } else {
            parent.find('.tax-percent[rel="'+rel+'"]').val( $.formatDecimal(result, 2) );
            // tax_percent[rel] += result;
        }

        _callTotalInvoice();

        return false;
    });
}

function getTotalPick(){
    if( $('.document-pick-info-detail .document-pick-price').length > 0 || $('.document-calc[rel="ppn"]').length > 0 ) {
        $('.pph-persen, .ppn-persen').off('keyup').keyup(function(){
            calcPPNPPH();
        });

        $('#pph-total,#ppn-total').off('keyup').keyup(function(){
            calcPPNPPHNominal();
        });

        var total = total_price = _callTotalInvoice();
        $('#grand-total-document').text(formatNumber(total_price));
    }

    // var ppn = 0;
    // var pph = 0;
    // var revenue_ppn = $('.invoice-ppn').val();
    // var revenue_pph = $('.invoice-pph').val();

    // if(typeof revenue_ppn != 'undefined' && revenue_ppn != ''){
    //     ppn = total * (parseInt(revenue_ppn) / 100);
    // }

    // $('#ppn-total-invoice').val(formatNumber(ppn));

    // if(typeof revenue_pph != 'undefined' && revenue_pph != ''){
    //     pph = total * (parseInt(revenue_pph) / 100);
    // }
    
    // $('#pph-total-invoice').val(formatNumber(pph));
    
    // // if(pph > 0){
    // //     total -= pph;
    // // }
    // if(ppn > 0){
    //     total += ppn;
    // }

    // $('#all-total').html('IDR '+formatNumber(total));

    // $('#grand-total-document').text('IDR '+formatNumber(total_price));
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
    
    // if(pph > 0){
    //     total -= pph;
    // }
    if(ppn > 0){
        total += ppn;
    }

    $('#all-total').html('IDR '+formatNumber(total));

    $('#grand-total-payment').text('IDR '+formatNumber(total_price));
}

var _callLeasingExpired = function( rel_id ) {
    var expired_date = $('.child-'+rel_id+' .leasing-expired-date');

    if( expired_date.length > 0 ) {
        var paid_date = $('.leasing-payment-date').val();
        var customPaidDate = _callReverseDate(paid_date);
        var customExpiredDate = expired_date.val();

        if( customExpiredDate < customPaidDate ) {
            $('.child-'+rel_id).addClass('expired');
        } else {
            $('.child-'+rel_id).removeClass('expired');
        }
    }
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
    $('#box-info-coa .click-child').click(function(){
        var self = $(this);
        click_option_coa(self)
    });
    
    $('.child-search .check-option').click(function(){
        var self = $(this);
        check_option(self)
    });

    function check_option(self){
        var parent = self.parents('.child-search');
        var rel_id = parent.attr('rel');

        if( $('#field-grand-total-document').length > 0 ) {
            $('.document-pick-info-detail').removeClass('hide');

            if(self.is(':checked')){
                if( $('.child-'+rel_id).length <= 0 ){
                    var html_content = '<tr class="child child-'+rel_id+'" rel="'+rel_id+'">'+parent.html()+'</tr>';
                    $('#field-grand-total-document').before(html_content);


                    $('.child-'+rel_id).find('.action-search').removeClass('hide');
                    $('.child-'+rel_id).find('.on-show').removeClass('hide');

                    $('.child-'+rel_id).find('.checkbox-detail').remove();
                    $('.child-'+rel_id).find('.on-remove').remove();

                    $.inputPrice({
                        obj: $('.child-'+rel_id+' .input_price'),
                    });
                    input_price_min($('.child-'+rel_id+' .input_price_min'));
                    delete_custom_field($('.child-'+rel_id+' .delete-custom-field'));

                    invoice_price_payment();
                    getTotalPick();
                    calcPPNPPH();
                    _callLeasingExpired(rel_id);

                    $.getLeasingPayment({
                        obj: $('.child-'+rel_id).find('.leasing-trigger'),
                    });
                    $.ajaxModal({
                        obj: $('.child-'+rel_id+' .ajaxModal'),
                    });
                }
            }else{
                $('.child-'+rel_id).remove();
                getTotalPick();
                calcPPNPPH();
                $.getLeasingGrandtotal();
            } 
        }
    }

    function check_option_coa(self){
        var parent = self.parents('.child-search');
        var allow_multiple = self.attr('data-allow-multiple');
        var rel_id = parent.attr('rel');

        $('.cashbank-info-detail').removeClass('hide');

        if( self.is(':checked') ){
            var leng_exists = $('.child-'+rel_id).length;

            if( leng_exists <= 0 || allow_multiple == 'true' ){
                var id_child = rel_id;

                if( leng_exists > 0 ) {
                    rel_id += '-' + leng_exists;
                }

                var html_content = '<tr class="child child-'+id_child+'" rel="'+rel_id+'">'+parent.html()+'</tr>';

                $('.cashbanks-info-table > tbody').append(html_content);

                var truck_obj = $('.child-'+id_child+'[rel="'+rel_id+'"]').find('.pick-truck input[type="text"]');
                var truck_browse_obj = $('.child-'+id_child+'[rel="'+rel_id+'"]').find('.pick-truck a.browse-docs');

                var truck_id = truck_obj.attr('id');
                var truck_href = truck_browse_obj.attr('href');

                $('.child-'+id_child+'[rel="'+rel_id+'"]').find('.action-search').removeClass('hide');
                $('.child-'+id_child+'[rel="'+rel_id+'"]').find('.checkbox-detail').remove();

                truck_obj.attr('id', truck_id + '-' + leng_exists);
                truck_browse_obj.attr('href', truck_href + '-' + leng_exists).attr('data-change', truck_id + '-' + rel_id);

                $.inputPrice({
                    obj: $('.child-'+id_child+'[rel="'+rel_id+'"] .input_price'),
                });
                input_price_min($('.child-'+id_child+'[rel="'+rel_id+'"] .input_price_min'));
                delete_custom_field($('.child-'+id_child+'[rel="'+rel_id+'"] .delete-custom-field'));
                ajaxModal($('.child-'+id_child+'[rel="'+rel_id+'"] .ajaxModal'));
                sisa_amount($('.child-'+id_child+'[rel="'+rel_id+'"] .sisa-amount'));
            }
        } else if( allow_multiple != 'true' ) {
            $('.child-'+rel_id).remove();
        } 
    }

    function click_option_coa(self){
        var rel_id = self.attr('rel');

        $('.cashbank-info-detail').removeClass('hide');

        var leng_exists = $('.child-'+rel_id).length;
        var id_child = rel_id;

        if( leng_exists > 0 ) {
            rel_id += '-' + leng_exists;
        }

        var html_content = '<tr class="child click-child child-'+id_child+'" rel="'+rel_id+'">'+self.html()+'</tr>';

        $('.cashbanks-info-table > tbody').append(html_content);

        var truck_obj = $('.child-'+id_child+'[rel="'+rel_id+'"]').find('.pick-truck input[type="text"]');
        var truck_browse_obj = $('.child-'+id_child+'[rel="'+rel_id+'"]').find('.pick-truck a.browse-docs');

        var truck_id = truck_obj.attr('id');
        var truck_href = truck_browse_obj.attr('href');

        $('.child-'+id_child+'[rel="'+rel_id+'"]').find('.action-search').removeClass('hide');
        $('.child-'+id_child+'[rel="'+rel_id+'"]').find('.checkbox-detail').remove();

        truck_obj.attr('id', truck_id + '-' + leng_exists);
        truck_browse_obj.attr('href', truck_href + '-' + leng_exists).attr('data-change', truck_id + '-' + rel_id);

        $.inputPrice({
            obj: $('.child-'+id_child+'[rel="'+rel_id+'"] .input_price'),
        });
        input_price_min($('.child-'+id_child+'[rel="'+rel_id+'"] .input_price_min'));
        delete_custom_field($('.child-'+id_child+'[rel="'+rel_id+'"] .delete-custom-field'));
        ajaxModal($('.child-'+id_child+'[rel="'+rel_id+'"] .ajaxModal'));
        sisa_amount($('.child-'+id_child+'[rel="'+rel_id+'"] .sisa-amount'));

        $.ajax({
            url: '/ajax/saveCache/Coa/'+rel_id+'/',
            type: 'POST',
        });

        $('#myModal').modal('hide');
        return false;
    }
}

function check_option(self){
    var parent = self.parents('.child');
    $('.checkbox-info-detail').removeClass('hide');
    var id = parent.attr('data-value');

    if(self.is(':checked')){
        if( $('#checkbox-info-table .child-'+id).length <= 0 ) {
            if($('.child-'+id).length <= 0){
                var html_content = '<tr class="child child-'+id+'">'+parent.html()+'</tr>';
                var sisa = convert_number(parent.find('.sisa-amount').val(), 'int');

                $('#checkbox-info-table').append(html_content);
                $('#checkbox-info-table .child-'+id+' .sisa-amount').val(sisa);

                $('.child-'+id).find('.checkbox-action').remove();
                $('.child-'+id).find('.on-remove').remove();
                $('.child-'+id).find('.on-show').removeClass('hide');
                
                $('#checkbox-info-table .child-'+id+' .document-table-action').removeClass('hide');
                $('.action-biaya-ttuj').removeClass('hide');

                $.inputPrice({
                    obj: $('.child-'+id+' .input_price'),
                    objComa: $('.child-'+id+' .input_price_coma'),
                });
                $.inputNumber({
                    obj: $('.child-'+id+' .input_number'),
                });
                input_price_min( $('.child-'+id+' .input_price_min') );
                sisa_amount($('#checkbox-info-table .child-'+id+' .sisa-amount'));
                delete_current_document($('#checkbox-info-table .child-'+id+' .document-table-action a'));
            }
        }
    }else{
        $('.child-'+id).remove();
    } 

    if( $('#total-biaya').length > 0 ) {
        calcTotalBiaya();
    }
}

var popup_checkbox = function(){
    $('.checkAll').off('click');
    $('.checkAll').click(function(){
        $('.check-option').not(this).prop('checked', this.checked);

        if($('.child .check-option').length > 0){
            jQuery.each( $('.child .check-option'), function( i, val ) {
                var self = $(this);
                check_option(self)
            });
        }
    });
    
    $('.child .check-option').off('click');
    $('.child .check-option').click(function(){
        var self = $(this);
        check_option(self)
    });
}

var laka_ttuj_change = function(){
    $('#laka-ttuj-change').change(function(){
        var self = $(this);
        var val = self.val();
        $('#laka-driver-change-id option:first').text('Pilih Supir Pengganti');
        
        if(val != ''){
            $.ajax({
                url: '/ajax/getInfoLaka/'+val+'/',
                type: 'POST',
                success: function(response, status) {
                    $('#city-laka').html($(response).filter('#destination-laka').html());

                    if($(response).find('#laka-driver-change-id').val() != ''){
                        var driver_name = $(response).find('#laka-driver-change-name').val();
                        $('#laka-driver-change-id').val($(response).find('#laka-driver-change-id').val());
                        $('#laka-driver-change-id').attr('disabled', true);

                        if( $('#laka-driver-change-id').val() != $(response).find('#laka-driver-change-id').val() ) {
                            $('#laka-driver-change-id').val('');
                            $('#laka-driver-change-id option:first').text(driver_name);
                        }
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
    var dataChange = $('#document-id').attr('data-change');

    if( dataChange != 'false' ) {
        $('#document-id').change(function(){
            var self = $(this);
            var val = self.val();
            var doc_type = $('.cash-bank-handle').val();
            var cash_bank_id = $('#cash-bank-id').val();
            var url = '/ajax/getCustomer/revenue_id:'+val+'/';

            if(doc_type == 'prepayment_in'){
                url = '/ajax/getCustomer/prepayment_id:'+val+'/';
            }

            if( cash_bank_id != '' ) {
                url = cash_bank_id+'/';
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
                        var truck_options = $(response).filter('#truck-options').html();
                        var ppn = $(response).filter('#ppn').html();
                        var content_table = $(response).filter('#content-table').html();

                        if( customer_id != '' && typeof customer_id != 'undefined' ) {
                            $('#receiver-type').val(receiver_type);
                            $('#receiver-id').val(customer_id);
                            $('#cash-bank-user').val(customer_name);
                            $('.cashbank-info-detail').removeClass('hide');

                            if(doc_type == 'prepayment_in'){
                                content_table = content_table.replace(/&lt;/gi, "<").replace(/&gt;/gi, ">").replace(/opentd/gi, "td").replace(/closetd/gi, "/td").replace(/opentr/gi, "tr").replace(/closetr/gi, "/tr");

                                $('.cashbanks-info-table > tbody').empty();
                                $('.cashbanks-info-table > tbody').html(content_table);
                                $.inputPrice({
                                    obj: $('.cashbanks-info-table > tbody .child .input_price'),
                                });
                                delete_custom_field();
                                ajaxModal($('.cashbanks-info-table > tbody .child .ajaxModal'));
                            } else {
                                var html_content = '<tr class="child child-'+coa_id+'" rel="'+coa_id+'"> \
                                    <td>\
                                        '+coa_code+' \
                                        <input type="hidden" name="data[CashBankDetail][coa_id][]" value="'+coa_id+'" id="CashBankDetailCoaId"> \
                                    </td> \
                                    <td> \
                                        '+coa_name+' \
                                    </td> \
                                    <td class="action-search pick-truck"> \
                                        '+truck_options+' \
                                    </td> \
                                    <td class="action-search"> \
                                        <input name="data[CashBankDetail][total][]" class="form-control input_price_coma sisa-amount text-right" type="text" id="CashBankDetailTotal" value="'+ppn+'"> \
                                    </td> \
                                    <td class="action-search"> \
                                        <a href="javascript:" class="delete-custom-field btn btn-danger btn-xs" action_type="cashbank_first"><i class="fa fa-times"></i> Hapus</a> \
                                    </td> \
                                </tr>';
                                $('.cashbanks-info-table > tbody').append(html_content);
                                $.inputPrice({
                                    objComa: $('.cashbanks-info-table > tbody .child:last-child .input_price_coma'),
                                });
                                $.inputNumber({
                                    obj: $('.cashbanks-info-table > tbody .child:last-child .input_number'),
                                });
                                ajaxModal($('.cashbanks-info-table > tbody .child .ajaxModal'));
                                sisa_amount($('.cashbanks-info-table > tbody .child .sisa-amount'));
                                delete_custom_field();
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
}

var convert_number = function ( num, type ) {
    if( typeof type == 'undefined' ) {
        type = 'int';
    }

    if( typeof num != 'undefined' ) {
        num = num.replace(/,/gi, "").replace(/ /gi, "").replace(/IDR/gi, "").replace(/Rp/gi, "");

        if( type == 'int' ) {
            num = parseInt(num);
        } else if( type == 'float' ) {
            num = parseFloat(num);
        }

        if( isNaN(num) ) {
            num = 0;
        }
    }

    return num;
}
var convert_decimal = function ( num, type ) {
    if( typeof type == 'undefined' ) {
        type = 'int';
    }

    if( typeof num != 'undefined' ) {
        num = num.replace(/\./g, "");
        num = num.replace(/,/gi, ".");

        if( type == 'int' ) {
            num = parseInt(num);
        } else if( type == 'float' ) {
            num = parseFloat(num);
        }

        if( isNaN(num) ) {
            num = 0;
        }
    }

    return num;
}

var calcTotalBiaya = function () {
    var biayaObj = $('#checkbox-info-table .sisa-amount');
    var biayaLen = $('#checkbox-info-table .sisa-amount').length;
    var coma = $.checkUndefined($('#total-biaya').attr('data-cent'), 0);
    var totalBiaya = 0;

    for (i = 0; i < biayaLen; i++) {
        totalBiaya += convert_number(biayaObj[i].value, 'float');
    };

    $('#total-biaya').html(formatNumber( totalBiaya, coma ));
    // $('#total-biaya').html(formatNumber( totalBiaya, 0 ));
}

var sisa_amount = function ( obj ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.sisa-amount');
    }

    obj.off('keyup');
    obj.keyup(function(){
        var self = $(this);
        var parent = self.parents('tr');
        var sisa = convert_number(self.val(), 'int');

        var emptyAlert = $.checkUndefined(self.attr('data-alert'), 'Silahkan isi biaya yang akan dibayar');
        var objTotal = parent.children('.total-value');
        var total = convert_number(objTotal.html(), 'int');
        var totalAlert = $.checkUndefined(objTotal.attr('data-alert'), 'Sisa tidak boleh melebihi total biaya');
        var allow = true;

        if( objTotal.length > 0 ) {
            if( sisa > total ) {
                alert(totalAlert);
                self.val(formatNumber( total, 0 ));
                allow = false;
            }
        }

        if( allow == true && sisa == 0 ) {
            alert(emptyAlert);
        }

        calcTotalBiaya();
    });
}

var delete_current_document = function ( obj ) {
    if( typeof obj == 'undefined' ) {
        obj = $('.document-table-action a');
    }

    obj.click(function(){
        var self = $(this);
        var id = self.attr('data-id');
        var parent = $('tr.'+id);
        var alert = $.checkUndefined(self.attr('data-alert'), 'Anda ingin menghapus biaya ini?');
        
        if( confirm(alert) ) {
            parent.remove();
            calcTotalBiaya();
        }

        return false;
    });
}

var view_sj_list = function( obj ) {
    obj.click(function(){
        var val = $(this).attr('data-driver');

        if(val != ''){
            $.ajax({
                url: '/revenues/surat_jalan_outstanding/'+val+'/',
                type: 'POST',
                success: function(response, status) {
                    if( $(response).filter('#wrapper-sj').html() != null ) {
                        $('#informasi-sj').html($(response).filter('#wrapper-sj').html());

                        $('#sj-remove').click(function(){
                            $('#informasi-sj').empty();
                        });
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        }
    });
}

var auth_form_open = function(obj){
    if( typeof obj == 'undefined' ) {
        obj = $('.auth-form-open');
    }

    obj.change(function(){
        var self = $(this);
        var parent = self.parents('.box');
        var val = self.val();
        var group_id = $('#group-id').val();
        var group_branch_id = self.attr('data-id');

        check_full_auth(parent, group_id, val, false, self, group_branch_id);

        // if(val != ''){
        //     $.ajax({
        //         url: '/ajax/auth_action_module/'+group_id+'/'+val+'/',
        //         type: 'POST',
        //         success: function(response, status) {
        //             if( $(response).filter('#box-action-auth').html() != null ) {
        //                 var target_box = parent.find('.auth-action-box');
                        
        //                 target_box.html($(response).filter('#box-action-auth').html()).show();                        
                        
        //                 parent.find('.trigger-collapse').click();

        //                 parent.find('.delete-custom-field').attr('group-branch-id', $(response).filter('#group_branch_id').html());
        //                 action_child_module();
        //             }
        //         },
        //         error: function(XMLHttpRequest, textStatus, errorThrown) {
        //             alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
        //             return false;
        //         }
        //     });
        // }else{
        //     parent.find('.auth-action-box').html('');
        //     parent.find('.trigger-collapse').html('<i class="fa fa-plus"></i>');            
        // }
    });
}

var action_child_module = function(obj){
    if( typeof obj == 'undefined' ) {
        obj = $('.action-child-module');
    }

    obj.click(function(){
        var self = $(this);
        var parent = $(this).parents('.box-body');
        var id = self.attr('action-id');
        var branch_id = self.attr('branch-id');
        var parent_div = self.parents('.action-link-auth');

        $.ajax({
            url: '/ajax/auth_action_child_module/'+branch_id+'/'+id+'/',
            type: 'POST',
            success: function(response, status) {
                parent_div.html(response);

                action_child_module(parent_div.children('.action-child-module'));
                parent.find('.trigger-collapse').click();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    });
}

var accordion_toggle = function(obj){
    if( typeof obj == 'undefined' ) {
        obj = $('.trigger-collapse');
    }

    obj.click(function(){
        var self = $(this);
        var rel = self.attr('rel');
        var parent = self.parents('.box');

        if(rel == 'plus'){
            self.html('<i class="fa fa-minus"></i>');
            self.attr('rel', 'times');
            parent.find('.auth-action-box').show();
        }else{
            self.html('<i class="fa fa-plus"></i>');
            parent.find('.auth-action-box').hide();
            self.attr('rel', 'plus');
        }
    });
}

function check_full_auth(parent, group_id, val, attr, self, group_branch_id){
    if(val != ''){
        var url = '/ajax/auth_action_module/'+group_id+'/'+val+'/';

        if(attr != false){
            url += attr + '/';
        }

        if( typeof group_branch_id != 'undefined' ) {
            url += 'id:'+group_branch_id+'/';
        }

        $.ajax({
            url: url,
            type: 'POST',
            success: function(response, status) {
                var status = $(response).filter('#status').text();
                var msg = $(response).filter('#message').text();

                if( status != 'error' ) {
                    if( $(response).filter('#box-action-auth').html() != null ) {
                        var target_box = parent.find('.auth-action-box');
                        
                        target_box.html($(response).filter('#box-action-auth').html()).show();
                        parent.find('.trigger-collapse').attr('rel', 'times').html('<i class="fa fa-minus"></i>');

                        parent.find('.delete-custom-field').attr('group-branch-id', $(response).filter('#group_branch_id').html());
                        action_child_module( parent.find('.action-child-module') );
                        branch_module_check();
                    }
                } else {
                    self.val('');
                    alert(msg);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    }else{
        parent.find('.auth-action-box').html('');
        parent.find('.trigger-collapse').html('<i class="fa fa-plus"></i>');            
    }
}

var branch_check = function(obj){
    if( typeof obj == 'undefined' ) {
        obj = $('.branch-check');
    }

    obj.click(function(){
        var text = 'check semua';
        var self = $(this);
        var attr = self.attr('type-action');
        var parent = self.parents('.box');

        var val = parent.find('.auth-form-open').val();
        var group_id = $('#group-id').val();

        if(attr == 'uncheckall'){
            text = 'tidak check semua';
        }

        if(val != ''){
            if( confirm('Apakah Anda ingin '+text+'?') ) {
                check_full_auth(parent, group_id, val, attr, self);
            } else {
                return false;
            }
        }else{
            alert('Harap Pilih Cabang');
        }
    });
}

var branch_module_check = function(obj){
    if( typeof obj == 'undefined' ) {
        obj = $('.branch-module-check');
    }

    obj.click(function(){
        var text = 'check semua';
        var self = $(this);
        var attr = self.attr('type-action');
        var parent_id = self.attr('parent-id');
        var main_parent = self.parents('.box');
        var parent = self.parents('.box-action-auth-module');
        var city_id = main_parent.find('.auth-form-open').val();

        var parent_rel = self.parents('.box').attr('rel');
        
        var val = parent.find('.auth-form-open').val();
        var group_id = $('#group-id').val();

        if(attr == 'uncheckall'){
            text = 'tidak check semua';
        }

        if(val != '' && city_id != '' && parent_id != ''){
            if( confirm('Apakah Anda ingin '+text+'?') ) {
                $.ajax({
                    url: '/ajax/check_per_branch/'+group_id+'/'+city_id+'/'+parent_id+'/'+attr+'/',
                    type: 'POST',
                    success: function(response, status) {
                        if( $(response).filter('.list-auth-action').html() != null ) {
                            var target_box = parent.find('.box-act');
                            
                            target_box.html($(response).filter('.list-auth-action').html());

                            action_child_module($('.box[rel="'+parent_rel+'"] .box-act .action-child-module'));
                        }
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                        return false;
                    }
                });
            } else {
                return false;
            }
        }else{
            alert('Error! harap menghubungi bagian IT');
        }
    });
}

var _callReverseDate = function ( date ) {
    var tmp = date.split('/');
    var result = date;

    if( tmp.length == 3 ) {
        day = tmp[0];
        month = tmp[1];
        year = tmp[2];

        result = year+'-'+month+'-'+day;
    }

    return result;
}

var _callMonthInstallment = function () {
    var tgl_leasing = $('.leasing-date-installment').val();
    var month_last_date = $('.leasing-last-month-installment').val();
    var year_last_date = $('.leasing-last-year-installment').val();

    var month = false;
    var year = false;
    var months = 0;

    if( tgl_leasing != '' && month_last_date != '' && year_last_date != '' ) {
        tmp = tgl_leasing.split('/');

        if( tmp.length == 3 ) {
            month = tmp[1];
            year = tmp[2];
        }

        var a = new Date(year, month),
        b = new Date(year_last_date, month_last_date),

        months = Math.ceil((b-a)/2628000000);

        $('.month-leasing').val(months);
    }
}

var calc_pokok_leasing = function () {
    // var tgl_leasing = $('.leasing-date-installment').val();
    // var month_last_date = $('.leasing-last-month-installment').val();
    // var year_last_date = $('.leasing-last-year-installment').val();
    var months = $('.month-leasing').val();
    var leasing_dp = convert_number($('.leasing-dp').val());
    var grand_total = convert_number($('#grand-total-leasing').html());
    // var total_leasing = convert_number($('.total-leasing').val());
    var annual_interest = convert_number($('.annual-interest').val(), 'float');

    // var month = false;
    // var year = false;
    // var months = 0;

    // if( tgl_leasing != '' && month_last_date != '' && year_last_date != '' ) {
    //     tmp = tgl_leasing.split('/');

    //     if( tmp.length == 3 ) {
    //         month = tmp[1];
    //         year = tmp[2];
    //     }

    //     var a = new Date(year, month),
    //     b = new Date(year_last_date, month_last_date),

    //     months = Math.floor((b-a)/2628000000);;
    // }

    if( !isNaN(months) && !isNaN(leasing_dp) && !isNaN(grand_total) ) {
        var year = months/12;
        var loan = grand_total-leasing_dp;
        var pokok = Math.ceil(loan/months);

        var rate_annual_interest = annual_interest/100;
        var bunga = Math.ceil((rate_annual_interest*year*loan)/months);
        // var bunga = Math.ceil((total_leasing/months)-pokok);

        $('.total-installment').val(formatNumber( pokok, 0 ));
        $('.bunga-leasing').val(formatNumber( bunga, 0 ));
    }
}

var _callTableLeasingExpired = function () {
    var obj = $('tr.child');
    var len = obj.length;

    for (i=0; i < len; i++) {
        var rel = obj[i].getAttribute('rel');

        _callLeasingExpired(rel);
    };
}

$(function() {
    leasing_action();
    laka_ttuj_change();
    invoice_price_payment();
    part_motor_lku();
    price_tipe_motor();
    price_perlengkapan();
    
    sisa_amount();
    delete_current_document();

    auth_form_open();
    action_child_module();
    accordion_toggle();
    branch_check();
    branch_module_check();

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

    $('#getKotaTujuan').change(function() {
        var self = $(this);

        if( self.val() != '' ) {
            $.ajax({
                url: '/ajax/getKotaTujuan/'+self.val()+'/',
                type: 'POST',
                success: function(response, status) {
                    $('.to_city #getTruck').attr('readonly', false).html($(response).filter('#to_city_id').html());
                    $.callChoosen({
                        obj: $('.to_city #getTruck'),
                    });
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
            $.callChoosen({
                obj: $('#getTruck'),
                init: 'destroy',
            });
        }
    });

    $('#getTruck').change(function() {
        if( $('.from_city #getKotaTujuan').length > 0 ) {
            var self = $(this);
            var from_city_id = $('.from_city #getKotaTujuan').val();
            var nopol = $('#truckID').val();
            var customer_id = $('#getKotaAsal').val();
            var lenCity = $('.city-retail-id').length;
            var cityRetail = $('.city-retail-id');
            var tipeMotorLen = $('#ttujDetail .tbody > div').length;
            var value = self.val();

            if( tipeMotorLen == 1 ) {
                $('#ttujDetail .tbody .field-copy .city-retail-id').val(value);
                $('#ttujDetail .tbody .field-copy .city-retail-id').trigger('change')
            }

            if( value != '' ) {
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
            } else {
                $('#truckID').val('').attr('disabled', true);
                $('#truckBrowse').attr('disabled', true);
                $('.driver_name').val('');
                $('.driver_id').val('');
                $('.truck_capacity').val('');
                $('#biaya-uang-jalan input').val('');
            }
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

    delete_custom_field();

    $('.submit-form').click(function() {
        var action_type = $(this).attr('action_type');
        var alert_msg = $('#ttuj-alert-msg');

        if( alert_msg.length > 0 ) {
            alert_msg_text = alert_msg.val().replace(/TenterT/gi, "\n");

            if( confirm(alert_msg_text) ) {
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
            } else {
                return false;
            }
        } else if( action_type == 'commit' ) {
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
                    $.inputNumber();
                    $.inputPrice();
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
                    $.inputNumber();
                    $.inputPrice();
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
        var action = $.checkUndefined(self.attr('data-action'), false);
        var url = '/ajax/getInfoTtujRevenue/'+self.val()+'/';

        if( action != false ) {
            url += action+'/';
        }


        if( self.val() != '' ) {
            findInfoTTujRevenue(url);
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
                $('#laka-driver-change-id').val('');
                $('#laka-driver-change-id option:eq("")').text('Pilih Supir Pengganti');

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
    });

    $('#getTtujCustomerInfoKsu').change(function(){
        var self = $(this);
        var val = self.val();
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
            $('.popover-hover-top-click.in,.popover-hover-bottom-click.in').trigger('click');
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
    ajaxLink();
    city_revenue_change();
    checkCharge( $('.additional-charge') );
    check_all_checkbox();

    $('.custom-find-invoice,#invoiceType').change(function(){
        var customer_id = $('.custom-find-invoice').val();
        var invoiceType = $('#invoiceType').val();
        var url = '/ajax/getInvoiceInfo/';

        if( customer_id != '' ) {
            url += customer_id+'/';
        }

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
                    var pattern = $.checkUndefined($(response).filter('#pattern-code').val(), '');

                    if( pattern == '' ) {
                        pattern = $.checkUndefined($(response).find('#pattern-code').val(), '');
                    }

                    $('#invoice-info').html(response);
                    $('#no_invoice').val(pattern);
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
        } else {
            url += 'angkut/';
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
    $.daterangepicker();

    $('.date-resign-handle').click(function(){
        if($('.date-resign-handle input').is(':checked')) {
            $('#resign-date').removeClass('hide');
        }else{
            $('#resign-date').addClass('hide');
        }
    });

    $('.completed-handle').click(function(){
        if($('.completed-handle input').is(':checked')) {
            $('#desc-complete').removeClass('hide');
            $('#date-complete').removeClass('hide');
        }else{
            $('#desc-complete').addClass('hide');
            $('#date-complete').addClass('hide');
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

    var _callCashBankHandle = function ( self ) {
        var value = self.val();

        if(value == 'in' || value == 'ppn_in' || value == 'prepayment_in'){
            $('.cash_bank_user_type').html('Diterima dari');
        }else{
            $('.cash_bank_user_type').html('Dibayar kepada');
        }
    }

    $('.cash-bank-handle').change(function(){
        var value = $(this).val();
        var prepayment_out_id = $('#prepayment-out-id').val();

        _callCashBankHandle( $(this) );

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

    _callCashBankHandle( $('.cash-bank-handle') );
    get_document_cashbank();

    // $('.invoice-pph, .invoice-ppn').keyup(function(){
    getTotalPick();
    // });

    $(document).ajaxStart(function() {
        if( !disableAjax ) {
            $("#ajaxLoading").slideDown(100);
        }
    }).ajaxStop(function() {
        if( !disableAjax ) {
            $("#ajaxLoading").slideUp(200);
        }
    });

    $('.handle-atpm').click(function(){
        if($(this).is(':checked')) {
            $('#atpm-box').removeClass('hide');
            $('.total-price-claim').text('-');
            $('#grand-total-ksu').text('IDR 0');
            $('.hide-atpm').addClass('hide');
        }else{
            $('#atpm-box').addClass('hide');
            $('.price-perlengkapan').val('');
            $('.hide-atpm').removeClass('hide');
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
    
    $('.driver-pengganti').change(function(){
        var val = $(this).val();

        if(val != ''){
            $.ajax({
                url: '/ajax/getSjDriver/'+val+'/',
                type: 'POST',
                success: function(response, status) {
                    if( $(response).filter('#sj_outstanding').html() != null ) {
                        $('.sj_outstanding_pengganti').html($(response).filter('#sj_outstanding').html());
                        view_sj_list( $('.sj_outstanding_pengganti #view_sj_outstanding') );
                    } else {
                        $('.sj_outstanding_pengganti').html('');
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        }
    });

    $(document).click(function(e){
        var target = $(e.target);
        
        if( target.parents('.columnDropdown').length > 0 ) {
            target.parents('.columnDropdown').addClass('open');
        } else {
            $('.columnDropdown').removeClass('open');
        }
    });

    $('#handle-module input').click(function(){
        if( $(this).is(':checked') ){
            $('.box-action-module').hide();
            $('#parent_branch').show();
            $('#order-control').attr('readonly', false);
        }else{
            $('.box-action-module').show();
            $('#parent_branch').hide();
            $('#parent_branch select').val('');
            $('#order-control').attr('readonly', true);
        }
    });

    $('.order-handle').change(function(){
        var val = $(this).val();

        var order = 4;
        if(val == 'lihat'){
            order = 1;
        }else if(val == 'tambah'){
            order = 2;
        }else if(val == 'ubah'){
            order = 3;
        }

        $('#order-control').val(order);
        $('#order-control').attr('readonly', true);
    });

    $('.change-branch').change(function(){
        var val = $(this).val();

        $('.default-branch-id').val(val);
    });

    $('.active-field').click(function(){
        var self = $(this);
        var val = self.attr('data-active');
        var fieldName = $('.'+val);

        if( fieldName.is(":disabled") ) {
            fieldName.attr('disabled', false);
            self.html('<i class="fa fa-angle-double-left "></i>');
        } else {
            fieldName.attr('disabled', true);
            self.html('<i class="fa fa-angle-double-right "></i>');
        }
    });

    input_price_min();
    datepicker();
    $.callChoosen();

    $('.leasing-dp,.total-leasing,.month-leasing,.leasing-dp,.price-leasing-truck,.annual-interest').blur(function(){
        calc_pokok_leasing();
    });

    $('form').submit(function(){
        var self = $(this);
        var button = self.find('button[type="submit"]');
        var idAttr = self.attr('id');

        if( idAttr != 'form-report' ) {
            button.attr('disabled', true);
        }
    });

    $('.trigger-disabled').click(function(){
        var self = $(this);
        var alert = $.checkUndefined(self.attr('data-alert'), null);
        var body = self.parents('body');
        var button = body.find('button[type="submit"],.trigger-disabled');

        if( alert != null ) {
            if( confirm(alert) ) {
                button.attr('disabled', true);
            } else {
                return false;
            }
        } else {
            button.attr('disabled', true);
        }
    });

    $('.leasing-last-year-installment,.leasing-last-month-installment,.leasing-last-day-installment').change(function(){
        _callMonthInstallment();
    });
    $('.leasing-date-installment').blur(function(){
        _callMonthInstallment();
    });

    $('.leasing-payment-date').change(function(){
        _callTableLeasingExpired()
    });
    $('.leasing-payment-date').keyup(function(){
        _callTableLeasingExpired()
    });
    $.getLeasingPayment({
        obj: $('.leasing-trigger'),
    });
    $('.btn-closing').click(function (e) {
        var self = $(this);
        var form = self.parents('form');
        var url = form.attr('action');
        var data = form.serialize();
        
        var progress = $(".loading-progress").progressTimer({
            timeLimit: 10,
            onFinish: function () {
                alert('Proses closing telah selesai');
                // window.location.reload();
            }
        });

        $.ajax({
            data: data,
            url: url,
            type: 'POST',
        }).error(function(){
            progress.progressTimer('error', {
                errorText:'Gagal melakukan proses',
                onFinish:function(){
                    alert('Gagal melakukan proses');
                }
            });
        }).done(function(){
            progress.progressTimer('complete');
        });

        return false;
    });

    $.inputPrice();
    $.inputNumber();
    $.rebuildFunction();
    $.onFocused();
    $.Autocomplete();
    $.callInterval();
    $.wheelPosition();

    // $('#ppn-total-invoice').blur(function(){
    //     var self = $(this);
    //     var ppn = self.val();
    //     var total = _callTotalInvoice();

    //     ppn = $.convertNumber(ppn, 'int');
    //     ppn = (ppn / total) * 100;
    //     ppn = $.formatDecimal(ppn, 2);

    //     $('.invoice-ppn').val(ppn);
    // });
    // $('#pph-total-invoice').blur(function(){
    //     var self = $(this);
    //     var pph = self.val();
    //     var total = _callTotalInvoice();

    //     pph = $.convertNumber(pph, 'int');
    //     pph = (pph / total) * 100;
    //     pph = $.formatDecimal(pph, 2);

    //     $('.invoice-pph').val(pph);
    // });

    $('#id-choosen').change(function(){
        var self = $(this);
        var data_reset = $.checkUndefined(self.attr('data-reset'), false);
        
        if( data_reset != false ) {
            $(data_reset).remove();
            $.getLeasingGrandtotal();
        }
    });

    $('.change-month,.change-year').change(function(){
        var month = $('.change-month').val();
        var year = $.convertNumber($('.change-year').val(), 'int', 0);
        
        $('.target-month').val(month);
        $('.target-year').val(year+1);
    });

    if( $('.data-on-change').length > 0 ) {
        $('.data-on-change').change(function(){
            var self = $(this);
            var val = self.val();

            if( val != '' ) {
                var text = self.find('option:selected').text();
                var target = self.attr('data-target');
                
                $(target).html(text);
            }
        });
        $('.data-on-change').trigger('change');
    }

    if( $('.search-collapse').length > 0 ) {
        $('.search-collapse').click(function(){
            var self = $(this);

            $.callChoosen({
                obj: $('.box-collapse .chosen-select'),
                init: 'destroy',
            });

            $.callChoosen({
                obj: $('.box-collapse .chosen-select'),
            });
        });
    }

    if( $('.load-chart').length > 0 ) {
        /*
         * Custom Label formatter
         * ----------------------
         */
        function labelFormatter(label, series) {
            return "<div style='font-size:13px; text-align:center; padding:2px; color: #fff; font-weight: 600;'>"
                    + label
                    + "<br/>"
                    + Math.round(series.percent) + "%</div>";
        }

        function _callLoadChart ( self ) {
            var url = self.attr('data-url');
            var type = self.attr('data-type');
            var target = self.attr('data-target');
            var dataForm = $.checkUndefined(self.attr('data-form'), null);
            var data = null;

            if( dataForm != null ) {
                data = $(dataForm).serialize();
            }

            $(target).html('');

            $.ajax({
                url: url,
                type: 'POST',
                data: data,
                success: function(result, status) {
                    if( result != '' ) {
                        result = jQuery.parseJSON(result);
                        var more = result.url;
                        var dataChart = result.chart;
                        
                        switch(type) {
                            case 'pie':
                                var donutData = dataChart;
                                // [
                                //     {label: "Series2", data: 30, color: "#3c8dbc"},
                                //     {label: "Series3", data: 20, color: "#0073b7"},
                                //     {label: "Series4", data: 50, color: "#00c0ef"}
                                // ];
                                $.plot(target, donutData, {
                                    series: {
                                        pie: {
                                            show: true,
                                            radius: 1,
                                            innerRadius: 0.5,
                                            label: {
                                                show: true,
                                                radius: 2 / 3,
                                                formatter: labelFormatter,
                                                threshold: 0.1
                                            }
                                        }
                                    },
                                });
                                
                                $(target).parents('.box').find('.box-footer').show();
                                $(target).parents('.box').find('.box-footer a').attr('href', more);
                                break;
                        }
                    } else {
                        $(target).html('<p class="alert alert-danger no-margin">Data belum tersedia</p>');
                        $(target).parents('.box').find('.box-footer').hide();
                    }
                },
                error: function(XMLHttpRequest, textStatus, errorThrown) {
                    alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                    return false;
                }
            });
        }

        $('.chart-change').change(function(){
            var self = $(this);
            var target = self.attr('data-target');

            _callLoadChart($(target));
        });

        $.each( $('.load-chart'), function( i, val ) {
            var self = $(this);
            
            _callLoadChart(self);
        });
    }

    $( "body" ).delegate( '.duplicate-tr', "init click", function(event) {
        var self = $(this);
        var parent = self.parents('tbody');
        var duplicate = self.parents('tr');
        var rel = duplicate.attr('rel');

        var uniqid = Date.now();
        var html = duplicate.html();

        html = '<tr class="pick-document" rel="'+rel+'" copy="'+uniqid+'">'+html+'</tr>';
        duplicate.after(html);

        var data_empty = $('tr[copy="'+uniqid+'"] .duplicate-empty');

        $('tr[copy="'+uniqid+'"] .duplicate-remove').remove();
        $.rebuildFunctionAjax($('tr[copy="'+uniqid+'"]'));

        var rowspan = $('.temp-document-picker tr.pick-document[rel="'+rel+'"]').length;
        duplicate.find('.duplicate-rowspan').attr('rowspan', rowspan);

        $.each( data_empty, function( i, val ) {
            var self = $(this);
            var type = self.attr('type');

            if( type == 'text' ) {
                self.val('');
            } else {
                self.html('');
            }
        });

        return false;
    });
});