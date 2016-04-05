<?php 
        $value = !empty($value)?$value:false;
        $id = $this->Common->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'id');
        $purchase_order_id = $this->Common->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'purchase_order_id');
        $price = $this->Common->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'price');

        $note = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'note');
        $total_remain = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'total_remain');
        $nodoc = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'nodoc');
        $transaction_date = $this->Common->filterEmptyField($value, 'PurchaseOrder', 'transaction_date');

        $customTotal = !empty($total)?$total:0;
        $transaction_date = $this->Common->formatDate($transaction_date, 'd/m/Y');
        $totalRemainFormat = $this->Common->getFormatPrice($total_remain, 0, 2);
        $priceFormat = $this->Common->getFormatPrice($price, 0, 2);

        $hiddenContent = $this->Form->hidden('PurchaseOrderPaymentDetail.id.', array(
            'value' => $id,
        )).$this->Form->hidden('PurchaseOrderPaymentDetail.purchase_order_id.', array(
            'value' => $purchase_order_id,
        ));
?>
<tr class="pick-document item" data-type="single-total" rel="<?php echo $purchase_order_id; ?>">
    <?php
            echo $this->Html->tag('td', $nodoc.$hiddenContent);
            echo $this->Html->tag('td', $transaction_date, array(
                'class' => 'text-center',
            ));
            echo $this->Html->tag('td', $note);
            echo $this->Html->tag('td', $totalRemainFormat, array(
                'class' => 'text-right',
            ));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderPaymentDetail.price.', false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'text-right price',
                'fieldError' => 'PurchaseOrderPaymentDetail.'.$idx.'.price',
                'attributes' => array(
                    'data-type' => 'input_price_coma',
                    'value' => $priceFormat,
                ),
            )));
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