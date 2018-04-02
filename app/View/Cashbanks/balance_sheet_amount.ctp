<div>
    <?php 
            if( !empty($result) ) {
                foreach ($result as $month => $total) {
                    $balance = !empty($total)?$this->Common->getFormatPrice($total, false, 2):0;

                    echo $this->Html->tag('div', $balance, array(
                        'class' => __('wrapper-coa-%s-%s', $id, $month),
                    ));
                }
            }
    ?>
</div>