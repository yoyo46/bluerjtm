<?php 
        if(!empty($ttujs)){
            $grandtotal_uang_jalan = 0;
            $grandtotal_uang_jalan_extra = 0;
            $grandtotal_total_uang_jalan = 0;
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
                // $uang_jalan_2 = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_2', 0);
                // $uang_jalan = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_1', 0) + $uang_jalan_2;
                $uang_jalan = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_1', 0);
                $uang_jalan_extra = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_extra', 0);
                $total_uang_jalan = $uang_jalan + $uang_jalan_extra;

                $grandtotal_uang_jalan += $uang_jalan;
                $grandtotal_uang_jalan_extra += $uang_jalan_extra;
                $grandtotal_total_uang_jalan += $total_uang_jalan;
                $grandtotal_unit += $total_unit;

                $content = $this->Common->_getDataColumn($this->Common->customDate($ttuj_date, 'd M Y'), 'Ttuj', 'ttuj_date', array(
                    'style' => 'text-align: center;',
                    'class' => 'ttuj_date',
                ));
                $content .= $this->Html->tag('td', $no_ttuj);
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
                $content .= $this->Html->tag('td', $note);
                $content .= $this->Common->_getDataColumn($total_unit, 'Ttuj', 'total_unit', array(
                    'class' => 'unit',
                    'style' => 'text-align: center;',
                ));
                $content .= $this->Common->_getDataColumn($uang_jalan, 'Ttuj', 'uang_jalan', array(
                    'class' => 'uang_jalan',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($uang_jalan_extra, 'Ttuj', 'uang_jalan_extra', array(
                    'class' => 'uang_jalan_extra',
                    'style' => 'text-align: right;',
                    'data-currency' => true,
                ));
                $content .= $this->Common->_getDataColumn($total_uang_jalan, 'Truck', 'total)uang_jalan', array(
                    'class' => 'total_uang_jalan',
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
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_uang_jalan), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_uang_jalan_extra), array(
                'style' => 'text-align: right;',
            ));
            $content .= $this->Html->tag('td', $this->Common->getFormatPrice($grandtotal_total_uang_jalan), array(
                'style' => 'text-align: right;',
            ));

            echo $this->Html->tag('tr', $content, array(
                'style' => 'font-weight: bold;'
            ));
        }
?>