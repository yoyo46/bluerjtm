<?php 
        $value = !empty($value)?$value:false;
        $id = $this->Common->filterEmptyField($value, 'AssetSellDetail', 'id');
        $asset_id = $this->Common->filterEmptyField($value, 'AssetSellDetail', 'asset_id');
        $price = $this->Common->filterEmptyField($value, 'AssetSellDetail', 'price');
        $nilai_perolehan = $this->Common->filterEmptyField($value, 'AssetSellDetail', 'nilai_perolehan');
        $ak_penyusutan = $this->Common->filterEmptyField($value, 'AssetSellDetail', 'ak_penyusutan');
        $name = $this->Common->filterEmptyField($value, 'AssetSellDetail', 'name');
        $note = $this->Common->filterEmptyField($value, 'AssetSellDetail', 'note');

        $priceFormat = $this->Common->getFormatPrice($price, 0, 2);
        $nilaiPerolehanFormat = $this->Common->getFormatPrice($nilai_perolehan, 0, 2);
        $akPenyusutanFormat = $this->Common->getFormatPrice($ak_penyusutan, 0, 2);

        $hiddenContent = $this->Form->hidden('AssetSellDetail.id.', array(
            'value' => $id,
        )).$this->Form->hidden('AssetSellDetail.asset_id.', array(
            'value' => $asset_id,
        ));
?>
<tr class="pick-document item" data-type="single-total" rel="<?php echo $asset_id; ?>">
    <?php
            echo $this->Html->tag('td', $name.$hiddenContent);
            echo $this->Html->tag('td', $note);
            echo $this->Html->tag('td', $nilaiPerolehanFormat, array(
                'class' => 'text-right calc-item',
                'rel' => 0,
            ));
            echo $this->Html->tag('td', $akPenyusutanFormat, array(
                'class' => 'text-right calc-item',
                'rel' => 1,
            ));
            echo $this->Html->tag('td', $this->Common->buildInputForm('AssetSellDetail.price.', false, array(
                'type' => 'text',
                'frameClass' => false,
                'class' => 'text-right price',
                'fieldError' => 'AssetSellDetail.'.$idx.'.price',
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