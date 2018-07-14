<?php
        if(!empty($values)){
            $tmpValues = $values;
            $margin_left = isset($margin_left)?$margin_left+30:10;

            foreach ($tmpValues as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Cogs', 'id');
                $cogs = $this->Common->filterEmptyField($value, 'Cogs', 'cogs_name');
                $parent_parent_id = $this->Common->filterEmptyField($value, 'Parent', 'parent_id');
                $parent_id = $this->Common->filterEmptyField($value, 'Cogs', 'parent_id');
                $dataCogs = $this->Common->filterEmptyField($value, 'Cogs');
                $childrens = $this->Common->filterEmptyField($value, 'children');
                
                if( empty($parent_id) ) {
                    $parent_name = $cogs;
                } else if( empty($parent_name) ) {
                    $parent_name = $cogs;
                }

                $fontWeight = 'normal';
                $colspan = false;

                if( !empty($childrens) || empty($parent_id) ) {
                    $fontWeight = 'bold';

                    if( !empty($childrens) ) {
                        $colspan = 6;
                    }
                }
?>
<tr class="wrapper-cogs-<?php echo $id; ?>">
    <?php 
            $wrapper_template = array();
            $className = false;
            $dataUrl = false;
            $wrapper_template = false;

            if( empty($childrens) ) {
                $className = 'ajax-infinity';
                $dataUrl = $this->Html->url(array(
                    'controller' => 'cashbanks',
                    'action' => 'profit_loss_per_point_amount',
                    $id,
                    $dateFrom,
                    $dateTo,
                    'admin' => false,
                ));
                $wrapper_template = __('.wrapper-coa-revenue-%s,.wrapper-coa-expense-%s,.wrapper-coa-maintenance-%s,.wrapper-coa-gross-profit-%s,.wrapper-coa-er-%s', $id, $id, $id, $id, $id);
            }
            
            echo $this->Html->tag('td', $cogs, array(
                'style' => sprintf('font-weight: %s;padding-left: %spx;', $fontWeight, $margin_left),
                'colspan' => $colspan,
                'class' => $className,
                'data-url' => $dataUrl,
                'data-infinity' => 'true',
                'data-wrapper-write-page' => $wrapper_template,
            ));

            if( empty($childrens) ) {
                echo $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right',
                    'class' => __('wrapper-coa-revenue-%s wrapper-coa-parent-%s', $id, $parent_id),
                    'rel' => 'revenue',
                ));
                echo $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right',
                    'class' => __('wrapper-coa-expense-%s wrapper-coa-parent-%s', $id, $parent_id),
                    'rel' => 'expense',
                ));
                echo $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right',
                    'class' => __('wrapper-coa-maintenance-%s wrapper-coa-parent-%s', $id, $parent_id),
                    'rel' => 'maintenance',
                ));
                echo $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right',
                    'class' => __('wrapper-coa-gross-profit-%s wrapper-coa-parent-%s', $id, $parent_id),
                    'rel' => 'gross-profit',
                ));
                echo $this->Html->tag('td', 0, array(
                    'style' => 'text-align: center',
                    'class' => __('wrapper-coa-er-%s wrapper-coa-parent-%s', $id, $parent_id),
                    'rel' => 'er',
                ));
            }
    ?>
</tr>
<?php
                if( !empty($childrens) ) {
                    echo $this->element('blocks/cashbanks/tables/profit_loss_per_point', array(
                        'values' => $childrens,
                        'parent_name' => $cogs,
                        'margin_left' => $margin_left,
                    ));
                }
            }


            if( !empty($parent_id) ) {
                $tmpTr = $this->Html->tag('td', sprintf(__('Total %s'), $parent_name), array(
                    'style' => 'font-weight: bold;padding-left: 10px;font-style: italic;',
                ));
                $tmpTr .= $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right;font-weight: bold;',
                ));
                $tmpTr .= $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right;font-weight: bold;',
                ));
                $tmpTr .= $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right;font-weight: bold;',
                ));
                $tmpTr .= $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right;font-weight: bold;',
                ));
                $tmpTr .= $this->Html->tag('td', 0, array(
                    'style' => 'text-align: center;font-weight: bold;',
                ));

                echo $this->Html->tag('tr', $tmpTr, array(
                    'class' => 'coa-parent wrapper-coa-'.$parent_id,
                    'coa-id' => $parent_id,
                ));
            }

            if( !empty($main_total) ) {
                $tmpTr = $this->Html->tag('td', __('Total'), array(
                    'style' => 'font-weight: bold;padding-left: 10px;font-style: italic;',
                ));
                $tmpTr .= $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right;font-weight: bold;',
                    'class' => 'wrapper-profit-loss',
                    'rel' => 'revenue',
                ));
                $tmpTr .= $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right;font-weight: bold;',
                    'class' => 'wrapper-profit-loss',
                    'rel' => 'expense',
                ));
                $tmpTr .= $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right;font-weight: bold;',
                    'class' => 'wrapper-profit-loss',
                    'rel' => 'maintenance',
                ));
                $tmpTr .= $this->Html->tag('td', 0, array(
                    'style' => 'text-align: right;font-weight: bold;',
                    'class' => 'wrapper-profit-loss',
                    'rel' => 'gross-profit',
                ));
                $tmpTr .= $this->Html->tag('td', 0, array(
                    'style' => 'text-align: center;font-weight: bold;',
                    'class' => 'wrapper-profit-loss',
                    'rel' => 'er',
                ));

                echo $this->Html->tag('tr', $tmpTr, array(
                    'class' => 'coa-parent',
                    'data-type' => 'end',
                ));
            }
        }
?>