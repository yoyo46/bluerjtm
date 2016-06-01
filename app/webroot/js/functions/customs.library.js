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

                $.calcTotal();
                $.rowRemoved();
                $.callChoosen({
                    obj: $('.form-added .chosen-select'),
                });
                $.qtyMuatanPress({
                    obj: $('.form-added .qty-muatan'),
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

    $.convertNumber = function(num, type, empty){
        if( typeof num != 'undefined' ) {
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
        } else {
            num = 0;
        }

        return num;
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
                var func = $.checkUndefined(self.attr('data-function'), false);
                var func_type = $.checkUndefined(self.attr('data-function-type'), false);

                self.val( $.convertDecimal(self, 2) );

                if( func != false ) {
                    eval(func + '(\''+func_type+'\');');
                }
            });

            jQuery.each( settings.objComa, function( i, val ) {
                var self = $(this);
                self.val( $.convertDecimal(self, 2) );
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

    $.pickData = function() {
        $('.browse-form table tr').off('click');
        $('.browse-form table tr').click(function(){
            var vthis = $(this);
            var data_value = vthis.attr('data-value');
            var data_change = vthis.attr('data-change');
            var data_ajax = vthis.attr('href');
            var data_change_extra = vthis.attr('data-change-extra');
            var data_extra_text = $.filterEmptyField(vthis.attr('data-extra-text'));
            var type = $(data_change).attr('type');
            var obj_note = $.checkUndefined(vthis.find('.document-note'), false);
            var data_note_target = $.checkUndefined(obj_note.attr('data-target-note'), false);
            var data_note_value = $.checkUndefined(obj_note.html(), false);

            if(data_change == '.cash-bank-auth-user'){
                data_change = vthis.attr('data-rel')+' '+data_change;
            }

            $(data_change).val(data_value);

            if( type != 'text' ) {
                $(data_change).trigger('change');
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

            if( typeof data_ajax != 'undefined' ) {
                $.directAjaxLink({
                    obj: vthis,
                });
            }

            if( typeof data_change_extra != 'undefined' ) {
                $(data_change_extra).html(data_extra_text);
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
                            var status = $(content).find('#msg-status').html();
                            var msg = $(content).find('#msg-text').html();
                            var contentHtml = $(content).filter(data_wrapper_write).html();

                            if(typeof contentHtml == 'undefined' ) {
                                contentHtml = $(content).find(data_wrapper_write).html();
                            }

                            if( $(data_wrapper_write).length > 0 ) {
                                if( status == 'success' && data_reload == 'true' ) {
                                    if(typeof data_reload_url == 'undefined' ) {
                                        window.location.reload();
                                    } else {
                                        location.href = data_reload_url;
                                    }
                                } else if(status == 'success' && typeof data_wrapper_success != 'undefined' && $(data_wrapper_success).length > 0 ) {
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
                                        $('#myModal .close.btn').trigger("click");
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
        }, options );

        var vthis = settings.obj;
        var url = vthis.attr('href');
        var alert_msg = vthis.attr('alert');
        var title = vthis.attr('title');
        var data_check = $.checkUndefined(vthis.attr('data-check'), false);
        var data_check_named = $.checkUndefined(vthis.attr('data-check-named'), false);
        var data_check_alert = $.checkUndefined(vthis.attr('data-check-alert'), false);
        var modalSize = $.checkUndefined(vthis.attr('data-size'), '');

        if( data_check != false ) {
            dataUrl = $(data_check).val();

            if( dataUrl == '' ) {
                alert(data_check_alert);
                return false;
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
                }
            });

            settings.objCheck.off('click');
            settings.objCheck.click(function(){
                var self = $(this);
                check_option_coa(self)
            });

            function check_option_coa(self){
                var parent = self.parents('.pick-document');
                var rel_id = parent.attr('rel');
                var temp_picker = $('.temp-document-picker');
                var pickDocumentStr = '.pick-document[rel="'+rel_id+'"]';
                var pickDocument = $(pickDocumentStr);

                if(self.is(':checked')){
                    if(temp_picker.find('.pick-document[rel="'+rel_id+'"]').length <= 0){
                        var html_content = '<tr class="pick-document" rel="'+rel_id+'">'+pickDocument.html()+'</tr>';
                        temp_picker.find('tbody').append(html_content);

                        temp_picker.find('td.hide').removeClass('hide');
                        temp_picker.find('td.removed').remove();

                        calcItemTotal(temp_picker);

                        temp_picker.removeClass('hide');
                    }
                } else {
                    temp_picker.find(pickDocumentStr).remove();
                    calcItemTotal(temp_picker);
                }

                $.rebuildFunction();
                $.inputPrice({
                    obj: temp_picker.find('.input_price'),
                });
                $.inputNumber();
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

        $.each( $('.pick-document'), function( i, val ) {
            var self = $(this);
            grandtotal += calculate(self);
        });

        if( objGrandTotal.length > 0 ) {
            objGrandTotal.html( $.formatDecimal(grandtotal, decimal) );
        }
    }

    function calculate(parent, self){
        var type = parent.attr('data-type');
        var objPrice = parent.find('.price');
        var self = $.checkUndefined(self, objPrice);

        var price = $.convertNumber(objPrice.val());
        var disc = $.convertNumber(parent.find('.disc').val());
        var ppn = $.convertNumber(parent.find('.ppn').val());
        var qty = $.convertNumber(parent.find('.qty').val(), 'int', 1);
        var ppn_include = $('.ppn_include:checked').val();
        var format_type = self.attr('data-type');

        if( type == 'single-total' ) {
            total = price;
        } else {
            total = ( ( price * qty ) - disc );

            if( ppn_include != 1 ) {
                total += ppn;
            }
        }

        if( format_type == 'input_price_coma' ) {
            self.val( $.convertDecimal(self, 2) );
        }

        return total;
    }

    $.calcTotal = function(options){
        var settings = $.extend({
            obj: $('.document-calc .price,.document-calc .disc,.document-calc .ppn,.document-calc .total,.document-calc .qty'),
            objClick: $('.ppn_include'),
            objDelete: $('.delete-document'),
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
        }

        if( settings.objClick.length > 0 ) {
            settings.objClick.off('click');
            settings.objClick.click(function(){
                $.each( $('.pick-document'), function( i, val ) {
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
                    calcGrandTotal();
                    calcItemTotal(table);
                }

                return false;
            });
        }
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
            num = num.replace(/,/gi, "").replace(/ /gi, "").replace(/IDR/gi, "").replace(/Rp/gi, "");

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
            
            $.directAjaxLink({
                obj: self,
            });

            return false;
        });

        settings.objChange.off('change');
        settings.objChange.change(function(){
            var self = $(this);
            var href = self.attr('href');

            href += '/' + self.val();

            $.directAjaxLink({
                obj: self,
                url: href,
            });

            return false;
        });

        settings.objBlur.off('blur');
        settings.objBlur.blur(function(){
            var self = $(this);

            $.directAjaxLink({
                obj: self,
            });

            return false;
        });
    }

    $.directAjaxLink = function( options ) {
        var settings = $.extend({
            obj: $('.ajax-link'),
            url: false,
        }, options );

        var url = settings.url;
        var parents = settings.obj.parents('.ajax-parent');
        var type = settings.obj.attr('data-type');
        var flag_alert = settings.obj.attr('data-alert');
        var data_ajax_type = settings.obj.attr('data-ajax-type');
        var data_wrapper_write = settings.obj.attr('data-wrapper-write');
        var data_action = settings.obj.attr('data-action');
        var data_pushstate = settings.obj.attr('data-pushstate');
        var data_url_pushstate = settings.obj.attr('data-url-pushstate');
        var data_form = settings.obj.attr('data-form');
        var data_scroll = settings.obj.attr('data-scroll');
        var data_show = settings.obj.attr('data-show');
        var func = $.checkUndefined(settings.obj.attr('data-function'), false);
        var func_type = $.checkUndefined(settings.obj.attr('data-function-type'), false);
        var formData = false; 

        if( url == false ) {
            url = settings.obj.attr('href');
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

                    if( $(data_wrapper_write).length > 0 ) {
                        $(data_wrapper_write).html(contentHtml);
                        $.rebuildFunctionAjax( $(data_wrapper_write) );

                        if(data_action == 'messages' ) {
                            var current_active = settings.obj.parents('li');
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
                }

                return false;
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert('Gagal melakukan proses. Silahkan coba beberapa saat lagi.');
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
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.daterangepicker({
                format: 'DD/MM/YYYY',
            });
        }
    }
}( jQuery ));