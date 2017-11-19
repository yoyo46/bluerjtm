<?php 
        $value = !empty($value)?$value:false;
        $assetGroups = !empty($assetGroups)?$assetGroups:false;
        $id = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'id');
        $truck_id = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'truck_id');
        $asset_id = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'asset_id');
        $price = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'price');
        $name = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'name');
        $note = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'note');
        $asset_group_id = $this->Common->filterEmptyField($value, 'PurchaseOrderAsset', 'asset_group_id');

        $customTotal = !empty($total)?$total:0;

        $hiddenContent = $this->Form->hidden('PurchaseOrderAsset.id.', array(
            'value' => $id,
        ));
        $hiddenContent .= $this->Form->hidden('PurchaseOrderAsset.truck_id.', array(
            'value' => $truck_id,
        ));
        $hiddenContent .= $this->Form->hidden('PurchaseOrderAsset.asset_id.', array(
            'value' => $asset_id,
        ));

        if( empty($idx) ) {
            $addClass = 'field-copy';
        } else {
            $addClass = '';
        }
?>
<tr class="pick-document item <?php echo $addClass; ?>" rel="<?php echo $idx; ?>" data-tag="tr">
    <?php
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderAsset.name.', false, array(
                'type' => 'text',
                'frameClass' => false,
                'fieldError' => array(
                    'PurchaseOrderAsset.'.$idx.'.name',
                    'PurchaseOrderAsset.'.$idx.'.Asset.name',
                ),
                'attributes' => array(
                    'value' => $name,
                ),
            )).$hiddenContent);
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderAsset.note.', false, array(
                'type' => 'text',
                'frameClass' => false,
                'fieldError' => 'PurchaseOrderAsset.'.$idx.'.note',
                'attributes' => array(
                    'value' => $note,
                ),
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderAsset.asset_group_id.', false, array(
                'frameClass' => false,
                'options' => $assetGroups,
                'empty' => __('Pilih Group Asset'),
                'fieldError' => 'PurchaseOrderAsset.'.$idx.'.asset_group_id',
                'attributes' => array(
                    'value' => $asset_group_id,
                ),
            )));
            echo $this->Html->tag('td', $this->Common->buildInputForm('PurchaseOrderAsset.price.', false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'text-right price',
                'fieldError' => 'PurchaseOrderAsset.'.$idx.'.price',
                'attributes' => array(
                    'data-type' => 'input_price_coma',
                    'value' => !empty($price)?$this->Common->getFormatPrice($price, 0, 2):false,
                ),
            )));
            if( empty($view) ) {
                echo $this->Html->tag('td', $this->Html->link($this->Common->icon('times'), '#', array(
                    'class' => 'removed btn btn-danger btn-xs',
                    'escape' => false,
                )), array(
                    'class' => 'text-center',
                ));
            }
    ?>
</tr>