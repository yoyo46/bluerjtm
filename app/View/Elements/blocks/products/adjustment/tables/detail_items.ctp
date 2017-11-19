<?php 
        $id = !empty($id)?$id:false;
        $price = $this->Common->filterEmptyField($value, $modelName, 'price');
        $qty = $this->Common->filterEmptyField($value, $modelName, 'qty');
        $code = $this->Common->filterEmptyField($value, $modelName, 'code');
        $name = $this->Common->filterEmptyField($value, $modelName, 'name');
        $unit = $this->Common->filterEmptyField($value, $modelName, 'unit');
        $total_qty = Common::hashEmptyField($value, $modelName.'.total_qty', 0);
        $qty_difference = $qty - $total_qty;
        $is_serial_number = $this->Common->filterEmptyField($value, $modelName, 'is_serial_number');
        $out = '';
        $in = '';

        if( $qty_difference > 0 ) {
            $disabled = false;
            $out = 'display: none;';
        } else {
            $disabled = true;
            $in = 'display: none;';
        }

        $data = $this->request->data;
        $customTotal = !empty($total)?$total:0;
        $targetQty = __('input-qty-%s', $id);
?>
<tr class="pick-document" rel="<?php echo $id; ?>">
    <?php
            echo $this->Html->tag('td', $code.$this->Form->hidden('ProductAdjustmentDetail.product_id.'.$id, array(
                'value' => $id,
            )));
            echo $this->Html->tag('td', $name);
            echo $this->Html->tag('td', $unit, array(
                'class' => 'text-center',
            ));

            if( !empty($view) ) {
                $note = $this->Common->filterEmptyField($value, $modelName, 'note', '-');
                $qty = Common::hashEmptyField($value, 'ProductAdjustmentDetail.qty', 0);
                $qty_difference = Common::hashEmptyField($value, 'ProductAdjustmentDetail.qty_difference', 0);
                $price = Common::hashEmptyField($value, 'ProductAdjustmentDetail.price', 0, array(
                    'type' => 'currency',
                ));

                echo $this->Html->tag('td', $note);
                echo $this->Html->tag('td', $total_qty, array(
                    'class' => 'text-center',
                ));
                echo $this->Html->tag('td', $qty, array(
                    'class' => 'text-center price_custom',
                    'rel' => 'qty',
                ));
                echo $this->Html->tag('td', $qty_difference, array(
                    'class' => 'text-center',
                ));
                echo $this->Html->tag('td', $price, array(
                    'class' => 'text-right',
                ));
            } else {
                $note = $this->Common->filterEmptyField($value, $modelName, 'note');

                echo $this->Html->tag('td', $this->Common->buildInputForm('ProductAdjustmentDetail.note.'.$id, false, array(
                    'type' => 'text',
                    'frameClass' => false,
                    'attributes' => array(
                        'value' => $note,
                    ),
                )), array(
                    'class' => 'text-center',
                ));
                echo $this->Html->tag('td', $total_qty, array(
                    'class' => 'text-center price_custom',
                    'rel' => 'qty-stock',
                ));
                echo $this->Html->tag('td', $this->Common->buildInputForm('ProductAdjustmentDetail.qty.'.$id, false, array(
                    'type' => 'text',
                    'fieldError' => 'ProductAdjustmentDetail.'.$key.'.qty',
                    'frameClass' => false,
                    'class' => 'input_number text-center price_custom',
                    'attributes' => array(
                        'value' => $qty,
                        'rel' => 'qty',
                        'data-adjutment' => '.qty-difference',
                    ),
                )));

                echo $this->Html->tag('td', $qty_difference, array(
                    'class' => __('text-center qty-difference %s', $targetQty),
                    'data-target' => '.price_custom[rel=qty-stock]',
                    'data-minus' => '.price_custom[rel=qty]',
                    'data-disabled' => '.price_custom[rel=price]',
                    'data-display' => '[[\'.serial-number-in\', \'plus\'],[\'.serial-number-out\', \'minus\']]',
                ));

                echo $this->Html->tag('td', $this->Common->buildInputForm('ProductAdjustmentDetail.price.'.$id, false, array(
                    'type' => 'text',
                    'fieldError' => 'ProductAdjustmentDetail.'.$key.'.price',
                    'frameClass' => false,
                    'class' => 'input_price text-right price_custom',
                    'attributes' => array(
                        'value' => $price,
                        'rel' => 'price',
                        'disabled' => $disabled,
                    ),
                )));
            }

            if( !empty($is_serial_number) ) {
                $serialNumbers = $this->Common->filterEmptyField($data, 'ProductAdjustmentDetailSerialNumber');

                if( !empty($view) ) {
                    $serial_numbers = $this->Common->filterEmptyField($serialNumbers, 'serial_numbers', $id, array());

                    if( !empty($serial_numbers) ) {
                        $serial_number_text = implode(', ', $serial_numbers);
                    } else {
                        $serial_number_text = '-';
                    }

                    echo $this->Html->tag('td', $serial_number_text, array(
                        'class' => 'text-center',
                    ));
                } else {
                    $lblSerialNumber = $this->element('blocks/products/receipts/tables/serial_number_counter', array(
                        'id' => $id,
                        'number' => $serial_number,
                    ));

                    if( !empty($view) && empty($serial_number) ) {
                        echo $this->Html->tag('td', __('Automatic'), array(
                            'class' => 'text-center',
                        ));
                    } else {
                        $serial_numbers = $this->Common->filterEmptyField($serialNumbers, 'serial_numbers', $id, array());

                        $serial_number_text = $this->Common->_callInputForm(__('ProductAdjustmentDetailSerialNumber.serial_numbers.%s', $id), array(
                            'div' => false,
                            'error' => false,
                            'class' => 'chosen-select form-control full',
                            'frameClass' => false,
                            'multiple' => true,
                            'options' => $serial_numbers,
                        )).$this->Html->link($this->Common->icon('plus-square'), array(
                            'controller'=> 'products', 
                            'action' => 'stocks',
                            $id,
                            'admin' => false,
                        ), array(
                            'escape' => false,
                            'allow' => true,
                            'class' => 'ajaxCustomModal browse-docs',
                            'title' => __('Stok Barang'),
                            'data-action' => 'browse-form',
                        ));

                        echo $this->Html->tag('td', $this->Html->tag('div', 
                            $this->Html->link($lblSerialNumber, array(
                                'controller'=> 'products', 
                                'action' => 'adjust_serial_numbers',
                                $id,
                                'view' => !empty($view)?$view:false,
                                'admin' => false,
                                'bypass' => true,
                            ), array(
                                'escape' => false,
                                'allow' => true,
                                'class' => __('ajaxCustomModal browse-docs serial-number-fill-%s', $id),
                                'title' => __('Serial Number'),
                                'data-action' => 'browse-form',
                                'data-form' => '.adjust-form',
                                'data-picker' => __('.%s', $targetQty),
                            )), array(
                                'class' => 'serial-number-in display-adjust',
                                'style' => $in,
                            )).
                            $this->Html->tag('div', $serial_number_text, array(
                                'style' => $out,
                                'class' => 'serial-number-out display-adjust pick-product-code',
                            )).
                            $this->Form->error('ProductAdjustmentDetail.'.$key.'.serial_number').
                            $this->Form->error('ProductAdjustmentDetail.'.$key.'.sn_match').
                            $this->Form->error('ProductAdjustmentDetail.'.$key.'.sn_empty'), array(
                            'class' => 'text-center',
                        ));
                    }
                }
            } else {
                echo $this->Html->tag('td', __('Automatic'), array(
                    'class' => 'text-center',
                ));
            }

            if( empty($view) ) {
                echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                    'class' => 'delete-document btn btn-danger btn-xs',
                    'escape' => false,
                )), array(
                    'class' => 'actions text-center',
                ));
            }
    ?>
</tr>