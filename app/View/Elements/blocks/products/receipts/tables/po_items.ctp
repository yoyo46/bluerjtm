<table>
    <tbody class="wrapper-table-documents">
        <?php
                $grandtotal = 0;
                $values = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail');

                if(!empty($values)){
                    foreach ($values as $key => $value) {
                        $id = $this->Common->filterEmptyField($value, 'Product', 'id');
                        $qty = $this->Common->filterEmptyField($value, 'PurchaseOrderDetail', 'qty');

                        echo $this->element('blocks/products/receipts/tables/items', array(
                            'id' => $id,
                            'modelName' => 'Product',
                            'value' => $value,
                            'qty' => $qty,
                            'key' => $key,
                        ));
                    }
                }
        ?>
    </tbody>
</table>