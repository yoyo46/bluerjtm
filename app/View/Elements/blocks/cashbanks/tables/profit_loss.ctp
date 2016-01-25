<?php
        if(!empty($values)){
            $tmpValues = $values;

            if( !empty($tmpValues['TotalCoa']) ) {
                unset($tmpValues['TotalCoa']);
            }

            foreach ($tmpValues as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Coa', 'id');
                $coa = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');
                $level = $this->Common->filterEmptyField($value, 'Coa', 'level');
                $parent_name = $this->Common->filterEmptyField($value, 'Parent', 'name');
                $parent_id = $this->Common->filterEmptyField($value, 'Coa', 'parent_id');
                $dataCoa = $this->Common->filterEmptyField($value, 'Coa');
                $childrens = $this->Common->filterEmptyField($value, 'children');

                if( !empty($parent_id) && ( $level == 4 || ( $level == 3 && !empty($childrens) ) || ( $level == 2 && !empty($childrens[0]['children']) ) ) ) {
                    $marginLeft = 10;
                    $fontWeight = 'normal';

                    if( $level != 4 ) {
                        $fontWeight = 'bold';
                        $colspan = 13;
                    } else {
                        $colspan = false;
                    }
                    if( $level != 2 ) {
                        $marginLeft = ($level - 2) * 30;
                    }
                    if( $level == 3 ) {
                        $fontWeight = '600';
                    }
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $coa, array(
                'style' => sprintf('font-weight: %s;padding-left: %spx;', $fontWeight, $marginLeft),
                'colspan' => $colspan,
            ));

            if($level == 4 && !empty($dateFrom) && !empty($dateTo) ) {
                $tmpDateFrom = $dateFrom;
                $tmpDateTo = $dateTo;

                while( $tmpDateFrom <= $tmpDateTo ) {
                    $balance = $this->Common->filterEmptyField($dataCoa, $tmpDateFrom, 'balancing', 0);
                    $balance = $this->Common->getFormatPrice($balance);

                    echo $this->Html->tag('td', $balance, array(
                        'style' => 'text-align: right',
                    ));

                    $tmpDateFrom = date('Y-m', strtotime('+1 Month', strtotime($tmpDateFrom)));
                }
            }
    ?>
</tr>
<?php
                }

                if( !empty($childrens) ) {
                    echo $this->element('blocks/cashbanks/tables/profit_loss', array(
                        'values' => $childrens,
                    ));
                }
            }

            if( !empty($parent_id) && ( $level == 4 || ( $level == 3 && !empty($childrens) ) || ( $level == 2 && !empty($childrens['children']) ) ) ) {
                if(!empty($dateFrom) && !empty($dateTo) ) {
                    $tmpDateFrom = $dateFrom;
                    $tmpDateTo = $dateTo;
                    $tmpTr = false;

                    while( $tmpDateFrom <= $tmpDateTo ) {
                        if( isset($values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing']) ) {
                            $total = $values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'];
                            $totalBalance = $this->Common->getFormatPrice($total);

                            $tmpTr .= $this->Html->tag('td', $totalBalance, array(
                                'style' => 'text-align: right;font-weight: bold;',
                            ));
                        }

                        $tmpDateFrom = date('Y-m', strtotime('+1 Month', strtotime($tmpDateFrom)));
                    }

                    if( !empty($tmpTr) ) {
                        if( $level-3 <= 0 ) {
                            $tempMarginLeft = 10;
                        } else {
                            $tempMarginLeft = ($level-3) * 30;
                        }

                        $tmpTr = $this->Html->tag('td', sprintf(__('Total %s'), $parent_name), array(
                            'style' => sprintf('font-weight: bold;padding-left: %spx;font-style: italic;', $tempMarginLeft),
                        )).$tmpTr;

                        echo $this->Html->tag('tr', $tmpTr);
                    }
                }
            }
        }
?>