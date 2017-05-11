<?php
        $id = !empty($id)?$id:false;
        $modelName = !empty($modelName)?$modelName:false;
        $qty = !empty($qty)?$qty:false;
        $document_detail_id = !empty($document_detail_id)?$document_detail_id:false;

        $code = $this->Common->filterEmptyField($value, $modelName, 'code');
        $name = $this->Common->filterEmptyField($value, $modelName, 'name');
        $unit = $this->Common->filterEmptyField($value, $modelName, 'unit');

        $targetQty = __('input-qty-%s', $id);
?>
<tr class="pick-document" rel="<?php echo $id; ?>">
    <?php
            echo $this->Html->tag('td', $code.
                $this->Form->hidden('ProductReturDetail.product_id.', array(
                    'value' => $id,
                )).
                $this->Form->hidden('ProductReturDetail.document_detail_id.', array(
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
                echo $this->Html->tag('td', $this->Common->buildInputForm('ProductReturDetail.qty.', false, array(
                    'type' => 'text',
                    'fieldError' => array(
                        'ProductReturDetail.'.$key.'.qty',
                        'ProductReturDetail.'.$key.'.over_retur'
                    ),
                    'frameClass' => false,
                    'class' => sprintf('price_custom input_number text-center %s', $targetQty),
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
                echo $this->Form->hidden($targetQty, array(
                    'value' => $qty,
                    'class' => $targetQty,
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