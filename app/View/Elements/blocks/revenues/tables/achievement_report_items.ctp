<?php
        if(!empty($ttujs)){
            $grandTotalPencapaian = 0;
            $grandTotalTarget = 0;
            $grandTotalPencapaianGroup = array();
            $grandTotalTargetGroup = array();
            $groupName = array();
            $totalPencapaianGroup = array();
            $totalTargetGroup = array();
            $temp_tye_id = false;
            $temp_group_id = false;
            $temp_manual_group = false;
            $finishTotal = array();

            foreach ($ttujs as $key => $value) {
                $id = $this->Common->filterEmptyField($value, 'Customer', 'id');
                $customer_name = $this->Common->filterEmptyField($value, 'Customer', 'code', '-');
                $customer_group_id = $this->Common->filterEmptyField($value, 'Customer', 'customer_group_id', '-');
                $manual_group = $this->Common->filterEmptyField($value, 'Customer', 'manual_group');
                $type = $this->Common->filterEmptyField($value, 'CustomerType', 'name');
                $type_id = $this->Common->filterEmptyField($value, 'CustomerType', 'id');
                $group_name = $this->Common->filterEmptyField($value, 'CustomerGroup', 'name');
                $branch_name = $this->Common->filterEmptyField($value, 'Branch', 'name');

                $totalSidePencapaian = 0;
                $totalSideTarget = 0;

                if( !empty($temp_group_id) && !empty($temp_tye_id) && ( $temp_group_id != $customer_group_id || $temp_tye_id != $type_id || $temp_manual_group != $manual_group ) ) {
                    echo $this->element('blocks/revenues/tables/achievement_report', array(
                        'manual_group' => $temp_manual_group,
                        'type_id' => $temp_tye_id,
                        'customer_group_id' => $temp_group_id,
                        'groupName' => $groupName,
                        'totalCnt' => $totalCnt,
                        'totalTargetGroup' => $totalTargetGroup,
                        'totalPencapaianGroup' => $totalPencapaianGroup,
                        'grandTotalTargetGroup' => $grandTotalTargetGroup,
                        'grandTotalPencapaianGroup' => $grandTotalPencapaianGroup,
                    ));

                    $finishTotal[$manual_group][$temp_tye_id][$temp_group_id] = true;
                }

                $temp_group_id = $customer_group_id;
                $temp_tye_id = $type_id;
                $temp_manual_group = $manual_group;
?>
<tr>
    <?php 
            echo $this->Html->tag('td', $branch_name);
            echo $this->Html->tag('td', $customer_name);
            // echo $this->Html->tag('td', $customer_name);

            if( isset($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $pencapaian = !empty($cntPencapaian[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))])?$cntPencapaian[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))]:'-';
                    $target_rit = !empty($targetUnit[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))])?$targetUnit[$value['Customer']['id']][date('Y-m', mktime(0, 0, 0, $fromMonth+$i, 1, $fromYear))]:'-';
                    $nameVal = 'totalPencapaian'.$i;
                    $nameValTarget = 'totalTarget'.$i;

                    if( empty($$nameVal) ) {
                        $$nameVal = 0;
                    }
                    if( empty($$nameValTarget) ) {
                        $$nameValTarget = 0;
                    }

                    if( is_numeric($pencapaian) ) {
                        $$nameVal += $pencapaian;
                        $totalSidePencapaian += $pencapaian;
                    }

                    if( is_numeric($target_rit) ) {
                        $$nameValTarget += $target_rit;
                        $totalSideTarget += $target_rit;
                    }

                    echo $this->Html->tag('td', $target_rit, array(
                        'style' => 'text-align: center;',
                    ));
                    echo $this->Html->tag('td', $pencapaian, array(
                        'style' => 'text-align: center;',
                    ));

                    if( !empty($totalPencapaianGroup[$i][$manual_group][$type_id][$customer_group_id]) ) {
                        $totalPencapaianGroup[$i][$manual_group][$type_id][$customer_group_id] += $pencapaian;
                    } else {
                        $totalPencapaianGroup[$i][$manual_group][$type_id][$customer_group_id] = $pencapaian;
                    }
                    if( !empty($totalTargetGroup[$i][$manual_group][$type_id][$customer_group_id]) ) {
                        $totalTargetGroup[$i][$manual_group][$type_id][$customer_group_id] += $target_rit;
                    } else {
                        $totalTargetGroup[$i][$manual_group][$type_id][$customer_group_id] = $target_rit;
                    }
                }
            }

            echo $this->Html->tag('td', $this->Number->format($totalSideTarget, false, array('places' => 0)), array(
                'style' => 'text-align: center;',
            ));
            echo $this->Html->tag('td', $this->Number->format($totalSidePencapaian, false, array('places' => 0)), array(
                'style' => 'text-align: center;',
            ));
            $grandTotalPencapaian += $totalSidePencapaian;
            $grandTotalTarget += $totalSideTarget;
    ?>
</tr>
<?php

                if( !empty($grandTotalPencapaianGroup[$manual_group][$type_id][$customer_group_id]) ) {
                    $grandTotalPencapaianGroup[$manual_group][$type_id][$customer_group_id] += $totalSidePencapaian;
                } else {
                    $grandTotalPencapaianGroup[$manual_group][$type_id][$customer_group_id] = $totalSidePencapaian;
                }
                if( !empty($grandTotalTargetGroup[$manual_group][$type_id][$customer_group_id]) ) {
                    $grandTotalTargetGroup[$manual_group][$type_id][$customer_group_id] += $totalSideTarget;
                } else {
                    $grandTotalTargetGroup[$manual_group][$type_id][$customer_group_id] = $totalSideTarget;
                }

                $groupName[$manual_group][$type_id][$customer_group_id] = $group_name;
            }

            if( empty($finishTotal[$manual_group][$temp_tye_id][$temp_group_id]) ) {
                echo $this->element('blocks/revenues/tables/achievement_report', array(
                    'manual_group' => $temp_manual_group,
                    'type_id' => $temp_tye_id,
                    'customer_group_id' => $temp_group_id,
                    'groupName' => $groupName,
                    'totalCnt' => $totalCnt,
                    'totalTargetGroup' => $totalTargetGroup,
                    'totalPencapaianGroup' => $totalPencapaianGroup,
                    'grandTotalTargetGroup' => $grandTotalTargetGroup,
                    'grandTotalPencapaianGroup' => $grandTotalPencapaianGroup,
                ));
            }
?>
<tr>
    <?php 
            echo $this->Html->tag('td', '&nbsp;', array(
                'style' => 'font-weight: bold;'
            ));
            echo $this->Html->tag('td', __('TOTAL'), array(
                'style' => 'font-weight: bold;'
            ));

            if( isset($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $totalPencapaian = 0;
                    $totalTarget = 0;
                    $nameVal = 'totalPencapaian'.$i;
                    $nameValTarget = 'totalTarget'.$i;

                    if( !empty($$nameVal) ) {
                        $totalPencapaian = $$nameVal;
                    }
                    if( !empty($$nameValTarget) ) {
                        $totalTarget = $$nameValTarget;
                    }
                    echo $this->Html->tag('td', $this->Number->format($totalTarget, false, array('places' => 0)));
                    echo $this->Html->tag('td', $this->Number->format($totalPencapaian, false, array('places' => 0)));
                }
            }

            echo $this->Html->tag('td', $this->Number->format($grandTotalTarget, false, array('places' => 0)));
            echo $this->Html->tag('td', $this->Number->format($grandTotalPencapaian, false, array('places' => 0)));
    ?>
</tr>
<?php
        }
?>