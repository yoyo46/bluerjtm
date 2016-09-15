<?php
        $id = !empty($id)?$id:false;
        $modelName = !empty($modelName)?$modelName:false;
        $qty = !empty($qty)?$qty:false;
        $serial_numbers = !empty($serial_numbers)?$serial_numbers:false;
        $spk_product_id = !empty($spk_product_id)?$spk_product_id:false;

        $code = $this->Common->filterEmptyField($value, $modelName, 'code');
        $name = $this->Common->filterEmptyField($value, $modelName, 'name');
        $unit = $this->Common->filterEmptyField($value, $modelName, 'name');
        $is_serial_number = $this->Common->filterEmptyField($value, $modelName, 'is_serial_number');

?>
<tr class="pick-document" rel="<?php echo $id; ?>">
    <?php
            echo $this->Html->tag('td', $code.
                $this->Form->hidden('ProductExpenditureDetail.product_id.', array(
                    'value' => $id,
                )).
                $this->Form->hidden('ProductExpenditureDetail.spk_product_id.', array(
                    'value' => $spk_product_id,
                ))
            );
            echo $this->Html->tag('td', $name);
            echo $this->Html->tag('td', $unit, array(
                'class' => 'text-center',
            ));
            echo $this->Html->tag('td', $spk_qty, array(
                'class' => 'text-center price_custom',
                'rel' => 'qty-spk',
            ));
            echo $this->Html->tag('td', $out_qty, array(
                'class' => 'text-center price_custom',
                'rel' => 'qty-out',
            ));
            echo $this->Html->tag('td', $this->Common->buildInputForm('ProductExpenditureDetail.qty.', false, array(
                'type' => 'text',
                'fieldError' => array(
                    'ProductExpenditureDetail.'.$key.'.qty',
                    'ProductExpenditureDetail.'.$key.'.qty_over',
                    'ProductExpenditureDetail.'.$key.'.out_stock',
                ),
                'frameClass' => false,
                'class' => 'price_custom input_number serial-number-input text-center',
                'attributes' => array(
                    'value' => $qty,
                    'rel' => 'qty',
                ),
            )));

            if( !empty($is_serial_number) ) {
                if( !empty($view) ) {
                    $data = $this->request->data;
                    $serialNumbers = $this->Common->filterEmptyField($data, 'ProductExpenditureDetailSerialNumber');
                    $serial_numbers = $this->Common->filterEmptyField($serialNumbers, 'serial_numbers', $id);

                    if( !empty($serial_numbers) ) {
                        $serial_number_text = implode(', ', $serial_numbers);
                    } else {
                        $serial_number_text = '-';
                    }
                } else {
                    $serial_number_text = $this->Common->_callInputForm(__('ProductExpenditureDetailSerialNumber.serial_numbers.%s', $id), array(
                        'div' => false,
                        'class' => 'chosen-select form-control full',
                        'multiple' => true,
                        'data-url' => $this->Html->url(array(
                            'controller' => 'products',
                            'action' => 'scan',
                            'admin' => false,
                        )),
                        'options' => $serial_numbers,
                        'fieldError' => array(
                            'ProductExpenditureDetail.'.$key.'.sn_match',
                            'ProductExpenditureDetail.'.$key.'.sn_empty'
                        ),
                    ));
                }
            } else {
                $serial_number_text = '-';
            }

            echo $this->Html->tag('td', $serial_number_text, array(
                'class' => 'text-center',
            ));

            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                'class' => 'delete-document btn btn-danger btn-xs',
                'escape' => false,
            )), array(
                'class' => 'text-center',
            ));
    ?>
</tr>