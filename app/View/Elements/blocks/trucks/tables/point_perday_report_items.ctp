<?php
        if(!empty($customers)){
            $grandTotalPencapaian = 0;
            $grandTotalTarget = 0;
            $grandTotalPencapaianGroup = array();
            $grandTotalTargetGroup = array();
            $groupName = array();
            $totalPencapaianGroup = array();
            $totalTargetGroup = array();
            $temp_type_id = false;
            $temp_group_id = false;
            $temp_manual_group = false;
            $finishTotal = array();
            $temp_total_group = false;
            $totalGroupTarget = array();
            $totalGroupPencapaian = array();
            $temp_total_delear_group = false;
            $totalDealerGroupTarget = array();
            $totalDealerGroupPencapaian = array();
            $finishTotalGroup = array();

            foreach ($customers as $key => $value) {
                $customer_id = $value['Customer']['id'];
                $customer_name = $this->Common->filterEmptyField($value, 'Customer', 'code', '-');
                $customer_group_id = $this->Common->filterEmptyField($value, 'Customer', 'customer_group_id', '-');
                $manual_group = $this->Common->filterEmptyField($value, 'Customer', 'manual_group');
                $total_group = $this->Common->filterEmptyField($value, 'Customer', 'total_group');
                $delear_group = $this->Common->filterEmptyField($value, 'Customer', 'delear_group');

                $type = $this->Common->filterEmptyField($value, 'CustomerType', 'name');
                $type_id = $this->Common->filterEmptyField($value, 'CustomerType', 'id');
                $group_name_depo = $this->Common->filterEmptyField($value, 'CustomerGroup', 'code_depo');
                $group_name_retail = $this->Common->filterEmptyField($value, 'CustomerGroup', 'code_retail');
                $branch_name = $this->Common->filterEmptyField($value, 'Branch', 'name');

                if( !empty($temp_manual_group) && !empty($manual_group) ) {
                    $customer_group_id = $temp_group_id;
                    $type_id = $temp_type_id;
                }

                if( !empty($temp_group_id) && !empty($temp_type_id) && ( $temp_group_id != $customer_group_id || $temp_type_id != $type_id || $temp_manual_group != $manual_group ) ) {
                    echo $this->element('blocks/trucks/tables/point_perday_report', array(
                        'manual_group' => $temp_manual_group,
                        'type_id' => $temp_type_id,
                        'customer_group_id' => $temp_group_id,
                        'groupName' => $groupName,
                        'lastDay' => $lastDay,
                        'totalTargetGroup' => $totalTargetGroup,
                        'totalPencapaianGroup' => $totalPencapaianGroup,
                        'grandTotalTargetGroup' => $grandTotalTargetGroup,
                        'grandTotalPencapaianGroup' => $grandTotalPencapaianGroup,
                    ));

                    $finishTotal[$manual_group][$temp_type_id][$temp_group_id] = true;
                }
                if( !empty($temp_total_group) && $temp_total_group != $total_group ) {
                    echo $this->element('blocks/trucks/tables/point_perday_total_group', array(
                        'total_group' => $temp_total_group,
                        'totalTargetGroup' => $totalGroupTarget,
                        'totalPencapaianGroup' => $totalGroupPencapaian,
                    ));

                    $finishTotalGroup['TotalGroup'][$temp_total_group] = true;
                }
                if( !empty($temp_total_delear_group) && $temp_total_delear_group != $delear_group ) {
                    echo $this->element('blocks/trucks/tables/point_perday_total_group', array(
                        'total_group' => $temp_total_delear_group,
                        'totalTargetGroup' => $totalDealerGroupTarget,
                        'totalPencapaianGroup' => $totalDealerGroupPencapaian,
                    ));

                    $finishTotalGroup['DelearGroup'][$temp_total_delear_group] = true;
                }

                $temp_group_id = $customer_group_id;
                $temp_type_id = $type_id;
                $temp_manual_group = $manual_group;
                $temp_total_group = $total_group;
                $temp_total_delear_group = $delear_group;
?>
<tr>
    <?php
            echo $this->Html->tag('td', $value['Customer']['code']);

            $totalMuatan = 0;
            for ($i=1; $i <= $lastDay; $i++) {
                $day = date('d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth))));
                $muatan = !empty($dataTtuj[$customer_id][$day])?$dataTtuj[$customer_id][$day]:0;
                
                if( !empty($muatan) ) {
                    $totalMuatan += $muatan;
                }
            }

            $target_rit = !empty($targetUnit[$customer_id][$currentMonth])?$targetUnit[$customer_id][$currentMonth]:0;
            echo $this->Html->tag('td', $target_rit, array(
                'style' => 'text-align: center;',
            ));

            echo $this->Html->tag('td', $totalMuatan, array(
                'style' => 'text-align: center;',
            ));

            $totalMuatan = 0;
            for ($i=1; $i <= $lastDay; $i++) {
                $day = date('d', mktime(0, 0, 0, date("m", strtotime($currentMonth)) , $i, date("Y", strtotime($currentMonth))));
                $muatan = !empty($dataTtuj[$customer_id][$day])?$dataTtuj[$customer_id][$day]:0;
                $muatanText = !empty($muatan)?$muatan:'-';
                $nameVal = 'totalPencapaian'.$i;

                if( empty($$nameVal) ) {
                    $$nameVal = 0;
                }

                if( !empty($muatan) ) {
                    $$nameVal += $muatan;
                    $totalMuatan += $muatan;
                }

                echo $this->Html->tag('td', $muatanText, array(
                    'style' => 'text-align: center;',
                ));

                if( !empty($totalPencapaianGroup[$i][$manual_group][$type_id][$customer_group_id]) ) {
                    $totalPencapaianGroup[$i][$manual_group][$type_id][$customer_group_id] += $muatan;
                } else {
                    $totalPencapaianGroup[$i][$manual_group][$type_id][$customer_group_id] = $muatan;
                }

                if( !empty($totalGroupPencapaian[$total_group][$i]) ) {
                    $totalGroupPencapaian[$total_group][$i] += $muatan;
                } else {
                    $totalGroupPencapaian[$total_group][$i] = $muatan;
                }

                if( !empty($totalDealerGroupPencapaian[$delear_group][$i]) ) {
                    $totalDealerGroupPencapaian[$delear_group][$i] += $muatan;
                } else {
                    $totalDealerGroupPencapaian[$delear_group][$i] = $muatan;
                }
            }

            $grandTotalPencapaian += $totalMuatan;
            $grandTotalTarget += $target_rit;
    ?>
</tr>
<?php 
                if( !empty($grandTotalPencapaianGroup[$manual_group][$type_id][$customer_group_id]) ) {
                    $grandTotalPencapaianGroup[$manual_group][$type_id][$customer_group_id] += $totalMuatan;
                } else {
                    $grandTotalPencapaianGroup[$manual_group][$type_id][$customer_group_id] = $totalMuatan;
                }
                if( !empty($grandTotalTargetGroup[$manual_group][$type_id][$customer_group_id]) ) {
                    $grandTotalTargetGroup[$manual_group][$type_id][$customer_group_id] += $target_rit;
                } else {
                    $grandTotalTargetGroup[$manual_group][$type_id][$customer_group_id] = $target_rit;
                }

                $groupName[$manual_group][$type_id][$customer_group_id]['name_depo'] = $group_name_depo;
                $groupName[$manual_group][$type_id][$customer_group_id]['name_retail'] = $group_name_retail;

                if( isset($groupName[$manual_group][$type_id][$customer_group_id]['cnt']) ) {
                    $groupName[$manual_group][$type_id][$customer_group_id]['cnt']++;
                } else {
                    $groupName[$manual_group][$type_id][$customer_group_id]['cnt'] = 0;
                }

                if( isset($totalGroupTarget[$total_group]['total']) ) {
                    $totalGroupTarget[$total_group]['total'] += $target_rit;
                    $totalGroupTarget[$total_group]['cnt']++;
                } else {
                    $totalGroupTarget[$total_group]['total'] = $target_rit;
                    $totalGroupTarget[$total_group]['cnt'] = 0;
                }

                if( !empty($totalGroupPencapaian[$total_group]['total']) ) {
                    $totalGroupPencapaian[$total_group]['total'] += $totalMuatan;
                } else {
                    $totalGroupPencapaian[$total_group]['total'] = $totalMuatan;
                }

                if( isset($totalDealerGroupTarget[$delear_group]['total']) ) {
                    $totalDealerGroupTarget[$delear_group]['total'] += $target_rit;
                    $totalDealerGroupTarget[$delear_group]['cnt']++;
                } else {
                    $totalDealerGroupTarget[$delear_group]['total'] = $target_rit;
                    $totalDealerGroupTarget[$delear_group]['cnt'] = 0;
                }

                if( !empty($totalDealerGroupPencapaian[$delear_group]['total']) ) {
                    $totalDealerGroupPencapaian[$delear_group]['total'] += $totalMuatan;
                } else {
                    $totalDealerGroupPencapaian[$delear_group]['total'] = $totalMuatan;
                }
            }

            if( empty($finishTotal[$manual_group][$temp_type_id][$temp_group_id]) && !empty($groupName[$manual_group][$temp_type_id][$temp_group_id]['cnt']) ) {
                echo $this->element('blocks/trucks/tables/point_perday_report', array(
                    'manual_group' => $temp_manual_group,
                    'type_id' => $temp_type_id,
                    'customer_group_id' => $temp_group_id,
                    'groupName' => $groupName,
                    'lastDay' => $lastDay,
                    'totalTargetGroup' => $totalTargetGroup,
                    'totalPencapaianGroup' => $totalPencapaianGroup,
                    'grandTotalTargetGroup' => $grandTotalTargetGroup,
                    'grandTotalPencapaianGroup' => $grandTotalPencapaianGroup,
                ));
            }
            if( !empty($finishTotalGroup['TotalGroup'][$temp_total_group]) ) {
                echo $this->element('blocks/trucks/tables/point_perday_total_group', array(
                    'total_group' => $temp_total_group,
                    'totalTargetGroup' => $totalGroupTarget,
                    'totalPencapaianGroup' => $totalGroupPencapaian,
                ));
            }
            if( !empty($finishTotalGroup['DelearGroup'][$temp_total_delear_group]) ) {
                echo $this->element('blocks/trucks/tables/point_perday_total_group', array(
                    'total_group' => $temp_total_delear_group,
                    'totalTargetGroup' => $totalDealerGroupTarget,
                    'totalPencapaianGroup' => $totalDealerGroupPencapaian,
                ));
            }
?>
<tr>
    <?php 
            echo $this->Html->tag('td', __('TOTAL'), array(
                'style' => 'font-weight: bold;'
            ));

            echo $this->Html->tag('td', $this->Number->format($grandTotalTarget, false, array('places' => 0)), array(
                'style' => 'text-align: center;',
            ));
            echo $this->Html->tag('td', $this->Number->format($grandTotalPencapaian, false, array('places' => 0)), array(
                'style' => 'text-align: center;',
            ));

            if( isset($lastDay) ) {
                for ($i=1; $i <= $lastDay; $i++) {
                    $totalPencapaian = 0;
                    $nameVal = 'totalPencapaian'.$i;

                    if( !empty($$nameVal) ) {
                        $totalPencapaian = $$nameVal;
                    }
                    echo $this->Html->tag('td', $this->Number->format($totalPencapaian, false, array('places' => 0)), array(
                        'style' => 'text-align: center;',
                    ));
                }
            }
    ?>
</tr>
<?php
        }else{
            echo $this->Html->tag('tr', $this->Html->tag('td', __('Data tidak ditemukan'), array(
                'colspan' => '7'
            )));
        }
?>