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
                }
            }
    ?>
</div>