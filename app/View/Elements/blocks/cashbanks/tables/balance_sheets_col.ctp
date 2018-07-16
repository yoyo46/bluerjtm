<?php 
        $coa_type = !empty($coa_type)?$coa_type:false;
        $element = 'blocks/cashbanks/tables/balance_sheets';

        $this->Html->addCrumb($module_title);

        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<div class="table-responsive">
    <?php 
            if(!empty($values)){
    ?>
    <table id="tt" class="table sorting">
        <thead>
            <tr>
                <?php
                        if( !empty($fieldColumn) ) {
                            echo $fieldColumn;
                        }
                ?>
            </tr>
        </thead>
        <tbody>
            <?php 
                    $balance = Common::hashEmptyField($values, 'balance');
                    $values = Common::_callUnset($values, array(
                        'balance',
                    ));

                    echo $this->element($element, array(
                        'values' => $values,
                        'coa_type' => $coa_type,
                        'main_total' => true,
                    ));
            ?>
            <tr>
                <?php 
                        echo $this->Html->tag('td', $this->Html->tag('strong', __('Total')), array(
                            'style' => 'padding-left:20px;',
                        ));
                        echo $this->Html->tag('td', Common::getFormatPrice($balance, 2), array(
                            'style' => 'text-align: right',
                        ));
                ?>
            </tr>
        </tbody>
    </table>
    <?php 
            } else {
                echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                    'class' => 'alert alert-warning text-center',
                ));
            }
    ?>
</div>