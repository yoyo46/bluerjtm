<?php 
        if(!empty($ttujs)){
            $grandtotal_uang_kuli_muat = 0;
            $grandtotal_uang_kuli_bongkar = 0;
            $grandtotal_asdp = 0;
            $grandtotal_uang_kawal = 0;
            $grandtotal_uang_keamanan = 0;
            $grandtotal = 0;
            $grandtotal_unit = 0;

            foreach ($ttujs as $key => $value) {
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $driver_pengganti = $this->Common->filterEmptyField($value, 'DriverPenganti', 'driver_name');
                $driver = $this->Common->filterEmptyField($value, 'Driver', 'driver_name', $driver_pengganti);
                $from_city = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $total_unit = $this->Common->filterEmptyField($value, 'Ttuj', 'total_unit', '-');
                $note = $this->Common->filterEmptyField($value, 'Ttuj', 'note');

                $uang_kuli_muat = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_kuli_muat', 0);
                $uang_kuli_bongkar = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_kuli_bongkar', 0);
                $asdp = $this->Common->filterEmptyField($value, 'Ttuj', 'asdp', 0);
                $uang_kawal = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_kawal', 0);
                $uang_keamanan = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_keamanan', 0);
                $total = $uang_kuli_muat + $uang_kuli_bongkar + $asdp + $uang_kawal + $uang_keamanan;

                $grandtotal_uang_kuli_muat += $uang_kuli_muat;
                $grandtotal_uang_kuli_bongkar += $uang_kuli_bongkar;
                $grandtotal_asdp += $asdp;
                $grandtotal_uang_kawal += $uang_kawal;
                $grandtotal_uang_keamanan += $uang_keamanan;
                $grandtotal += $total;
                $grandtotal_unit += $total_unit;

                $content = $this->Common->_getDataColumn($this->Common->customDate($ttuj_date, 'd M Y'), 'Ttuj', 'ttuj_date', array(
                    'style' => 'text-align: center;',
                    'class' => 'ttuj_date',
                ));
                $content .= $this->Common->_getDataColumn($no_ttuj, 'Ttuj', 'no_ttuj', array(
                    'class' => 'no_ttuj',
                ));
                $content .= $this->Common->_getDataColumn($nopol, 'Ttuj', 'nopol', array(
                    'class' => 'nopol',
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

            $content = $this->Html->tag('td', __('Total'), array(
                'style' => 'text-align: right;',
                'colspan' => 7,
            ));
            $content .= $this->Html->tag('td', $grandtotal_unit, array(
                'style' => 'text-align: center;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getCurrencyPrice($grandtotal_uang_kuli_muat), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getCurrencyPrice($grandtotal_uang_kuli_bongkar), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getCurrencyPrice($grandtotal_asdp), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getCurrencyPrice($grandtotal_uang_kawal), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getCurrencyPrice($grandtotal_uang_keamanan), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getCurrencyPrice($grandtotal), array(
                'style' => 'text-align: right;',
            ));

            echo $this->Html->tag('tr', $content, array(
                'style' => 'font-weight: bold;'
            ));
        }
?>