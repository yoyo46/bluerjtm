<div>
    <?php 
            if( !empty($result) ) {
                foreach ($result as $month => $total) {
                    $balance = !empty($total)?Common::getFormatPrice($total):0;

                    echo $this->Html->tag('div', $balance, array(
                        'class' => __('wrapper-coa-%s-%s', $month, $id),
                        'rel' => $month,
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
    ?>
</div>