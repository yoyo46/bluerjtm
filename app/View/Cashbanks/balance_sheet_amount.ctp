<div>
    <?php 
            if( !empty($result) ) {
                foreach ($result as $month => $total) {
                    $balance = !empty($total)?Common::getFormatPrice($total):0;
                    $op = ($total>=0)?'+':'-';

                    echo $this->Html->tag('div', $balance, array(
                        'class' => __('wrapper-coa-%s-%s', $id, $month),
                        'rel' => $tmpDateFrom,
                        'data-op' => $op,
                    ));
                    echo $this->Html->tag('div', $month, array(
                        'class' => 'coa-month',
                    ));
                    echo $this->Html->tag('div', $total, array(
                        'class' => 'coa-total',
                        'rel' => $month,
                    ));
                }
            }
            
            echo $this->Html->tag('div', $coa_type, array(
                'class' => 'coa-type',
                'rel' => $month,
            ));
    ?>
</div>