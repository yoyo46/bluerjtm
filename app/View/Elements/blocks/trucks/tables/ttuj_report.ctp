<?php 
        if(!empty($ttujs)){
            $grandtotal_uang_kuli_muat = 0;
            $grandtotal_uang_kuli_bongkar = 0;
            $grandtotal_asdp = 0;
            $grandtotal_uang_kawal = 0;
            $grandtotal_uang_keamanan = 0;
            $grandtotal = 0;

            $grandtotal_uang_jalan = 0;
            $grandtotal_uang_jalan_2 = 0;
            $grandtotal_uang_jalan_extra = 0;
            $grandtotal_commission = 0;
            $grandtotal_commission_extra = 0;

            $grandtotal_unit = 0;

            foreach ($ttujs as $key => $value) {
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $from_city = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $total_unit = $this->Common->filterEmptyField($value, 'Ttuj', 'total_unit', '-');
                $note = $this->Common->filterEmptyField($value, 'Ttuj', 'note');
                $capacity = $this->Common->filterEmptyField($value, 'Truck', 'capacity');

                $uang_kuli_muat = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_kuli_muat', 0);
                $uang_kuli_bongkar = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_kuli_bongkar', 0);
                $asdp = $this->Common->filterEmptyField($value, 'Ttuj', 'asdp', 0);
                $uang_kawal = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_kawal', 0);
                $uang_keamanan = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_keamanan', 0);
                $total = $uang_kuli_muat + $uang_kuli_bongkar + $asdp + $uang_kawal + $uang_keamanan;

                $uang_jalan_id = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_id', '-');
                $uang_jalan = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_1', 0);
                $uang_jalan_2 = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_2', 0);
                $uang_jalan_extra = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_extra', 0);
                $commission = $this->Common->filterEmptyField($value, 'Ttuj', 'commission', 0);
                $commission_extra = $this->Common->filterEmptyField($value, 'Ttuj', 'commission_extra', 0);

                $branch = $this->Common->filterEmptyField($value, 'Branch', 'code');
                $customer = $this->Common->filterEmptyField($value, 'Customer', 'code');
                
                $driver = $this->Common->_callGetDriver($value);

                $total += $uang_jalan + $uang_jalan_extra + $uang_jalan_2 + $commission + $commission_extra;

                $grandtotal_uang_jalan += $uang_jalan;
                $grandtotal_uang_jalan_extra += $uang_jalan_extra;
                $grandtotal_uang_jalan_2 += $uang_jalan_2;
                $grandtotal_commission += $commission;
                $grandtotal_commission_extra += $commission_extra;

                $grandtotal_uang_kuli_muat += $uang_kuli_muat;
                $grandtotal_uang_kuli_bongkar += $uang_kuli_bongkar;
                $grandtotal_asdp += $asdp;
                $grandtotal_uang_kawal += $uang_kawal;
                $grandtotal_uang_keamanan += $uang_keamanan;
                $grandtotal += $total;
                $grandtotal_unit += $total_unit;

                $content = $this->Common->_getDataColumn($branch, 'Branch', 'code', array(
                    'class' => 'branch',
                ));
                $content .= $this->Common->_getDataColumn($this->Common->customDate($ttuj_date, 'd M Y'), 'Ttuj', 'ttuj_date', array(
                    'style' => 'text-align: center;',
                    'class' => 'ttuj_date',
                ));
                $content .= $this->Common->_getDataColumn($no_ttuj, 'Ttuj', 'no_ttuj', array(
                    'class' => 'no_ttuj',
                ));
                $content .= $this->Common->_getDataColumn($customer, 'Customer', 'code', array(
                    'class' => 'customer',
                ));
                $content .= $this->Common->_getDataColumn($nopol, 'Ttuj', 'nopol', array(
                    'class' => 'nopol',
                ));
                $content .= $this->Common->_getDataColumn($capacity, 'Truck', 'capacity', array(
                    'class' => 'capacity',
                    'style' => 'text-align: center;',
                ));
                $content .= $this->Common->_getDataColumn($driver, 'Driver', 'driver_name', array(
                    'class' => 'driver',
                ));
                $content .= $this->Common->_getDataColumn($from_city, 'Ttuj', 'from_city_name', array(
                    'class' => 'from_city',
                ));
                $content .= $this->Common->_getDataColumn($to_city, 'Ttuj', 'to_city_name', array(
                    'class' => 'to_city',
                ));
                $content .= $this->Common->_getDataColumn($note, 'Ttuj', 'note', array(
                    'class' => 'note',
                ));
                $content .= $this->Common->_getDataColumn($total_unit, 'Ttuj', 'total_unit', array(
                    'class' => 'unit',
                    'style' => 'text-align: center;',
                ));
                $content .= $this->Common->_getDataColumn(__('#%s', $uang_jalan_id), 'Ttuj', 'uang_jalan_id', array(
                    'class' => 'uang_jalan_id',
                    'style' => 'text-align: center;',
                ));
                $content .= $this->Common->_getDataColumn($uang_jalan, 'Ttuj', 'uang_jalan', array(
                    'class' => 'uang_jalan',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($uang_jalan_2, 'Ttuj', 'uang_jalan_2', array(
                    'class' => 'uang_jalan_2',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($uang_jalan_extra, 'Ttuj', 'uang_jalan_extra', array(
                    'class' => 'uang_jalan_extra',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($commission, 'Ttuj', 'commission', array(
                    'class' => 'commission',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($commission_extra, 'Ttuj', 'commission_extra', array(
                    'class' => 'commission_extra',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($uang_kuli_muat, 'Ttuj', 'uang_kuli_muat', array(
                    'class' => 'uang_kuli_muat',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($uang_kuli_bongkar, 'Ttuj', 'uang_kuli_bongkar', array(
                    'class' => 'uang_kuli_bongkar',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($asdp, 'Ttuj', 'asdp', array(
                    'class' => 'asdp',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($uang_kawal, 'Ttuj', 'uang_kawal', array(
                    'class' => 'uang_kawal',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($uang_keamanan, 'Ttuj', 'uang_keamanan', array(
                    'class' => 'uang_keamanan',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($total, 'Ttuj', 'total', array(
                    'class' => 'total',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));

                echo $this->Html->tag('tr', $content);
            }

            $content = $this->Html->tag('td', '&nbsp;');
            $content .= $this->Html->tag('td', '&nbsp;');
            $content .= $this->Html->tag('td', '&nbsp;');
            $content .= $this->Html->tag('td', '&nbsp;');
            $content .= $this->Html->tag('td', '&nbsp;');
            $content .= $this->Html->tag('td', '&nbsp;');
            $content .= $this->Html->tag('td', '&nbsp;');
            $content .= $this->Html->tag('td', '&nbsp;');
            $content .= $this->Html->tag('td', '&nbsp;');
            $content .= $this->Html->tag('td', __('Total'), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $grandtotal_unit, array(
                'style' => 'text-align: center;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_uang_jalan), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_uang_jalan_2), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_uang_jalan_extra), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_commission), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_commission_extra), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_uang_kuli_muat), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_uang_kuli_bongkar), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_asdp), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_uang_kawal), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_uang_keamanan), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal), array(
                'style' => 'text-align: right;',
            ));

            echo $this->Html->tag('tr', $content, array(
                'style' => 'font-weight: bold;'
            ));
        }
?>