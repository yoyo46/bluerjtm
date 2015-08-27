<?php 
        if( !empty($totalTargetGroup[$total_group]['cnt']) ) {
?>
<tr style="font-weight:bold;">
    <?php 
            $total_group = !empty($total_group)?$total_group:'-';
            $total_target = !empty($totalTargetGroup[$total_group]['total'])?$totalTargetGroup[$total_group]['total']:'-';
            $total_pencapaian = !empty($totalPencapaianGroup[$total_group]['total'])?$totalPencapaianGroup[$total_group]['total']:'-';

            echo $this->Html->tag('td', __('TOTAL ').$total_group);

            if( !empty($lastDay) ) {
                for ($i=1; $i <= $lastDay; $i++) {
                    $pencapaian = !empty($totalPencapaianGroup[$total_group][$i])?$totalPencapaianGroup[$total_group][$i]:'-';

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