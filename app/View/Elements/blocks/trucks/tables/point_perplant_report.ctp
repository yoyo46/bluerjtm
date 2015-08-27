<?php 
        $name_depo = !empty($groupName[$manual_group][$type_id][$customer_group_id]['name_depo'])?$groupName[$manual_group][$type_id][$customer_group_id]['name_depo']:'-';
        $name_retail = !empty($groupName[$manual_group][$type_id][$customer_group_id]['name_retail'])?$groupName[$manual_group][$type_id][$customer_group_id]['name_retail']:'-';

        if( !empty($groupName[$manual_group][$type_id][$customer_group_id]['cnt']) && ( $name_depo != '-' || $manual_group ) ) {
?>
<tr style="font-weight:bold;">
    <?php 
            $total_target = !empty($grandTotalTargetGroup[$manual_group][$type_id][$customer_group_id])?$grandTotalTargetGroup[$manual_group][$type_id][$customer_group_id]:'-';
            $total_pencapaian = !empty($grandTotalPencapaianGroup[$manual_group][$type_id][$customer_group_id])?$grandTotalPencapaianGroup[$manual_group][$type_id][$customer_group_id]:'-';

            if( !empty($manual_group) ) {
                echo $this->Html->tag('td', __('TOTAL ').$manual_group);
            } else {
                switch ($type_id) {
                    case '1':
                        echo $this->Html->tag('td', __('TOTAL ').$name_retail);
                        break;
                    
                    default:
                        echo $this->Html->tag('td', __('TOTAL ').$name_depo);
                        break;
                }
            }
            
            if( !empty($branches) ) {
                foreach ($branches as $branch_id => $branch) {
                    $pencapaian = !empty($totalPencapaianGroup[$branch_id][$manual_group][$type_id][$customer_group_id])?$totalPencapaianGroup[$branch_id][$manual_group][$type_id][$customer_group_id]:'-';

                    echo $this->Html->tag('td', $pencapaian, array(
                        'style' => 'text-align:center;',
                    ));
                }
            }

            echo $this->Html->tag('td', $total_pencapaian, array(
                'style' => 'text-align:center;',
            ));
            echo $this->Html->tag('td', $total_target, array(
                'style' => 'text-align:center;',
                'class' => 'test',
            ));
    ?>
</tr>
<?php 
        }
?>