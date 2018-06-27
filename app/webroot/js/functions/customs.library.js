(function ( $ ) {
    $.checkUndefined = function (value, _default) {
        if(typeof value == 'undefined' ) {
            value = _default;
        }

        return value;
    }

    $.onFocused = function (options) {
        var settings = $.extend({
            obj: $('.on-focus'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.focus();
        }
    }

    $.rowAdded = function(options){
        var settings = $.extend({
            obj: $('.field-added'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            settings.obj.click(function(e){
                var chosen = $('.form-added .chosen-select');

                if( chosen.length > 0 ) {
                    $.callChoosen({
                        obj: chosen,
                        init: 'destroy',
                    });
                }

                var self = $(this);
                var parentAdded = self.parents('.form-added');
                var content = parentAdded.find('.field-content');
                var temp = parentAdded.find('.field-copy');
                var value = temp.html();
                var tag = temp.attr('data-tag');

                if( tag == 'tr' ) {
                    value = '<tr class="pick-document item">'+value+'</tr>';
                }

                content.append( value );

                $('.field-content > .item:last-child input,.field-content > .item:last-child select').val('');
                $('.field-content > .item:last-child .error-message').remove();

                if( $('.field-content > .item:last-child .tipe_motor_id').length > 0 ) {
                    $('.field-content > .item:last-child .tipe_motor_id').change(function() {
                        $.getNopol();
                    });
                }

                if( $('.field-content > .item:last-child .city-retail-id').length > 0 && $('#getTruck').length > 0 ) {
                   $('.field-content > .item:last-child .city-retail-id').val( $('#getTruck').val() );
                }
                
                var chosen = $('.form-added .chosen-select');

                $.calcTotal();
                $.rowRemoved();
                $.qtyMuatanPress({
                    obj: $('.form-added .qty-muatan'),
                });
                $.callChoosen({
                    obj: chosen,
                });
                $.inputPrice({
                    obj: $('.field-content > div.item:last-child .input_price'),
                    objComa: $('.field-content > div.item:last-child .input_price_coma'),
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
                var data_eval = $.checkUndefined(self.attr('data-eval'), null);

                item.remove();
                $.getNopol();

                if( data_eval != null ) {
                    eval(data_eval);
                }

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
                settings.obj.each(function(i,item){
                    var tag = $.checkUndefined($(item).attr('data-tag'), null);

                    if( tag == 'true' ) {
                        tag = true;
                    } else {
                        tag = false;
                    }

                    $(item).select2({
                        tags: tag,
                    });
                });
            } else {
                settings.obj.each(function(i,item){
                    $(item).select2(settings.init);
                });
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
        var totalMuatanExtra = 0;
        var commission_min_qty = $('.commission_min_qty').val();

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
            var qtyMuatan = $.convertNumber(qtyMuatanObj[i].value, 'int');
            var converter_uang_jalan_extra = $.convertNumber($('#converter-uang-jalan-extra-'+group_motor_id).html(), 'int');

            total_muatan += qtyMuatan;

            if( converter_uang_jalan_extra != 0 ) {
                totalMuatanExtra += qtyMuatan * converter_uang_jalan_extra;
            } else {
                totalMuatanExtra += qtyMuatan;
            }

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

            // if( typeof $('.uang-jalan-extra-'+group_motor_id).html() != 'undefined' ) {
            //     var qtyMuatanExtra = qtyMuatan;
            //     uang_jalan_extra_tipe_motor = parseInt($('.uang-jalan-extra-'+group_motor_id).html());
            //     min_capacity_uang_jalan_extra = parseInt($('.uang-jalan-extra-min-capacity-'+group_motor_id).html());

            //     if( converter_uang_jalan_extra != 0 ) {
            //         qtyMuatanExtra = qtyMuatanExtra * converter_uang_jalan_extra;
            //     }

            //     if( qtyMuatanExtra > min_capacity_uang_jalan_extra ) {
            //         var capacityCost = qtyMuatanExtra - min_capacity_uang_jalan_extra;
            //         total_uang_jalan_extra += uang_jalan_extra_tipe_motor*capacityCost;
            //         min_capacity_use += capacityCost;
            //     }
            // }

            // if( typeof $('.commission-extra-'+group_motor_id).html() != 'undefined' ) {
            //     commission_extra_tipe_motor = parseInt($('.commission-extra-'+group_motor_id).html());
            //     min_capacity_commission_extra = parseInt($('.commission-extra-min-capacity-'+group_motor_id).html());


            //     if( qtyMuatan > min_capacity_commission_extra ) {
            //         var capacityCost = qtyMuatan - min_capacity_commission_extra;

            //         if( converter_uang_jalan_extra != 0 ) {
            //             commission_extra_tipe_motor *= converter_uang_jalan_extra;
            //         }

            //         total_commission_extra += commission_extra_tipe_motor*capacityCost;
            //         min_capacity_use += capacityCost;
            //     }
            // }
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

        if( totalMuatanExtra > min_capacity ) {
            var capacityCost = totalMuatanExtra - min_capacity;
            
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

        if( total_muatan > commission_min_qty ) {
            var capacityCost = total_muatan - commission_min_qty;
            
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
        $('.uang_jalan_2').val( formatNumber( uang_jalan_2, 0 ) );
        $('.uang_kuli_muat').val( formatNumber( uang_kuli_muat, 0 ) );
        $('.uang_kuli_bongkar').val( formatNumber( uang_kuli_bongkar, 0 ) );
        $('.asdp').val( formatNumber( asdp, 0 ) );
        $('.uang_kawal').val( formatNumber( uang_kawal, 0 ) );
        $('.uang_keamanan').val( formatNumber( uang_keamanan, 0 ) );
        $('.uang_jalan_extra').val( formatNumber( uang_jalan_extra, 0 ) );
        $('.commission').val( formatNumber( commission, 0 ) );
        $('.commission_extra').val( formatNumber( commission_extra, 0 ) );

        if( uang_jalan_2 != 0 ) {
            $('.wrapper_uang_jalan_2').removeClass('hide');
        } else {
            $('.wrapper_uang_jalan_2').addClass('hide');
        }

        if( uang_kuli_muat != 0 ) {
            $('.wrapper_uang_kuli_muat').removeClass('hide');
        } else {
            $('.wrapper_uang_kuli_muat').addClass('hide');
        }

        if( uang_kuli_bongkar != 0 ) {
            $('.wrapper_uang_kuli_bongkar').removeClass('hide');
        } else {
            $('.wrapper_uang_kuli_bongkar').addClass('hide');
        }

        if( asdp != 0 ) {
            $('.wrapper_asdp').removeClass('hide');
        } else {
            $('.wrapper_asdp').addClass('hide');
        }

        if( uang_kawal != 0 ) {
            $('.wrapper_uang_kawal').removeClass('hide');
        } else {
            $('.wrapper_uang_kawal').addClass('hide');
        }

        if( uang_keamanan != 0 ) {
            $('.wrapper_uang_keamanan').removeClass('hide');
        } else {
            $('.wrapper_uang_keamanan').addClass('hide');
        }

        if( uang_jalan_extra != 0 ) {
            $('.wrapper_uang_jalan_extra').removeClass('hide');
            $('.wrapper_min_capacity').removeClass('hide');
        } else {
            $('.wrapper_uang_jalan_extra').addClass('hide');
            $('.wrapper_min_capacity').addClass('hide');
        }

        if( commission != 0 ) {
            $('.wrapper_commission').removeClass('hide');
        } else {
            $('.wrapper_commission').addClass('hide');
        }

        if( commission_extra != 0 ) {
            $('.wrapper_commission_extra').removeClass('hide');
        } else {
            $('.wrapper_commission_extra').addClass('hide');
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
            getTotal( $(this).parents('tr') );
        });

        // getTotal( settings.obj.parents('tr') );
    }

    $.convertDecimal = function(self, decimal){
        var val = $.convertNumber(self.val(), 'string');
        var decimal = $.checkUndefined(decimal, 0);
        
        return $.formatDecimal(val, decimal);
    }

    $.inputPrice = function(options){
        var settings = $.extend({
            obj: $('.input_price'),
            objComa: $('.input_price_coma'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.priceFormat({
                doneFunc: function(obj, val) {
                    currencyVal = val;
                    currencyVal = currencyVal.replace(/,/gi, "")
                    obj.next(".input_hidden").val(currencyVal);
                }
            });
        }

        if( settings.objComa.length > 0 ) {
            settings.objComa.off('blur');
            settings.objComa.blur(function(){
                var self = $(this);
                var dec = $.checkUndefined(self.attr('data-decimal'), 2);
                var func = $.checkUndefined(self.attr('data-function'), false);
                var func_type = $.checkUndefined(self.attr('data-function-type'), false);

                self.val( $.convertDecimal(self, dec) );

                if( func != false ) {
                    eval(func + '(\''+func_type+'\');');
                }
            });

            jQuery.each( settings.objComa, function( i, val ) {
                var self = $(this);
                var dec = $.checkUndefined(self.attr('data-decimal'), 2);
                self.val( $.convertDecimal(self, dec) );
            });
        }
    }

    $.inputNumber = function(options){
        var settings = $.extend({
            obj: $('.input_number'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('keypress');
            settings.obj.keypress(function(event) {
                var charCode = (event.which) ? event.which : event.keyCode;

                if( (this.value.length == 0 && charCode == 46) || charCode == 33 || charCode == 64 || charCode == 35 || charCode == 36 || charCode == 37 || charCode == 94 || charCode == 38 || charCode == 42 || charCode == 40 || charCode == 41
                    ){
                    return false;
                } else {
                    if (
                        charCode == 8 ||  /*backspace*/
                        charCode == 46 || /*point*/
                        charCode == 9 || /*Tab*/
                        charCode == 27 || /*esc*/
                        charCode == 13 || /*enter*/
                        // charCode == 97 || 
                        // Allow: Ctrl+A
                        // (charCode == 65 && event.ctrlKey === true) ||
                        // Allow: home, end, left, right
                        (charCode >= 35 && charCode < 39) || ( charCode >= 48 && charCode <= 57 )
                        ) 
                    {
                        return true;
                    }else if (          
                        (charCode != 46 || ($(this).val().indexOf('.') != -1)) || 
                        (charCode < 48 || charCode > 57)) 
                    {
                        event.preventDefault();
                    }
                }
            });     
        }
    }

    $.serialNumber = function(options){
        var settings = $.extend({
            obj: $('.serial-number-input'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('keyup');
            settings.obj.keyup(function(event) {
                var vthis = $(this);
                var data_default = $.checkUndefined(vthis.attr('data-default'), '');
                var data_target = $.checkUndefined(vthis.attr('data-target'), false);

                if( data_target != false ) {
                    $(data_target).html(data_default);
                }
            });     
        }
    }

    $.pickData = function() {
        $('.browse-form table tr a').off('click');
        $('.browse-form table tr a').click(function(){
            var href = $(this).attr('href');
            var target = $(this).attr('target');
            
            window.open(href, target);

            return false;
        });

        $('.browse-form table tr').off('click');
        $('.browse-form table tr').click(function(){
            var vthis = $(this);
            var data_value = vthis.attr('data-value');
            var data_change = vthis.attr('data-change');
            var data_ajax = vthis.attr('href');
            var data_change_extra = vthis.attr('data-change-extra');
            var data_extra_text = $.filterEmptyField(vthis.attr('data-extra-text'));
            var data_trigger = $.filterEmptyField(vthis.attr('data-trigger'));
            var type = $(data_change).attr('type');
            var obj_note = $.checkUndefined(vthis.find('.document-note'), false);
            var data_note_target = $.checkUndefined(obj_note.attr('data-target-note'), false);
            var data_note_value = $.checkUndefined(obj_note.html(), false);
            var data_duplicate = $.filterEmptyField(vthis.attr('data-duplicate'));
            var data_wrapper_write = $.filterEmptyField(vthis.attr('data-wrapper-write'));
            var flag = true;

            if(data_change == '.cash-bank-auth-user'){
                data_change = vthis.attr('data-rel')+' '+data_change;
            }

            $(data_change).val(data_value);

            if( type != 'text' ) {
                $(data_change).trigger('change');
            }
            if( data_trigger != false ) {
                $(data_change).trigger(data_trigger);
            }
            
            if(data_change == '#receiver-id'){
                var receiver_type = '';

                $('#receiver-type').val(vthis.attr('data-type'));
                $('#cash-bank-user,#ttuj-receiver').val(vthis.attr('data-text'));

                if( vthis.attr('data-type') == 'Driver' ) {
                    receiver_type = 'Supir';
                } else if( vthis.attr('data-type') == 'Empoye' ) {
                    receiver_type = 'Karyawan';
                } else {
                    receiver_type = vthis.attr('data-type');
                }
                
                $('#tag-receiver-type').html('('+receiver_type+')');
                $('#hid-receiver-type').val(vthis.attr('data-type'));
            }

            if( data_note_target != false ){
                var note = $.checkUndefined($(data_note_target).val(), '');

                if( note == '' ) {
                    $(data_note_target).val(data_note_value);
                }
            }

            if( $(data_wrapper_write).length > 0 ) {
                if( data_duplicate == 'false' ) {
                    var rel = $(data_wrapper_write).attr('rel');

                    if( data_value == rel ) {
                        flag = false;
                    }
                }
            }

            if( flag == true ) {
                $(data_wrapper_write).attr('rel', data_value);

                if( typeof data_ajax != 'undefined' ) {
                    $.directAjaxLink({
                        obj: vthis,
                    });
                }
            }

            if( typeof data_change_extra != 'undefined' ) {
                $(data_change_extra).html(data_extra_text);
            }

            if( $('.document-calc table tbody[data-remove="true"]').length > 0 ) {
                $('.document-calc table tbody[data-remove="true"]').html('');
                calcGrandTotalCustom();
            }

            $('#myModal').modal('hide');
            return false;
        });
    }

    $.ajaxForm = function( options ) {
        var settings = $.extend({
            obj: $('.ajax-form'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('submit');
            settings.obj.submit(function(){
                var self = $(this);

                getAjaxForm ( self );

                return false;
            });

            function getAjaxForm ( self ) {
                var url = self.attr('action');
                var type = self.attr('data-type');
                var flag_alert = self.attr('data-alert');
                var data_ajax_type = self.attr('data-ajax-type');
                var formData = self.serialize(); 
                var data_wrapper_write = self.attr('data-wrapper-write');
                var data_wrapper_success = self.attr('data-wrapper-success');
                var data_pushstate = self.attr('data-pushstate');
                var data_url_pushstate = self.attr('data-url-pushstate');
                var data_reload = self.attr('data-reload');
                var data_reload_url = self.attr('data-reload-url');
                var data_close_modal = self.attr('data-close-modal');

                if( flag_alert != null ) {
                    if ( !confirm(flag_alert) ) { 
                        return false;
                    }
                }

                if(typeof data_ajax_type == 'undefined' ) {
                    data_ajax_type = 'html';
                }

                if(typeof data_wrapper_write == 'undefined' ) {
                    data_wrapper_write = '#wrapper-write';
                }

                if(typeof data_pushstate == 'undefined' ) {
                    data_pushstate = false;
                }

                if(typeof data_url_pushstate != 'undefined' ) {
                    data_url_pushstate = url;
                }

                if(typeof type == 'undefined' ) {
                    type = 'content';
                }

                $.ajax({
                    url: url,
                    type: 'POST',
                    dataType: data_ajax_type,
                    data: formData,
                    success: function(result) {
                        if( type == 'content' ) {
                            var content = result;
                            var status = $.checkUndefined($(content).find('#msg-status').html(), $(content).filter('#msg-status').html());
                            var msg = $.checkUndefined($(content).find('#msg-text').html(), $(content).filter('#msg-text').html());
                            var contentHtml = $(content).filter(data_wrapper_write).html();

                            if(typeof contentHtml == 'undefined' ) {
                                contentHtml = $(content).find(data_wrapper_write).html();
                            }

                            if( status == 'success' && data_reload == 'true' ) {
                                if(typeof data_reload_url == 'undefined' ) {
                                    window.location.reload();
                                } else {
                                    location.href = data_reload_url;
                                }
                            } else if( $(data_wrapper_write).length > 0 ) {
                                if(status == 'success' && typeof data_wrapper_success != 'undefined' && $(data_wrapper_success).length > 0 ) {
                                    contentHtml = $(content).filter(data_wrapper_success).html();

                                    if(typeof contentHtml == 'undefined' ) {
                                        contentHtml = $(content).find(data_wrapper_success).html();
                                    }

                                    $(data_wrapper_success).html(contentHtml);
                                    $.rebuildFunctionAjax( $(data_wrapper_success) );

                                    if( data_pushstate != false ) {
                                        window.history.pushState('data', '', data_url_pushstate);
                                    }


                                    if( data_close_modal == 'true' ) {
                                        $('#myModal .close.btn,#myModal button.close').trigger("click");
                                    }
                                } else {
                                    $(data_wrapper_write).html(contentHtml);
                                    $.rebuildFunctionAjax( $(data_wrapper_write) );
                                }
                            }
                        }

                        return false;
                    },
                    error: function(XMLHttpRequest, textStatus, errorThrown) {
                        alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                        return false;
                    }
                });

                return false;
            }
        }
    }

    $.directAjaxModal = function(options){
        var settings = $.extend({
            obj: $('.ajaxCustomModal'),
            objId: $('#myModal'),
            defaultTitle: null,
            defaultUrl: null,
        }, options );

        var vthis = settings.obj;
        var url = vthis.attr('href');
        var alert_msg = vthis.attr('alert');
        var data_check = $.checkUndefined(vthis.attr('data-check'), false);
        var data_check_named = $.checkUndefined(vthis.attr('data-check-named'), false);
        var data_check_alert = $.checkUndefined(vthis.attr('data-check-alert'), false);
        var data_check_empty = $.checkUndefined(vthis.attr('data-check-empty'), null);
        var data_check_encode = $.checkUndefined(vthis.attr('data-check-encode'), null);
        var modalSize = $.checkUndefined(vthis.attr('data-size'), '');
        var data_form = $.checkUndefined(vthis.attr('data-form'), false);
        var data_picker = $.checkUndefined(vthis.attr('data-picker'), false);
        var title = vthis.attr('title');
        var formData = false;

        if( settings.defaultTitle != null ) {
            title = settings.defaultTitle;
        }

        if( settings.defaultUrl != null ) {
            url = settings.defaultUrl;
        }

        if( data_form != false ) {
            formData = $(data_form).serialize(); 
        }
        if( data_picker != false ) {
            if( $(data_picker).is('input') ) {
                var picker_id = $(data_picker).val();
            } else {
                var picker_id = $(data_picker).html();
            }
            
            url += '/picker:' + picker_id + '/';
        }

        if( data_check != false ) {
            dataUrl = $(data_check).val();

            if( data_check_encode == 'true' ) {
                dataUrl = dataUrl.replace(/\//gi, "[slash]");
            }

            if( dataUrl == '' ) {
                if( data_check_empty != 'true' ) {
                    alert(data_check_alert);
                    return false;
                }
            } else {
                if( data_check_named != false ) {
                    dataUrl = data_check_named + ':' + dataUrl;
                }
                
                url += '/' + dataUrl + '/';
            }
        }

        $('.modal-body').html('');

        if( alert_msg != null ) {
            if ( !confirm(alert_msg) ) { 
                return false;
            }
        }

        $.ajax({
            url: url,
            type: 'POST',
            data: formData,
            success: function(response, status) {
                if( title !== undefined ) {
                    settings.objId.find('.modal-title').html(title).show();
                } else {
                    settings.objId.find('.modal-title').hide();
                }

                if( modalSize != '' ) {
                    settings.objId.find('.modal-dialog').removeClass('modal-sm').removeClass('modal-lg').removeClass('modal-xs').removeClass('modal-md');
                    settings.objId.find('.modal-dialog').addClass(modalSize);
                }

                settings.objId.find('.modal-body').html(response);
                settings.objId.modal({
                    show: true,
                });
                $.rebuildFunctionAjax( settings.objId );

                if( vthis.hasClass('wheel-position') ) {
                    var product_id = vthis.attr('rel');
                    var objWheel = $('.wheel-position-input[rel="'+product_id+'"]');
                    var tire_qty = 0;

                    objWheel.each(function(i,item){
                        var val = $(item).val();
                        var position = val.split('|');

                        var position_name = $.filterEmptyField(position[0]);
                        var truck_name = $.filterEmptyField(position[1]);
                        
                        $('.truck-item[rel="'+truck_name+'"] .'+position_name).addClass('active');
                        tire_qty++;
                    });

                    $('.wheel-position-qty').val(tire_qty)
                }

                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    }

    $.ajaxModal = function(options){
        var settings = $.extend({
            obj: $('.ajaxCustomModal'),
            objId: $('#myModal'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click');
            settings.obj.click(function(msg) {
                var self = $(this);

                $.directAjaxModal({
                    obj: self,
                    objId: settings.objId,
                });

                return false;
            });
        }
    }

    $.documentPicker = function(options){
        var settings = $.extend({
            obj: $('.document-picker'),
            objCheck: $('.document-picker .check-option'),
            objCheckAll: $('.checkAll'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.objCheckAll.off('click');
            settings.objCheckAll.click(function(){
                if(settings.objCheckAll.length > 0){
                    settings.objCheck.not(this).prop('checked', this.checked);
                    
                    jQuery.each( settings.objCheck, function( i, val ) {
                        var self = $(this);
                        check_option_coa(self)
                    });
                    
                    calcGrandTotalCustom();
                }
            });

            settings.objCheck.off('click');
            settings.objCheck.click(function(){
                var self = $(this);
                check_option_coa(self);
                calcGrandTotalCustom();
            });

            function check_option_coa(self){
                var self_value = self.val();
                var parent = self.parents('.pick-document');
                var rel_id = $.checkUndefined(parent.attr('rel'), null);
                var rel_table = $.checkUndefined(parent.attr('data-table'), null);
                var data_type = $.checkUndefined(parent.attr('data-type'), null);

                var pickDocumentStr = '.document-picker .pick-document[rel="'+rel_id+'"]';
                var pickDocument = $(pickDocumentStr);
                var chosenBuild = $('.document-picker td .chosen-select.select2-hidden-accessible');
                var chosen = $('.document-picker td .chosen-select');

                if( rel_table != null && $('.temp-document-picker[rel="'+rel_table+'"').length > 0 ) {
                    var temp_picker = $('.temp-document-picker[rel="'+rel_table+'"');
                } else {
                    var temp_picker = $('.temp-document-picker');
                }

                if( chosenBuild.length > 0 ) {
                    $.callChoosen({
                        obj: chosenBuild,
                        init: 'destroy',
                    });
                }

                if(self.is(':checked')){
                    switch (data_type) { 
                        case 'select-multiple':
                            var document_selected = temp_picker.find('.pick-document[rel="'+rel_id+'"]');

                            if( document_selected.length == 0 ) {
                                document_selected = temp_picker;
                            }

                            var chosenBuild = document_selected.find('.chosen-select.select2-hidden-accessible');
                            var existing_selected = document_selected.find('.chosen-select option[value="'+self_value+'"]:selected');
                            var existing = document_selected.find('.chosen-select option[value="'+self_value+'"]');

                            if(existing_selected.length <= 0){
                                if( chosenBuild.length > 0 ) {
                                    $.callChoosen({
                                        obj: chosenBuild,
                                        init: 'destroy',
                                    });
                                }

                                if( existing.length > 0 ) {
                                    existing.remove();
                                }

                                var html_content = '<option value="'+self_value+'" selected="selected">'+self_value+'</option>';
                                document_selected.find('.chosen-select').append(html_content);
                                
                                var chosen = document_selected.find('.chosen-select');
                                
                                if( chosen.length > 0 ) {
                                    $.callChoosen({
                                        obj: chosen,
                                    });
                                }
                            }
                        break;

                        default:
                            if(temp_picker.find('.pick-document[rel="'+rel_id+'"]').length <= 0){
                                var html_content = '<tr class="pick-document" rel="'+rel_id+'">'+pickDocument.html()+'</tr>';

                                temp_picker.find('tbody').append(html_content);

                                temp_picker.find('.pick-document[rel="'+rel_id+'"] td.hide').removeClass('hide');
                                temp_picker.find('.pick-document[rel="'+rel_id+'"] td[data-display="hide"]').addClass('hide');
                                
                                temp_picker.find('td.removed').remove();

                                calcItemTotal(temp_picker);
                                temp_picker.removeClass('hide');
                                
                                if( chosen.length > 0 ) {
                                    $.callChoosen({
                                        obj: temp_picker.find('td .chosen-select'),
                                    });
                                }
                            }
                        break;
                    }
                } else {
                    switch (data_type) { 
                        case 'select-multiple':
                            var document_selected = temp_picker.find('.pick-document[rel="'+rel_id+'"]');
                            var document_single_selected = $('#search-select-multiple');

                            if( document_single_selected.length > 0 ) {
                                var document_selected = document_single_selected;
                                var chosenBuild = document_single_selected.find('.chosen-select.select2-hidden-accessible');
                                var existing = document_single_selected.find('.chosen-select option[value="'+self_value+'"]');
                            } else {
                                var chosenBuild = document_selected.find('.chosen-select.select2-hidden-accessible');
                                var existing = document_selected.find('.chosen-select option[value="'+self_value+'"]');
                            }

                            if(existing.length > 0){
                                if( chosenBuild.length > 0 ) {
                                    $.callChoosen({
                                        obj: chosenBuild,
                                        init: 'destroy',
                                    });
                                }

                                existing.remove();
                                var chosen = document_selected.find('.chosen-select');

                                if( chosen.length > 0 ) {
                                    $.callChoosen({
                                        obj: chosen,
                                    });
                                }
                            }
                        break;

                        default:
                            temp_picker.find(pickDocumentStr).remove();
                            calcItemTotal(temp_picker);
                        break;
                    }
                }

                $.rebuildFunction();
                $.inputPrice({
                    obj: temp_picker.find('.input_price'),
                });
                $.inputNumber();
                $.wheelPosition({
                    obj: temp_picker.find('.wrapper-wheel-position .truck-item .tire'),
                    objTire: temp_picker.find('.wheel-position'),
                    objSubmit: temp_picker.find('.wheel-position-submit'),
                });
            }
        }
    }

    function calcItemTotal ( obj ) {
        if( obj.find('tbody .calc-item').length > 0 ) {
            var itemTotal = [];

            $.each( obj.find('tbody .calc-item'), function( i, val ) {
                var self = $(this);
                var rel = self.attr('rel');
                var total = $.checkUndefined(itemTotal[rel], 0);
                
                itemTotal[rel] = total + $.convertNumber(self.html());
            });

            $.each( itemTotal, function( i, val ) {
                obj.find('tfoot .calc-total[rel="'+i+'"]').html($.formatDecimal(val, 2));
            });
        }
    }

    function calcGrandTotal () {
        var objGrandTotal = $('.temp-document-picker .grandtotal .total');
        var grandtotal = 0;

        var decimal = $.checkUndefined(objGrandTotal.attr('data-decimal'), 0);

        if( $('.document-calc .pick-document').length > 0 ) {
            $.each( $('.document-calc .pick-document'), function( i, val ) {
                var self = $(this);
                grandtotal += calculate(self);
            });
        }

        if( objGrandTotal.length > 0 ) {
            objGrandTotal.html( $.formatDecimal(grandtotal, decimal) );
        }
    }

    function calculate(parent, self, type){
        var type = $.checkUndefined(type, parent.attr('data-type'));
        var objPrice = parent.find('.price');
        var self = $.checkUndefined(self, objPrice);

        var price = $.convertNumber(objPrice.val());
        var disc = $.convertNumber(parent.find('.disc').val());
        var ppn = $.convertNumber(parent.find('.ppn').val());
        var qty = $.convertNumber(parent.find('.qty').val(), 'int', 1);
        var calc_type = $.checkUndefined(parent.find('.calc-type').val(), null);

        var ppn_include = $('.ppn_include:checked').val();
        var format_type = self.attr('data-type');

        if( type == 'single-total' || calc_type == 'borongan' ) {
            total = price;
        } else {
            // total = ( ( price * qty ) - disc );
            total = price - disc;

            if( ppn_include != 1 ) {
                total += ppn;
            }

            total = total * qty;
        }

        if( format_type == 'input_price_coma' ) {
            self.val( $.convertDecimal(self, 2) );
        }

        return total;
    }

    function calcGrandTotalQty () {
        var objGrandTotal = $('.temp-document-picker .grandtotal .total');
        var grandtotal = 0;
        var decimal = $.checkUndefined(objGrandTotal.attr('data-decimal'), 0);

        $.each( $('document-calc .pick-document .qty'), function( i, val ) {
            var self = $(this);
            grandtotal += $.convertNumber(self.val());
        });

        if( objGrandTotal.length > 0 ) {
            objGrandTotal.html( $.formatDecimal(grandtotal, decimal) );
        }
    }
    function calcGrandTotalCustom () {
        var objGrandTotal = $('.temp-document-picker .grandtotal .total_custom');

        $.each( objGrandTotal, function( i, val ) {
            var objTotal = $(this);
            var grandtotal = 0;
            var rel = objTotal.attr('rel');
            var pickDocument = objTotal.parents('.document-calc').find('.pick-document');
            var decimal = $.checkUndefined(objTotal.attr('data-decimal'), 0);

            $.each( pickDocument, function( i, val ) {
                var self = $(this);
                var priceObj = self.find('.price_custom[rel="'+rel+'"]');
                
                var type = priceObj.attr('type');

                if( type == 'text' ) {
                    var input = priceObj.val();
                } else {
                    var input = priceObj.html();
                }

                var price = $.convertNumber(input);
                var format_type = priceObj.attr('data-type');

                grandtotal += price;

                if( format_type == 'input_price_coma' ) {
                    priceObj.val( $.convertDecimal(priceObj, 2) );
                }
            });

            if( objTotal.length > 0 ) {
                objTotal.html( $.formatDecimal(grandtotal, decimal) );
            }
        });
    }
    
    function callcAdjustment (self, parent) {
        var dataAdjutment = $.checkUndefined(self.attr('data-adjutment'), null);
        var qtyStock = $.convertNumber(parent.find('[rel="qty-stock"]').html(), 'float', 0);
        var qty = $.convertNumber(parent.find('[rel="qty"]').val(), 'float', 0);

        var qtyAdjustment = qty - qtyStock;

        parent.find(dataAdjutment).html(qtyAdjustment);
        
        // var dataTarget = parent.find(dataAdjutment).attr('data-target');
        // var dataMinus = parent.find(dataAdjutment).attr('data-minus');
        var dataDisabled = parent.find(dataAdjutment).attr('data-disabled');
        var dataDisplay = parent.find(dataAdjutment).attr('data-display');
        var typeAdjustment = 'minus';

        if( qtyAdjustment > 0 ) {
            parent.find(dataDisabled).attr('disabled', false);
            typeAdjustment = 'plus';
        } else {
            parent.find(dataDisabled).attr('disabled', true);
        }

        dataDisplay = eval(dataDisplay);

        if( $.isArray(dataDisplay) ) {
            parent.find('.display-adjust').hide();

            $.each( dataDisplay, function( i, val ) {
                target = val[0];
                type = val[1];

                if( type == typeAdjustment ) {
                    parent.find(target).show();
                }
            });
        }
    }

    function _callTotalCustom (self) {
        var rel = self.attr('rel');
        var dataAdjutment = $.checkUndefined(self.attr('data-adjutment'), null);

        var parent = self.parents('.pick-document');
        var objTotal = parent.find('.total_custom[rel="'+rel+'"]');
        var objTotalRow = parent.find('.total_row');
        
        var input = $.convertNumber(self.val(), self.html());
        var price = $.convertNumber(input);

        var data_max = $.convertNumber(self.attr('data-max'), 'float', false);
        var data_max_alert = $.checkUndefined(self.attr('data-max-alert'), false);

        if( data_max != false ) {
            if( price > data_max ) {
                alert(data_max_alert);
                total = data_max;

                self.val( $.formatDecimal(total) );
            } else {
                total = calculate(parent, self);
            }
        } else {
            total = calculate(parent, self);
        }

        objTotal.html( $.formatDecimal(total, 2) );
        objTotalRow.html( $.formatDecimal(total, 2) );
        calcGrandTotalCustom();

        if( dataAdjutment != null ) {
            callcAdjustment(self, parent);
        }
    }

    $.calcTotal = function(options){
        var settings = $.extend({
            obj: $('.document-calc .price,.document-calc .disc,.document-calc .ppn,.document-calc .total,.document-calc .qty'),
            objQty: $('.document-calc-qty .qty'),
            objClick: $('.ppn_include'),
            objDelete: $('.delete-document'),
            objCustom: $('.document-calc .price_custom'),
            objCalc: $('.document-calc .calc-type'),
            // objPpnCustom: $('.document-calc .ppn_trigger'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('blur');
            settings.obj.blur(function(){
                var self = $(this);
                var parent = self.parents('.pick-document');
                var objTotal = parent.find('.total');
                var objPrice = parent.find('.price');
                var price = $.convertNumber(objPrice.val());

                var data_max = $.convertNumber(objPrice.attr('data-max'), 'float', false);
                var data_max_alert = $.checkUndefined(objPrice.attr('data-max-alert'), false);

                if( data_max != false ) {
                    if( price > data_max ) {
                        alert(data_max_alert);
                        total = data_max;

                        objPrice.val( $.formatDecimal(total) );
                    } else {
                        total = calculate(parent, self);
                    }
                } else {
                    total = calculate(parent, self);
                }

                objTotal.html( $.formatDecimal(total, 2) );
                calcGrandTotal();
            });

            calcGrandTotal();
        }

        if( settings.objQty.length > 0 ) {
            settings.objQty.off('blur');
            settings.objQty.blur(function(){
                var self = $(this);
                var parent = self.parents('.temp-document-picker');
                var objTotal = parent.find('.total');
                var qty = $.convertNumber(self.val());

                var data_max = $.convertNumber(objTotal.attr('data-max'), 'float', false);
                var data_max_alert = $.checkUndefined(objTotal.attr('data-max-alert'), false);
                var format_type = self.attr('data-type');

                if( data_max != false ) {
                    if( qty > data_max ) {
                        alert(data_max_alert);
                        total = data_max;

                        self.val( total );
                    }
                }

                if( format_type == 'input_price_coma' ) {
                    self.val( $.convertDecimal(self, 2) );
                }

                calcGrandTotalQty();
            });

            calcGrandTotalQty();
        }

        if( settings.objClick.length > 0 ) {
            settings.objClick.off('click');
            settings.objClick.click(function(){
                $.each( $('.document-calc .pick-document'), function( i, val ) {
                    var parent = $(this);
                    var objTotal = parent.find('.total');

                    total = calculate(parent);
                    objTotal.html( $.formatDecimal(total) );
                });
                
                calcGrandTotal();
            });
        }

        if( settings.objDelete.length > 0 ) {
            settings.objDelete.off('click');
            settings.objDelete.click(function(){
                var self = $(this);
                var parent = self.parents('.pick-document');
                var table = self.parents('table.form-added');

                if ( confirm('Hapus dokumen ini?') ) { 
                    parent.remove();
                    calcItemTotal(table);
                    calcGrandTotalCustom();
                    $.calcTotal();
                }

                return false;
            });
        }

        settings.objCustom.off('blur').blur(function(){
            _callTotalCustom($(this));
        });
        settings.objCalc.off('change').change(function(){
            _callTotalCustom($(this));
        });

        calcGrandTotal();
    }

    $.filterEmptyField = function(num, empty){
        if( typeof empty == 'undefined' ) {
            empty = '';
        }

        if( typeof num == 'undefined' ) {
            num = empty;
        }

        return num;
    }

    $.convertNumber = function(num, type, empty){
        if( typeof empty == 'undefined' ) {
            empty = 0;
        }

        if( typeof num != 'undefined' ) {
            firstChar = num.charAt(0);
            num = num.replace(/,/gi, "").replace(/ /gi, "").replace(/IDR/gi, "").replace(/Rp/gi, "").replace(/\(/gi, "").replace(/\)/gi, "");

            if( typeof type == 'undefined' ) {
                type = 'int';
            }

            if( type == 'int' ) {
                num = num*1;
            } else if( type == 'float' ) {
                num = parseFloat(num);
            }

            if( type == 'int' || type == 'float' ) {
                if( isNaN(num) ) {
                    num = 0;
                }
            }

            if( firstChar == '(' ) {
                num = num * -1;
            }
        } else {
            num = empty;
        }

        return num;
    }

    $.formatDecimal = function(number, decimals, dec_point, thousands_sep){
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

    $.dropdownFix = function(options){
        var settings = $.extend({
            obj: $('.columnDropdown'),
        }, options );

        settings.obj.on({
            "shown.bs.dropdown": function() { this.closable = false; },
            "click":             function(e) { 
                var target = $(e.target);

                if( target.parents('ul.dropdown-menu').length == 0 ) 
                    this.closable = true;
                else 
                    this.closable = false; 
            },
            "hide.bs.dropdown":  function() { return this.closable; }
        });
    }

    $.rebuildFunction = function() {
        $.ajaxLink();
        $.ajaxForm();
        $.rowAdded();
        $.ajaxModal();
        $.documentPicker();
        $.calcTotal();
        $.checkAll();
        $.dropdownFix();
        $.serialNumber();
        $.handle_toggle();
        $.new_handle_toggle();
    }

    $.checkAll = function() {
        if( $('.check-all').length > 0 ) {
            $('.check-all').off('click');
            $('.check-all').click(function(){
                var self = $(this);
                var parent = self.parents('.parent-check-branch');
                var target = parent.find('.check-branch');

                if(self.is(':checked')){
                    target.prop('checked', true);
                } else {
                    target.prop('checked', false);
                }
            });
        }
    }

    $.rebuildFunctionAjax = function( obj ) {
        $.inputPrice({
            obj: obj.find('.input_price'),
        });
        $.inputNumber({
            obj: obj.find('.input_number'),
        });
        $.dropdownFix({
            obj: obj.find('.columnDropdown'),
        });
        $.checkAll();

        datepicker(obj.find('.custom-date'));
        timepicker(obj.find('.timepicker'));
        $.daterangepicker({
            obj: obj.find('.date-range'),
        })

        $.rebuildFunction();
        $.pickData();
        $.callChoosen({
            obj: obj.find('.chosen-select'),
        });
        $.callInterval({
            obj: obj.find('.call-interval'),
        });
        $.wheelPosition({
            obj: obj.find('.wrapper-wheel-position .truck-item .tire'),
            objTire: obj.find('.wheel-position'),
            objSubmit: obj.find('.wheel-position-submit'),
        });
    }

    $.ajaxLink = function( options ) {
        var settings = $.extend({
            obj: $('.ajax-link'),
            objChange: $('.ajax-change'),
            objBlur: $('.ajax-blur'),
        }, options );

        settings.obj.off('click');
        settings.obj.click(function(){
            var self = $(this);
            disableAjax = false;
            
            $.directAjaxLink({
                obj: self,
            });

            return false;
        });

        if( settings.objChange.length > 0 ) {
            var data_trigger = settings.objChange.attr('data-trigger');
            var type = settings.objChange.attr('type');

            if( type == 'radio' ) {
                settings.objChange.each(function(){
                    var self = $(this);

                    if(self.is(':checked')){
                        switch (data_trigger) { 
                            case 'handle-toggle':
                                handle_toggle(self);
                            break;
                            case 'handle-toggle-click':
                                $.new_handle_toggle();
                            break;
                        }
                    }
                });
            }

            settings.objChange.off('change').change(function(event){
                var self = $(this);
                var href = self.attr('href');
                var data_trigger = self.attr('data-trigger');

                href += '/' + self.val();

                switch (data_trigger) { 
                    case 'handle-toggle':
                        handle_toggle(self);
                    break;
                    case 'handle-toggle-click':
                        _callHandleToggle(self, self.val(), event);
                    break;
                }

                $.directAjaxLink({
                    obj: self,
                    url: href,
                });

                if( self.hasClass('chosen-select') ) {
                    $.callChoosen({
                        obj: self,
                        init: 'destroy',
                    });
                    $.callChoosen({
                        obj: self,
                    });
                }

                return false;
            });
        }

        settings.objBlur.off('blur');
        settings.objBlur.blur(function(){
            var self = $(this);

            $.directAjaxLink({
                obj: self,
            });

            return false;
        });
    }

    var idxAjax = 0;
    var debitTotal = {};
    var creditTotal = {};
    var coaPeriod = {};

    $.directAjaxLink = function( options ) {
        var settings = $.extend({
            obj: $('.ajax-link'),
            url: false,
        }, options );

        var infinity = $.checkUndefined(settings.obj.attr('data-infinity'), false);

        if( infinity == 'true' ) {
            var obj_content = settings.obj.get(idxAjax);
            obj_content = $(obj_content);
            $("#ajaxLoading").show();

            if( obj_content.length === 0 ) {
                var objCoaParent = $('.coa-parent');

                if( objCoaParent.length > 0 ) {
                    objCoaParent.each(function(){
                        var coaParentSelf = $(this);
                        var coa_id = coaParentSelf.attr('coa-id');
                        var data_type = coaParentSelf.attr('data-type');
                        var child = $('.wrapper-coa-parent-'+coa_id);
                        
                        if( data_type == 'end' ) {
                            $.each(coaPeriod, function(idx, period){
                                var tmpDebitTotal = $.checkUndefined(debitTotal[period], 0);
                                var tmpCreditTotal = $.checkUndefined(creditTotal[period], 0);

                                var profit_loss = tmpCreditTotal - tmpDebitTotal;
                                $('.wrapper-profit-loss[rel="'+period+'"]').html($.formatDecimal(profit_loss));
                            });
                        } else if( child.length > 0 ) {
                            child.each(function(){
                                var childSelf = $(this);
                                var dt = childSelf.attr('rel');
                                var objCoa = $('.wrapper-coa-parent-'+coa_id+'[rel="'+dt+'"]');
                                var grandtotal = 0;

                                if( objCoa.length > 0 ) {
                                    objCoa.each(function(){
                                        var objCurrent = $(this);
                                        var op = objCurrent.attr('data-op');
                                        // var data_type = objCurrent.attr('data-type');
                                        var total = $.convertNumber(objCurrent.html());

                                        // console.log(total);
                                        // console.log(op);
                                        // if( op == '-' ) {
                                        //     grandtotal -= total;
                                        // } else {
                                            grandtotal += total;
                                        // }
                                    });


                                    if( grandtotal < 0 ) {
                                        grandtotal = grandtotal * -1;
                                        grandtotal_format = $.formatDecimal(grandtotal);
                                        grandtotal_format = '('+grandtotal_format+')';
                                        op = '-';
                                    } else {
                                        grandtotal_format = $.formatDecimal(grandtotal);
                                        op = '+';
                                    }

                                    $('.wrapper-coa-parent[coa-id="'+coa_id+'"][rel="'+dt+'"]').attr('data-op', op).html( grandtotal_format );
                                }
                            });
                        }
                    });
                }

                $("#ajaxLoading").slideUp(200);
                return false;
            }
        } else {
            var obj_content = settings.obj;
        }

        var url = settings.url;

        var parents = obj_content.parents('.ajax-parent');
        var type = obj_content.attr('data-type');
        var flag_alert = obj_content.attr('data-alert');
        var data_ajax_type = obj_content.attr('data-ajax-type');
        var data_wrapper_write = obj_content.attr('data-wrapper-write');
        var data_wrapper_write_page = $.checkUndefined(obj_content.attr('data-wrapper-write-page'), false);
        var data_action = obj_content.attr('data-action');
        var data_pushstate = obj_content.attr('data-pushstate');
        var data_url_pushstate = obj_content.attr('data-url-pushstate');
        var data_form = obj_content.attr('data-form');
        var data_scroll = obj_content.attr('data-scroll');
        var data_show = obj_content.attr('data-show');
        var func = $.checkUndefined(obj_content.attr('data-function'), false);
        var func_type = $.checkUndefined(obj_content.attr('data-function-type'), false);
        var data_empty = $.checkUndefined(obj_content.attr('data-empty'), null);
        var formData = false; 

        if( url == false ) {
            url = obj_content.attr('data-url');
            url = $.checkUndefined(obj_content.attr('href'), url);
        }

        if( flag_alert != null ) {
            if ( !confirm(flag_alert) ) { 
                return false;
            }
        }

        if(typeof data_ajax_type == 'undefined' ) {
            data_ajax_type = 'html';
        }

        if(typeof data_wrapper_write == 'undefined' ) {
            data_wrapper_write = '#wrapper-write';
        }

        if(typeof data_pushstate == 'undefined' ) {
            data_pushstate = false;
        }

        if(typeof data_url_pushstate == 'undefined' ) {
            data_url_pushstate = url;
        }

        if(typeof data_form != 'undefined' ) {
            var formData = $(data_form).serialize(); 
        }

        if(typeof type == 'undefined' ) {
            type = 'content';
        }

        $.ajax({
            url: url,
            type: 'POST',
            dataType: data_ajax_type,
            data: formData,
            success: function(result) {
                var msg = result.msg;
                var status = result.status;

                if( data_empty != null ) {
                    $(data_empty).empty();
                    calcGrandTotalCustom();
                }

                if( type == 'content' ) {
                    var contentHtml = $(result).filter(data_wrapper_write).html();

                    if( data_pushstate != false ) {
                        window.history.pushState('data', '', data_url_pushstate);
                    }

                    if(typeof contentHtml == 'undefined' ) {
                        contentHtml = $(result).find(data_wrapper_write).html();
                    }

                    if(typeof data_scroll != 'undefined' ) {
                        var theOffset = $(data_scroll).offset();
                        $('html, body').animate({
                            scrollTop: theOffset.top
                        }, 2000);
                    }

                    if( data_wrapper_write_page != false ) {
                        var data_wrapper_arr = data_wrapper_write_page.split(',');

                        $.each(data_wrapper_arr, function(index, identifier){
                            var targetWrapper = $.trim(identifier);
                            var contentPage = $(result).filter(targetWrapper).html();

                            if( typeof contentPage == 'undefined' ) {
                                contentPage = $(result).find(targetWrapper).html();
                            }

                            if( $(targetWrapper).length > 0 ) {
                                $(targetWrapper).html(contentPage);
                            }
                            
                            $.rebuildFunctionAjax( $(targetWrapper) );
                        });
                    } else if( $(data_wrapper_write).length > 0 ) {
                        $(data_wrapper_write).html(contentHtml);
                        $.rebuildFunctionAjax( $(data_wrapper_write) );
                    }

                    if( $(data_wrapper_write).length > 0 || data_wrapper_write_page != false ) {
                        if(data_action == 'messages' ) {
                            var current_active = obj_content.parents('li');
                            var data_active = $('.list-inbox li');

                            data_active.removeClass('active');
                            current_active.addClass('active');
                            current_active.removeClass('unread');
                        }else if(data_action == 'input-file' ) {
                            $.handle_input_file();
                            $.option_image_ebrosur();
                            $.generateLocation();
                            $.updateLiveBanner();
                            $.limit_word_package();
                        }
                    }

                    if( func != false ) {
                        eval(func + '('+func_type+');');
                    }

                    if(typeof data_show != 'undefined' ) {
                        $(data_show).show().removeClass('hide');
                    }

                    if( infinity == 'true' ) {
                        idxAjax++;
                        
                        var coa_type = $.checkUndefined($(result).find('.coa-type').html(), null);

                        if( coa_type != null ) {
                            coa_month = $(result).find('.coa-month');

                            coa_month.each(function(){
                                var monthSelf = $(this);
                                var month = monthSelf.html();
                                var coa_total = $.convertNumber($(result).find('.coa-total[rel="'+month+'"]').html());

                                if( coa_type == 'debit' ) {
                                    var tmpTotal = $.checkUndefined(debitTotal[month], 0);
                                    debitTotal[month] = tmpTotal + coa_total;
                                } else {
                                    var tmpTotal = $.checkUndefined(creditTotal[month], 0);
                                    creditTotal[month] = tmpTotal + coa_total;
                                }
                                
                                // var checkPeriod = $.checkUndefined(coaPeriod[month], null);

                                // if( checkPeriod == null ) {
                                //     var lenPeriod = coaPeriod.length;
                                    coaPeriod[month] = month;
                                // }
                            });
                        }

                        $.directAjaxLink({
                            obj: $('.ajax-infinity'),
                        });
                    }
                }

                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                console.log('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
                return false;
            }
        });
    }

    $.Autocomplete = function( options ) {
        var settings = $.extend({
            obj: $('#autocomplete'),
        }, options );

        function _callAutocomplete ( objTarget ) {
            var url = objTarget.attr('data-ajax-url');
            var data_change = objTarget.attr('data-change');

            objTarget.typeahead({
                source: function (query, process) {
                    return $.ajax({
                        url: url,
                        type     : 'POST',
                        data: {query: query},
                        loadingClass: "loading-circle",
                        dataType: 'json',
                        success: function(json) {
                            return process(json);
                        }
                    });
                },
                highlighter: function (item) {
                    var is_highlighter = objTarget.attr('data-highlighter');

                    var regex = new RegExp( '(' + this.query + ')', 'gi' );
                    
                    if(!is_highlighter){
                        result = "$1";
                    }else{
                        result = "<strong>$1</strong>";
                    }

                    return item.replace( regex, result );
                },
                minLength: 3,
                afterSelect: function(item){
                    option_id = encodeURI(item.split(/,(.+)?/)[0]);

                    if($('#handle-ajax-medias').length > 0){
                        var target_ajax = $('#handle-ajax-medias');
                        
                        var url = target_ajax.attr('href');
                        
                        url_targer = url+'/'+option_id+'/'+$('.file-image-ebrosur').val();

                        target_ajax.attr('href', url_targer);

                        target_ajax.trigger('click');

                        target_ajax.attr('href', url);
                    }

                    if( data_change == 'true' || data_change == 'trigger' ) {
                        if( data_change == 'true' ) {
                            var href = objTarget.attr('href') + '/' + option_id + '/';

                            objTarget.attr('href', href);
                        }

                        objTarget.attr('is_selected', true);
                        $.directAjaxLink({
                            obj: objTarget,
                        });
                    }
                }
            });

            objTarget.on('keydown', function(e){
                var code = e.keyCode || e.which;
                if(code != 9 && code != 20 && code != 16 & code != 17) {
                    var self = $(this);
                    var has_been_selected = self.attr('is_selected');

                    if( has_been_selected !== undefined ) {
                        self.val('');
                        self.removeAttr('is_selected');
                        $.directAjaxLink({
                            obj: self,
                        });
                        return false;
                    }
                }
            });
        }

        if( settings.obj.length > 0 ) {
            _callAutocomplete(settings.obj);
        }
    }
    
    settings_nilai_perolehan = $('.nilai_perolehan');

    if( settings_nilai_perolehan.length > 0 ) {
        var calcDeprBulan = function ( type ) {
            settings_nilai_perolehan = $('.nilai_perolehan');
            settings_nilai_sisa = $('.nilai_sisa');
            settings_umur_ekonomis = $('.umur_ekonomis');
            settings_depr_bulan = $('.depr_bulan');
            settings_ak_penyusutan = $('.ak_penyusutan');
            settings_nilai_buku = $('.nilai_buku');
            settings_asset_group = $('.asset_group');

            ak_penyusutan = $.convertNumber(settings_ak_penyusutan.val(), 'float', 0);
            nilai_perolehan = $.convertNumber(settings_nilai_perolehan.val(), 'float', 0);
            nilai_sisa = $.convertNumber(settings_nilai_sisa.val(), 'float', 0);
            umur_ekonomis = $.convertNumber(settings_umur_ekonomis.val(), 'float', 0);

            if( nilai_perolehan != 0 ) {
                if( type != 'depr_bulan' ) {
                    depr_bulan = ( ( nilai_perolehan - nilai_sisa ) / settings_umur_ekonomis.val() ) / 12;
                    settings_depr_bulan.val( $.formatDecimal(depr_bulan, 2) );
                }

                nilai_buku = nilai_perolehan - ak_penyusutan;
                
                settings_ak_penyusutan.val( $.formatDecimal(ak_penyusutan, 2) );
                settings_nilai_buku.val( $.formatDecimal(nilai_buku, 2) );
            }
        }
    }

    $.daterangepicker = function( options ) {
        var settings = $.extend({
            obj: $('.date-range'),
            objCustom: $('.date-range-custom'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.daterangepicker({
                format: 'DD/MM/YYYY',
            });
        }

        if( settings.objCustom.length > 0 ) {
            settings.objCustom.each(function(){
                var self = $(this);
                var limitday = $.convertNumber(self.attr('data-limit'), 'int');

                if( limitday == 0 ) {
                    limitday = false;
                }

                self.daterangepicker({
                    format: 'DD/MM/YYYY',
                    dateLimit: { days: limitday },
                });
            });
        }
    }

    function handle_toggle (self) {
        var match = self.attr('data-match');
        var value = self.val();
        var result = false;

        match = eval(match);

        if( $.isArray(match) ) {
            $.each( match, function( i, val ) {
                target = val[0];
                dataMatch = val[1];
                type = val[2];

                var key = $.inArray( value, dataMatch );

                if( key >= 0 ) {
                    result = true;
                    $(target).removeClass('hide');
                } else {
                    result = false;
                }

                switch (type) { 
                    case 'fade':
                        if( result ) {
                            $(target).fadeIn();
                        } else {
                            $(target).fadeOut();
                        }
                    break;
                    case 'slide':
                        if( result ) {
                            $(target).slideDown();
                        } else {
                            $(target).slideUp();
                        }
                    break;
                }

                if( !result ) {
                    $(target).find('input:not([data-keep="true"]),select').val('');
                    $(target).find('select.calc-type').val('borongan');

                    $.callChoosen({
                        obj: $(target).find('.chosen-select'),
                    });
                }
            });
        }
    }

    $.handle_toggle = function(options){
        var settings = $.extend({
            obj: '.handle-toggle',
        }, options );
        
        $( "body" ).delegate( settings.obj, "change", function() {
            handle_toggle($(this));
        });
    }
    
    $.callInterval = function(options){
        var settings = $.extend({
            obj: $('.call-interval'),
            init: false,
        }, options );

        if( settings.obj.length > 0 ) {
            var self = settings.obj;
            var interval = $.checkUndefined(self.attr('data-interval'), 5000);

            setInterval(function(){
                var trigger = $.checkUndefined($('.trigger-interval').val(), 'true');
                
                if( trigger == 'true' ) {
                    disableAjax = true;

                     $.directAjaxLink({
                        obj: self,
                    });
                 }
            }, interval);
        }
    }
    
    $.wheelPosition = function(options){
        var settings = $.extend({
            obj: $('.wrapper-wheel-position .truck-item .tire'),
            objTire: $('.wheel-position'),
            objSubmit: $('.wheel-position-submit'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('click').click(function(e){
                if( $('.wheel-position-qty').length > 0 ) {
                    var self = $(this);
                    var max_qty = $.convertNumber($('.wheel-position-max-qty').val(), 'float');
                    var tire_qty = $.convertNumber($('.wheel-position-qty').val(), 'int');

                    if( self.hasClass('active') ) {
                        self.removeClass('active');
                        tire_qty--;
                    } else {
                        self.addClass('active');
                        tire_qty++;
                    }

                    if( tire_qty > max_qty ) {
                        alert('Qty telah habis terpakai, klik tombol submit untuk melanjutkan');
                        self.removeClass('active');
                        tire_qty--;
                    }

                    $('.wheel-position-qty').val(tire_qty);
                }
            });
        }

        if( settings.objTire.length > 0 ) {
            settings.objTire.off('click').click(function(e){
                var self = $(this);
                var parents = self.parents('tr');
                var href = self.attr('href');
                var title = self.attr('title');

                var objQty = parents.find('[rel="qty"]');
                var product_id = parents.attr('rel');

                if( objQty.attr('type') == 'text' ) {
                    var valQty = objQty.val();
                } else {
                    var valQty = objQty.html();
                }

                var qty = $.convertNumber(valQty, 'float');

                if( qty != 0 ) {
                    href += '/' + qty;

                    $.directAjaxModal({
                        obj: self,
                        defaultTitle: title + ' ('+qty+' Ban)',
                        defaultUrl: href,
                    });
                } else {
                    alert('Mohon masukan qty terlebih dahulu');
                    objQty.focus();
                }

                return false
            });
        }

        if( settings.objSubmit.length > 0 ) {
            settings.objSubmit.off('click').click(function(e){
                var self = $('.wrapper-wheel-position .truck-item .tire.active');
                var product_id = $('.wheel-position-product-id').val();
                var product_qty = $('.wheel-position-max-qty').val();
                var tmp = '';

                if( self.length == 0 ) {
                    alert('Mohon pilih ban roda truk yang akan diganti');
                } else if( self.length < product_qty ) {
                    alert('Mohon pilih '+product_qty+' ban roda truk yang akan diganti');
                } else {
                    $('.wheel-position-input[rel="'+product_id+'"]').remove();

                    self.each(function(i,item){
                        var name = $.checkUndefined($(item).attr('class'), null);
                        name = name.replace(/tire/gi, "").replace(/active/gi, "");

                        var parents = $(item).parents('.truck-item');
                        truck_name = parents.attr('rel');
                        
                        name = $.trim(name) + '|' + truck_name;

                        tmp += '<input name="data[SpkProduct][tire_position]['+product_id+'][]" type="hidden" value="'+name+'" class="wheel-position-input" rel="'+product_id+'">';
                    });
                    
                    $('.pick-document[rel="'+product_id+'"]').append(tmp);
                    $('.pick-document[rel="'+product_id+'"] .wheel-position').html('Set ('+product_qty+' Ban)');
                    $('.pick-document[rel="'+product_id+'"] .error-message').remove();
                    $('#myModal .close.btn,#myModal button.close').trigger("click");
                }
                return false
            });
        }
    }

    $('.change-remove').off('change').change(function(){
        var self = $(this);
        var target = self.attr('data-target');

        $(target).remove();

        return false;
    });

    function _callHandleToggle ( self, value, event ) {
        var match = $.checkUndefined(self.attr('data-match'));
        var resetTarget = $.checkUndefined(self.data('reset-target'), true);
        var targetDisabled = $.checkUndefined(self.data('target-disabled'), null);
        var result = false;
        match = eval(match);

        if($.isArray(match) ) {
            $.each( match, function( i, val ) {
                target = $.checkUndefined(val[0]);
                dataMatch = $.checkUndefined(val[1]);
                type = $.checkUndefined(val[2]);
                reset = $.checkUndefined(val[3], 'true');

                if($(target).length){
                    var key = $.inArray( value, dataMatch );

                    if( key >= 0 ) {
                        result = true;
                        $(target).removeClass('hide');
                    } else {
                        result = false;
                    }

                    switch (type) { 
                        case 'fade':
                            if( result ) {
                                $(target).fadeIn();
                            } else {
                                $(target).fadeOut();
                            }
                        break;
                        case 'slide':
                            if( result ) {
                                $(target).slideDown();
                            } else {
                                $(target).slideUp();
                            }
                        break;
                        default : 
                            if( result ) {
                                $(target).show();
                            } else {
                                $(target).hide();
                            }
                        break;
                    }

                //  force clear target value on change event
                    var inputs = $(target).find(':input');

                    if( reset == 'false' ) {
                      resetTarget = false;
                    } else {
                      resetTarget = $.inArray( event.type, ['change', 'click'] ) && resetTarget === true;
                    }

                    if(inputs.length){
                        if( !result && resetTarget == true){
                        // if( resetTarget == true){
                        //  reset all inputs value
                            inputs.each(function(index, element){
                                var type = $(this).attr('type');

                                if( $.inArray(type, ['checkbox', 'radio']) !== -1 ) {
                                    $(this).prop('checked', false);
                                } else {
                                    $(this).val('').prop('checked', false);
                                }
                            });
                        }

                        inputs.prop('disabled', !result);
                        $('[data-match-type="remove"]').remove();

                        if( targetDisabled != null ) {
                            $(targetDisabled).prop('disabled', !result);
                            
                            if( result && resetTarget && inputs.val() == '' && targetDisabled != null ) {
                                $(targetDisabled).each(function(index, element){
                                    var type = $(this).attr('type');

                                    if( type == 'checkbox' ) {
                                        $(this).prop('checked', false);
                                    } else {
                                        $(this).val('').prop('checked', false);
                                    }
                                });
                            }
                        }

                        var ckeditorInput = inputs.filter('.ckeditor-short, .ckeditor');
                        if(ckeditorInput.length && event.type != 'init'){
                            $._resetEditor(ckeditorInput);
                        }
                    }

                    var innerToggles = $(target).find('.handle-toggle:visible, .handle-toggle-click:visible');

                    if(resetTarget && innerToggles.length){
                    //  re-trigger init toggle (karena di dalam element yang di toggle kemungkinan masih ada toggle yang kena reset)
                        innerToggles.trigger('init');
                    }
                }
                else{
                //  jangan return, looping jadi stop
                //  return false;
                }
            });
        }
    }

    $.new_handle_toggle = function(options){
        var settings = $.extend({
            obj: '.handle-toggle-change',
            objToggleClick: '.handle-toggle-click',
            objToggleContent: '.handle-toggle-content',
            objDisplayToggle: '.display-toggle', 
        }, options );

        $( "body" ).delegate( settings.obj, "init change", function(event) {
            var self = $(this);
            var input_type = $.checkUndefined(self.attr('type'));
            var value = self.val();

            if( $.inArray(input_type, ['radio', 'checkbox']) !== -1 ) {
                if( !self.is(':checked') ) {
                    value = false;
                }
            }

            _callHandleToggle(self, value, event);
        });

        $( "body" ).delegate( settings.objToggleClick, "init click", function(event) {
            var self = $(this);
            var input_type = $.checkUndefined(self.attr('type'));
            var value = self.val();

            if( $.inArray(input_type, ['radio', 'checkbox']) !== -1 ) {
                if( !self.is(':checked') ) {
                    value = false;
                }
            }

            _callHandleToggle(self, value, event);
        });

        $('body').delegate(settings.objDisplayToggle, 'click', function(){
            var self = $(this);
            var target = $(self.data('target'));
            var clear = $.checkUndefined(self.data('clear'), false);

            if(target.length){
                var toggleClass = $.checkUndefined(self.data('class'), 'hide');

                if(clear === true){
                    target.attr('class', '');   
                }

                target.toggleClass(toggleClass);
            }
        });

        $( "body" ).delegate( settings.objToggleContent, "init click", function(event) {
            var self = $(this);
            
            var target = self.attr('data-target');
            var reverse = self.attr('data-reverse');
            var type = self.attr('data-type');
            
            if(target != ''){
                if( self.is(':checked') ) {
                    if(type == 'disabled-input'){
                        if(reverse == 'true'){
                            $(target+' input').attr('disabled', false);
                        }else{
                            $(target+' input').attr('disabled', true);
                        }
                    }else{
                        if(reverse == 'true'){
                            $(target).fadeOut();
                        }else{
                            $(target).fadeIn();
                        }
                    }
                } else {
                    if(type == 'disabled-input'){
                        if(reverse == 'true'){
                            $(target+' input').attr('disabled', true);
                        }else{
                            $(target+' input').attr('disabled', false);
                        }
                    }else{
                        if(reverse == 'true'){
                            $(target).fadeIn();
                        }else{
                            $(target).fadeOut();
                        }
                    }
                }

                if(gmapRku.length > 0){
                    map = gmapRku.gmap3('get');
                    google.maps.event.trigger(map, 'resize');
                }
            }
        });

    //  trigger init event for first page load
        if($(settings.obj).length){
            $(settings.obj).trigger('init');
        }
        if($(settings.objToggleContent).length){
            $(settings.objToggleContent).trigger('init');
        }

        if($(settings.objToggleClick+'[type="radio"]:checked').length){
            $(settings.objToggleClick+'[type="radio"]:checked').trigger('init');
        } else if($(settings.objToggleClick).length){
            $(settings.objToggleClick).trigger('init');
        }
    }

    $( "body" ).delegate( '.empty-change', "init change", function(event) {
        var self = $(this);
        
        var target = self.attr('data-target');
        
        if(target != ''){
            $(target).empty();
            
            calcGrandTotalCustom();
        }
    });

    if( $('.ajax-infinity').length > 0 ) {
        $("#ajaxLoading").slideDown(100);
        $.directAjaxLink({
            obj: $('.ajax-infinity'),
        });
    }
}( jQuery ));