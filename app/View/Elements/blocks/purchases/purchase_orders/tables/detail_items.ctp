<?php 
        $product_id = $this->Common->filterEmptyField($value, $modelName, 'product_id');
        $supplier_quotation_id = $this->Common->filterEmptyField($value, $modelName, 'supplier_quotation_id');
        $price = $this->Common->filterEmptyField($value, $modelName, 'price');
        $disc = $this->Common->filterEmptyField($value, $modelName, 'disc');
        $ppn = $this->Common->filterEmptyField($value, $modelName, 'ppn');
        $qty = $this->Common->filterEmptyField($value, $modelName, 'qty');

        $code = $this->Common->filterEmptyField($value, 'Product', 'code');
        $name = $this->Common->filterEmptyField($value, 'Product', 'name');

        $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');

        $customTotal = !empty($total)?$total:0;

        if( !empty($supplier_quotation_id) ) {
            $disabled = true;
        } else {
            $disabled = false;
        }
?>
<tr class="pick-document" rel="<?php echo $product_id; ?>">
    <?php
            echo $this->Html->tag('td', $code.$this->Form->hidden('PurchaseOrderDetail.product_id.'.$product_id, array(
                'value' => $product_id,
            )).$this->Form->hidden('PurchaseOrderDetail.supplier_quotation_detail_id.'.$product_id, array(
                'value' => $sq_detail_id,
            )));
            echo $this->Html->tag('td', $name);
            echo $this->Html->tag('td', $unit);
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderDetail.qty.'.$product_id, false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'qty input_number text-right',
                'attributes' => array(
                    'value' => $qty,
                ),
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderDetail.price.'.$product_id, false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'text-right price',
                'disabled' => $disabled,
                'attributes' => array(
                    'value' => $price,
                    'data-type' => 'input_price_coma',
                ),
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderDetail.disc.'.$product_id, false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'disc text-right',
                'disabled' => $disabled,
                'attributes' => array(
                    'value' => $disc,
                    'data-type' => 'input_price_coma',
                ),
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderDetail.ppn.'.$product_id, false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'ppn text-right',
                'disabled' => $disabled,
                'attributes' => array(
                    'value' => $ppn,
                    'data-type' => 'input_price_coma',
                ),
            )));
            echo $this->Html->tag('td', $customTotal, array(
                'class' => 'total text-right',
            ));
            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                'class' => 'delete-document btn btn-danger btn-xs',
                'escape' => false,
            )));
    ?>
</tr>