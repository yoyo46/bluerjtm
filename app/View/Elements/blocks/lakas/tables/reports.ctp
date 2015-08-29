<?php
        if(!empty($lakas)){
            $withNumber = !empty($withNumber)?$withNumber:false;
            $no = 1;

            foreach ($lakas as $key => $value) {
                $tgl_laka = $this->Common->filterEmptyField($value, 'Laka', 'tgl_laka');
                $nopol = $this->Common->filterEmptyField($value, 'Laka', 'nopol');
                $change_driver_name = $this->Common->filterEmptyField($value, 'Laka', 'change_driver_name');
                $driver_name = $this->Common->filterEmptyField($value, 'Laka', 'driver_name', $change_driver_name);
                $lokasi_laka = $this->Common->filterEmptyField($value, 'Laka', 'lokasi_laka');
                $description_laka = $this->Common->filterEmptyField($value, 'Laka', 'description_laka');
                $insurance = $this->Common->filterEmptyField($value, 'Laka', 'insurances');
                $completed = $this->Common->filterEmptyField($value, 'Laka', 'completed');

                $capacity = $this->Common->filterEmptyField($value, 'Truck', 'capacity');
                $category = $this->Common->filterEmptyField($value, 'TruckCategory', 'name');

                if( in_array(1, $insurance) ) {
                    $insurance = __('Tidak');
                    $lblClass = 'warning';
                } else {
                    $insurance =__('Ada');
                    $lblClass = 'info';
                }

                if( !empty($completed) ) {
                    $statusCompleted = __('Aktif');
                    $lblClassComplated = 'primary';
                } else {
                    $statusCompleted = __('Selesai');
                    $lblClassComplated = 'success';
                }

                $customTglLaka = $this->Common->customDate($tgl_laka, 'd M Y');
                $lblCompleted = !empty($completed)?'success':'danger';

                $insurance = $this->Html->tag('span', $insurance, array(
                    'class' => sprintf('label label-%s', $lblClass),
                ));
                $completed = $this->Html->tag('span', $statusCompleted, array(
                    'class' => sprintf('label label-%s', $lblClassComplated),
                ));
?>
<tr>
    <?php 
            if( !empty($withNumber) ) {
                echo $this->Html->tag('td', $no, array(
                    'style' => 'text-align:center;',
                ));
            }

            echo $this->Html->tag('td', $customTglLaka, array(
                'style' => 'text-align:center;',
            ));
            echo $this->Html->tag('td', $nopol, array(
                'style' => 'text-align:center;',
            ));
            echo $this->Html->tag('td', $capacity, array(
                'style' => 'text-align:center;',
            ));
            echo $this->Html->tag('td', $category, array(
                'style' => 'text-align:center;',
            ));
            echo $this->Html->tag('td', $driver_name);
            echo $this->Html->tag('td', $lokasi_laka);
            echo $this->Html->tag('td', $description_laka);
            echo $this->Html->tag('td', $insurance, array(
                'style' => 'text-align:center;',
            ));
            echo $this->Html->tag('td', $completed, array(
                'style' => 'text-align:center;',
            ));
            echo $this->Html->tag('td', 0, array(
                'style' => 'text-align:right;',
            ));
    ?>
</tr>
<?php
                $no++;
            }
        }
?>