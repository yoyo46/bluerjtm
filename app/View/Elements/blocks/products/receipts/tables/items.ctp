<?php
        $id = !empty($id)?$id:false;
        $modelName = !empty($modelName)?$modelName:false;
        $qty = !empty($qty)?$qty:false;
        $serial_number = !empty($serial_number)?$serial_number:false;

        $code = $this->Common->filterEmptyField($value, $modelName, 'code');
        $name = $this->Common->filterEmptyField($value, $modelName, 'name');
        $is_serial_number = $this->Common->filterEmptyField($value, $modelName, 'is_serial_number');
        $unit = $this->Common->filterEmptyField($value, $modelName, 'name');

        $targetQty = sprintf('inpu-qty-%s', $id);
        $lblSerialNumber = __('Masukan No. Seri %s', $this->Common->icon('plus-square', false, 'i', 'ml5'));
?>
<tr class="pick-document" rel="<?php echo $id; ?>">
    <?php
            echo $this->Html->tag('td', $code.$this->Form->hidden('ProductReceiptDetail.product_id.', array(
                'value' => $id,
            )));
            echo $this->Html->tag('td', $name);
            echo $this->Html->tag('td', $this->Common->buildInputForm('ProductReceiptDetail.qty.', false, array(
                'type' => 'text',
                'fieldError' => 'ProductReceiptDetail.'.$key.'.qty',
                'frameClass' => false,
                'class' => sprintf('qty input_number serial-number-input text-center %s', $targetQty),
                'attributes' => array(
                    'value' => $qty,
                    'data-target' => sprintf('.serial-number-fill-%s', $id),
                    'data-default' => $lblSerialNumber,
                ),
            )));
            echo $this->Html->tag('td', $unit, array(
                'class' => 'text-center',
            ));

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
                    'class' => sprintf('ajaxCustomModal browse-docs serial-number-fill-%s', $id),
                    'title' => __('Serial Number'),
                    'data-action' => 'browse-form',
                    'data-form' => '.receipt-form',
                    'data-picker' => sprintf('.%s', $targetQty),
                )).$this->Form->error('ProductReceiptDetail.'.$key.'.serial_number'), array(
                    'class' => 'text-center',
                ));
            } else {
                echo $this->Html->tag('td', __('Automatic'), array(
                    'class' => 'text-center',
                ));
            }

            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                'class' => 'delete-document btn btn-danger btn-xs',
                'escape' => false,
            )), array(
                'class' => 'text-center',
            ));
    ?>
</tr>