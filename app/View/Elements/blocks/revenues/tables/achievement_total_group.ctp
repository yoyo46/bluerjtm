<?php 
        if( !empty($totalTargetGroup[$total_group]['cnt']) ) {
?>
<tr style="font-weight:bold;">
    <?php 
            $total_group = !empty($total_group)?$total_group:0;
            $total_target = !empty($totalTargetGroup[$total_group]['total'])?$totalTargetGroup[$total_group]['total']:0;
            $total_pencapaian = !empty($totalPencapaianGroup[$total_group]['total'])?$totalPencapaianGroup[$total_group]['total']:0;

            echo $this->Html->tag('td', __('TOTAL ').$total_group);

            if( isset($totalCnt) ) {
                for ($i=0; $i <= $totalCnt; $i++) {
                    $target = !empty($totalTargetGroup[$total_group][$i])?$totalTargetGroup[$total_group][$i]:0;
                    $pencapaian = !empty($totalPencapaianGroup[$total_group][$i])?$totalPencapaianGroup[$total_group][$i]:0;

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
<?php 
        }
?>