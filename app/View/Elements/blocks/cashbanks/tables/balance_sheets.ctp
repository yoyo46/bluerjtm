<?php
        $coa_type = !empty($coa_type)?$coa_type:false;

        if(!empty($values)){
            foreach ($values as $id => $value) {
                $parent_coa = Common::hashEmptyField($value, 'name');
                $parent_id = Common::hashEmptyField($value, 'parent_id');
                $parent_level = Common::hashEmptyField($value, 'level');
                $parent_balance = Common::hashEmptyField($value, 'balance');
                $children = Common::hashEmptyField($value, 'children');
                $parent_padding_left = $parent_level * 20;

                $val = Common::_callUnset($value, array(
                    'name',
                    'parent_id',
                    'level',
                    'balance',
                ));
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $this->Html->tag('strong', $parent_coa), array(
                'style' => __('padding-left:%spx;', $parent_padding_left),
            ));
            echo $this->Html->tag('td', '', array(
                'style' => 'text-align: right',
            ));
    ?>
</tr>
<?php
                if( !empty($children) ) {
                    foreach ($children as $key => $child) {
                        $coa = Common::hashEmptyField($child, 'name');
                        $balance = Common::hashEmptyField($child, 'balance');
                        $level = Common::hashEmptyField($child, 'level');
                        $padding_left = $level * 20;
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $coa, array(
                'style' => __('padding-left:%spx;', $padding_left),
            ));
            echo $this->Html->tag('td', Common::getFormatPrice($balance, 2), array(
                'style' => 'text-align: right',
            ));
    ?>
</tr>
<?php
                    }
                } else if( !empty($val) && empty($children) ) {
                    echo $this->element('blocks/cashbanks/tables/balance_sheets', array(
                        'values' => $val,
                        'coa_type' => $coa_type,
                        'text' => true,
                    ));
                }
            }
        }
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $this->Html->tag('strong', __('Total %s', $parent_coa)), array(
                'style' => __('padding-left:%spx;', $parent_padding_left),
            ));
            echo $this->Html->tag('td', Common::getFormatPrice($parent_balance, 2), array(
                'style' => 'text-align: right',
            ));
    ?>
</tr>