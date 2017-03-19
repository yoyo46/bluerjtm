<?php
        $id = !empty($id)?$id:false;
        $modelName = !empty($modelName)?$modelName:false;
        $qty = !empty($qty)?$qty:false;
        // $serial_numbers = !empty($serial_numbers)?$serial_numbers:false;
        $spk_product_id = !empty($spk_product_id)?$spk_product_id:false;
        $data = $this->request->data;

        $code = $this->Common->filterEmptyField($value, $modelName, 'code');
        $name = $this->Common->filterEmptyField($value, $modelName, 'name');
        $unit = $this->Common->filterEmptyField($value, $modelName, 'name');
        $is_serial_number = $this->Common->filterEmptyField($value, $modelName, 'is_serial_number');
        $transaction_date = Common::hashEmptyField($data, 'ProductExpenditure.transaction_date');

        if( !empty($transaction_date) && $transaction_date != '-' ) {
            $error_stock = __('Jml qty melebihi stok barang per tgl %s', $transaction_date);
        } else {
            $error_stock = __('Jml qty melebihi stok barang');
        }

        $data = $this->request->data;
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
            
            if( empty($view) ) {
                echo $this->Html->tag('td', $out_qty, array(
                    'class' => 'text-center price_custom',
                    'rel' => 'qty-remain',
                ));
            }

            if( empty($view) ) {
                echo $this->Html->tag('td', $this->Common->buildInputForm('ProductExpenditureDetail.qty.', false, array(
                    'type' => 'text',
                    'fieldError' => array(
                        'ProductExpenditureDetail.'.$key.'.qty',
                        'ProductExpenditureDetail.'.$key.'.qty_over',
                         array(
                            'error' => 'ProductExpenditureDetail.'.$key.'.out_stock',
                            'text' => $error_stock,
                        ),
                    ),
                    'frameClass' => false,
                    'class' => 'price_custom input_number serial-number-input text-center',
                    'attributes' => array(
                        'value' => $qty,
                        'rel' => 'qty',
                    ),
                )));
            } else {
                echo $this->Html->tag('td', $qty, array(
                    'class' => 'text-center price_custom',
                    'rel' => 'qty',
                ));
            }

            // if( !empty($is_serial_number) ) {
                if( !empty($view) ) {
                    $serialNumbers = Common::hashEmptyField($value, 'ProductExpenditureDetailSerialNumber');
                    $serial_numbers = array();

                    if( !empty($serialNumbers) ) {
                        foreach ($serialNumbers as $key => $sn) {
                            $qty = Common::hashEmptyField($sn, 'ProductExpenditureDetailSerialNumber.qty');
                            $serial_number = Common::hashEmptyField($sn, 'ProductExpenditureDetailSerialNumber.serial_number');

                            if( $qty > 1 ) {
                                $serial_numbers[] = __('%s(%s)', $serial_number, $qty);
                            } else {
                                $serial_numbers[] = $serial_number;
                            }
                        }
                        
                        $serial_number_text = implode(', ', $serial_numbers);
                    } else {
                        $serial_number_text = '-';
                    }
                } else {
                    $serialNumbers = $this->Common->filterEmptyField($data, 'ProductExpenditureDetailSerialNumber');
                    $serial_numbers = $this->Common->filterEmptyField($serialNumbers, 'serial_numbers', $id, array());

                    $serial_number_text = $this->Common->_callInputForm(__('ProductExpenditureDetailSerialNumber.serial_numbers.%s', $id), array(
                        'div' => false,
                        'error' => false,
                        'class' => 'chosen-select form-control full',
                        'multiple' => true,
                        'options' => $serial_numbers,
                        // 'data-url' => $this->Html->url(array(
                        //     'controller' => 'products',
                        //     'action' => 'scan',
                        //     'admin' => false,
                        // )),
                        // 'fieldError' => array(
                        //     'ProductExpenditureDetail.'.$key.'.sn_match',
                        //     'ProductExpenditureDetail.'.$key.'.sn_empty'
                        // ),
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
                }
            // } else {
            //     $serial_number_text = '-';
            // }

            echo $this->Html->tag('td', $serial_number_text.$this->Form->error('ProductExpenditureDetail.'.$key.'.sn_match').$this->Form->error('ProductExpenditureDetail.'.$key.'.sn_empty'), array(
                'class' => 'text-center pick-product-code',
            ));

            if( empty($view) ) {
                echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                    'class' => 'delete-document btn btn-danger btn-xs',
                    'escape' => false,
                )), array(
                    'class' => 'text-center',
                ));
            }
    ?>
</tr>