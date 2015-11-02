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

    $.convertNumber = function(num, type){
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
            var converter_uang_jalan_extra = $.convertNumber($('#converter-uang-jalan-extra-'+tipe_motor_id).html(), 'int');

            total_muatan += qtyMuatan;

            if( converter_uang_jalan_extra != 0 ) {
                totalMuatanExtra += qtyMuatan * converter_uang_jalan_extra;
            } else {
                totalMuatanExtra = total_muatan;
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

            if( typeof $('.commission-extra-'+group_motor_id).html() != 'undefined' ) {
                commission_extra_tipe_motor = parseInt($('.commission-extra-'+group_motor_id).html());
                min_capacity_commission_extra = parseInt($('.commission-extra-min-capacity-'+group_motor_id).html());


                if( qtyMuatan > min_capacity_commission_extra ) {
                    var capacityCost = qtyMuatan - min_capacity_commission_extra;

                    if( converter_uang_jalan_extra != 0 ) {
                        commission_extra_tipe_motor *= converter_uang_jalan_extra;
                    }

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

    $.inputPrice = function(options){
        var settings = $.extend({
            obj: $('.input_price'),
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
    }

    $.inputNumber = function(options){
        var settings = $.extend({
            obj: $('.input_number'),
        }, options );

        if( settings.obj.length > 0 ) {
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
        }, options );

        if( settings.obj.length > 0 ) {
            settings.objCheck.off('click');
            settings.objCheck.click(function(){
                if(settings.objCheck.length > 0){
                    jQuery.each( settings.objCheck, function( i, val ) {
                        var self = $(this);
                        check_option_coa(self)
                    });
                }
            });

            function check_option_coa(self){
                var parent = self.parents('.pick-document');
                var rel_id = parent.attr('rel');
                var temp_picker = $('.temp-document-picker');
                var pickDocument = $('.pick-document[rel="'+rel_id+'"]');

                if(self.is(':checked')){
                    if(temp_picker.find('.pick-document[rel="'+rel_id+'"]').length <= 0){
                        var html_content = '<tr class="pick-document" rel="'+rel_id+'">'+pickDocument.html()+'</tr>';
                        temp_picker.find('tbody').append(html_content);

                        temp_picker.find('td.hide').removeClass('hide');
                        temp_picker.find('td.removed').remove();

                        temp_picker.removeClass('hide');
                        $.rebuildFunction();
                        $.inputPrice({
                            obj: temp_picker.find('.input_price'),
                        });
                    }
                }
            }
        }
    }

    $.calcTotal = function(options){
        var settings = $.extend({
            obj: $('.document-calc .price,.document-calc .disc,.document-calc .ppn,.document-calc .total'),
        }, options );

        if( settings.obj.length > 0 ) {
            settings.obj.off('blur');
            settings.obj.blur(function(){
                var self = $(this);
                var parent = self.parents('.pick-document');
                var objTotal = parent.find('.total');
                var objGrandTotal = $('.temp-document-picker .grandtotal .total');
                var grandtotal = 0;

                total = calculate(parent);
                objTotal.html( $.formatDecimal(total) );

                $.each( $('.pick-document'), function( i, val ) {
                    var self = $(this);
                    grandtotal += calculate(self);
                });

                objGrandTotal.html( $.formatDecimal(grandtotal) );
            });
        }

        function calculate(parent){
            var price = $.convertNumber(parent.find('.price').val());
            var disc = $.convertNumber(parent.find('.disc').val());
            var ppn = $.convertNumber(parent.find('.ppn').val());

            total = ( price - disc ) + ppn;

            return total;
        }
    }

    $.convertNumber = function(num, type){
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

            if( isNaN(num) ) {
                num = 0;
            }
        } else {
            num = 0;
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

    $.rebuildFunction = function() {
        $.ajaxForm();
        $.rowAdded();
        $.ajaxModal();
        $.documentPicker();
        $.calcTotal();
    }

    $.rebuildFunctionAjax = function( obj ) {
        $.rebuildFunction();
        $.inputPrice({
            obj: obj.find('.input_price'),
        });
    }
}( jQuery ));