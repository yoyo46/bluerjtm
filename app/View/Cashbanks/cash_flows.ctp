<?php 
        $dataRequest = $this->request->data;
        $period_text = !empty($period_text)?$period_text:false;

        $start_day = $this->Common->formatDate($dateFrom, 'd');
        $full_name = $this->Common->filterEmptyField($User, 'Employe', 'full_name');
        $cashflows = $this->Common->filterEmptyField($data, 'Coas');
        $journalcoa = $this->Common->filterEmptyField($dataRequest, 'Search', 'journalcoa');

        $element = 'blocks/cashbanks/tables/cash_flows';
        $dataColumns = array(
            'coa' => array(
                'name' => __('COA'),
                'style' => 'text-align: left;width: 60%;',
            ),
            'total' => array(
                'name' => __('Total'),
                'style' => 'text-align: center;',
            ),
        );
        $fieldColumn = $this->Common->_generateShowHideColumn( $dataColumns, 'field-table' );

        if( $data_action == 'excel' ){
            $filename = sprintf('%s - %s', $sub_module_title, $period_text);
            header('Content-type: application/ms-excel');
            header('Content-Disposition: attachment; filename='.$filename.'.xls');
        } else {
            $this->Html->addCrumb($sub_module_title);
            echo $this->element('blocks/cashbanks/searchs/cash_flows');
        }

        if( !empty($journalcoa) ) {
?>
<div class="content invoice">
    <?php 
            echo $this->element('blocks/common/box_header', array(
                'title' => sprintf('%s - %s', $sub_module_title, $period_text),
                '_print' => array(
                    '_excel' => true,
                    '_pdf' => false,
                ),
            ));
    ?>
    <div class="table-responsive" id="cash-flow">
        <?php 
                if( !empty($cashflows) ){
                    ksort($cashflows);

                    foreach ($cashflows as $type => $cashflow) {
                        $transactions = $this->Common->filterEmptyField($data, 'CashFlow', $type);
        ?>
        <div class="box box-primary">
            <?php 
                    echo $this->element('blocks/common/box_header', array(
                        'title' => __('Cash %s', strtoupper($type)),
                    ));
            ?>
            <div class="box-body">
                <table id="tt" class="table table-bordered table-colored">
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
                                'data' => $data,
                                'values' => $cashflow,
                                'transactions' => $transactions,
                                'type' => $type,
                            )));
                    ?>
                </table>
            </div>
        </div>
        <?php 
                    }
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
<?php 
        }
?>