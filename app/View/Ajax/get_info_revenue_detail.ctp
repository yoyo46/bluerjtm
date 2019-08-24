<?php
        $is_charge = !empty($is_charge)?$is_charge:false;
        $action_type = !empty($action_type)?$action_type:false;
        $tarif_angkutan_type = $this->Common->filterEmptyField($tarif, 'tarif_angkutan_type', false, 'angkut');
        $jenis_unit = $this->Common->filterEmptyField($tarif, 'jenis_unit', false, 'per_unit');
        $price = $this->Common->filterEmptyField($tarif, 'tarif', false, 0);
        $tarif_angkutan_id = $this->Common->filterEmptyField($tarif, 'tarif_angkutan_id', false, 0);
        $tarif_extra = Common::hashEmptyField($tarif, 'tarif_extra', 0);
        $tarif_extra_min_capacity = Common::hashEmptyField($tarif, 'tarif_extra_min_capacity', 0);
        $tarif_extra_per_unit = Common::hashEmptyField($tarif, 'tarif_extra_per_unit', 0);

        if( !empty($is_charge) ) {
            if( $jenis_unit == 'per_truck' ) {
                $totalPrice = $price;
            } else {
                $totalPrice = $price * $qty;
            }

            $totalPriceCustom = $this->Common->getFormatPrice($totalPrice);
            $checkedCharge = true;
        } else {
            $totalPrice = '';
            $totalPriceCustom = '';
            $checkedCharge = false;
        }

        echo $this->Form->hidden('RevenueDetail.tarif_angkutan_type.', array(
            'id' => 'tarif_angkutan_type',
            'required' => false,
            'value' => $tarif_angkutan_type,
        ));
        echo $this->Form->hidden('RevenueDetail.tarif_angkutan_id.', array(
            'id' => 'tarif_angkutan_id',
            'required' => false,
            'value' => $tarif_angkutan_id,
        ));
        echo $this->Form->hidden('RevenueDetail.tarif_extra.', array(
            'id' => 'tarif_extra',
            'required' => false,
            'value' => $tarif_extra,
        ));
        echo $this->Form->hidden('RevenueDetail.tarif_extra_min_capacity.', array(
            'id' => 'tarif_extra_min_capacity',
            'required' => false,
            'value' => $tarif_extra_min_capacity,
        ));
        echo $this->Form->hidden('RevenueDetail.tarif_extra_per_unit.', array(
            'id' => 'tarif_extra_per_unit',
            'required' => false,
            'value' => $tarif_extra_per_unit,
        ));

        if( empty($tarif) ){
            $link = $this->Html->link(__('disini'), array(
                'controller' => 'settings',
                'action' => 'tarif_angkutan_add'
            ), array(
                'target' => 'blank'
            ));
            $price_msg = sprintf(__('Tarif tidak ditemukan, silahkan buat tarif angkutan %s'), $link);
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
                'value' =>  $jenis_unit,
            ));
    ?>
</div>
<div id="price-data">
    <?php 
            $inputType = 'hidden';
            $inputClass = '';

            if( $action_type == 'manual' ) {
                $inputType = 'text';
                $inputClass = 'input_price text-right';
            } else if( !empty($price_msg) ) {
                echo $this->Html->tag('span', $price_msg);
            } else if( !empty($is_charge) ) {
                echo $this->Html->tag('span', $this->Common->getFormatPrice($price));
            }

            echo $this->Form->input('RevenueDetail.price_unit.', array(
                'type' => $inputType,
                'label' => false,
                'class' => 'form-control price-unit-revenue '.$inputClass,
                'required' => false,
                'value' => $price,
            ));
    ?>
</div>
<div id="total-price-revenue">
    <?php   
            // if( $action_type == 'manual' && $jenis_unit == 'per_truck' && !empty($is_charge) ) {
            //     echo $this->Form->input('RevenueDetail.total_price_unit.', array(
            //         'type' => 'text',
            //         'label' => false,
            //         'class' => 'form-control total-revenue-perunit input_price text-right',
            //         'required' => false,
            //         'value' => $totalPriceCustom,
            //     ));
            // } else {
                if( !empty($is_charge) ) {
                    echo $this->Html->tag('span', $totalPriceCustom, array(
                        'class' => 'total-revenue-perunit-text'
                    ));
                }

                echo $this->Form->hidden('RevenueDetail.total_price_unit.', array(
                    'class' => 'total-revenue-perunit',
                    'value' => $totalPrice,
                ));
            // }
    ?>
</div>
<div id="additional-charge-data">
    <?php
            echo $this->Form->checkbox('RevenueDetail.is_charge_temp.', array(
                'label' => false,
                'class' => 'additional-charge',
                'required' => false,
                'hiddenField' => false,
                'checked' => $checkedCharge,
                'value' => 1,
            ));
            echo $this->Form->hidden('RevenueDetail.is_charge.', array(
                'value' => $checkedCharge,
                'class' => 'additional-charge-hidden',
            ));
    ?>
</div>
<td class="action text-center" id="handle-row">
    <?php
            echo $this->Html->link($this->Common->icon('copy'), '#', array(
                'class' => 'duplicate-row btn btn-warning btn-xs',
                'escape' => false,
                'title' => 'Duplicate'
            ));
            echo $this->Html->link($this->Common->icon('times'), '#', array(
                'class' => 'delete-custom-field btn btn-danger btn-xs',
                'escape' => false,
                'action_type' => 'revenue_detail',
                'title' => __('Hapus Muatan')
            ));
    ?>
</td>