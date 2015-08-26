<tr style="font-weight:bold;">
    <?php 
            $group_name = !empty($groupName[$manual_group][$type_id][$customer_group_id])?$groupName[$manual_group][$type_id][$customer_group_id]:'-';
            $total_target = !empty($grandTotalTargetGroup[$manual_group][$type_id][$customer_group_id])?$grandTotalTargetGroup[$manual_group][$type_id][$customer_group_id]:'-';
            $total_pencapaian = !empty($grandTotalPencapaianGroup[$manual_group][$type_id][$customer_group_id])?$grandTotalPencapaianGroup[$manual_group][$type_id][$customer_group_id]:'-';

            echo $this->Html->tag('td', '');

            if( !empty($manual_group) ) {
                echo $this->Html->tag('td', __('TOTAL MPM [MD-DLR]'));
            } else {
                echo $this->Html->tag('td', __('TOTAL ').$group_name);
            }

            if( !empty($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $target = !empty($totalTargetGroup[$i][$manual_group][$type_id][$customer_group_id])?$totalTargetGroup[$i][$manual_group][$type_id][$customer_group_id]:'-';
                    $pencapaian = !empty($totalPencapaianGroup[$i][$manual_group][$type_id][$customer_group_id])?$totalPencapaianGroup[$i][$manual_group][$type_id][$customer_group_id]:'-';

                    echo $this->Html->tag('td', $target, array(
                        'style' => 'text-align:center;',
                    ));
                    echo $this->Html->tag('td', $pencapaian, array(
                        'style' => 'text-align:center;',
                    ));
                }
            }

            echo $this->Html->tag('td', $total_target, array(
                'style' => 'text-align:center;',
                'class' => 'test',
            ));
            echo $this->Html->tag('td', $total_pencapaian, array(
                'style' => 'text-align:center;',
            ));
    ?>
</tr>