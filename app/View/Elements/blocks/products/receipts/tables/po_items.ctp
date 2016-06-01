<table>
    <tbody class="wrapper-table-documents">
        <?php
                $grandtotal = 0;
                $values = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail');

                if(!empty($values)){
                    foreach ($values as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'Product', 'id');
                        $code = $this->Common->filterEmptyField($value, 'Product', 'code');
                        $name = $this->Common->filterEmptyField($value, 'Product', 'name');
                        $unit = $this->Common->filterEmptyField($value, 'ProductUnit', 'name');

                        $qty = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'qty');

                        $grandtotal += $qty;
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
                        'class' => 'qty input_number text-center',
                        'attributes' => array(
                            'value' => $qty,
                        ),
                    )));
                    echo $this->Html->tag('td', $unit, array(
                        'class' => 'text-center',
                    ));
                    echo $this->Html->tag('td', $this->Html->link(sprintf(__('Masukan No. Seri %s'), $this->Common->icon('plus-square', false, 'i', 'ml5')), array(
                        'controller'=> 'products', 
                        'action' => 'receipt_serial_numbers',
                        $id,
                        'admin' => false,
                    ), array(
                        'escape' => false,
                        'class' => 'ajaxModal browse-docs',
                        'title' => __('Serial Number'),
                        'data-action' => 'browse-form',
                    )), array(
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
        <?php
                    }
                }
        ?>
    </tbody>
</table>