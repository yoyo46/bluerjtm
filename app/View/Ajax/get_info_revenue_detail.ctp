<?php
        echo $this->Form->hidden('Revenue.payment_type', array(
            'type' => 'text',
            'label' => false,
            'id' => 'main_jenis_unit',
            'required' => false,
            'value' =>  !empty($mainTarif['jenis_unit']) ? $mainTarif['jenis_unit'] : 0
        ));

        if(!empty($detail['RevenueDetail']['price_unit'])){
            $price = $detail['RevenueDetail']['price_unit'];
        }else{
            $link = $this->Html->link(__('disini'), 
                array('controller' => 'settings', 'action' => 'tarif_angkutan_add'),
                array('target' => 'blank')
            );
            $price = sprintf(__('Tarif tidak ditemukan, silahkan buat tarif angkutan %s'), $link);
        }

        $total = 0;
        $flagTruck = false;

        if( !empty($detail['RevenueDetail']['price_unit']['jenis_unit']) && $detail['RevenueDetail']['price_unit']['jenis_unit'] == 'per_truck' && !$is_charge ) {
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
                'value' =>  !empty($price['jenis_unit']) ? $price['jenis_unit'] : 0
            ));
    ?>
</div>
<div id="price-data">
    <?php 
            if( !$flagTruck && empty($is_charge) ) {
                if(is_array($price)){
                    $price = $price['tarif'];
                    echo $this->Number->format($price, Configure::read('__Site.config_currency_code'), array('places' => 0));
                }else{
                    echo $price;
                }
            } else if( is_string($price) ) {
                echo $price;
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
            echo $this->Form->checkbox('RevenueDetail.is_charge_temp.', array(
                'label' => false,
                'class' => 'additional-charge',
                'required' => false,
                'value' => 1,
                'checked' => !empty($is_charge)?true:false,
                'hiddenField' => false,
                'disabled' => ( !empty($flagTruck) || !empty($is_charge) )?false:true,
            ));
            echo $this->Form->hidden('RevenueDetail.is_charge.', array(
                'value' => !empty($is_charge)?1:0,
                'class' => 'additional-charge-hidden',
            ));
    ?>
</div>
<div id="total-price-revenue">
    <?php   
            $formatValuePrice = '';
            $value_price = 0;

            if( !$flagTruck ) {
                if(is_array($price)){
                    if(!empty($price) && !empty($qty) && $price['jenis_unit'] == 'per_unit'){
                        $value_price = $price['tarif'] * $qty;
                    }else if(!empty($price) && $price['jenis_unit'] == 'per_truck'){
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