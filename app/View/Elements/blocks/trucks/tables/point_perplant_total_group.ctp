<?php 
        if( !empty($totalTargetGroup[$total_group]['cnt']) ) {
?>
<tr style="font-weight:bold;">
    <?php 
            $total_group = !empty($total_group)?$total_group:'-';
            $total_target = !empty($totalTargetGroup[$total_group]['total'])?$totalTargetGroup[$total_group]['total']:'-';
            $total_pencapaian = !empty($totalPencapaianGroup[$total_group]['total'])?$totalPencapaianGroup[$total_group]['total']:'-';

            echo $this->Html->tag('td', __('TOTAL ').$total_group);

            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_target), array(
                'style' => 'text-align:center;',
                'class' => 'test',
            ));
            echo $this->Html->tag('td', $this->Common->getFormatPrice($total_pencapaian), array(
                'style' => 'text-align:center;',
            ));

            if( !empty($branches) ) {
                foreach ($branches as $branch_id => $branch) {
                    $pencapaian = !empty($totalPencapaianGroup[$total_group][$branch_id])?$totalPencapaianGroup[$total_group][$branch_id]:'-';

                    echo $this->Html->tag('td', $pencapaian, array(
                        'style' => 'text-align:center;',
                    ));
                }
            }
    ?>
</tr>
<?php 
        }
?>