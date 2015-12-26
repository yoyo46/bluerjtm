<?php
        if(!empty($values)){
            foreach ($values as $key => $value) {
                $nopol = $this->Common->filterEmptyField($value, 'Ttuj', 'nopol');
                $capacity = $this->Common->filterEmptyField($value, 'Truck', 'capacity');
                $no_ttuj = $this->Common->filterEmptyField($value, 'Ttuj', 'no_ttuj');
                $qty = $this->Common->filterEmptyField($value, 'Qty');

                $tgljam_berangkat = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_berangkat');
                $tgljam_tiba = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_tiba');
                $tgljam_balik = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_balik');
                $tgljam_pool = $this->Common->filterEmptyField($value, 'Ttuj', 'tgljam_pool');

                $driver_name = $this->Common->filterEmptyField($value, 'Ttuj', 'driver_name');
                $driver_name = $this->Common->filterEmptyField($value, 'DriverPenganti', 'driver_name', $driver_name);

                $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');

                $customDateBerangkat = $this->Common->formatDate($tgljam_berangkat, 'd/m/Y H:i');
                $customDateTiba = $this->Common->formatDate($tgljam_tiba, 'd/m/Y H:i');
                $customDateBalik = $this->Common->formatDate($tgljam_balik, 'd/m/Y H:i');
                $customDatePool = $this->Common->formatDate($tgljam_pool, 'd/m/Y H:i');
                $status = $this->Revenue->_callStatusTTUJ($value, 'sort', true);

                $arrive_lead_time = $this->Common->filterEmptyField($value, 'ArriveLeadTime', 'total_hour', '-');
                $target_arrive_lead_time = $this->Common->filterEmptyField($value, 'UangJalan', 'arrive_lead_time', '-');
                
                $back_lead_time = $this->Common->filterEmptyField($value, 'BackLeadTime', 'total_hour', '-');
                $target_back_lead_time = $this->Common->filterEmptyField($value, 'UangJalan', 'back_lead_time');

                $totalLeadTime = $arrive_lead_time + $back_lead_time;

                if( $arrive_lead_time > $target_arrive_lead_time ) {
                    $arrive_lead_time = $this->Html->tag('div', $arrive_lead_time, array(
                        'style' => 'color: #ff0000;'
                    ));
                }

                if( !empty($totalLeadTime) ) {
                    if( $totalLeadTime > $target_back_lead_time ) {
                        $totalLeadTime = $this->Html->tag('div', $totalLeadTime, array(
                            'style' => 'color: #ff0000;'
                        ));
                    }
                } else {
                    $totalLeadTime = '-';
                }
                // if( $back_lead_time > $target_back_lead_time ) {
                //     $back_lead_time = $this->Html->tag('div', $back_lead_time, array(
                //         'style' => 'color: #ff0000;'
                //     ));
                // }
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $nopol);
            echo $this->Html->tag('td', $capacity);
            echo $this->Html->tag('td', $driver_name);
            echo $this->Html->tag('td', $no_ttuj);
            echo $this->Html->tag('td', $from_city_name);
            echo $this->Html->tag('td', $to_city_name);
            echo $this->Html->tag('td', $qty);
            echo $this->Html->tag('td', $status);
            echo $this->Html->tag('td', $customDateBerangkat);
            echo $this->Html->tag('td', $customDateTiba);
            echo $this->Html->tag('td', $arrive_lead_time);
            echo $this->Html->tag('td', $target_arrive_lead_time);
            echo $this->Html->tag('td', $customDateBalik);
            echo $this->Html->tag('td', $customDatePool);
            echo $this->Html->tag('td', $back_lead_time);
            echo $this->Html->tag('td', $totalLeadTime);
            echo $this->Html->tag('td', $target_back_lead_time);
    ?>
</tr>
<?php
            }
        }
?>