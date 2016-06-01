<?php 
        $product_id = $this->Common->filterEmptyField($value, $modelName, 'product_id');
        $qty = $this->Common->filterEmptyField($value, $modelName, 'qty');
?>
<tr class="pick-document" rel="<?php echo $product_id; ?>">
    <?php
            echo $this->Html->tag('td', $code.$this->Form->hidden('ProductReceiptDetail.product_id.'.$product_id, array(
                'value' => $product_id,
            )));
            echo $this->Html->tag('td', $name);
            echo $this->Html->tag('td', $this->Common->buildInputForm('ProductReceiptDetail.qty.'.$product_id, false, array(
                'type' => 'text',
                'fieldError' => 'ProductReceiptDetail.'.$idx.'.qty',
                'frameClass' => false,
                'class' => 'qty input_number text-right',
                'attributes' => array(
                    'value' => $qty,
                ),
            )));
            echo $this->Html->tag('td', $unit);
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderDetail.price.'.$product_id, false, array(
                'type' => 'text',
                'fieldError' => 'PurchaseOrderDetail.'.$idx.'.price',
                'frameClass' => false,
                'class' => 'text-right price',
                'disabled' => $disabled,
                'attributes' => array(
                    'value' => $this->Common->getFormatPrice($price, '', 2),
                    'data-type' => 'input_price_coma',
                ),
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderDetail.disc.'.$product_id, false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'disc text-right',
                'disabled' => $disabled,
                'attributes' => array(
                    'value' => $this->Common->getFormatPrice($disc, '', 2),
                    'data-type' => 'input_price_coma',
                ),
            )));
            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('plus-square'), array(
                'controller'=> 'products', 
                'action' => 'receipt_serial_numbers',
                $product_id,
                'admin' => false,
            ), array(
                'escape' => false,
                'class' => 'ajaxModal browse-docs',
                'title' => __('Serial Number'),
                'data-action' => 'browse-form',
            )));
            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                'class' => 'delete-document btn btn-danger btn-xs',
                'escape' => false,
            )));
    ?>
</tr>