(function ( $ ) {
    $.rowAdded = function(options){
        var settings = $.extend({
            obj: $('.field-added'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.click(function(e){
                if( $('.field-copy .tipe_motor_id').length > 0 ) {
                    $.callChoosen({
                        obj: $('.field-copy .tipe_motor_id'),
                        init: 'destroy',
                    });
                }
                if( $('.field-copy .city-retail-id').length > 0 ) {
                    $.callChoosen({
                        obj: $('.field-copy .city-retail-id'),
                        init: 'destroy',
                    });
                }

                var self = $(this);
                var parentAdded = self.parents('.form-added');
                var content = parentAdded.find('.field-content');
                var temp = parentAdded.find('.field-copy');
                var value = temp.html();

                content.append( value );

                $('.field-content > div.item:last-child input,.field-content > div.item:last-child select').val('');

                if( $('.field-content > div.item:last-child .tipe_motor_id').length > 0 ) {
                    $('.field-content > div.item:last-child .tipe_motor_id').change(function() {
                        $.getNopol();
                    });
                }

                if( $('.field-content > div.item:last-child .city-retail-id').length > 0 && $('#getTruck').length > 0 ) {
                   $('.field-content > div.item:last-child .city-retail-id').val( $('#getTruck').val() );
                }

                $.rowRemoved();
                $.callChoosen({
                    obj: $('.form-added .chosen-select'),
                });
                $.qtyMuatanPress({
                    obj: $('.form-added .qty-muatan'),
                });

                return false;
            });

            $.rowRemoved();
        }
    }

    $.rowRemoved = function(options){
        var settings = $.extend({
            obj: $('.form-added .removed'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            settings.obj.click(function(e){
                var self = $(this);

                var item = self.parents('.item');

                item.remove();
                $.getNopol();
                return false;
            });
        }
    }
    $.callChoosen = function(options){
        var settings = $.extend({
            obj: $('.chosen-select'),
            init: false,
        }, options );

        if( settings.obj.length > 0 ) {
            if( settings.init == false ) {
                settings.obj.select2();
            } else {
                settings.obj.select2(settings.init);
            }
        }
    }

    $.qtyMuatanPress = function(options){
        var settings = $.extend({
            obj: $('.qty-muatan'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('keyup');
            settings.obj.keyup(function() {
                $.calcUangJalan();
            });
        }
    }

    $.calcUangJalan = function(){
        var qtyLen = $('#ttujDetail .tbody > div').length;
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

        if( $('.uang_jalan_2').length > 0 ) {
            var uang_jalan_2 = $('.uang_jalan_2').val().replace(/,/gi, "");
        } else {
            var uang_jalan_2 = '';
        }

        var tipeMotorObj = $('#ttujDetail .tbody .tipe_motor_id');
        var qtyMuatanObj = $('#ttujDetail .tbody .qty-muatan');

        for (var i = 0; i < qtyLen; i++) {
            var tipe_motor_id = tipeMotorObj[i].value;
            var group_motor_id = parseInt($('#group_tipe_motor_id option[value="'+tipe_motor_id+'"]').text());
            var qtyMuatan = parseInt(qtyMuatanObj[i].value);
            
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

    $.getNopol = function(){
        var self = $('#truckID');

        if( self.length > 0 ) {
            var action_type = self.attr('data-action');
            var truck_id = self.val();

            if( action_type == 'truck-mutation' ) {
                $.ajax({
                    url: '/ajax/getDataTruck/' + truck_id + '/',
                    type: 'POST',
                    success: function(response, status) {
                        var branch_name = $(response).filter('#branch_name').html();
                        var category_name = $(response).filter('#category_name').html();
                        var facility_name = $(response).filter('#facility_name').html();
                        var driver_name = $(response).filter('#driver_name').html();
                        var truck_capacity = $(response).filter('#truck_capacity').html();
                        var truck_customers = $(response).filter('#truck_customers').html();

                        $('#branch_name').val(branch_name);
                        $('#truck_category').val(category_name);
                        $('#truck_facility').val(facility_name);
                        $('#driver_name').val(driver_name);
                        $('#truck_capacity').val(truck_capacity);
                        $('#truck_customers').html(truck_customers);
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                        return false;
                    }
                });
            } else if( $('.from_city #getKotaTujuan').length > 0 ) {
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

                    var qtyLen = $('#ttujDetail .tbody > div').length;
                    var total_muatan = 0;
                    var qtyMuatanObj = $('#ttujDetail .tbody .qty-muatan');

                    for (var i = 0; i < qtyLen; i++) {
                        var qtyMuatan = parseInt(qtyMuatanObj[i].value);

                        if( isNaN(qtyMuatan) ) {
                            qtyMuatan = 0;
                        }
                        total_muatan += qtyMuatan;
                    };

                    if( isNaN( total_muatan ) || total_muatan == 0 ) {
                        total_muatan = 0;
                    }
                    $('.total-unit-muatan').html(total_muatan);
                }
            }
        }
    }

    $.getLeasingGrandtotal = function(){
        var obj = $('.document-pick-info-detail .leasing-total');
        var objTotal = $('#field-grand-total-document .total');
        var leng = obj.length;

        if( objTotal.length > 0 ) {
            var total = 0;

            for (i = 0; i < leng; i++) {
                total += convert_number(obj[i].innerHTML, 'int');
            };

            var formatTotal = formatNumber( total, 0 );
            objTotal.html(formatTotal);
        }
    }

    $.getLeasingPayment = function(options){
        var settings = $.extend({
            obj: $('.leasing-trigger'),
        }, options );

        var getTotal = function ( parent ) {
            var installment = convert_number(parent.find('.installment').val(), 'int');
            var installmentRate = convert_number(parent.find('.installment-rate').val(), 'int');
            var denda = convert_number(parent.find('.denda').val(), 'int');

            var total = installment+installmentRate+denda;
            var formatTotal = formatNumber( total, 0 );

            parent.find('.leasing-total').html(formatTotal);
            $.getLeasingGrandtotal();
        };

        settings.obj.keyup(function(){
            getTotal( settings.obj.parents('tr') );
        });

        getTotal( settings.obj.parents('tr') );
    }
}( jQuery ));