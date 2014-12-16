$(function() {
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
});