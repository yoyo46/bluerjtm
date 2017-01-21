<?php
        $id = !empty($id)?$id:false;
        $modelName = !empty($modelName)?$modelName:false;
        $qty = !empty($qty)?$qty:false;
        $serial_number = !empty($serial_number)?$serial_number:false;
        $document_detail_id = !empty($document_detail_id)?$document_detail_id:false;

        $code = $this->Common->filterEmptyField($value, $modelName, 'code');
        $name = $this->Common->filterEmptyField($value, $modelName, 'name');
        $is_serial_number = $this->Common->filterEmptyField($value, $modelName, 'is_serial_number');
        $unit = $this->Common->filterEmptyField($value, $modelName, 'unit');

        $targetQty = __('input-qty-%s', $id);
        $lblSerialNumber = __('Masukan No. Seri %s', $this->Common->icon('plus-square', false, 'i', 'ml5'));
?>
<tr class="pick-document" rel="<?php echo $id; ?>">
    <?php
            echo $this->Html->tag('td', $code.
                $this->Form->hidden('ProductReceiptDetail.product_id.', array(
                    'value' => $id,
                )).
                $this->Form->hidden('ProductReceiptDetail.document_detail_id.', array(
                    'value' => $document_detail_id,
                ))
            );
            echo $this->Html->tag('td', $name);
            echo $this->Html->tag('td', $unit, array(
                'class' => 'text-center',
            ));
            echo $this->Html->tag('td', $doc_qty, array(
                'class' => 'text-center price_custom',
                'rel' => 'qty-dic',
            ));

            if( empty($view) ) {
                echo $this->Html->tag('td', $in_qty, array(
                    'class' => 'text-center price_custom',
                    'rel' => 'qty-in',
                ));
            }

            if( empty($view) ) {
                echo $this->Html->tag('td', $this->Common->buildInputForm('ProductReceiptDetail.qty.', false, array(
                    'type' => 'text',
                    'fieldError' => array(
                        'ProductReceiptDetail.'.$key.'.qty',
                        'ProductReceiptDetail.'.$key.'.over_receipt'
                    ),
                    'frameClass' => false,
                    'class' => sprintf('price_custom input_number serial-number-input text-center %s', $targetQty),
                    'attributes' => array(
                        'value' => $qty,
                        'data-target' => sprintf('.serial-number-fill-%s', $id),
                        'data-default' => $lblSerialNumber,
                        'rel' => 'qty',
                    ),
                )));
            } else {
                echo $this->Html->tag('td', $qty, array(
                    'class' => 'text-center price_custom',
                    'rel' => 'qty',
                ));
            }

            if( !empty($is_serial_number) ) {
                if( !empty($serial_number) ) {
                    $lblSerialNumber = $this->element('blocks/products/receipts/tables/serial_number_counter', array(
                        'id' => $id,
                        'number' => $serial_number,
                    ));
                }

                echo $this->Html->tag('td', $this->Html->link($lblSerialNumber, array(
                    'controller'=> 'products', 
                    'action' => 'receipt_serial_numbers',
                    $id,
                    'admin' => false,
                    'bypass' => true,
                ), array(
                    'escape' => false,
                    'class' => __('ajaxCustomModal browse-docs serial-number-fill-%s', $id),
                    'title' => __('Serial Number'),
                    'data-action' => 'browse-form',
                    'data-form' => '.receipt-form',
                    'data-picker' => __('.%s', $targetQty),
                )).$this->Form->error('ProductReceiptDetail.'.$key.'.serial_number'), array(
                    'class' => 'text-center',
                ));
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
                    'class' => 'text-center',
                ));
            }
    ?>
</tr>