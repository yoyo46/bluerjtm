<?php
        if(!empty($values)){
            $result = !empty($result)?$result:false;
            $padding_left = !empty($padding_left)?$padding_left:0;

            foreach ($values as $key => $value) {
                $id = Common::hashEmptyField($value, 'Cogs.id');
                $cogs = Common::hashEmptyField($value, 'Cogs.name');
                $children = Common::hashEmptyField($value, 'children');
                $balance_rev = Common::hashEmptyField($value, 'Cogs.balance_rev');
                $balance_exp = Common::hashEmptyField($value, 'Cogs.balance_exp');
                $balance_maintain = Common::hashEmptyField($value, 'Cogs.balance_maintain');
                $balance_other = Common::hashEmptyField($value, 'Cogs.balance_other');

                $out = $balance_exp + $balance_maintain + $balance_other;
                $gross_profit = $balance_rev + $out;
                $er = Common::_callER($out, $balance_rev);
            
                if( !empty($children) ) {
                    $colspan = '6';
                    $label = $this->Html->tag('strong', $cogs);
                } else {
                    $colspan = '';
                    $label = $cogs;
                }

                $style_padding_left = $padding_left * 20;
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $label, array(
                'colspan' => $colspan,
                'style' => __('padding-left:%spx;', $style_padding_left),
            ));
            
            if( empty($children) ) {
                echo $this->Html->tag('td', Common::getFormatPrice($balance_rev, 2), array(
                    'style' => 'text-align: right',
                ));
                echo $this->Html->tag('td', Common::getFormatPrice($balance_exp, 2), array(
                    'style' => 'text-align: right',
                ));
                echo $this->Html->tag('td', Common::getFormatPrice($balance_maintain, 2), array(
                    'style' => 'text-align: right',
                ));
                echo $this->Html->tag('td', Common::getFormatPrice($balance_other, 2), array(
                    'style' => 'text-align: right',
                ));
                echo $this->Html->tag('td', Common::getFormatPrice($gross_profit, 2), array(
                    'style' => 'text-align: right',
                ));
                echo $this->Html->tag('td', __('%s%%', round($er, 2)), array(
                    'style' => 'text-align: center',
                ));
            }
    ?>
</tr>
<?php
                if( !empty($children) ) {
                    echo $this->element('blocks/cashbanks/tables/profit_loss_per_point', array(
                        'values' => $children,
                        'padding_left' => $padding_left+1,
                    ));

                    $tmpTr = $this->Html->tag('td', $this->Html->tag('strong', __('Total %s', $cogs)), array(
                        'style' => __('padding-left:%spx;font-weight: bold;font-style: italic;', $style_padding_left),
                    ));
                    $tmpTr .= $this->Html->tag('td', Common::getFormatPrice($balance_rev, 2), array(
                        'style' => 'text-align: right;font-weight: bold;',
                    ));
                    $tmpTr .= $this->Html->tag('td', Common::getFormatPrice($balance_exp, 2), array(
                        'style' => 'text-align: right;font-weight: bold;',
                    ));
                    $tmpTr .= $this->Html->tag('td', Common::getFormatPrice($balance_maintain, 2), array(
                        'style' => 'text-align: right;font-weight: bold;',
                    ));
                    $tmpTr .= $this->Html->tag('td', Common::getFormatPrice($balance_other, 2), array(
                        'style' => 'text-align: right;font-weight: bold;',
                    ));
                    $tmpTr .= $this->Html->tag('td', Common::getFormatPrice($gross_profit, 2), array(
                        'style' => 'text-align: right;font-weight: bold;',
                    ));
                    $tmpTr .= $this->Html->tag('td', __('%s%%', round($er, 2)), array(
                        'style' => 'text-align: center;font-weight: bold;',
                    ));

                    echo $this->Html->tag('tr', $tmpTr);
                }
            }

            if( !empty($result) ) {
                $balance_rev = Common::hashEmptyField($result, 'total_balance_rev');
                $balance_exp = Common::hashEmptyField($result, 'total_balance_exp');
                $balance_maintain = Common::hashEmptyField($result, 'total_balance_maintain');
                $balance_other = Common::hashEmptyField($result, 'total_balance_other');

                $out = $balance_exp + $balance_maintain + $balance_other;
                $gross_profit = $balance_rev + $out;
                $er = Common::_callER($out, $balance_rev);

                $tmpTr = $this->Html->tag('td', $this->Html->tag('strong', __('Total')), array(
                    'style' => 'font-weight: bold;padding-left: 0;font-style: italic;',
                ));
                $tmpTr .= $this->Html->tag('td', Common::getFormatPrice($balance_rev, 2), array(
                    'style' => 'text-align: right;font-weight: bold;',
                ));
                $tmpTr .= $this->Html->tag('td', Common::getFormatPrice($balance_exp, 2), array(
                    'style' => 'text-align: right;font-weight: bold;',
                ));
                $tmpTr .= $this->Html->tag('td', Common::getFormatPrice($balance_maintain, 2), array(
                    'style' => 'text-align: right;font-weight: bold;',
                ));
                $tmpTr .= $this->Html->tag('td', Common::getFormatPrice($balance_other, 2), array(
                    'style' => 'text-align: right;font-weight: bold;',
                ));
                $tmpTr .= $this->Html->tag('td', Common::getFormatPrice($gross_profit, 2), array(
                    'style' => 'text-align: right;font-weight: bold;',
                ));
                $tmpTr .= $this->Html->tag('td', __('%s%%', round($er, 2)), array(
                    'style' => 'text-align: center;font-weight: bold;',
                ));

                echo $this->Html->tag('tr', $tmpTr);
            }
        }
?>