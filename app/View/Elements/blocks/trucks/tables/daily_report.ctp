<?php 
        if(!empty($ttujs)){
            foreach ($ttujs as $key => $value) {
                $ttuj_date = $this->Common->filterEmptyField($value, 'Ttuj', 'ttuj_date');
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $driver_pengganti = $this->Common->filterEmptyField($value, 'DriverPenganti', 'driver_name');
                $driver = $this->Common->filterEmptyField($value, 'Driver', 'driver_name', $driver_pengganti);
                $to_city = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $total_unit = $this->Common->filterEmptyField($value, 'Ttuj', 'total_unit', '-');
                $uang_jalan_2 = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_2', 0);
                $uang_jalan = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_1', 0) + $uang_jalan_2;
                $uang_jalan_extra = $this->Common->filterEmptyField($value, 'Ttuj', 'uang_jalan_extra', 0);
                $total_uang_jalan = $uang_jalan + $uang_jalan_extra;

                $content = $this->Common->_getDataColumn($this->Common->customDate($ttuj_date), 'Ttuj', 'ttuj_date', array(
                    'style' => 'text-align: center;',
                    'class' => 'ttuj_date',
                ));
                $content .= $this->Common->_getDataColumn($nopol, 'Ttuj', 'nopol', array(
                    'class' => 'nopol',
                ));
                $content .= $this->Common->_getDataColumn($driver, 'Driver', 'driver_name', array(
                    'class' => 'driver',
                ));
                $content .= $this->Common->_getDataColumn($to_city, 'Ttuj', 'to_city_name', array(
                    'class' => 'to_city',
                ));
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
        }
?>