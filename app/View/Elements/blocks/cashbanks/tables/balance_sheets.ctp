<?php
        if(!empty($values)){
            $tmpValues = $values;

            if( !empty($tmpValues['TotalCoa']) ) {
                unset($tmpValues['TotalCoa']);
            }

            foreach ($tmpValues as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Coa', 'id');
                $coa = $this->Common->filterEmptyField($value, 'Coa', 'coa_name');
                $coa_name = $this->Common->filterEmptyField($value, 'Coa', 'name');
                $level = $this->Common->filterEmptyField($value, 'Coa', 'level');
                $parent_name = $this->Common->filterEmptyField($value, 'Parent', 'name');
                $parent_parent_id = $this->Common->filterEmptyField($value, 'Parent', 'parent_id');
                $parent_id = $this->Common->filterEmptyField($value, 'Coa', 'parent_id');
                $dataCoa = $this->Common->filterEmptyField($value, 'Coa');
                $childrens = $this->Common->filterEmptyField($value, 'children');

                $children = !empty($childrens[0]['children'])?$childrens[0]['children']:false;
                $subchildren = !empty($childrens[0]['children'][0]['children'])?$childrens[0]['children'][0]['children']:false;

                if( ( $level == 1 && !empty($subchildren) ) || $level == 4 || ( $level == 3 && !empty($childrens) ) || ( $level == 2 && !empty($children) ) ) {
                    $marginLeft = 10;
                    $fontWeight = 'normal';

                    if( $level != 4 ) {
                        $fontWeight = 'bold';
                        $colspan = 13;
                    } else {
                        $colspan = false;
                    }

                    if( $level != 1 ) {
                        $marginLeft = ($level - 1) * 30;
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
                    'action' => 'balance_sheet_amount',
                    $id,
                    $dateFrom,
                    $dateTo,
                    'admin' => false,
                ));

                while( $tmpDateFrom <= $tmpDateTo ) {
                    $balance = $this->Common->filterEmptyField($dataCoa, $tmpDateFrom, 'balancing', 0);

                    $op = ($balance>0)?'+':'-';
                    $balance = !empty($balance)?$this->Common->getFormatPrice($balance, false, 2):0;
                    $classItem = __('wrapper-coa-%s-%s', $id, $tmpDateFrom);

                    $coa_month_content .= $this->Html->tag('td', $balance, array(
                        'style' => 'text-align: right',
                        'class' => $classItem.__(' wrapper-coa-parent-%s', $parent_id),
                        'rel' => $tmpDateFrom,
                        'data-op' => $op,
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
                    echo $this->element('blocks/cashbanks/tables/balance_sheets', array(
                        'values' => $childrens,
                    ));
                }
            }

            if( ( $level == 1 && !empty($childrens[0]['children'][0]['children']) ) || $level == 4 || ( $level == 3 && !empty($childrens) ) || ( $level == 2 && !empty($childrens['children']) ) ) {

                if(!empty($dateFrom) && !empty($dateTo) ) {
                    $tmpDateFrom = $dateFrom;
                    $tmpDateTo = $dateTo;
                    $tmpTr = false;

                    if( $level == 1 ) {
                        $totalCoaName = $coa_name;
                        $tmp_parent_id = $id;
                    } else {
                        $totalCoaName = $parent_name;
                        $tmp_parent_id = $parent_id;
                    }

                    while( $tmpDateFrom <= $tmpDateTo ) {
                        if( $level == 1 ) {
                            $idx = isset($values['TotalCoa'])?$values['TotalCoa']:false;
                        } else {
                            $idx = isset($values['TotalCoa'][$parent_id])?$values['TotalCoa'][$parent_id]:false;
                        }

                        if( isset($idx[$tmpDateFrom]['balancing']) ) {
                            $total = $idx[$tmpDateFrom]['balancing'];
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
                        if( $level == 1 ) {
                            $tempMarginLeft = 10;
                        } else {
                            $tempMarginLeft = ($level-2) * 30;
                        }

                        $tmpTr = $this->Html->tag('td', sprintf(__('Total %s'), $totalCoaName), array(
                            'style' => sprintf('font-weight: bold;padding-left: %spx;font-style: italic;', $tempMarginLeft),
                        )).$tmpTr;

                        echo $this->Html->tag('tr', $tmpTr, array(
                            'class' => 'coa-parent wrapper-coa-'.$tmp_parent_id,
                            'coa-id' => $tmp_parent_id,
                        ));
                    }
                }
            }
        }
?>