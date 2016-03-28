<?php 
        $value = !empty($value)?$value:false;
        $assetGroups = !empty($assetGroups)?$assetGroups:false;
        $price = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'price');
        $name = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'name');
        $note = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'note');
        $asset_group_id = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'asset_group_id');

        $customTotal = !empty($total)?$total:0;
?>
<tr class="pick-document" rel="<?php echo $id; ?>">
    <?php
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderAsset.name.'.$id, false, array(
                'type' => 'text',
                'frameClass' => false,
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderAsset.note.'.$id, false, array(
                'type' => 'text',
                'frameClass' => false,
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderAsset.asset_group_id.'.$id, false, array(
                'frameClass' => false,
                'options' => $assetGroups,
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderAsset.price.'.$id, false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'input_price_coma text-right',
            )));
            echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                'class' => 'delete-document btn btn-danger btn-xs',
                'escape' => false,
            )), array(
                'class' => 'text-center',
            ));
    ?>
</tr>