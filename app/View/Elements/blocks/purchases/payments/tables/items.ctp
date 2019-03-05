<?php 
        $value = !empty($value)?$value:false;
        $data = $this->request->data;
        $document_type = $this->Common->filterEmptyField($data, 'PurchaseOrderPayment', 'document_type', 'po');

        switch ($document_type) {
            case 'spk':
                $modelNameDocument = 'Spk';
                break;
            
            default:
                $modelNameDocument = 'PurchaseOrder';
                break;
        }

        $id = $this->Common->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'id');
        $document_id = $this->Common->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'document_id');
        $price = $this->Common->filterEmptyField($value, 'PurchaseOrderPaymentDetail', 'price');

        $note = $this->Common->filterEmptyField($value, $modelNameDocument, 'note', '-');
        
        $grandtotal = Common::hashEmptyField($value, $modelNameDocument.'.grandtotal');
        $grandtotal = Common::hashEmptyField($value, $modelNameDocument.'.total_'.$document_type, $grandtotal);

        $total_paid = $this->Common->filterEmptyField($value, $modelNameDocument, 'total_paid');
        $nodoc = $this->Common->filterEmptyField($value, $modelNameDocument, 'nodoc');
        $transaction_date = $this->Common->filterEmptyField($value, $modelNameDocument, 'transaction_date');

        $transaction_date = $this->Common->formatDate($transaction_date, 'd M Y');
        $grandtotal = $this->Common->getFormatPrice($grandtotal, 0, 2);
        // $totalRemainFormat = $this->Common->getFormatPrice($total_remain, 0, 2);
        $total_paid = $this->Common->getFormatPrice($total_paid, 0, 2);
        $priceFormat = $this->Common->getFormatPrice($price, 0, 2);

        $hiddenContent = $this->Form->hidden('PurchaseOrderPaymentDetail.id.', array(
            'value' => $id,
        )).$this->Form->hidden('PurchaseOrderPaymentDetail.document_id.', array(
            'value' => $document_id,
        ));
?>
<tr class="pick-document item" data-type="single-total" rel="<?php echo $document_id; ?>">
    <?php
            echo $this->Html->tag('td', $nodoc.$hiddenContent);
            echo $this->Html->tag('td', $transaction_date, array(
                'class' => 'text-center',
            ));
            echo $this->Html->tag('td', $note);
            echo $this->Html->tag('td', $grandtotal, array(
                'class' => 'text-right',
            ));
            echo $this->Html->tag('td', $total_paid, array(
                'class' => 'text-right',
            ));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderPaymentDetail.price.', false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'text-right price_custom',
                'fieldError' => 'PurchaseOrderPaymentDetail.'.$idx.'.price',
                'attributes' => array(
                    'data-type' => 'input_price_coma',
                    'value' => $priceFormat,
                    'rel' => 'price',
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