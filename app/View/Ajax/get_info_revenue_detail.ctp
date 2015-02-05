<?php
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
        if(is_array($price)){
            echo $this->Number->format($price['tarif'], Configure::read('__Site.config_currency_code'), array('places' => 0));
        }else{
            echo $price;
        }
        echo $this->Form->hidden('RevenueDetail.price_unit.', array(
            'type' => 'text',
            'label' => false,
            'class' => 'form-control price-unit-revenue input_number',
            'required' => false,
            'value' => (is_array($price)) ? $price['tarif'] : 0
        ));
    ?>
</div>
<div id="total-price-revenue">
    <?php 
        $value_price = 0;
        if(is_array($price)){
            if(!empty($price) && !empty($qty) && $price['jenis_unit'] == 'per_unit'){
                $value_price = $price['tarif'] * $qty;
            }else if(!empty($price) && !empty($qty) && $price['jenis_unit'] == 'per_truck'){
                $value_price = $price['tarif'];
            }
        }

        $total += $value_price;

        echo $this->Html->tag('span', $this->Number->currency($value_price, Configure::read('__Site.config_currency_code'), array('places' => 0)), array(
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