<?php 
        $element = 'blocks/cashbanks/tables/profit_loss_per_point';
        $full_name = !empty($User['Employe']['full_name'])?$User['Employe']['full_name']:false;
        $dataColumns = array(
            'cogs_name' => array(
                'name' => __('Cost Center'),
                'style' => 'text-align: left;vertical-align: middle;',
                'data-options' => 'field:\'cogs_name\',width:300,styler:cellStyler',
                'align' => 'left',
            ),
            'revenue' => array(
                'name' => __('Revenue'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'revenue\',width:100',
                'align' => 'right',
                'class' => 'string',
            ),
            'expense' => array(
                'name' => __('Expense'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'expense\',width:100',
                'align' => 'right',
                'class' => 'string',
            ),
            'maintenance' => array(
                'name' => __('Maintenance'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'maintenance\',width:100',
                'align' => 'right',
                'class' => 'string',
            ),
            'gross_profit' => array(
                'name' => __('Gross Profit'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'gross_profit\',width:100',
                'align' => 'right',
                'class' => 'string',
            ),
            'er' => array(
                'name' => __('E/R (%)'),
                'style' => 'text-align: center;vertical-align: middle;',
                'data-options' => 'field:\'er\',width:100',
                'align' => 'center',
                'class' => 'string',
            ),
        );

        if( !empty($data_action) ){
            $filename = $this->Common->toSlug($sub_module_title);

            header('Content-type: application/ms-excel');
            header('Content-Disposition: attachment; filename='.$filename.'.xls');
        } else {
            $this->Html->addCrumb($module_title);

            echo $this->element('blocks/cashbanks/searchs/profit_loss_per_point');
        }

        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );
?>
<section class="content invoice">
    <h2 class="page-header">
        <i class="fa fa-globe"></i> <?php echo $sub_module_title;?>
    </h2>
    <?php 
            if( empty($data_action) ){
                echo $this->Common->_getPrint(array(
                    '_attr' => array(
                        'escape' => false,
                    ),
                ));
            }
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
            <?php 
                    echo $this->Html->tag('tbody', $this->element($element, array(
                        'values' => Common::hashEmptyField($values, 'data'),
                        'result' => $values,
                    )));
            ?>
        </table>
        <?php 
                } else {
                    echo $this->Html->tag('p', __('Data belum tersedia.'), array(
                        'class' => 'alert alert-warning text-center',
                    ));
                }
        ?>
    </div><!-- /.box-body -->
    <?php 
            echo $this->Html->tag('div', sprintf(__('Printed on : %s, by : %s'), date('d F Y'), $this->Html->tag('span', $full_name)), array(
                'style' => 'font-size: 14px;font-style: italic;margin-top: 10px;'
            ));
    ?>
</div>