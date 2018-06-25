<?php
        if(!empty($values)){
            $main_total = !empty($main_total)?$main_total:false;
            $tmpValues = $values;

            if( !empty($tmpValues['TotalCoa']) ) {
                unset($tmpValues['TotalCoa']);
            }

            foreach ($tmpValues as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Coa', 'id');
                $coa = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');
                $level = $this->Common->filterEmptyField($value, 'Coa', 'level');
                $type = $this->Common->filterEmptyField($value, 'Coa', 'type');
                $parent_name = $this->Common->filterEmptyField($value, 'Parent', 'name');
                $parent_parent_id = $this->Common->filterEmptyField($value, 'Parent', 'parent_id');
                $parent_id = $this->Common->filterEmptyField($value, 'Coa', 'parent_id');
                $dataCoa = $this->Common->filterEmptyField($value, 'Coa');
                $childrens = $this->Common->filterEmptyField($value, 'children');

                if( !empty($parent_id) && ( $level == 4 || ( $level == 3 && !empty($childrens) ) || ( $level == 2 && !empty($childrens[0]['children']) ) ) ) {
                    $marginLeft = 10;
                    $fontWeight = 'normal';

                    if( $level != 4 ) {
                        $fontWeight = 'bold';
                        $colspan = 2;
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
<tr class="wrapper-coa-<?php echo $id; ?>">
    <?php 
            $coa_month_content = false;
            $wrapper_template = array();
            $className = false;
            $dataUrl = false;

            if($level == 4 && !empty($dateFrom) && !empty($dateTo) ) {
                $tmpDateFrom = $dateFrom;
                $tmpDateTo = $dateTo;
                $className = 'ajax-infinity';
                $dataUrl = $this->Html->url(array(
                    'controller' => 'cashbanks',
                    'action' => 'profit_loss_amount',
                    $id,
                    $dateFrom,
                    $dateTo,
                    'admin' => false,
                ));

                while( $tmpDateFrom <= $tmpDateTo ) {
                    $balance = $this->Common->filterEmptyField($dataCoa, $tmpDateFrom, 'balancing', 0);
                    
                    $op = ($balance>0)?'+':'-';
                    $balanceStr = Common::getFormatPrice($balance);
                    $classItem = __('wrapper-coa-%s-%s', $id, $tmpDateFrom);

                    $coa_month_content .= $this->Html->tag('td', $balanceStr, array(
                        'style' => 'text-align: right',
                        'class' => $classItem.__(' wrapper-coa-parent-%s', $parent_id),
                        'rel' => $tmpDateFrom,
                        'data-op' => $op,
                        'data-type' => $type,
                    ));

                    $tmpDateFrom = date('Y-m', strtotime('+1 Month', strtotime($tmpDateFrom)));
                    $wrapper_template[] = $classItem;
                }
            }

            $wrapper_template = implode(',.', $wrapper_template);
            $wrapper_template = !empty($wrapper_template)?'.'.$wrapper_template:false;
            
            echo $this->Html->tag('td', $coa, array(
                'style' => sprintf('font-weight: %s;padding-left: %spx;', $fontWeight, $marginLeft),
                'colspan' => $colspan,
                'class' => $className,
                'data-url' => $dataUrl,
                'data-infinity' => 'true',
                'data-wrapper-write-page' => $wrapper_template,
            ));
            echo $coa_month_content;
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

                    if( $level == 1 ) {
                        $tmp_parent_id = $id;
                    } else {
                        $tmp_parent_id = $parent_id;
                    }

                    while( $tmpDateFrom <= $tmpDateTo ) {
                        if( isset($values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing']) ) {
                            $total = $values['TotalCoa'][$parent_id][$tmpDateFrom]['balancing'];
                            $totalBalance = $this->Common->getFormatPrice($total, false, 2);

                            $tmpTr .= $this->Html->tag('td', $totalBalance, array(
                                'style' => 'text-align: right;font-weight: bold;',
                                'class' => 'wrapper-coa-parent '.__('wrapper-coa-parent-%s', $parent_parent_id),
                                'coa-id' => $tmp_parent_id,
                                'rel' => $tmpDateFrom,
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

                        echo $this->Html->tag('tr', $tmpTr, array(
                            'class' => 'coa-parent wrapper-coa-'.$tmp_parent_id,
                            'coa-id' => $tmp_parent_id,
                            'data-type' => $type,
                        ));
                    }
                }
            }

            if( !empty($main_total) ) {
                $summaryProfitLoss = !empty($summaryProfitLoss)?$summaryProfitLoss:false;
                $debit_total = Common::hashEmptyField($summaryProfitLoss, 'Journal.debit_total');
                $credit_total = Common::hashEmptyField($summaryProfitLoss, 'Journal.credit_total');

                $tmpTr = $this->Html->tag('td', __('Laba Rugi'), array(
                    'style' => 'font-weight: bold;font-style: italic;',
                )).
                $this->Html->tag('td', Common::getFormatPrice($credit_total - $debit_total), array(
                    'style' => 'text-align: right;font-weight: bold;',
                    'class' => 'wrapper-profit-loss',
                ));

                echo $this->Html->tag('tr', $tmpTr, array(
                    'class' => 'coa-parent',
                    'data-type' => 'end',
                ));
            }
        }
?>