<?php
        $tarif_angkutan_type = !empty($tarif['tarif_angkutan_type'])?$tarif['tarif_angkutan_type']:'angkut';
        $jenis_unit_angkutan =!empty($tarif['jenis_unit']) ? $tarif['jenis_unit'] : 'per_unit';
        $jenis_unit = !empty($jenis_unit)?$jenis_unit:$jenis_unit_angkutan;
        $from_ttuj = !empty($from_ttuj)?true:false;

        echo $this->Form->hidden('RevenueDetail.tarif_angkutan_type.', array(
            'id' => 'tarif_angkutan_type',
            'required' => false,
            'value' => $tarif_angkutan_type,
        ));
        echo $this->Form->hidden('RevenueDetail.tarif_angkutan_id.', array(
            'id' => 'tarif_angkutan_id',
            'required' => false,
            'value' => !empty($tarif['tarif_angkutan_id'])?$tarif['tarif_angkutan_id']:0,
        ));

        if(!empty($tarif)){
            $price = $tarif;
        }else{
            $link = $this->Html->link(__('disini'), array(
                'controller' => 'settings',
                'action' => 'tarif_angkutan_add'
            ), array(
                'target' => 'blank'
            ));
            $price = sprintf(__('Tarif tidak ditemukan, silahkan buat tarif angkutan %s'), $link);
        }

        if( !empty($truck['Truck']['capacity']) ) {
            echo $this->Form->hidden('RevenueDetail.tarif_angkutan_type.', array(
                'id' => 'truck_capacity',
                'required' => false,
                'value' => $truck['Truck']['capacity'],
            ));
        }

        $total = 0;
        $flagTruck = false;

        if( $jenis_unit == 'per_truck' && !$is_charge ) {
            $flagTruck = true;
        }
?>
<div id="qty-tipe-motor-data">
    <?php
            echo $this->Form->input('RevenueDetail.qty_unit.', array(
                'type' => 'text',
                'label' => false,
                'class' => 'form-control revenue-qty input_number',
                'required' => false,
            ));
            echo $this->Form->hidden('RevenueDetail.payment_type.', array(
                'type' => 'text',
                'label' => false,
                'class' => 'jenis_unit',
                'required' => false,
                'value' =>  $jenis_unit_angkutan,
            ));
    ?>
</div>
<div id="price-data">
    <?php 
            if( $jenis_unit_angkutan != 'per_truck' ) {
                if( empty($is_charge) || $tarif_angkutan_type != 'angkut' ) {
                    if(is_array($price)){
                        $price = $price['tarif'];
                        echo $this->Html->tag('span', $this->Number->format($price, Configure::read('__Site.config_currency_code'), array('places' => 0)));
                    }else{
                        echo $this->Html->tag('span', $price);
                    }
                } else if( is_string($price) ) {
                    echo $this->Html->tag('span', $price);
                }
            }

            echo $this->Form->hidden('RevenueDetail.price_unit.', array(
                'type' => 'text',
                'label' => false,
                'class' => 'form-control price-unit-revenue input_number',
                'required' => false,
                'value' => (is_numeric($price)) ? $price : 0
            ));
    ?>
</div>
<div id="additional-charge-data">
    <?php
            if( empty($ttuj_id) ) {
                $data_type = 'revenue-manual';
                $from_ttuj = false;
            } else {
                $data_type = false;
            }

            if( !empty($is_charge) && empty($from_ttuj) ) {
                $checkedCharge = true;
            } else if( !empty($from_ttuj) ) {
                $checkedCharge = false;
            } else {
                $checkedCharge = false;
            }

            if( ( !empty($flagTruck) || !empty($is_charge) || $tarif_angkutan_type != 'angkut' ) && empty($from_ttuj) ) {
                $disabledCharge = false;
            } else {
                $disabledCharge = true;
            }

            echo $this->Form->checkbox('RevenueDetail.is_charge_temp.', array(
                'label' => false,
                'class' => 'additional-charge',
                'required' => false,
                'value' => 1,
                'checked' => $checkedCharge,
                'hiddenField' => false,
                'disabled' => $disabledCharge,
                'data-type' => $data_type,
            ));
            echo $this->Form->hidden('RevenueDetail.is_charge.', array(
                'value' => $checkedCharge,
                'class' => 'additional-charge-hidden',
            ));
            echo $this->Form->hidden('RevenueDetail.from_ttuj.', array(
                'type' => 'text',
                'label' => false,
                'class' => 'form-control from-ttuj',
                'required' => false,
                'value' => $from_ttuj,
            ));
    ?>
</div>
<div id="total-price-revenue">
    <?php   
            $formatValuePrice = '';
            $value_price = 0;

            if( ( $jenis_unit_angkutan == 'per_truck' && ($tarif_angkutan_type == 'angkut' || ( $tarif_angkutan_type != 'angkut' || !empty($is_charge) ) ) ) || $jenis_unit == 'per_unit' ) {
                if( $tarif_angkutan_type != 'angkut' && is_numeric($price) ) {
                    if(!empty($price) && !empty($qty) && $jenis_unit_angkutan == 'per_unit'){
                        $value_price = $price * $qty;
                    }else if(!empty($price) && $jenis_unit_angkutan == 'per_truck'){
                        $value_price = $price;
                    }
                } else if(is_array($price)){
                    if(!empty($price) && !empty($qty) && $jenis_unit_angkutan == 'per_unit'){
                        $value_price = $price['tarif'] * $qty;
                    }else if(!empty($price) && $jenis_unit_angkutan == 'per_truck'){
                        $value_price = $price['tarif'];
                    }
                }

                $total += $value_price;
                $formatValuePrice = $this->Number->currency($value_price, Configure::read('__Site.config_currency_code'), array('places' => 0));
            }

            echo $this->Html->tag('span', $formatValuePrice, array(
                'class' => 'total-revenue-perunit'
            ));

            echo $this->Form->hidden('RevenueDetail.total_price_unit.', array(
                'class' => 'total-price-perunit',
                'required' => false,
                'value' => $value_price
            ));
    ?>
</div>
<div id="handle-row">
    <?php
            if( !empty($price['tarif']) && is_numeric($price['tarif'])){
                echo $this->Html->link('<i class="fa fa-copy"></i>', 'javascript:', array(
                    'class' => 'duplicate-row btn btn-warning btn-xs',
                    'escape' => false,
                    'title' => 'Duplicate'
                ));
            }
    ?>
</div>
<div id="additional-total-revenue">
    <?php
            if( !empty($is_charge) && !is_string($price) ){
                echo $formatValuePrice;
            }
    ?>
</div>