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
                $driver_name = $this->Common->filterEmptyField($value, 'DriverPengganti', 'driver_name', $driver_name);

                $from_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'from_city_name');
                $to_city_name = $this->Common->filterEmptyField($value, 'Ttuj', 'to_city_name');
                $total_leadtime = $this->Common->filterEmptyField($value, 'Ttuj', 'total_leadtime', '-');

                $customDateBerangkat = $this->Common->formatDate($tgljam_berangkat, 'd/m/Y H:i');
                $customDateTiba = $this->Common->formatDate($tgljam_tiba, 'd/m/Y H:i');
                $customDateBalik = $this->Common->formatDate($tgljam_balik, 'd/m/Y H:i');
                $customDatePool = $this->Common->formatDate($tgljam_pool, 'd/m/Y H:i');
                $status = $this->Revenue->_callStatusTTUJ($value, 'sort', true);

                $arrive_leadtime = $this->Ttuj->_callLeadTime($value, 'arrive');
                $back_leadtime = $this->Ttuj->_callLeadTime($value, 'back');

                $arrive_leadtime_total = $this->Common->filterEmptyField($value, 'Ttuj', 'arrive_leadtime_total');
                $back_leadtime_total = $this->Common->filterEmptyField($value, 'Ttuj', 'back_leadtime_total');

                $arrive_over_time = $this->Common->filterEmptyField($value, 'UangJalan', 'arrive_lead_time');
                $back_orver_time = $this->Common->filterEmptyField($value, 'UangJalan', 'back_lead_time');

                if( $arrive_leadtime_total > $arrive_over_time ){
                    $labelClass = 'danger';
                } else {
                    $labelClass = 'success';
                }

                $customLeadTimeArrive = $this->Html->tag('span', $arrive_leadtime, array(
                    'class' => sprintf('block label label-%s', $labelClass),
                ));

                if( $back_leadtime_total > $back_orver_time ){
                    $labelClass = 'danger';
                } else {
                    $labelClass = 'success';
                }
                
                $customLeadTimeBack = $this->Html->tag('span', $back_leadtime, array(
                    'class' => sprintf('block label label-%s', $labelClass),
                ));
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
            echo $this->Html->tag('td', $customLeadTimeArrive);
            echo $this->Html->tag('td', $arrive_over_time);
            echo $this->Html->tag('td', $customDateBalik);
            echo $this->Html->tag('td', $customDatePool);
            echo $this->Html->tag('td', $customLeadTimeBack);
            echo $this->Html->tag('td', $total_leadtime);
            echo $this->Html->tag('td', $back_orver_time);
    ?>
</tr>
<?php
            }
        }
?>