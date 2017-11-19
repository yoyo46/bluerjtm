<?php 
        // Data Truck
        $nopol = $this->Common->filterEmptyField($truck, 'TruckMutation', 'nopol');
        $branch_name = $this->Common->filterEmptyField($truck, 'TruckMutation', 'branch_name');
        $category = $this->Common->filterEmptyField($truck, 'TruckMutation', 'category');
        $facility = $this->Common->filterEmptyField($truck, 'TruckMutation', 'facility');
        $driver_name = $this->Common->filterEmptyField($truck, 'TruckMutation', 'driver_name');
        $capacity = $this->Common->filterEmptyField($truck, 'TruckMutation', 'capacity');

        // Data Truck
        $change_nopol = $this->Common->filterEmptyField($truck, 'TruckMutation', 'change_nopol');
        $change_branch_name = $this->Common->filterEmptyField($truck, 'TruckMutation', 'change_branch_name');
        $change_category = $this->Common->filterEmptyField($truck, 'TruckMutation', 'change_category');
        $change_facility = $this->Common->filterEmptyField($truck, 'TruckMutation', 'change_facility');
        $change_driver_name = $this->Common->filterEmptyField($truck, 'TruckMutation', 'change_driver_name');
        $change_capacity = $this->Common->filterEmptyField($truck, 'TruckMutation', 'change_capacity');

        switch ($type) {
            case 'truck':
                $fieldName = 'TruckMutationOldCustomer';
                break;
            
            default:
                $fieldName = 'TruckMutationCustomer';
                break;
        }
?>
<ul class="list-mutation">
    <?php
            if( !empty($nopol) && !empty($change_nopol) ) {
                echo $this->Html->tag('li', sprintf('%s: %s', $this->Html->tag('span', __('No. Pol')), $nopol));
            }
            if( !empty($branch_name) && !empty($change_branch_name) ) {
                echo $this->Html->tag('li', sprintf('%s: %s', $this->Html->tag('span', __('Cabang')), $branch_name));
            }
            if( !empty($category) && !empty($change_category) ) {
                echo $this->Html->tag('li', sprintf('%s: %s', $this->Html->tag('span', __('Jenis Truk')), $category));
            }
            if( !empty($facility) && !empty($change_facility) ) {
                echo $this->Html->tag('li', sprintf('%s: %s', $this->Html->tag('span', __('Fasilitas Truk')), $facility));
            }
            if( !empty($driver_name) && !empty($change_driver_name) ) {
                echo $this->Html->tag('li', sprintf('%s: %s', $this->Html->tag('span', __('Supir')), $driver_name));
            }
            if( !empty($capacity) && !empty($change_capacity) ) {
                echo $this->Html->tag('li', sprintf('%s: %s', $this->Html->tag('span', __('Kapasitas')), $capacity));
            }

            if( !empty($truck[$fieldName]) ) {
                $ulContent = '';
                echo $this->Html->tag('li', __('Alokasi Truk'));

                foreach ($truck[$fieldName] as $key => $value) {
                    $customer_name = !empty($value[$fieldName]['customer_name'])?$value[$fieldName]['customer_name']:false;
                    $ulContent .= $this->Html->tag('li', $customer_name);
                }

                echo $this->Html->tag('ul', $ulContent, array(
                    'class' => 'list-customer',
                ));
            }
    ?>
</ul>